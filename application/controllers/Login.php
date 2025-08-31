<?php

class Login extends SYS_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Exibe formulário de login.
     *
     * @return void
     */
    public function index(): void
    {
        $this->session->sess_destroy();
        $this->assets->js('login.js');
        $this->assets->css('login.css');
        $redirectTo = $this->input->get('redirect_to');
        $this->load->view('login/login.php', ['redirect_to' => $redirectTo]);
    }

    /**
     * Processa autenticação.
     *
     * @return void
     */
    public function auth(): void
    {
        if ($this->form_validation->run('login/auth')) {
            $response = $this->usuarios_model->checkLogin($this->input->post('user'), $this->input->post('pass'));
            if ($response === false) {
                set_status_header(401, 'Usuário ou senha inválidos');
                return;
            }
            $this->_setSession($response, $this->input->post('redirect_to'));
        } else {
            $this->form_validation->set_error_delimiters('', '');
            set_status_header(401, validation_errors());
            return;
        }
    }

    /**
     * Define dados de sessão e redirecionamento.
     *
     * @param array $data
     * @param string|null $redirectTo
     * @return void
     */
    public function _setSession(array $data, ?string $redirectTo = null): void
    {
        if ($data['usr_preferencias'] != "") {
            $data['usr_preferencias'] = json_decode($data['usr_preferencias'], true);
            if (array_key_exists('ultima_aba', $data['usr_preferencias']) && array_key_exists($data['usr_preferencias']['ultima_aba'], $this->vars['menu'])) {
                $redirect = $data['usr_preferencias']['ultima_aba'];
            } else {
                $redirect = '/transacoes';
            }
        } else {
            $redirect = '/transacoes';
        }

        $this->session->set_userdata($data);

        if ($redirectTo) {
            $redirect = $redirectTo;
        }

        exit(json_encode([
            'status' => 'success',
            'redirect' => $redirect
        ]));
    }

    /**
     * Encerra a sessão atual e redireciona para o login.
     *
     * @return void
     */
    public function sair(): void
    {
        $redirectTo = $this->input->get('redirect_to');
        $pingError = $this->session->flashdata('ping_error');
        $this->session->unset_userdata(array_keys($_SESSION));
        $this->session->set_flashdata('ping_error', $pingError);
        $_SESSION = [];
        unset($_SESSION);
        session_destroy();
        $loginUrl = 'login';
        if ($redirectTo) {
            $loginUrl .= '?redirect_to=' . rawurlencode($redirectTo);
        }
        redirect($loginUrl);
    }
}