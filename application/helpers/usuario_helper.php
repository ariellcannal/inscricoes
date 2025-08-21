<?php
if (! function_exists('BI_usuario')) {
    
    function BI_usuario($postdata, $xcrud) {
        if($postdata->get('usr_empresa') == ''){
            $postdata->set('usr_master','1');
        }
        if($postdata->get('usr_master') == '1'){
            $postdata->set('usr_empresa',null);
            $postdata->set('usr_permissao','Administrador');
        }
    }
}
if (! function_exists('BU_usuario')) {
    
    function BU_usuario($postdata, $xcrud) {
        if($postdata->get('usr_empresa') == ''){
            $postdata->set('usr_master','1');
        }
        if($postdata->get('usr_master') == '1'){
            $postdata->set('usr_empresa',null);
            $postdata->set('usr_permissao','Administrador');
        }
    }
}
