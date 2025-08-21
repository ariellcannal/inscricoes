<?php
use Dompdf\Dompdf;
use Dompdf\Options;
use CANNALInscricoes\Entities\OperadorasEntity;

if (! function_exists('INS_replace_remove')) {

    function INS_replace_remove($ins_id, $xcrud)
    {
        $ci = &get_instance();
        $ci->load->model('inscricoes_model');
        if ($ci->inscricoes_model->removerInscricao($ins_id)) {
            $xcrud->set_notify('Inscrição ' . $ins_id . ' removida', 'success');
        } else {
            $xcrud->set_notify('Não é possível remover a inscrição ' . $ins_id, 'error', true);
        }
        return $ins_id;
    }
}

if (! function_exists('BI_inscricao_admin')) {

    function BI_inscricao_admin($postdata, $xcrud)
    {
        $ci = &get_instance();
        $ci->session->set_userdata('last_grupo', $postdata->get('ins_grupo'));
        $ci->session->set_userdata('last_aluno', $postdata->get('ins_aluno'));
        $xcrud->pass_default('ins_grupo', $postdata->get('ins_grupo'));
        $xcrud->pass_default('ins_aluno', $postdata->get('ins_aluno'));
    }
}

if (! function_exists('AI_inscricao_admin')) {

    function AI_inscricao_admin($postdata, $ins_id, $xcrud)
    {
        $ci = &get_instance();
        $ci->load->model('inscricoes_model');
        $ci->inscricoes_model->setTotaisInscricao($ins_id);
    }
}

if (! function_exists('AU_inscricao_admin')) {

    function AU_inscricao_admin($postdata, $ins_id, $xcrud)
    {
        $ci = &get_instance();
        $ci->load->model('inscricoes_model');
        $ci->inscricoes_model->setTotaisInscricao($ins_id);
    }
}

if (! function_exists('BU_inscricao_aluno')) {

    function BU_inscricao_aluno($postdata, $ins_id, $xcrud)
    {
        BI_inscricao_aluno($postdata, $xcrud, $ins_id);
    }
}
if (! function_exists('BI_inscricao_aluno')) {

    function BI_inscricao_aluno($postdata, $xcrud, $ins_id = null)
    {
        global $processoSeletivo, $capture, $alu, $ins, $grp, $opr;

        // VALIDA IDADE DO ALUNO
        $data_inicio = new DateTime($postdata->get('a.alu_nascimento'));
        $data_fim = new DateTime(date('Y-m-d'));
        $dateInterval = $data_inicio->diff($data_fim);
        if ($dateInterval->y < 18) {
            $xcrud->set_notify('Você precisa ser maior de 18 anos.', 'error', true);
            return false;
        }

        foreach ($postdata->to_array() as $k => $v) {
            if (strstr($k, 'a.')) {
                $aluno[str_replace('a.', '', $k)] = $v;
            }
        }

        $ci = &get_instance();
        $ci->load->helper('alunos_helper');
        $ci->load->model('grupos_model');
        $ci->load->model('inscricoes_model');
        $ci->load->model('operadoras_model');
        $ci->load->library('controllers/AlunosLib', null, 'alunos');

        if (! empty($ins_id)) {
            $ci->load->model('alunos_model');
            $ins = $ci->inscricoes_model->getInscricaoCompleta($ins_id);
            if (! empty($ins['ins_aprovada'])) {
                $capture = true;
            }
        }
        $grp = $ci->grupos_model->getRow($postdata->get('ins_grupo'));
        if ($grp['grp_processoSeletivo'] == 1) {
            $processoSeletivo = true;
        } else {
            $capture = true;
        }

        if (count($aluno)) {
            $alu = $ci->alunos->check($aluno);
            $alu_id = $alu['alu_id'];
            if ($alu_id) {
                $postdata->set('ins_aluno', $alu_id);
            } else {
                $ci->logs->write('ERROR', 'Falha ao cadastrar ou selecionar o aluno.');
                $xcrud->set_notify('Falha ao realizar a inscrição ' . $ci->logs->getLogName(), 'error', true);
                return false;
            }
        } else {
            $ci->logs->write('ERROR', 'Falha ao obter dados do aluno.');
            $xcrud->set_message('Falha ao realizar a inscrição ' . $ci->logs->getLogName(), 'error');
            return false;
        }
        $ci->logs->setLogName('ALU_' . $alu_id . '_' . time(), true);

        if (empty($postdata->get('alu_cartoes'))) {
            $postdata->set('alu_cartoes', 'novo');
        }

        $opr = new OperadorasEntity();
        if (! $grp['grp_operadora']) {
            $opr->importArray($ci->operadoras_model->getDefault());
        } else if ($grp_operadora = $ci->operadoras_model->getRow($grp['grp_operadora'])) {
            $opr->importArray($grp_operadora);
        } else {
            $ci->logs->write('ERROR', 'Operadora não localizada');
            $xcrud->set_notify('Operadora indisponível ' . $ci->logs->getLogName(), 'error', true);
            return false;
        }

        $ins_tempData['fop'] = $postdata->get('fop');
        $fop = explode('_', $postdata->get('fop'));
        $postdata->set('ins_forma', $fop[0]);
        $postdata->set('ins_tempData', json_encode($ins_tempData));
    }
}

