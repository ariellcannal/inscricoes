<?php
if (! function_exists('AI_transacao')) {

    function AI_transacao($postdata, $otr_id, $xcrud)
    {
        $ci = &get_instance();
        $ci->load->library('controllers/TransacoesLib', null, 'transacoes');
        $ci->transacoes->sincronizar($otr_id, null, false);
    }
}