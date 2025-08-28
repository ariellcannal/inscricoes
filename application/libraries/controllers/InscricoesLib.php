<?php
use CANNALInscricoes\Entities\AlunosEntity;
use CANNALInscricoes\Entities\OperadorasTransacoesEntity;
use CANNALPagamentos\Entities\Cartao;
use CANNALPagamentos\Entities\Pedido;
use CANNALPagamentos\Entities\Cliente;
use CANNALPagamentos\Entities\Transacao;

class InscricoesLib
{

    private $CI;

    public $estadosBrasileiros = array(
        0 => 'Selecione',
        'AC' => 'Acre',
        'AL' => 'Alagoas',
        'AP' => 'Amapá',
        'AM' => 'Amazonas',
        'BA' => 'Bahia',
        'CE' => 'Ceará',
        'DF' => 'Distrito Federal',
        'ES' => 'Espírito Santo',
        'GO' => 'Goiás',
        'MA' => 'Maranhão',
        'MT' => 'Mato Grosso',
        'MS' => 'Mato Grosso do Sul',
        'MG' => 'Minas Gerais',
        'PA' => 'Pará',
        'PB' => 'Paraíba',
        'PR' => 'Paraná',
        'PE' => 'Pernambuco',
        'PI' => 'Piauí',
        'RJ' => 'Rio de Janeiro',
        'RN' => 'Rio Grande do Norte',
        'RS' => 'Rio Grande do Sul',
        'RO' => 'Rondônia',
        'RR' => 'Roraima',
        'SC' => 'Santa Catarina',
        'SP' => 'São Paulo',
        'SE' => 'Sergipe',
        'TO' => 'Tocantins'
    );

    function __construct()
    {
        $this->CI = &get_instance();
    }

    public function aprovar($ins_id)
    {
        $this->CI->load->helper('inscricoes_helper');
        $this->CI->load->model('inscricoes_model');
        $this->CI->load->model('alunos_model');
        $this->CI->load->model('grupos_model');
        $vars['ins'] = $this->CI->inscricoes_model->getInscricaoCompleta($ins_id);
        $vars['alu'] = $this->CI->alunos_model->getRow($vars['ins']['ins_aluno']);
        $vars['grp'] = $this->CI->grupos_model->getRow($vars['ins']['ins_grupo']);
        $this->aprovar_inscricao($ins_id);
        exit('Você aprovou a inscrição de ' . $vars['alu']['alu_nomeArtistico']);
    }

    public function reprovar($ins_id)
    {
        $this->CI->load->model('alunos_model');
        $this->CI->load->model('inscricoes_model');
        $this->CI->load->model('grupos_model');
        $ins = $this->CI->inscricoes_model->getInscricaoCompleta($ins_id);
        $alu = $this->CI->alunos_model->getRow($ins['ins_aluno']);
        $grp = $this->CI->grupos_model->getRow($ins['ins_grupo']);

        $this->CI->alunos_model->updateInscricao($ins_id, [
            'ins_status' => '0'
        ]);
        exit('Você reprovou a inscrição de ' . $alu['alu_nomeArtistico']);
    }

    public function totalizar()
    {
        $this->CI->load->model('inscricoes_model');
        foreach ($this->CI->inscricoes_model->getInscricoesGruposAtivos() as $ins) {
            $this->CI->inscricoes_model->setTotaisInscricao($ins['ins_id']);
        }
        redirect('inscricoes');
    }

    public function sincronizar($ins_id)
    {
        $this->CI->load->model('operadoras_model');
        $this->CI->load->model('inscricoes_model');
        $this->CI->load->library('controllers/TransacoesLib', null, 'transacoes');
        $this->CI->transacoes->sincronizar($this->CI->operadoras_model->getTransacoesPorInscricao($ins_id),0,false);
        $this->CI->inscricoes_model->setTotaisInscricao($ins_id);
    }

