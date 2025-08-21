<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Recebiveis_model extends SYS_Model
{

    protected string $prefix = 'rec_';

    protected string $table = 'recebiveis';

    /**
     * Construtor da classe Recebiveis_model.
     */
    public function __construct()
    {
        parent::__construct();
        return $this;
    }

    public function confirmar($rec_id, $rec_valorLiquido = null, $rec_dataRecebimento = null)
    {
        $rec = $this->getRecebiveisCompleto($rec_id);
        if (! $rec['rec_valorLiquido']) {
            return [
                'status' => false,
                'message' => 'O valor líquido está zerado.'
            ];
        } else if ($rec['rec_valorLiquido'] > $rec['rec_valor']) {
            return [
                'status' => false,
                'message' => 'O valor líquido é maior que o valor bruto.'
            ];
        }
        if ($rec['grp_repasseAtivado'] != '1') {
            return [
                'status' => false,
                'message' => 'Os repasses deste grupo estão bloqueados.'
            ];
        }

        $this->db->where('dst_grupo', $rec['ins_grupo']);
        $dst = $this->db->get('grupos_distribuicao')->result_array();
        $soma = 0;
        foreach ($dst as $row) {
            $soma += $row['dst_porcentagem'];
        }
        if ($soma != 100) {
            return [
                'status' => false,
                'message' => 'A soma da distribuição é diferente de 100%.'
            ];
        }

        /* TUDO CERTO - CONFIRMA */
        $set = [
            'rec_recebido' => 1
        ];
        if ($rec_dataRecebimento) {
            $set['rec_dataRecebimento'] = $rec_dataRecebimento;
        } else {
            $set['rec_dataRecebimento'] = date('Y-m-d H:i:s');
        }
        if ($rec_valorLiquido) {
            $set['rec_valorLiquido'] = $rec_valorLiquido;
        }
        if ($this->db->update('recebiveis', $set, [
            'rec_id' => $rec_id
        ])) {
            $repasses = (int) $this->inserirRepasses($rec_id);
        } else {
            $repasses = 0;
        }
        $ci = &get_instance();
        $ci->load->model('inscricoes_model');
        $ci->inscricoes_model->setTotaisInscricao($rec['rec_inscricao']);
        return [
            'status' => 'true',
            'message' => $repasses . ' repasse(s) inserido(s)'
        ];
    }

    function inserirRepasses($rec_id)
    {
        $this->db->join('inscricoes', 'rec_inscricao = ins_id');
        $this->db->where('rec_id', $rec_id);
        $rec = $this->db->get('recebiveis')->row_array();

        /* VERIFICA SE JÁ NÃO EXISTE REPASSES */
        $teste = $this->db->get_where('recebiveis_repasses', [
            'rre_recebivel' => $rec_id
        ])->result_array();
        if ($teste) {
            /* repasses já inseridos */
            return false;
        }

        $this->db->where('dst_grupo', $rec['ins_grupo']);
        $dst = $this->db->get('grupos_distribuicao')->result_array();
        $soma = 0;
        foreach ($dst as $row) {
            $valor_rep = $rec['rec_valorLiquido'] * ($row['dst_porcentagem'] / 100);
            $valor_rep = $rec['rec_valorLiquido'] * ($row['dst_porcentagem'] / 100);
            $strValue = strval($valor_rep);
            $parts = explode('.', $strValue);
            if (count($parts) == 2) {
                $cents = substr($parts[1], 0, 2);
                $cents = str_pad($cents, 2, '0', STR_PAD_RIGHT);
            } else {
                $cents = '00';
            }
            $valor_rep = $parts[0] . '.' . $cents;
            
            $valor_rep = round($rec['rec_valorLiquido'] * ($row['dst_porcentagem'] / 100), 2);
            $soma += $valor_rep;
            $rre[] = array(
                'rre_recebivel' => $rec_id,
                'rre_usuario' => $row['dst_usuario'],
                'rre_porcentagemUsuario' => $row['dst_porcentagem'],
                'rre_valor' => $valor_rep
            );
        }
        if (isset($rre)) {
            do {
                $soma = 0;
                foreach ($rre as $key => $row) {
                    $rre[$key]['rre_valor'] -= 0.01;
                    $soma += $rre[$key]['rre_valor'];
                }
            } while ($soma > $rec['rec_valorLiquido']);
            $this->db->insert_batch('recebiveis_repasses', $rre);
            return $this->db->affected_rows();
        }
        return null;
    }

    function desconfirmar($rec_id)
    {
        $this->db->where('rre_recebivel', $rec_id);
        $this->db->delete('recebiveis_repasses');

        $this->db->update('recebiveis', array(
            'rec_recebido' => 0
        ), array(
            'rec_id' => $rec_id
        ));
        return $this->db->affected_rows();
    }

    function checkRepasse($rec_id)
    {
        $this->db->where('rre_repasse <>', null);
        $this->db->join('recebiveis_repasses', 'recebiveis_repasses.rre_repasse = repasses.rep_id');
        $this->db->where('recebiveis_repasses.rre_recebivel', $rec_id);
        $total = $this->db->count_all_results('repasses');
        return $total;
    }

    function getRecebiveisCompleto($rec_id)
    {
        $this->db->where('rec_id', $rec_id);
        $this->db->join('inscricoes', 'rec_inscricao = ins_id');
        $this->db->join('alunos', 'ins_aluno = alu_id');
        $this->db->join('grupos', 'ins_grupo = grp_id');
        return $this->db->get('recebiveis')->row_array();
    }

    function getRecebiveisPrevistos($data_ate)
    {
        $this->db->where('rec_dataRecebimento <=', date('Y-m-d', strtotime($data_ate)));
        $this->db->where('rec_recebido', false);
        $result = $this->db->get('recebiveis');
        //echo $this->db->last_query();exit;
        return $result->result_array();
    }

    function incluirRecebiveis($recebiveis)
    {
        $this->db->insert_batch('recebiveis', $recebiveis);
        return $this->db->affected_rows();
    }

    function incluirRecebivel($recebivel)
    {
        $this->db->insert('recebiveis', $recebivel);
        return $this->db->insert_id();
    }

    function getRecebiveisPendentes($rec_id = null)
    {
        if ($rec_id) {
            $this->db->where('rec_id', $rec_id);
        }
        $this->db->where('rec_recebido', '0');
        $this->db->where('rec_operadoraStatus', 'paid');
        $r = $this->db->get('recebiveis');
        return $r->result_array();
    }

    public function getRecebiveisPorTransacao($rec_transacao, $rec_parcela = null)
    {
        if ($rec_parcela) {
            $this->db->where('rec_parcela', $rec_parcela);
        }
        $this->db->where('rec_transacao', $rec_transacao);
        $r = $this->db->get('recebiveis');
        return $r->result_array();
    }

    public function getRecebiveisPorOperadoraId($rec_operadoraId)
    {
        $this->db->where('rec_operadoraID', $rec_operadoraId);
        $r = $this->db->get('recebiveis');
        return $r->result_array();
    }

    public function removerFromTransacao($rec_transacao)
    {
        $this->db->where('rec_transacao', $rec_transacao);
        $this->db->delete('recebiveis');
        return $this->db->affected_rows();
    }
}
