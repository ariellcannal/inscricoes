<?php
use Dompdf\Dompdf;
use Dompdf\Options;

class RepassesLib
{

    private $CI;

    function __construct()
    {
        $this->CI = &get_instance();
    }

    function consolidar()
    {
        if (! $this->CI->input->is_ajax_request() && ! $this->CI->session->userdata('usr_id')) {
            show_404();
        } else {
            $this->CI->load->model('repasses_model');
            $this->CI->load->library('controllers/TransacoesLib', null, 'transacoes');
            
            $this->CI->transacoes->sincronizar(null, 7, false);
            $this->CI->repasses_model->consolidar();
        }
    }

    function efetivar()
    {
        if ($this->CI->input->post('rep_id') && $this->CI->input->is_ajax_request()) {
            $this->CI->load->model('repasses_model');
            $rep = $this->CI->repasses_model->efetivarRepasse($this->CI->input->post('rep_id'));
            if ($rep && $rep['usr_alertaRepasse'] == '1') {
                $this->CI->initMail();
                $this->CI->mail->addAddress($rep['usr_email']);
                $this->CI->mail->subject('Grupos de Estudos - Novo Repasse');
                $mensagem = $this->CI->load->view('emails/coordenador/novoRepasse.php', [
                    'rep' => $rep
                ], true);
                $mensagem .= $this->CI->relatorio($rep['usr_id'], true, false, 'previstos');
                $this->CI->mail->message($mensagem);
                if (! $this->CI->mail->send()) {}
            } else if (! $rep) {
                return set_status_header(400,'Repasse não efetivado');
            }
        } else {
            show_404();
        }
    }

    function desefetivar()
    {
        if ($this->CI->input->post('rep_id') && $this->CI->input->is_ajax_request()) {
            $this->CI->load->model('repasses_model');
            $this->CI->repasses_model->desefetivar($this->CI->input->post('rep_id'));
        } else {
            show_404();
        }
    }

    function relatorio($usr_id, $return_html = false, $return_pdf = false, $modo = 'completo')
    {
        $this->CI->load->model('recebiveis_model');
        $this->CI->load->model('repasses_model');
        $this->CI->load->model('usuarios_model');
        $this->CI->vars['months'] = 3;
        $this->CI->vars['usr'] = $this->CI->usuarios_model->getUsuario($usr_id);
        $this->CI->vars['pendentes'] = $this->CI->repasses_model->getRepassesPorUsuario($usr_id, null, false);
        $this->CI->vars['pagos'] = $this->CI->repasses_model->getRepassesPorUsuario($usr_id, date('Y-m-d', strtotime('-' . $this->CI->vars['months'] . ' months')) . ' 00:00:00', false, 'DESC');
        $this->CI->vars['previstos'] = $this->CI->repasses_model->getRepassesPrevistosPorUsuario($usr_id);
        $this->CI->vars['grupos'] = $this->CI->repasses_model->getRelatorioPorUsuario($usr_id, date('Y-m-d', strtotime('-' . $this->CI->vars['months'] . ' months')) . ' 00:00:00');
        // exit(var_dump($this->CI->vars['pendentes']));
        if ($modo == 'completo') {
            $html = $this->CI->load->view('relatorio_repasses/completo.php', $this->CI->vars, true);
        } elseif ($modo == 'previstos') {
            $html = $this->CI->load->view('relatorio_repasses/previstos.php', [
                'previstos' => $this->CI->vars['previstos']
            ], true);
        } elseif ($modo == 'pendentes') {
            $html = $this->CI->load->view('relatorio_repasses/pendentes.php', [
                'pendentes' => $this->CI->vars['pendentes']
            ], true);
        } elseif ($modo == 'pagos') {
            $html = $this->CI->load->view('relatorio_repasses/pagos.php', [
                'pagos' => $this->CI->vars['pagos']
            ], true);
        }
        if ($return_html) {
            return $html;
        }

        $dompdf_options = new Options();
        $dompdf_options->setDefaultMediaType('all');
        $dompdf_options->setIsRemoteEnabled(true);
        $dompdf_options->setIsFontSubsettingEnabled(true);

        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->setOptions($dompdf_options);
        $dompdf->loadHtml($html);
        $dompdf->render();

        if ($return_pdf == false) {
            $dompdf->stream('Relatorio de Recebiveis - ' . $this->CI->vars['usr']['usr_nome'] . '.pdf', [
                "Attachment" => false
            ]);
        } else {
            return $dompdf->output();
        }
    }
}