    public function aprovar_inscricao($ins_id)
    {
        global $capture, $transacao;
        $capture = true;
        $envia = false;

        $this->CI->load->model('inscricoes_model');
        $this->CI->load->model('alunos_model');
        $this->CI->load->model('grupos_model');
        $this->CI->load->helper('inscricoes_helper');
        $this->CI->load->library('mail');

        $vars['ins'] = $this->CI->inscricoes_model->getInscricaoCompleta($ins_id);
        $vars['alu'] = $this->CI->alunos_model->getRow($vars['ins']['ins_aluno']);

        $this->CI->logs->setLogName('ALU_' . $vars['alu']['alu_id'] . '_' . time(), true);

        if (empty($vars['ins'])) {
            return set_status_header(404,'Essa inscrição não foi localizada');
        } else if (! empty($vars['ins']['ins_aprovada'])) {
            // return set_status_header(400,'Essa inscrição já foi aprovada');
        }
        $ins_update['ins_aprovada'] = date('Y-m-d H:i:s');
        if (! empty($vars['ins']['ins_tempData'])) {
            $json = json_decode($vars['ins']['ins_tempData'], true);

            if (! empty($json['fop'])) {
                $fop = explode('_', $json['fop']);
                $ins_update['ins_forma'] = $fop[0];
            }

            if (! empty($json['cartao']) && transacao($ins_id) !== false) {
                if ($transacao->getConfirmada()) {
                    $this->email_inscricao($ins_id, 'pagamento_confirmado');
                } else {
                    $envia = true;
                }
            } else {
                $envia = true;
            }
        } else {
            $envia = true;
        }
        if ($envia && ! empty($vars['alu']['alu_email'])) {
            $this->email_inscricao($ins_id, 'inscricao_aprovada');
        }
        if (! empty($ins_update)) {
            $this->CI->alunos_model->updateInscricao($ins_id, $ins_update);
        }
    }

