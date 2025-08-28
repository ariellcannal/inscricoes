<?php

class Login extends SYS_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->session->sess_destroy();
        $this->assets->js('login.js');
        $this->assets->css('login.css');
        $this->load->view('login/login.php');
    }

    public function auth()
    {
        if ($this->form_validation->run('login/auth')) {
            $resposta = $this->usuarios_model->checkLogin($this->input->post('user'), $this->input->post('pass'));
            if ($resposta === false) {
                return set_status_header(401,'Usuário ou senha inválidos');
            } else {
                $this->_setSession($resposta);
            }
        } else {
            $this->form_validation->set_error_delimiters('', '');
            return set_status_header(401,validation_errors());
        }
    }

    public function _setSession($dados, $redirecionar = false)
    {
        if ($dados['usr_preferencias'] != "") {
            $dados['usr_preferencias'] = json_decode($dados['usr_preferencias'], true);
            if (array_key_exists('ultima_aba', $dados['usr_preferencias']) && array_key_exists($dados['usr_preferencias']['ultima_aba'], $this->vars['menu'])) {
                $redirect = $dados['usr_preferencias']['ultima_aba'];
            } else {
                $redirect = '/transacoes';
            }
        } else {
            $redirect = '/transacoes';
        }
        $this->session->set_userdata($dados);

        if ($redirecionar === true) {
            redirect($redirect);
        }
        exit(json_encode(array(
            'status' => 'success',
            'redirect' => $redirect
        )));
    }

    function sair()
    {
        $ping_error = $this->session->flashdata('ping_error');
        $this->session->unset_userdata(array_keys($_SESSION));
        $this->session->set_flashdata('ping_error', $ping_error);
        $_SESSION = array();
        unset($_SESSION);
        session_destroy();
        redirect('login');
    }
}