if (! function_exists('AU_inscricao_aluno')) {

    function AU_inscricao_aluno($postdata, $ins_id, $xcrud)
    {
        AI_inscricao_aluno($postdata, $ins_id, $xcrud);
    }
}
if (! function_exists('AI_inscricao_aluno')) {

    function AI_inscricao_aluno($postdata, $ins_id, $xcrud)
    {
        global $processoSeletivo, $capture, $transacao, $opr, $interface;
        $ci = &get_instance();
        $ci->load->model('recebiveis_model');
        $ci->load->library('controllers/InscricoesLib', null, 'inscricoes');

        if (! $opr) {
            $opr = new OperadorasEntity($ci->operadoras_model->getDefault());
        }
        $class = ucfirst($opr->getInterface());
        $class = "CANNALPagamentos\\Interfaces\\" . $class;
        if (ENVIRONMENT == 'production') {
            $interface = new $class($opr->getProductionKey(), $opr->getNome());
        } else {
            $interface = new $class($opr->getDevelopmentKey(), $opr->getNome());
        }

        if ($ci->inscricoes->set_transacao($ins_id, $postdata, $xcrud, $postdata->get('operadora')) !== false) {
            $ci->load->model('inscricoes_model');
            $ci->load->model('alunos_model');
            $ci->load->model('grupos_model');
            $ci->inscricoes_model->unificaInscricao($postdata->get('ins_grupo'), $postdata->get('ins_aluno'), $ins_id);

            if ($processoSeletivo && ! $capture) {
                $ci->inscricoes->email_inscricao($ins_id, 'inscricao_recebida_processo');
            } else if ($transacao->getTipo() == 'pix') {
                $ci->inscricoes->email_inscricao($ins_id, 'pagamento_pix');
            } else if (! $capture) {
                $ci->inscricoes->email_inscricao($ins_id, 'inscricao_recebida');
            } else if ($transacao->getConfirmada()) {
                $ci->inscricoes->email_inscricao($ins_id, 'pagamento_confirmado', $transacao);
            }

            if ($processoSeletivo && ! $capture) {
                $ci->inscricoes->email_inscricao($ins_id, 'solicita_aprovacao');
            }
        }
        $ci->load->model('inscricoes_model');
        $ci->inscricoes_model->setTotaisInscricao($ins_id);
    }
}

