<?php
if (! function_exists('BI_recebivel')) {

    function BI_recebivel($postdata, $xcrud)
    {
        $ci = &get_instance();
        $ci->load->library('controllers/RecebiveisLib', null, 'recebiveis');
        $ci->load->model('inscricoes_model');
        $ci->load->model('grupos_model');
        $ci->load->model('grupos_model');
        $ci->session->set_userdata('last_grupo', $postdata->get('i.ins_grupo'));
        $ci->session->set_userdata('last_aluno', $postdata->get('i.ins_aluno'));
        $xcrud->pass_default('i.ins_grupo', $postdata->get('i.ins_grupo'));
        $xcrud->pass_default('i.ins_aluno', $postdata->get('i.ins_aluno'));
        $xcrud->pass_default('rec_dataTransacao', $postdata->get('rec_dataTransacao'));
        $i = $ci->inscricoes_model->checkInscricao($postdata->get('i.ins_grupo'), $postdata->get('i.ins_aluno'));
        if (! $i) {
            // FAZ A INSCRIÇÃO
            $i['ins_aluno'] = $postdata->get('i.ins_aluno');
            $i['ins_grupo'] = $postdata->get('i.ins_grupo');
            $i['ins_data'] = date('Y-m-d H:i:s');
            $i['ins_IP'] = $_SERVER['REMOTE_ADDR'];
            $i['ins_user'] = $ci->session->userdata('usr_id');
            $i['ins_id'] = $ci->inscricoes_model->inserir($i);
            if (! $i['ins_id']) {
                $xcrud->set_notify('Não foi possível fazer a inscrição.', 'error', true);
                return false;
            }
        }
        $postdata->set('rec_inscricao', $i['ins_id']);
        $postdata->set('rec_valor', (float) str_replace(',', '.', str_replace('.', '', str_replace('R$ ', '', $postdata->get('rec_valor')))));
        if ($postdata->get('rec_creditoUtilizado') != '') {
            // TENTA UTILIZAR CRÉDITO
            $ci->load->model('alunos_model');
            $alc = $ci->alunos_model->getCredito($postdata->get('rec_creditoUtilizado'));
            $alc['alc_valorUtilizado'] = (float) $alc['alc_valorUtilizado'];
            $alc['alc_valorInicial'] = (float) $alc['alc_valorInicial'];
            // print_r($alc);print_r($postdata->to_array());print((string)$alc['alc_valorUtilizado']+$postdata->get('rec_valor'));
            if ($alc && $alc['alc_aluno'] == $postdata->get('i.ins_aluno') && ($alc['alc_valorUtilizado'] + $postdata->get('rec_valor')) <= $alc['alc_valorInicial']) {
                $ci->alunos_model->utilizarCredito($postdata->get('rec_creditoUtilizado'), $postdata->get('rec_valor'));
                $postdata->set('rec_valorLiquido', '0');
                $postdata->set('rec_dataRecebimento', date('Y-m-d H:i:s'));
                $postdata->set('rec_forma', 'Crédito Aluno');
                $postdata->set('rec_recebido', '1');
            } else {
                $xcrud->set_notify('Não foi possível utilizar este crédito. Valor disponível: R$' . number_format($alc['alc_valorInicial'] - $alc['alc_valorUtilizado'], 2, ',', '.'), 'error', true);
                return false;
            }
        } else {
            $postdata->del('rec_creditoUtilizado');
            $postdata->set('rec_dataRecebimento', $postdata->get('rec_dataTransacao'));
            $postdata->set('rec_valorLiquido', $ci->recebiveis->valorLiquido($postdata->get('rec_valor'), $postdata->get('rec_forma')));
        }
        $postdata->set('rec_valor', str_replace('.', ',', $postdata->get('rec_valor')));
        $postdata->set('rec_valorLiquido', str_replace('.', ',', $postdata->get('rec_valorLiquido')));
    }
}
if (! function_exists('BU_recebivel')) {

    function BU_recebivel($postdata, $rec_id, $xcrud)
    {
        $ci = &get_instance();
        $ci->load->config('taxas');
        $ci->load->helper('dates_helper');
        $postdata->set('rec_valor', (float) str_replace(',', '.', str_replace('.', '', str_replace('R$ ', '', $postdata->get('rec_valor')))));
        if ($postdata->get('rec_creditoUtilizado') != '') {
            // TENTA UTILIZAR CRÉDITO
            $ci->load->model('alunos_model');
            $alc = $ci->alunos_model->getCredito($postdata->get('rec_creditoUtilizado'));
            $alc['alc_valorUtilizado'] = (float) $alc['alc_valorUtilizado'];
            $alc['alc_valorInicial'] = (float) $alc['alc_valorInicial'];
            // print_r($alc);print_r($postdata->to_array());print((string)$alc['alc_valorUtilizado']+$postdata->get('rec_valor'));
            if ($alc && $alc['alc_aluno'] == $postdata->get('i.ins_aluno') && ($alc['alc_valorUtilizado'] + $postdata->get('rec_valor')) <= $alc['alc_valorInicial']) {
                $ci->alunos_model->utilizarCredito($postdata->get('rec_creditoUtilizado'), $postdata->get('rec_valor'));
                $postdata->set('rec_valorLiquido', '0');
                $postdata->set('rec_dataRecebimento', date('Y-m-d H:i:s'));
                $postdata->set('rec_forma', 'Crédito Aluno');
                $postdata->set('rec_recebido', '1');
            } else {
                $xcrud->set_notify('Não foi possível utilizar este crédito. Valor disponível: R$' . number_format($alc['alc_valorInicial'] - $alc['alc_valorUtilizado'], 2, ',', '.'), 'error', true);
                return false;
            }
        } else {
            $postdata->del('rec_creditoUtilizado');
            $postdata->set('rec_valorLiquido', $ci->recebiveis->valorLiquido($postdata->get('rec_valor'), $postdata->get('rec_forma')));
        }

        $postdata->set('rec_valor', str_replace('.', ',', $postdata->get('rec_valor')));
        $postdata->set('rec_valorLiquido', str_replace('.', ',', $postdata->get('rec_valorLiquido')));
    }
}
if (! function_exists('BR_recebivel')) {

    function BR_recebivel($rec_id, $xcrud)
    {
        $ci = &get_instance();
        $ci->load->model('recebiveis_model');
        $rec = $ci->recebiveis_model->getRecebiveisCompleto($rec_id);
        global $temp_ins_id;
        $temp_ins_id = $rec['ins_id'];
    }
}

if (! function_exists('AI_recebivel')) {

    function AI_recebivel($postdata, $rec_id, $xcrud)
    {
        $ci = &get_instance();
        $ci->load->model('inscricoes_model');
        $ci->load->model('recebiveis_model');
        $rec = $ci->recebiveis_model->getRecebiveisCompleto($rec_id);
        if ($rec) {
            $ci->inscricoes_model->setTotaisInscricao($rec['ins_id']);
        }
    }
}
if (! function_exists('AU_recebivel')) {

    function AU_recebivel($postdata, $rec_id, $xcrud)
    {
        $ci = &get_instance();
        $ci->load->model('inscricoes_model');
        $ci->load->model('recebiveis_model');
        $rec = $ci->recebiveis_model->getRecebiveisCompleto($rec_id);
        if ($rec) {
            $ci->inscricoes_model->setTotaisInscricao($rec['ins_id']);
        }
    }
}
if (! function_exists('AR_recebivel')) {

    function AR_recebivel($rec_id, $xcrud)
    {
        global $temp_ins_id;
        $ci = &get_instance();
        $ci->load->model('inscricoes_model');
        $ci->load->model('recebiveis_model');
        $ci->inscricoes_model->setTotaisInscricao($temp_ins_id);
    }
}