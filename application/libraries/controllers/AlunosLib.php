<?php
use CANNALInscricoes\Entities\AlunosEntity;
use CANNALInscricoes\Entities\OperadorasEntity;
use CANNALPagamentos\Entities\Cliente;

class AlunosLib
{

    private $CI;

    function __construct()
    {
        $this->CI = &get_instance();
    }

    function check_cpf()
    {
        if ($this->CI->input->post('cpf') && $this->CI->input->post('grp') && $this->CI->input->is_ajax_request()) {
            $this->CI->load->model('alunos_model');

            $alu = $this->CI->alunos_model->checkAlunoCPF($this->CI->input->post('cpf'));
            if ($alu) {
                $this->CI->load->model('inscricoes_model');
                $this->CI->load->model('grupos_model');
                $this->CI->load->model('operadoras_model');

                $grp = $this->CI->grupos_model->getRow($this->CI->input->post('grp'));
                $opr = new OperadorasEntity($this->CI->operadoras_model->getRow($grp['grp_operadora']));
                $cli = new Cliente($alu, 'alu_');
                $cli->setIdOperadora($this->CI->alunos_model->getOperadoraMeta($alu['alu_id'], $grp['grp_operadora'], 'id'));
                $alu = new AlunosEntity($alu);

                $alu_ins = $this->CI->inscricoes_model->checkInscricao($this->CI->input->post('grp'), $alu->getId());
                $xcrud = xcrud_get_instance('inscricao_form');
                $xcrud->import_vars();
                $xcrud->_get_theme_config();
                $xcrud->_get_language_static();
                $alu->setFoto($xcrud->create_image('a.alu_foto', $alu->getFoto()));
                $alu->setCv($xcrud->create_file('a.alu_cv', $alu->getCv()));

                if ($cli->getIdOperadora()) {
                    $this->CI->logs->setLogName('ALU_'.$alu->getId() . '_' . time(), true);
                    $class = "CANNALPagamentos\\Interfaces\\" . ucfirst($opr->getInterface());
                    if (ENVIRONMENT == 'production' || FORCE_OPERADORA_PRODUCTION === TRUE) {
                        $interface = new $class($opr->getProductionKey(), $opr->getNome());
                    } else {
                        $interface = new $class($opr->getDevelopmentKey(), $opr->getNome());
                    }
                    $cartoes = $interface->getCards($cli);
                    if (is_countable($cartoes) && count($cartoes)) {
                        foreach ($cartoes as $c) {
                            $alu_cartoes[] = [
                                'id' => $c->getId(),
                                'label' => $c->getBandeira() . ' final ****' . $c->getUltimosQuatro()
                            ];
                        }
                    }
                }
                $alu = $alu->toArray();
                $alu['alu_ins'] = $alu_ins;
                if (isset($alu_cartoes)) {
                    $alu['alu_cartoes'] = $alu_cartoes;
                }
            }
            exit(json_encode($alu));
        } else {
            show_404();
        }
    }

    function check($alu_arr)
    {
        $this->CI->load->model('alunos_model');

        $alu_arr['alu_nome'] = ucwords($alu_arr['alu_nome']);
        $alu_arr['alu_nomeArtistico'] = ucwords($alu_arr['alu_nomeArtistico']);
        if (array_key_exists('alu_id', $alu_arr) && ! empty($alu_arr['alu_id'])) {
            $this->CI->alunos_model->update($alu_arr['alu_id'], $alu_arr);
            $alu_id = $alu_arr['alu_id'];
        } else if (array_key_exists('alu_cpf', $alu_arr)) {
            $alu = $this->CI->alunos_model->checkAlunoCPF($alu_arr['alu_cpf']);
            if (! $alu) {
                // ALUNO NÃO EXISTE
                $alu_id = $this->CI->alunos_model->insert($alu_arr);
            } else {
                $alu_id = $alu['alu_id'];
                // ALUNO EXISTE
                $this->CI->alunos_model->update($alu_id, $alu_arr);
            }
        }

        return $this->CI->alunos_model->getRow($alu_id);
    }
}