<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config = array(
    'login/auth' => array(
        array(
            'field'   => 'user',
            'label'   => 'Usuário',
            'rules'   => 'trim|strip_tags|required'
        ),
        array(
            'field'   => 'pass',
            'label'   => 'Senha',
            'rules'   => 'trim|strip_tags|required'
        )
    )
);


/* End of file form_validation.php */
/* Location: ./application/config/form_validation.php */