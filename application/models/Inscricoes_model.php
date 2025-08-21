<?php

class Inscricoes_model extends SYS_Model
{

    protected string $prefix = 'ins_';

    protected string $table = 'inscricoes';
    
    /**
     * Construtor padrão.
     * Aciona o construtor da classe pai.
     */
    public function __construct()
    {
        parent::__construct();
        return $this;
    }

    public function inserir($ins)
    {
        $this->db->insert('inscricoes', $ins);
        return $this->db->insert_id();
    }

    public function getInscricaoCompleta($ins_id)
    {
        $this->db->select('CONCAT(UCASE(LEFT(SUBSTRING_INDEX(`alu_nomeArtistico`, " ", 1), 1)), LCASE(SUBSTRING(SUBSTRING_INDEX(`alu_nomeArtistico`, " ", 1), 2))) as alu_primeiroNome, alunos.*, DATE_FORMAT(alu_nascimento,"%d/%m/%Y") as alu_nascimento_br, grupos.*, inscricoes.*');
        $this->db->join('alunos', 'ins_aluno = alu_id');
        $this->db->join('grupos', 'ins_grupo = grp_id');
        return $this->db->get_where('inscricoes', array(
            'ins_id' => $ins_id
        ))->row_array();
    }

    function unificaInscricao($ins_grupo, $ins_aluno, $ins_id_denitivo)
    {
        $this->db->select('ins_id');
        $this->db->order_by('ins_data', 'ASC');
        $ins = $this->db->get_where('inscricoes', array(
            'ins_grupo' => $ins_grupo,
            'ins_aluno' => $ins_aluno
        ))->result_array();
        $ins_def = [];
        foreach ($ins as $i) {
            foreach ($i as $k => $v) {
                if (! empty($v) && empty($ins_def[$k]) && $k != 'ins_id') {
                    $ins_def[$k] = $v;
                }
            }

            if ($i['ins_id'] != $ins_id_denitivo) {
                // atualizar creditos
                $this->db->set('alc_inscricao', $ins_id_denitivo);
                $this->db->where('alc_inscricao', $i['ins_id']);
                $this->db->update('alunos_creditos');

                // atualizar recebiveis
                $this->db->set('rec_inscricao', $ins_id_denitivo);
                $this->db->where('rec_inscricao', $i['ins_id']);
                $this->db->update('recebiveis');

                $this->db->where('ins_id', $i['ins_id']);
                $this->db->delete('inscricoes');
            }
        }
        if (! empty($ins_def)) {
            $this->db->set($ins_def);
            $this->db->where('ins_id', $ins_id_denitivo);
            $this->db->update('inscricoes');
        }
    }

    function setTotaisInscricao($ins_id)
    {
        $ins = $this->db->get_where('inscricoes', [
            'ins_id' => $ins_id
        ]);
        $ins = $ins->row_array();

        $gfp = $this->db->get_where('grupos_formas', [
            'gfp_id' => $ins['ins_forma']
        ]);
        $gfp = $gfp->row_array();

        $valorModulo = $gfp['gfp_valorTotal'] - $ins['ins_valorDesconto'];

        $this->db->select('IFNULL(SUM(otr_valorBruto),0) as totalPago', false);
        $this->db->where('otr_confirmada', '1');
        $this->db->where('otr_inscricao', $ins_id);
        $totalPago = $this->db->get('operadoras_transacoes')->row_array()['totalPago'];

        $this->db->select('IFNULL(SUM(rec_valor),0) as totalPago', false);
        $this->db->where('rec_transacao', null);
        $this->db->where('rec_inscricao', $ins_id);
        $totalPago += $this->db->get('recebiveis')->row_array()['totalPago'];

        $totalDevido = $valorModulo - $totalPago;

        if ($ins['ins_status'] != "Cancelada") {
            if ($valorModulo == (float) $ins['ins_valorDesconto']) {
                $this->db->set('ins_status', 'Confirmada');
            } else if ($totalDevido >= $valorModulo) {
                $this->db->set('ins_status', 'Pendente');
            } else if ($totalDevido > 0) {
                $this->db->set('ins_status', 'Devedora');
            } else if ($totalDevido <= 0) {
                $this->db->set('ins_status', 'Confirmada');
            }
        }
        $this->db->set('ins_valorModulo', $valorModulo);
        $this->db->set('ins_valorTotalPago', $totalPago);
        $this->db->set('ins_valorDevido', $totalDevido);
        $this->db->where('ins_id', $ins_id);
        $this->db->update('inscricoes');
    }

    function getInscricaoPorTransacaoId($transacao_id)
    {
        $this->db->select('otr_inscricao');
        $this->db->where('otr_operadoraID', $transacao_id);
        $otr = $this->db->get('operadoras_transacoes')->row_array();

        $this->db->where('ins_id', $otr['otr_inscricao']);
        return $this->db->get('inscricoes')->row_array();
    }

    function getInscricoesGruposAtivos()
    {
        $this->db->join('grupos', 'ins_grupo = grp_id');
        $this->db->where('grp_ativo', '1');
        return $this->db->get('inscricoes')->result_array();
    }

    function checkInscricao($grp_id, $alu_id)
    {
        return $this->db->get_where('inscricoes', array(
            'ins_grupo' => $grp_id,
            'ins_aluno' => $alu_id
        ))->row_array();
    }

    public function removerInscricao($ins_id)
    {
        $this->db->where('otr_confirmada', '1');
        $this->db->where('otr_inscricao', $ins_id);
        if ($this->db->count_all_results('operadoras_transacoes')) {
            return false;
        }

        $this->db->where('rec_recebido', '1');
        $this->db->where('rec_inscricao', $ins_id);
        if ($this->db->count_all_results('recebiveis')) {
            return false;
        }

        $this->db->where('rec_inscricao', $ins_id);
        $this->db->delete('recebiveis');

        $this->db->where('otr_inscricao', $ins_id);
        $this->db->delete('operadoras_transacoes');

        $this->db->where('ins_id', $ins_id);
        $this->db->delete('inscricoes');

        return $this->db->affected_rows();
    }
}
