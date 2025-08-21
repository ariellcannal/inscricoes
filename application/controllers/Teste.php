<?php
use CANNALPagamentos\Interfaces\Pagarme;
use CANNALPagamentos\Entities\Cliente;
use CANNALPagamentos\Entities\Cartao;
use CANNALPagamentos\Entities\Pedido;
use CANNALInscricoes\Entities\OperadorasEntity;
use CANNALLogs\Logs;

class Teste extends SYS_Controller
{

    function __construct()
    {
        parent::__construct();
        if (ENVIRONMENT != 'development' && ! $this->session->userdata('usr_id')) {
            show_404();
        }
    }

    public function index()
    {
        $this->load->view('teste.html');
        return;
        $this->initMail();
        $this->mail->SMTPDebug = 1;
        $this->mail->addAddress('ariell@cannal.com.br');
        $this->mail->subject('Teste');
        $this->mail->message('Teste');
        exit((string) $this->mail->send());
        exit(crypt('A648h722c#', $this->config->item('encryption_key')));
        $log = new Logs('teste\test:sde', 'parac:unde');
        $log->write('debug', 'teste');
        exit();
        $this->load->model('alunos_model');
        $this->load->model('operadoras_model');
        $alu = new Cliente($this->alunos_model->getRow(117), 'alu_');
        $opr = $this->operadoras_model->getRow('pagarme_cannal');

        $interface = new Pagarme($opr['opr_developmentKey'], $opr['opr_nome']);
        $cartao = new Cartao();
        $cartao->setNumero(5226267532758523);
        $cartao->setCodigo('084');
        $cartao->setNome('ARIEL H CANAL');
        $cartao->setVencimentoMes(06);
        $cartao->setVencimentoAno(32);

        $pedido = new Pedido(01554502, 1, 1, 'teste', 'teste');
        $transacao = $interface->creditcard($alu, $pedido, $cartao);
        $this->alunos_model->setOperadoraMeta($alu->getId(), $opr['opr_nome'], 'customer_id', $alu->getIdOperadora());
        $this->alunos_model->setOperadoraMeta($alu->getId(), $opr['opr_nome'], 'address_id', $transacao->get);
        $this->alunos_model->setOperadoraMeta($alu->getId(), $opr['opr_nome'], 'card_id', $alu->getIdOperadora());
        var_dump($transacao);
        exit();
    }
}