<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Alunos_model extends SYS_Model
{

    protected string $table = 'alunos';

    protected string $prefix = 'alu_';

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        parent::__construct();
        return $this;
    }

    function insert($alu)
    {
        if (! $alu['alu_cpf']) {
            return false;
        }
        $alu['alu_modificacao'] = date('Y-m-d H:i:s');
        $this->db->select('alu_id');
        $alu_id = $this->db->get_where('alunos', array(
            'alu_cpf' => $alu['alu_cpf']
        ))->row_array();
        if (! empty($alu_id)) {
            $alu_id = $alu_id['alu_id'];
            $this->db->update('alunos', $alu, array(
                'alu_id' => $alu_id
            ));
            return $alu_id;
        } else {
            $alu['alu_criacao'] = $alu['alu_modificacao'];
            $this->db->insert('alunos', $alu);
            return $this->db->insert_id();
        }
    }

    function checkAlunoCPF($cpf)
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        $this->db->select("*, DATE_FORMAT(alu_nascimento,'%d/%m/%Y') as alu_nascimento");
        $this->db->where("EXTRACT_NUMBERS(alu_cpf) = " . $cpf, null, true);
        $r = $this->db->get('alunos');
        // print $this->db->last_query();
        if (! $r) {
            return false;
        }
        return $r->row_array();
    }

    function updateInscricao($ins_id, $data)
    {
        $this->db->set($data);
        $this->db->where('ins_id', $ins_id);
        $this->db->update('inscricoes');
        return $this->db->affected_rows();
    }

    function getCredito($alc_id)
    {
        return $this->db->get_where('alunos_creditos', array(
            'alc_id' => $alc_id
        ))->row_array();
    }

    function utilizarCredito($alc_id, $valor)
    {
        $alc = $this->getCredito($alc_id);
        $alc['alc_valorUtilizado'] = (float) $alc['alc_valorUtilizado'] + (float) $valor;
        $this->db->update('alunos_creditos', $alc, array(
            'alc_id' => $alc_id
        ));
        return $this->db->affected_rows();
    }

    function mesclar(array $alu_ids)
    {
        if (count($alu_ids) < 2) {
            return false;
        }
        $this->db->where_in('alu_id', $alu_ids);
        $this->db->order_by('alu_modificacao', 'DESC');
        $this->db->order_by('alu_criacao', 'DESC');
        $rows = $this->db->get('alunos')->result_array();
        $id_destino = end($rows)['alu_id'];
        $update = [];
        $delete = [];

        foreach ($rows as $row) {
            $id_origem = $row['alu_id'];
            foreach ($row as $k => $v) {
                if (! in_array($k, [
                    'alu_id'
                ]) && (! isset($update[$k]) || empty($update[$k]))) {
                    $update[$k] = $v;
                }
            }

            $this->db->set('alc_aluno', $id_destino);
            $this->db->where('alc_aluno', $id_origem);
            $this->db->update('alunos_creditos');

            $this->db->where('ins_aluno', $id_origem);
            foreach ($this->db->get('inscricoes')->result_array() as $i) {
                // verificar se existe outra inscrição para o mesmo grupo, para o novo id
                $this->db->where('ins_aluno', $id_destino);
                $this->db->where('ins_grupo', $i['ins_grupo']);
                $this->db->where('ins_id <>', $i['ins_id']);
                $ins_novo = $this->db->get('inscricoes')->row_array();
                if ($ins_novo) {
                    // já tem uma inscrição
                    $ins_novo = $ins_novo['ins_id'];

                    $this->db->set('alc_inscricao', $ins_novo);
                    $this->db->where('alc_inscricao', $i['ins_id']);
                    $this->db->update('alunos_creditos');

                    $this->db->set('rec_inscricao', $ins_novo);
                    $this->db->where('rec_inscricao', $i['ins_id']);
                    $this->db->update('recebiveis');

                    $this->db->where('ins_id', $i['ins_id']);
                    $antigo = $this->db->get('inscricoes')->result_array();
                    $this->db->where('ins_id', $ins_novo);
                    $novo = $this->db->get('inscricoes')->result_array();
                    $atualizar = [];
                    foreach ($novo as $key => $val) {
                        if ($val == "" && $antigo[$key] != "") {
                            $atualizar[$key] = $antigo[$key];
                        }
                    }
                    if (count($atualizar)) {
                        $this->db->set($atualizar);
                        $this->db->where('ins_id', $ins_novo);
                        $this->db->update('inscricoes');
                    }
                    $this->db->where('ins_id', $i['ins_id']);
                    $this->db->delete('inscricoes');
                } else {
                    // não tem uma inscrição
                    $this->db->set('ins_aluno', $id_destino);
                    $this->db->where('ins_aluno', $id_origem);
                    $this->db->update('inscricoes');
                }
            }

            $this->db->set('prs_aluno', $id_destino);
            $this->db->where('prs_aluno', $id_origem);
            $this->db->update('presenca');

            $this->db->where('alu_id', $id_origem);
            $antigo = $this->db->get('alunos')->row_array();

            $this->db->where('alu_id', $id_destino);
            $novo = $this->db->get('alunos')->row_array();

            foreach ($novo as $key => $val) {
                if ($val == "" && $antigo[$key] != "") {
                    $update[$key] = $antigo[$key];
                }
            }

            if ($id_origem != $id_destino) {
                $delete[] = $id_origem;
            }
        }
        if (count($update)) {
            // var_dump($update);
            $this->db->set($update);
            $this->db->where('alu_id', $id_destino);
            $this->db->update('alunos');
        }
        if (count($delete)) {
            $this->db->where_in('alu_id', $delete);
            $this->db->delete('alunos');
        }
        return true;
    }

    function getPresencas($alu_id, $grp_id)
    {
        $this->db->where('prs_grupo', $grp_id);
        $this->db->where('prs_aluno', $alu_id);
        $this->db->order_by('prs_data', 'DESC');
        $r = $this->db->get('presenca');
        return $r->result_array();
    }

    function setOperadoraMeta($alu_id, $opr_name, $opr_key, $opr_value)
    {
        if (! $opr_value) {
            return false;
        }
        $data['aop_aluno'] = $alu_id;
        $data['aop_operadora'] = $opr_name;
        $data['aop_key'] = $opr_key;
        $data['aop_environment'] = ENVIRONMENT;
        $row = $this->db->get_where('alunos_operadoras_meta', $data)->row_array();

        $data['aop_value'] = $opr_value;
        $this->db->set($data);
        if ($row) {
            // atualiza
            $this->db->where('aop_id', $row['aop_id']);
            $this->db->update('alunos_operadoras_meta');
        } else {
            // cria
            $this->db->insert('alunos_operadoras_meta');
        }

        return $this->db->affected_rows();
    }

    function getOperadoraMeta($alu_id, $opr_name, $opr_key)
    {
        $data['aop_aluno'] = $alu_id;
        $data['aop_operadora'] = $opr_name;
        $data['aop_key'] = $opr_key;
        $data['aop_environment'] = ENVIRONMENT;
        $row = $this->db->get_where('alunos_operadoras_meta', $data)->row_array();
        return $row ? $row['aop_value'] : null;
    }
}