if (! function_exists('reenviar_confirmacao')) {

    function reenviar_confirmacao($xcrud)
    {
        $ci = &get_instance();
        $ci->load->library('controllers/InscricoesLib', null, 'inscricoes');
        $ci->load->model('operadoras_model');
        $transacoes = $ci->operadoras_model->getTransacoesPorInscricao($xcrud->get('primary'), [
            'otr_confirmada' => '1'
        ]);
        if ($transacoes) {
            foreach ($transacoes as $transacao) {
                if ($ci->inscricoes->email_inscricao($xcrud->get('primary'), 'pagamento_confirmado', $transacao)) {
                    $xcrud->set_notify('OTR ' . $transacao['otr_id'] . ': e-mail enviado', 'success', true);
                } else {
                    $xcrud->set_notify('OTR ' . $transacao['otr_id'] . ': falha', 'error', true);
                }
            }
        }
    }
}

if (! function_exists('solicitar_aprovacao_admin')) {

    function solicitar_aprovacao_admin($xcrud)
    {
        $ci = &get_instance();
        $ci->load->library('controllers/InscricoesLib', null, 'inscricoes');
        if ($ci->inscricoes->email_inscricao($xcrud->get('primary'), 'solicita_aprovacao')) {
            $xcrud->set_notify('Solicitação enviada para coordenadores', 'success', true);
        } else {
            $xcrud->set_notify('Não foi possível enviar solicitação', 'error', true);
        }
    }
}

if (! function_exists('aprovar_admin')) {

    function aprovar_admin($xcrud)
    {
        $ci = &get_instance();
        $ci->load->library('controllers/InscricoesLib', null, 'inscricoes');
        $ci->inscricoes->aprovar_inscricao($xcrud->get('primary'));
    }
}

if (! function_exists('enviar_declaracao')) {

    function enviar_declaracao($xcrud = null, $ins_id = null, $download = false)
    {
        if (! $ins_id && ! $xcrud) {
            return null;
        }

        if (! $ins_id) {
            $ins_id = $xcrud->get('primary');
        }

        $ci = &get_instance();
        $ci->load->model('inscricoes_model');
        $ci->load->model('alunos_model');
        $ci->load->model('grupos_model');
        $vars['ins'] = $ci->inscricoes_model->getInscricaoCompleta($ins_id);
        $vars['alu'] = $ci->alunos_model->getRow($vars['ins']['ins_aluno']);
        $vars['grp'] = $ci->grupos_model->getRow($vars['ins']['ins_grupo']);
        $vars['grp']['grp_coordenadores'] = $ci->grupos_model->getCoordenadoresDoGrupo($vars['grp']['grp_id']);
        foreach ($vars['grp']['grp_coordenadores'] as $row) {
            $vars['grp_coordenadores'][] = $row['usr_nome'];
        }
        $vars['grp_coordenadores'] = implode(' e ', $vars['grp_coordenadores']);
        $vars['grp']['grp_dataInicio'] = date('d/m/Y', strtotime($vars['grp']['grp_dataInicio']));
        $vars['grp']['grp_dataFim'] = date('d/m/Y', strtotime($vars['grp']['grp_dataFim']));
        $vars['carga_horaria'] = strtotime($vars['grp']['grp_horaFim']) - strtotime($vars['grp']['grp_horaInicio']);
        $entrada = $vars['grp']['grp_horaInicio'];
        $saida = $vars['grp']['grp_horaFim'];
        $hora1 = explode(":", $entrada);
        $hora2 = explode(":", $saida);
        $acumulador1 = ($hora1[0] * 3600) + ($hora1[1] * 60) + $hora1[2];
        $acumulador2 = ($hora2[0] * 3600) + ($hora2[1] * 60) + $hora2[2];
        $resultado = $acumulador2 - $acumulador1;
        $hora_ponto = floor($resultado / 3600);
        $resultado = $resultado - ($hora_ponto * 3600);
        $min_ponto = floor($resultado / 60);
        $resultado = $resultado - ($min_ponto * 60);
        $tempo = $hora_ponto;
        $vars['carga_horaria'] = $tempo * $vars['grp']['grp_encontros'];
        $vars['title'] = 'Declaração - ' . $vars['alu']['alu_nomeArtistico'] . ' - ' . $vars['grp']['grp_nomePublico'] . ' - Grupo TAPA';
        $html = $ci->load->view('inscricao/declaracao.php', $vars, true);

        $dompdf_options = new Options();
        $dompdf_options->setDefaultMediaType('all');
        $dompdf_options->setIsRemoteEnabled(true);
        $dompdf_options->setIsFontSubsettingEnabled(true);

        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->setOptions($dompdf_options);
        $dompdf->loadHtml($html);
        $dompdf->render();

        $tmpfile_path = $_SERVER['DOCUMENT_ROOT'] . "/writable/alunos/" . $vars['title'] . " - " . date('YmdHis') . ".pdf";
        file_put_contents($tmpfile_path, $dompdf->output());
        if ($download) {
            $ci->load->helper('download');
            force_download($vars['title'] . '.pdf', $dompdf->output());
        } else {
            $ci->initMail();
            $ci->mail->addAddress($vars['alu']['alu_email']);
            $ci->mail->subject('Declaração - ' . $vars['alu']['alu_nomeArtistico'] . ' - ' . $vars['grp']['grp_nomePublico'] . ' - Grupo TAPA');
            $ci->mail->message($ci->load->view('emails/aluno/declaracao.php', $vars, true));
            $ci->mail->attach($dompdf->output(), '', 'Declaração - ' . $vars['alu']['alu_nomeArtistico'] . ' - ' . $vars['grp']['grp_nomePublico'] . ' - Grupo TAPA.pdf');
            if ($ci->mail->send()) {
                if ($xcrud) {
                    $xcrud->set_notify('Declaração enviada', 'success', true);
                }
                return true;
            } else {
                if ($xcrud) {
                    $xcrud->set_notify('Não foi possível enviar a declaração', 'error', true);
                }
                return true;
            }
        }
        // unlink($tmpfile_path);
    }
}

