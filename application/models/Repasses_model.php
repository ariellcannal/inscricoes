<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Repasses_model extends SYS_Model
{

    protected string $prefix = 'rep_';

    protected string $table = 'repasses';

    protected int $retencaoRepasse = 0;

    /**
     * Construtor da classe Operadoras_model.
     */
    public function __construct()
    {
        parent::__construct();
        return $this;
    }

    function getRepassesPorUsuario($usr_id, $months = null, $retornar_grupos = false, $order = 'ASC')
    {
        if ($retornar_grupos) {
            $this->db->select("grp_id, grp_nome", false);
        }
        $this->db->select("rec.rec_id AS rec_id, rec_parcela AS parcela, rre.rre_porcentagemUsuario AS porcentagem, DATE_FORMAT(rep.rep_efetivado,'%d/%m/%Y') as data, grp.grp_nome as grupo, alu_nomeArtistico as aluno, SUM(rre_valor) as valor", false);
        $this->db->from('repasses rep');
        $this->db->join('usuarios usr', 'rep.rep_usuario = usr.usr_id');
        $this->db->join('recebiveis_repasses rre', 'rre.rre_repasse = rep.rep_id');
        $this->db->join('recebiveis rec', 'rec.rec_id = rre.rre_recebivel');
        $this->db->join('inscricoes ins', 'rec.rec_inscricao = ins.ins_id');
        $this->db->join('grupos grp', 'grp.grp_id = ins.ins_grupo');
        $this->db->join('alunos alu', 'alu.alu_id = ins.ins_aluno');
        $this->db->where('rep_usuario', $usr_id);
        $this->db->where('rre.rre_usuario', $usr_id);
        if (! is_null($months)) {
            $this->db->where('rep_efetivado >=', $months);
        } else {
            $this->db->where('rep_efetivado', $months, false);
        }
        if ($retornar_grupos) {
            $this->db->group_by('grp.grp_id');
        }
        $this->db->group_by('rec_inscricao');
        $this->db->group_by('rep_efetivado');
        $this->db->group_by('rep.rep_usuario');
        $this->db->order_by('rep_efetivado ' . $order);
        $this->db->order_by('grupo ASC');
        $this->db->order_by('alu_nome ASC');
        $this->db->order_by('valor DESC');
        $r = $this->db->get();
        return $r->result_array();
    }

    function getRelatorioPorUsuario($usr_id, $months)
    {
        $usr_id = (int) $usr_id;
        $grupos = $this->getRepassesPorUsuario($usr_id, $months, true);
        $retorno = [];
        foreach ($grupos as $grp) {
            $grp_id = (int) $grp['grp_id'];
            $retorno[$grp_id]['grupo'] = $grp['grp_nome'];
            $retorno[$grp_id]['total_grupo'] = $this->db
                ->select('SUM(rec_valorLiquido) as totalGrupo')
                ->from('recebiveis')
                ->join('inscricoes', 'ins_id = rec_inscricao')
                ->where('ins_grupo', $grp_id)
                ->get()
                ->row_array()['totalGrupo'];
            $retorno[$grp_id]['repassado'] = $this->db
                ->select('SUM(rre_valor) as repassado')
                ->from('recebiveis_repasses')
                ->join('recebiveis', 'rre_recebivel = rec_id')
                ->join('inscricoes', 'ins_id = rec_inscricao')
                ->where('rre_usuario', $usr_id)
                ->where('ins_grupo', $grp_id)
                ->get()
                ->row_array()['repassado'];
            $r = $this->db
                ->select('SUM(rec_valorLiquido*(dst_porcentagem/100)) as aRepassar, dst.dst_porcentagem')
                ->from('recebiveis')
                ->join('inscricoes', 'ins_id = rec_inscricao')
                ->join('grupos_distribuicao dst', 'ins_grupo = dst_grupo')
                ->where('dst_usuario', $usr_id)
                ->where('ins_grupo', $grp_id)
                ->get()
                ->row_array();
            $retorno[$grp_id]['a_repassar'] = $r['aRepassar'] - $retorno[$grp_id]['repassado'];
            $retorno[$grp_id]['porcentagem'] = $r['dst_porcentagem'];
        }
        return $retorno;
    }

    function getRepassesPrevistosPorUsuario($usr_id)
    {
        $this->db->select(" rec.rec_id AS rec_id,
                            rec.rec_parcela AS parcela,
                            grp.grp_nome AS grupo,
                            alu.alu_nomeArtistico AS aluno,
                            ROUND((rec.rec_valorLiquido*(dst.dst_porcentagem/100)),2) AS valor,
                            dst.dst_porcentagem AS porcentagem,
                            rec.rec_dataTransacao, 
                            rec.rec_dataRecebimento,
                            DATA_RECEBIMENTO(rec.rec_dataTransacao,rec.rec_dataRecebimento) AS rec_dataSegura,
                            DATE_FORMAT(CASE WHEN DAYOFWEEK(DATA_RECEBIMENTO(rec.rec_dataTransacao,rec.rec_dataRecebimento)) = 3 THEN DATA_RECEBIMENTO(rec.rec_dataTransacao,rec.rec_dataRecebimento)
                            ELSE DATE_ADD(DATA_RECEBIMENTO(rec.rec_dataTransacao,rec.rec_dataRecebimento), INTERVAL (10 - DAYOFWEEK(DATA_RECEBIMENTO(rec.rec_dataTransacao,rec.rec_dataRecebimento))) % 7 DAY)
                            END,'%d/%m/%Y') AS proximoRepasse", false);
        $this->db->from('recebiveis rec');
        $this->db->join('operadoras_transacoes otr', 'rec.rec_transacao = otr.otr_id AND otr.otr_confirmada = 1','LEFT');
        $this->db->join('inscricoes ins', 'rec.rec_inscricao = ins.ins_id');
        $this->db->join('grupos grp', 'grp.grp_id = ins.ins_grupo');
        $this->db->join('alunos alu', 'alu.alu_id = ins.ins_aluno');
        $this->db->join('grupos_distribuicao dst', 'dst.dst_grupo = grp.grp_id');
        $this->db->where('dst.dst_usuario', $usr_id);
        $this->db->where('rec.rec_valorLiquido >', '0');
        $this->db->where('grp.grp_repasseAtivado', '1');
        $this->db->where('rec.rec_id NOT IN (SELECT rre.rre_recebivel FROM recebiveis_repasses rre WHERE rre_repasse IS NOT NULL)', null, false);
        $this->db->order_by('rec_dataRecebimento', 'ASC');
        //print $query = $this->db->get_compiled_select();exit();
        $r = $this->db->get();
        return $r->result_array();
    }

    function efetivarRepasse($rep_id)
    {
        $this->db->where('rep_efetivado', null);
        $this->db->update('repasses', array(
            'rep_efetivado' => date('Y-m-d H:i:s')
        ), array(
            'rep_id' => $rep_id
        ));
        if ($this->db->affected_rows()) {
            $this->db->join('usuarios', 'rep_usuario = usr_id');
            $r = $this->db->get_where('repasses', [
                'rep_id' => $rep_id
            ]);
            return $r->row_array();
        } else {
            return false;
        }
    }

    function desefetivar($rep_id)
    {
        $this->db->where('rep_efetivado IS NOT NULL', null, false);
        $this->db->update('repasses', array(
            'rep_efetivado' => null
        ), array(
            'rep_id' => $rep_id
        ));
        return $this->db->affected_rows();
    }
    
    function getRepassesEmRetencao(bool $retornarTotal = false){
        if($retornarTotal){
            $this->db->select('SUM(rre_valor) as total');
        }
        else{
            $this->db->select('rre_id,rre_usuario,rre_valor');
        }
        
        $this->db->join('recebiveis', 'rre_recebivel = rec_id');
        $this->db->where('rre_repasse', null);
        $this->db->where('rec_dataTransacao >=', date('Y-m-d', strtotime('- ' . $this->retencaoRepasse . ' days')) . ' 00:00:00');
        //$query = $this->db->get_compiled_select('recebiveis_repasses');exit($query);
        if($retornarTotal){
            return $this->db->get('recebiveis_repasses')->row_array()['total'];
        }
        else{
            return $this->db->get('recebiveis_repasses')->result_array();
        }
        
    }

    function consolidar()
    {
        $this->db->select('rre_id,rre_usuario,rre_valor');
        $this->db->join('recebiveis', 'rre_recebivel = rec_id');
        $this->db->where('rre_repasse', null);
        $this->db->where('rec_dataTransacao <', date('Y-m-d', strtotime('- ' . $this->retencaoRepasse . ' days')) . ' 00:00:00');
        // $query = $this->db->get_compiled_select();exit($query);
        $rre = $this->db->get('recebiveis_repasses')->result_array();
        foreach ($rre as $row) {
            $t[$row['rre_usuario']][$row['rre_id']] = $row['rre_valor'];
        }
        if (isset($t)) {
            foreach ($t as $usr => $rre) {
                $rep['rep_usuario'] = $usr;
                $rep['rep_valor'] = 0;
                $rep['rep_data'] = date('Y-m-d H:i:s');
                $update_rre = [];
                foreach ($rre as $rre_id => $rre_valor) {
                    $rep['rep_valor'] += $rre_valor;
                    $update_rre[] = $rre_id;
                }
                if ($rep['rep_valor'] > 0) {
                    $this->db->insert('repasses', $rep);
                    
                    $this->db->where_in('rre_id', $update_rre);
                    $this->db->update('recebiveis_repasses', [
                        'rre_repasse' => $this->db->insert_id()
                    ]);
                }
            }
            return true;
        }
        return null;
    }

    function mesclar(array $ids)
    {
        if (count($ids) < 2) {
            return false;
        }
        $this->db->where_in('rep_id', $ids);
        $this->db->where('rep_efetivado', null);
        $this->db->order_by('rep_data', 'ASC');
        $rows = $this->db->get('repasses')->result_array();
        $id_destino = end($rows)['rep_id'];
        $update = [
            'rep_valor' => 0
        ];
        $delete = [];
        foreach ($rows as $row) {
            $id_origem = $row['rep_id'];
            foreach ($row as $k => $v) {
                if (! in_array($k, [
                    'rep_valor',
                    'rep_id'
                ]) && (! isset($update[$k]) || empty($update[$k]))) {
                    $update[$k] = $v;
                }
            }
            $update['rep_valor'] += $row['rep_valor'];

            $this->db->set('rre_repasse', $id_destino);
            $this->db->where('rre_repasse', $id_origem);
            $this->db->update('recebiveis_repasses');
            if ($id_origem != $id_destino) {
                $delete[] = $id_origem;
            }
        }
        if (count($update)) {
            // var_dump($update);
            $this->db->set($update);
            $this->db->where('rep_id', $id_destino);
            $this->db->update('repasses');
        }
        if (count($delete)) {
            $this->db->where_in('rep_id', $delete);
            $this->db->delete('repasses');
        }
        return true;
    }
}
