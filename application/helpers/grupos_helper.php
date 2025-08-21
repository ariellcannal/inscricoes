<?php
// require_once (__DIR__ . '/../../vendor/autoload.php');
if (! function_exists('BI_grupo')) {

    function BI_grupo($postdata, $xcrud)
    {
        $postdata->set('grp_slug', urlencode(str_replace(' ', '-', tirarAcentos(strtolower($postdata->get('grp_nomePublico'))))));
    }
}
if (! function_exists('BU_grupo')) {

    function BU_grupo($postdata, $xcrud)
    {
        // $postdata->set('grp_slug', urlencode(str_replace(' ', '-', strtolower($postdata->get('grp_nomePublico')))));
    }
}
if (! function_exists('AC_grupo')) {

    function AC_grupo($original_id, $new_id, $xcrud)
    {
        $ci = &get_instance();
        $ci->load->model('grupos_model');
        $ci->grupos_model->cloneGrupo($original_id, $new_id);
    }
}
if (! function_exists('BI_gfp')) {

    function BI_gfp($postdata, $xcrud)
    {
        if ($postdata->get('gfp_parcelas') > 1) {
            $postdata->set('gfp_aceitaCartao', '1');
        }
        $total = str_replace('.', '', $postdata->get('gfp_valorTotal'));
        $total = (float) str_replace(',', '.', $total);
        $postdata->set('gfp_descricao', 'R$'.number_format($total,2,',','.') . ' ' . ($postdata->get('gfp_parcelas') > 1 ? 'em até ' . $postdata->get('gfp_parcelas') . 'x no' : 'no') . ' ' . ($postdata->get('gfp_aceitaCartao') ? "cartão de crédito" : "PIX"));
    }
}
if (! function_exists('BU_gfp')) {

    function BU_gfp($postdata, $gfp_id, $xcrud)
    {
        $total = str_replace('.', '', $postdata->get('gfp_valorTotal'));
        $total = (float) str_replace(',', '.', $total);
        $postdata->set('gfp_descricao', 'R$'.number_format($total,2,',','.') . ' ' . ($postdata->get('gfp_parcelas') > 1 ? 'em até ' . $postdata->get('gfp_parcelas') . 'x no' : 'no') . ' ' . ($postdata->get('gfp_aceitaCartao') ? "cartão de crédito" : "PIX"));
    }
}
if (! function_exists('linkWhatsGrupo')) {

    function linkWhatsGrupo($value, $fieldname, $primary_key, $row, $xcrud)
    {
        $link = 'https://api.whatsapp.com/send?phone=55' . preg_replace('/[^0-9]/', '', $row['a.alu_celular']) . '&text=' . urlencode($value);
        return '<a href=' . $link . ' target="_blank"><i class="fab fa-whatsapp"></i></a>';
    }
}
if (! function_exists('BI_presenca')) {

    function BI_presenca($postdata, $xcrud)
    {
        foreach ($postdata->to_array() as $k => $v) {
            if (strstr($k, 'presenca.alu_')) {
                $aluno[str_replace('presenca.', '', $k)] = $v;
            }
        }
        $ci = &get_instance();
        $ci->load->helper('alunos_helper');
        $ci->load->library('controllers/AlunosLib', null, 'alunos');
        
        if (count($aluno)) {
            $alu = $ci->alunos->check($aluno);
            $alu_id = $alu['alu_id'];
            if ($alu_id) {
                $postdata->set('prs_aluno', $alu_id);
            } else {
                $xcrud->set_notify('Não foi possível encontrar ou cadastrar o aluno.', 'error', true);
                return false;
            }
        } else {
            $xcrud->set_notify('Falha na solicitação.', 'error', true);
            return false;
        }

        $ci->load->model('alunos_model');
        $presencas = $ci->alunos_model->getPresencas($alu_id, $postdata->get('prs_grupo'));
        if (! empty($presencas) && date('Y-m-d') == date('Y-m-d', strtotime($presencas[0]['prs_data']))) {
            $xcrud->set_notify('Você já registrou presença neste grupo hoje.', 'error', true);
            return false;
        }
        if (ENVIRONMENT != "development" && ! empty($_COOKIE['tapa_presenca_ultimo']) && date('Y-m-d') == date('Y-m-d', strtotime($_COOKIE['tapa_presenca_ultimo']))) {
            $xcrud->set_notify('Você só pode registrar uma presença por dispositivo, por aula.', 'error', true);
            return false;
        }
        setcookie('tapa_presenca_ultimo', date('Y-m-d H:i:s'), time() + 3600, '/', 'oficinas.cannal.com.br', true);
        setcookie('tapa_presenca', $alu_id, time() + 3600, '/', 'oficinas.cannal.com.br', true);
    }
}
