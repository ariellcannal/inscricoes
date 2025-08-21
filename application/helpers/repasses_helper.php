<?php
if (! function_exists('repasse_data')) {

    function repasse_data($val, $field, $mode, $row, $xcrud)
    {
        if ($val != "" && $mode = 'list') {
            return date('d/m/Y', strtotime($val));
        }
        return $val;
    }
}
if (! function_exists('mergeRepasse')) {

    function mergeRepasse(array $rep_ids, $xcrud)
    {
        if ($rep_ids) {
            $ci = &get_instance();
            $ci->load->model('repasses_model');
            if ($ci->repasses_model->mesclar($rep_ids)) {
                $xcrud->set_notify('Repasses mesclados com sucesso', 'success');
            }
        }
    }
}