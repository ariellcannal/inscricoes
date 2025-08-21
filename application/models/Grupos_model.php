<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Grupos_model extends SYS_Model
{

    protected string $table = 'grupos';

    protected string $prefix = 'grp_';

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        parent::__construct();
        return $this;
    }

    function getBySlug($grp_slug)
    {
        return $this->db->get_where('grupos', array(
            'grp_slug' => $grp_slug
        ))->row_array();
    }

    function getGrupoPresenca()
    {
        $time_now = date('H:i');
        $date_now = date('Y-m-d');
        $this->db->where('grp_ativo', '1');
        $this->db->where('FIND_IN_SET(grp_diaSemana,"' . date('w') . '")', null, false);
        $this->db->where('grp_dataInicio <=', $date_now);
        $this->db->where('grp_dataFim >=', $date_now);
        $this->db->where('ADDTIME(grp_horaInicio,"-01:00:00") <= "' . $time_now . '" AND ADDTIME(grp_horaFim,"01:00:00") >= "' . $time_now . '"', null, false);
        $this->db->limit(1);
        $r = $this->db->get('grupos');
        return $r->row_array();
    }

    function getBySlugOrID($str)
    {
        $this->db->select('*,DATE_FORMAT(grp_dataFim, "%d/%m") as grp_fim, DATE_FORMAT(grp_dataInicio, "%d/%m") as grp_inicio, DATE_FORMAT(grp_horaInicio, "%H:%i") as grp_horaInicio, DATE_FORMAT(grp_horaFim, "%H:%i") as grp_horaFim');
        $this->db->where('grp_id', $str);
        $this->db->or_where('grp_slug', $str);
        $this->db->limit(1);
        $this->db->order_by('grp_ativo', 'DESC');
        $this->db->order_by('grp_id', 'DESC');
        $this->db->order_by('grp_atualizacao', 'DESC');
        $r = $this->db->get('grupos');
        // print $this->db->last_query();exit;
        return $r->row_array();
    }

    function getCoordenadoresDoGrupo($grp_id)
    {
        $this->db->where('FIND_IN_SET(usr_id,(SELECT grp_coordenadores FROM grupos WHERE grp_id = ' . $grp_id . '))', null, false);
        return $this->db->get('usuarios')->result_array();
    }

    function getAtivos()
    {
        $this->db->order_by('grp_nomePublico', 'ASC');
        return $this->db->get_where('grupos', array(
            'grp_ativo' => 1
        ))->result_array();
    }

    function getLastDate()
    {
        $this->db->select('grp_atualizacao');
        $this->db->where('grp_dataFim >', 'NOW()', false);
        $this->db->where('grp_ativo', '1');
        $this->db->where('grp_exibeSite', '1');
        $this->db->order_by('grp_atualizacao', 'DESC');
        $this->db->limit(1);
        return $this->db->get('grupos')->row_array()['grp_atualizacao'];
    }

    function getWS()
    {
        $this->db->select('*,DATE_FORMAT(grp_dataFim, "%d/%m") as grp_fim, DATE_FORMAT(grp_dataInicio, "%d/%m") as grp_inicio, DATE_FORMAT(grp_horaInicio, "%H:%i") as grp_horaInicio, DATE_FORMAT(grp_horaFim, "%H:%i") as grp_horaFim');
        $this->db->where('grp_dataFim >', 'NOW()', false);
        $this->db->where('grp_ativo', '1');
        $this->db->where('grp_exibeSite', '1');
        $this->db->order_by('grp_diaSemana', 'ASC');
        $this->db->order_by('grp_horaInicio', 'ASC');
        $retorno = $this->db->get('grupos')->result_array();
        // print $this->db->last_query();

        foreach ($retorno as $k => $grp) {
            $this->db->where_in('usr_id', explode(',', $grp['grp_coordenadores']));
            $retorno[$k]['grp_coordenadores'] = $this->db->get('usuarios')->result_array();
        }
        return $retorno;
    }

    function getFormas($grp_id, $linkOculto = null, $publico = true)
    {
        if (! empty($publico) && empty($linkOculto)) {
            $this->db->where('gfp_publico', '1');
        }
        if (! empty($linkOculto)) {
            $this->db->where('gfp_linkOculto', $linkOculto);
            $this->db->where('(gfp_linkOcultoValidade IS NULL OR gfp_linkOcultoValidade > NOW())', null, false);
        }
        $this->db->order_by('gfp_ordem', 'ASC');
        $this->db->order_by('gfp_parcelas', 'ASC');
        $this->db->order_by('gfp_valorTotal', 'DESC');
        $r = $this->db->get_where('grupos_formas', array(
            'gfp_grupo' => $grp_id
        ));
        // print $this->db->last_query();exit;
        return $r->result_array();
    }

    function getForma($gfp_id)
    {
        return $this->db->get_where('grupos_formas', array(
            'gfp_id' => $gfp_id
        ))->row_array();
    }

    function CSV($grp_id)
    {
        $this->db->select('alu_nomeArtistico as Name, alu_celular as "Phone", alu_email as "E-mail 1 - Value"');
        $this->db->join('alunos', 'ins_aluno = alu_id');
        $this->db->where('ins_grupo', $grp_id);
        $r = $this->db->get('inscricoes');
        $this->load->dbutil();
        return $this->dbutil->csv_from_result($r);
    }

    function getDistribuicao($grp_id)
    {
        $this->db->where('dst_grupo', $grp_id);
        return $this->db->get('grupos_distribuicao')->result_array();
    }

    function cloneGrupo($old_id, $new_id, $clonarInscricoes = false)
    {
        $ins = array();
        foreach ($this->db->get_where('grupos_distribuicao', [
            'dst_grupo' => $old_id
        ])->result_array() as $row) {
            unset($row['dst_id']);
            $row['dst_grupo'] = $new_id;
            $ins[] = $row;
        }
        if (count($ins)) {
            $this->db->insert_batch('grupos_distribuicao', $ins);
        }

        $ins = array();
        foreach ($this->db->get_where('grupos_formas', [
            'gfp_grupo' => $old_id
        ])->result_array() as $row) {
            unset($row['gfp_id']);
            $row['gfp_grupo'] = $new_id;
            $ins[] = $row;
        }
        if (count($ins)) {
            $this->db->insert_batch('grupos_formas', $ins);
        }

        if ($clonarInscricoes) {
            $ins = array();
            foreach ($this->db->get_where('inscricoes', [
                'ins_grupo' => $old_id
            ])->result_array() as $row) {
                unset($row['ins_id']);
                $row['ins_grupo'] = $new_id;
                $row['ins_data'] = date('Y-m-d H:i:s');
                $row['ins_user'] = $this->session->userdata('usr_id');
                $row['ins_forma'] = null;
                $ins[] = $row;
            }
            if (count($ins)) {
                $this->db->insert_batch('inscricoes', $ins);
            }
        }
        return $new_id;
    }

    function getAlunosInscritos($grp_id, $so_inscricoes_confirmadas = true)
    {
        $this->db->select('*');
        $this->db->join('alunos', 'ins_aluno = alu_id');
        $this->db->where('ins_grupo', $grp_id);
        if ($so_inscricoes_confirmadas) {
            $this->db->where('ins_valorDevido <=', '0');
        }
        $this->db->order_by('alu_nomeArtistico', 'ASC');
        return $this->db->get('inscricoes')->result_array();
    }
}