if (! function_exists('whatsAppMsg')) {

    function whatsAppMsg($xcrud)
    {
        if (! $xcrud->get('msg')) {
            return;
        }
        $ins_id = $xcrud->get('primary');
        $msg = $xcrud->get('msg');
        $ci = &get_instance();
        $ci->load->model('inscricoes_model');
        $ci->load->model('alunos_model');
        $ci->load->model('grupos_model');

        $ins = $ci->inscricoes_model->getInscricaoCompleta($ins_id);
        foreach ($ins as $k => $v) {
            if (! is_null($v)) {
                $msg = str_replace('{' . $k . '}', $v, $msg);
            }
        }
        $ins['alu_celular'] = preg_replace('/\D+/', '', $ins['alu_celular']);

        $url = "https://web.whatsapp.com/send?phone=55" . $ins['alu_celular'] . "&text=" . rawurlencode($msg);
        // $url = "whatsapp://send?phone=55" . $ins['alu_celular'] . "&text=" . rawurlencode($msg);
        // $xcrud->set_notify($msg, 'error', true);
        $out = '<script type="text/javascript">';
        $out .= 'shareButton("' . $ins['grp_nomePublico'] . '", "' . $msg . '", "' . $url . '")';
        $out .= '</script>';
        print $out;
    }
}

if (! function_exists('sincronizarInscricao')) {

    function sincronizarInscricao($xcrud = null)
    {
        $ins_id = $xcrud->get('primary');
        $ci = &get_instance();
        $ci->load->library('controllers/InscricoesLib', null, 'inscricoes');
        $ci->load->model('inscricoes_model');
        $ci->inscricoes->sincronizar($ins_id);
        $xcrud->set_notify('INS ' . $ins_id . ' sincronizada', 'success', true);
    }
}