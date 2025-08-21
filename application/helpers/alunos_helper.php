<?php
if (! function_exists('AI_aluno')) {

    function AI_aluno($postdata, $alu_id, $xcrud)
    {
        $ci = &get_instance();
        $ci->session->set_userdata('last_aluno', $alu_id);
        $xcrud->pass_default('ins_aluno', $alu_id);
    }
}
if (! function_exists('AU_aluno')) {

    function AU_aluno($postdata, $alu_id, $xcrud)
    {
        $ci = &get_instance();
        $ci->session->set_userdata('last_aluno', $alu_id);
        $xcrud->pass_default('ins_aluno', $alu_id);
    }
}
if (! function_exists('AI_presenca')) {

    function AI_presenca($postdata, $prs_id, $xcrud)
    {
        $ci = &get_instance();
        $ci->session->set_userdata('last_grupo', $postdata->get('prs_grupo'));
        $ci->session->set_userdata('last_dataAula', $postdata->get('prs_dataAula'));
        $xcrud->pass_default('prs_grupo', $postdata->get('prs_grupo'));
        $xcrud->pass_default('prs_dataAula', $postdata->get('prs_dataAula'));
    }
}
if (! function_exists('AU_presenca')) {

    function AU_presenca($postdata, $prs_id, $xcrud)
    {
        $ci = &get_instance();
        $ci->session->set_userdata('last_grupo', $postdata->get('prs_grupo'));
        $ci->session->set_userdata('last_dataAula', $postdata->get('prs_dataAula'));
        $xcrud->pass_default('prs_grupo', $postdata->get('prs_grupo'));
        $xcrud->pass_default('prs_dataAula', $postdata->get('prs_dataAula'));
    }
}
if (! function_exists('mergeAluno')) {

    function mergeAluno($alu_ids, $xcrud)
    {
        if ($alu_ids) {
            $ci = &get_instance();
            $ci->load->model('alunos_model');
            if ($ci->alunos_model->mesclar($alu_ids)) {
                $xcrud->set_notify('Alunos mesclados com sucesso', 'success');
            }
        }
    }
}