    public function set_transacao($ins_id, &$postdata = null, &$xcrud = null)
    {
        global $transacao, $pedido, $qtd_parcelas, $val_total, $processoSeletivo, $capture, $interface;

        $this->CI->load->model('grupos_model');
        $this->CI->load->model('recebiveis_model');
        $this->CI->load->model('inscricoes_model');
        $this->CI->load->model('alunos_model');
        $this->CI->load->helper('dates_helper');
        $this->CI->load->helper('alunos_helper');

        $ins = $this->CI->inscricoes_model->getInscricaoCompleta($ins_id);
        $alu = new Cliente($this->CI->alunos_model->getRow($ins['ins_aluno']), 'alu_');
        $alu->setIdOperadora($this->CI->alunos_model->getOperadoraMeta($ins['ins_aluno'], $interface->getNome(), 'id'));

        $grp = $this->CI->grupos_model->getRow($ins['ins_grupo']);

        $ins_tempData = json_decode($ins['ins_tempData'], true);
        if (! empty($postdata) && ! empty($postdata->get('fop'))) {
            $fop = explode('_', $postdata->get('fop'));
        } else if (! empty($ins_tempData['fop'])) {
            $fop = explode('_', $ins_tempData['fop']);
        }
        $gfp = $this->CI->grupos_model->getForma($fop[0]);
        if (! $gfp && ! empty($xcrud)) {
            $this->CI->logs->write('ERROR', 'Falha ao selecionar forma de pagamento do grupo. Fop[0] = ' . $fop[0]);
            $xcrud->set_notify('Falha ao realizar a inscrição: ' . $this->CI->logs->getLogName(), 'alert', true);
            return false;
        } else if (! $gfp) {
            $this->CI->logs->write('DEBUG', 'Problema ao selecionar forma de pagamento do grupo');
            return false;
        } else {
            $ins_update['ins_forma'] = $fop[0];

            $qtd_parcelas = $fop[1];
            $gfp['gfp_aceitaCartao'] = $fop[2];
            $val_total = $gfp['gfp_valorTotal'];

            if (! empty($ins['ins_valorDesconto'])) {
                $val_total = $val_total - $ins['ins_valorDesconto'];
            }
            if ($ins['ins_valorTotalPago'] > 0 && $ins['ins_valorDevido'] > 0) {
                $val_total = $ins['ins_valorDevido'];
            }
            if (ENVIRONMENT == 'development') {
                // $val_total = 1.00 * $qtd_parcelas;
            }

            $pedido_id = 'G' . str_pad($ins['ins_grupo'], 3, '0', STR_PAD_RIGHT);
            $pedido_id .= 'A' . str_pad($ins['ins_aluno'], 3, '0', STR_PAD_RIGHT);
            $pedido_id .= substr(time(), strlen(time()) - 3, 3);
            $pedido = new Pedido($pedido_id, $val_total, $qtd_parcelas, substr('TAPA' . strtolower($grp['grp_idFaturaCartao']), 0, 12), $grp['grp_nomePublico']);

            if ($gfp['gfp_aceitaCartao'] == 1) {
                if (! empty($postdata) && $postdata->get('alu_cartoes') == 'novo') {
                    // aluno digitou um novo cartao
                    $cartao = new Cartao();
                    $cartao->setSalvar(true);
                    $cartao->setNumero($postdata->get('inscricoes.rec_cartao'));
                    $cartao->setCodigo($postdata->get('inscricoes.rec_cartaoCodigo'));
                    $cartao->setNome(strtoupper(preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim($postdata->get('inscricoes.rec_cartaoNome'))))));
                    $validade = explode('/', $postdata->get('inscricoes.rec_cartaoValidade'));
                    $cartao->setVencimentoMes(trim($validade[0]));
                    $cartao->setVencimentoAno(trim($validade[1]));
                    if ($processoSeletivo && ! $capture) {
                        $cartao = $interface->saveCard($alu, $cartao);
                        if ($cartao->getId()) {
                            $ins_tempData['cartao'] = $cartao->getId();
                            $ins_update['ins_tempData'] = json_encode($ins_tempData);
                        }
                    }
                } else if (! empty($postdata)) {
                    // aluno selecionou um cartao salvo
                    if ($processoSeletivo) {
                        $ins_tempData['cartao'] = $postdata->get('alu_cartoes');
                        $ins_update['ins_tempData'] = json_encode($ins_tempData);
                    }
                    $cartao = $postdata->get('alu_cartoes');
                } else if ($capture && ! empty($ins_tempData['cartao'])) {
                    // usa cartao salvo no ins_tempData
                    $ins_tempData['cartao'] = json_decode(json_encode($ins_tempData['cartao']), true);
                    $cartao = $ins_tempData['cartao'];
                }

                if ($capture && ! empty($cartao)) {
                    $transacao = $interface->creditcard($alu, $pedido, $cartao);
                    if ($transacao === false) {
                        $xcrud->set_notify('Por favor, insira novamente os dados do cartão.', 'alert', true);
                    }
                }
            } else if ($capture) {
                $transacao = $interface->pix($alu, $pedido);
            }
        }

        if ($transacao instanceof Transacao) {
            $this->CI->load->library('controllers/TransacoesLib', null, 'transacoes');
            $transacao = new OperadorasTransacoesEntity($transacao->toArray(true));
            $transacao->setForma($this->CI->transacoes->selectForma($transacao));
            $transacao->setInscricao($ins_id);
            $transacao->setValorLiquido(0);
            $otr_id = $this->CI->operadoras_model->inserirTransacao($transacao->toArray(false));
            $transacao->setId($otr_id);

            if ($transacao->getOperadoraErros() && ! empty($xcrud)) {
                $xcrud->set_notify('Transação não aprovada: ' . $transacao->getOperadoraErros(), 'alert', true);
            } else if ($transacao->getOperadoraErros()) {
                $this->email_inscricao($ins_id, 'transacao_nao_aprovada', $transacao);
            } else {
                return $this->set_recebiveis($transacao, $ins_id);
            }
            return false;
        } else if ($capture) {
            if (! empty($xcrud)) {
                $xcrud->set_notify('Falha ao realizar transação: ' . $this->CI->logs->getLogName(), 'alert', true);
            }
            return false;
        }
        if (! empty($ins_update)) {
            $this->CI->alunos_model->updateInscricao($ins_id, $ins_update);
        }
        return true;
    }

    public function set_recebiveis($transacao, $ins_id)
    {
        $this->CI->load->library('controllers/TransacoesLib', null, 'transacoes');
        return $this->CI->transacoes->sincronizar($transacao->getId(), null, false);
    }

    public function email_inscricao($ins_id, $tipo, $transacao = null)
    {
        $this->CI->initMail();
        $this->CI->load->model('inscricoes_model');
        $this->CI->load->model('alunos_model');
        $this->CI->load->model('grupos_model');
        $vars['ins'] = $this->CI->inscricoes_model->getInscricaoCompleta($ins_id);
        $vars['grp'] = $this->CI->grupos_model->getRow($vars['ins']['ins_grupo']);
        $vars['alu'] = $this->CI->alunos_model->getRow($vars['ins']['ins_aluno']);
        if (is_array($transacao)) {
            $transacao = new OperadorasTransacoesEntity($transacao);
        }
        $vars['transacao'] = $transacao;
        if ($tipo == 'solicita_aprovacao') {
            /* E-MAIL PARA O COORDERNADOR */
            $destinatarios = array();
            foreach ($this->CI->grupos_model->getCoordenadoresDoGrupo($vars['ins']['ins_grupo']) as $row) {
                if ($row['usr_recebeInscricoes'] == 1 && $row['usr_email'] != "") {
                    $destinatarios[] = $row['usr_email'];
                }
            }
            if (count($destinatarios)) {
                $this->CI->logs->write('DEBUG', 'Enviar solicitação de aprovação');

                foreach ($destinatarios as $d) {
                    $this->CI->mail->addAddress($d);
                }
                $this->CI->mail->subject('[NOVO CANDIDATO] ' . $vars['alu']['alu_nomeArtistico'] . ' - ' . $vars['grp']['grp_nomePublico']);
                $this->CI->mail->message($this->CI->load->view('emails/coordenador/novaInscricaoCandidato.php', $vars, true));
                $name = $vars['alu']['alu_cv'];
                $pos = strpos($name, '_@XCRUD');
                if ($pos === false) {
                    $name;
                } else if ($pos === 0) {
                    $name = substr($name, 7, strlen($name) - 7);
                } else {
                    $ext = substr($name, strrpos($name, '.'), strlen($name) - strrpos($name, '.'));
                    $name = substr($name, 0, $pos) . $ext;
                }
                $this->CI->mail->attach($_SERVER['DOCUMENT_ROOT'] . '/writable/alunos/' . $vars['alu']['alu_cv']);
                return $this->CI->mail->send();
            }
        } else if ($tipo == 'transacao_nao_aprovada') {
            /* E-MAIL PARA O ALUNO */
            $this->CI->mail->addAddress($vars['alu']['alu_email']);
            $this->CI->mail->subject('[INSCRIÇÃO RECEBIDA] ' . $vars['grp']['grp_nomePublico']);
            $this->CI->mail->message($this->CI->load->view('emails/aluno/falhaCartao.php', $vars, true));
            return $this->CI->mail->send();
        } else if ($tipo == 'inscricao_aprovada') {
            /* E-MAIL PARA O ALUNO */
            $this->CI->mail->addAddress($vars['alu']['alu_email']);
            $this->CI->mail->subject('[INSCRIÇÃO APROVADA] ' . $vars['grp']['grp_nomePublico']);
            $this->CI->mail->message($this->CI->load->view('emails/aluno/inscricaoAprovada.php', $vars, true));
            return $this->CI->mail->send();
        } else if ($tipo == 'pagamento_confirmado') {
            /* E-MAIL PARA O ALUNO */
            $this->CI->mail->addAddress($vars['alu']['alu_email']);
            $this->CI->mail->subject('[INSCRIÇÃO APROVADA] ' . $vars['grp']['grp_nomePublico']);
            $this->CI->mail->message($this->CI->load->view('emails/aluno/pagamentoConfirmado_' . $transacao->getTipo() . '.php', $vars, true));
            $this->CI->mail->send();

            /* E-MAIL PARA O COORDERNADOR */
            $destinatarios = array();
            foreach ($this->CI->grupos_model->getCoordenadoresDoGrupo($vars['ins']['ins_grupo']) as $row) {
                if ($row['usr_recebeInscricoes'] == 1 && $row['usr_email'] != "") {
                    $destinatarios[] = $row['usr_email'];
                }
            }
            if (count($destinatarios)) {
                $vars['inscricoes'] = $this->CI->grupos_model->getAlunosInscritos($vars['grp']['grp_id']);
                foreach ($destinatarios as $d) {
                    $this->CI->mail->addAddress($d);
                }
                $this->CI->mail->subject('[NOVA INSCRIÇÃO CONFIRMADA] ' . $vars['grp']['grp_nomePublico']);
                $msg = $this->CI->load->view('emails/coordenador/novaInscricao.php', $vars, true);
                $msg .= $this->CI->load->view('emails/coordenador/listaInscritos.php', $vars, true);
                $this->CI->mail->message($msg);
                return $this->CI->mail->send();
            }
        } else if ($tipo == 'inscricao_recebida_processo') {
            $this->CI->mail->addAddress($vars['alu']['alu_email']);
            $this->CI->mail->subject('[INSCRIÇÃO RECEBIDA] ' . $vars['grp']['grp_nomePublico']);
            $this->CI->mail->message($this->CI->load->view('emails/aluno/inscricaoRecebidaProcesso.php', $vars, true));
            return $this->CI->mail->send();
        } else if ($tipo == 'pagamento_pix') {
            $this->CI->mail->addAddress($vars['alu']['alu_email']);
            $this->CI->mail->subject('[INSCRIÇÃO RECEBIDA] ' . $vars['grp']['grp_nomePublico']);
            $this->CI->mail->message($this->CI->load->view('emails/aluno/pagamentoPIX.php', $vars, true));
            return $this->CI->mail->send();
        } else if ($tipo == 'inscricao_recebida') {
            $this->CI->mail->addAddress($vars['alu']['alu_email']);
            $this->CI->mail->subject('[INSCRIÇÃO RECEBIDA] ' . $vars['grp']['grp_nomePublico']);
            $this->CI->mail->message($this->CI->load->view('emails/aluno/inscricaoRecebida.php', $vars, true));
            return $this->CI->mail->send();
        } else if ($tipo == 'confirmar_estorno') {
            $this->CI->mail->addAddress($vars['alu']['alu_email']);
            $this->CI->mail->subject('[ESTORNO] ' . $vars['grp']['grp_nomePublico']);
            $this->CI->mail->message($this->CI->load->view('emails/aluno/confirmacaoEstorno.php', $vars, true));
            return $this->CI->mail->send();
        } else {
            return null;
        }
    }

    public function whatsAppMsg()
    {
        if ($this->CI->input->post('msg')) {
            $this->CI->session->set_userdata('whatsAppMsg', $this->CI->input->post('msg'));
        }
    }
}