<?php

class Config extends SYS_Controller
{
    /**
     * Itens do submenu de configurações
     */
    public array $submenu = [
        'config/acoes' => 'Ações',
        'config/operadoras' => 'Operadoras',
        'config/taxas' => 'Taxas',
        'config/senha' => 'Senha'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
        $this->load->model('usuarios_model');
    }

    public function index(): void
    {
        redirect('/config/acoes');
    }

    public function acoes(): void
    {
        $conteudo = $this->load->view('config/acoes.php', $this->vars, true);
        $this->_view($conteudo);
    }

    public function taxas(): void
    {
        redirect('/config/acoes');
    }

    public function operadoras(): void
    {
        redirect('/config/acoes');
    }

    /**
     * Tela de manutenção de senhas
     */
    public function senha(): void
    {
        $conteudo = $this->load->view('config/senha.php', $this->vars, true);
        $this->_view($conteudo);
    }

    /**
     * Re-hash de senhas legadas para o padrão atual
     */
    public function rehash_senhas(): void
    {
        $total = $this->usuarios_model->rehashSenhasAntigas();
        $_SESSION['alert_success'][] = $total . ' senhas atualizadas.';
        redirect('/config/senha');
    }

    private function _view(string $conteudo): void
    {
        $this->vars['submenu'] = $this->submenu;
        $this->vars['conteudo'] = $conteudo;
        $this->load->view('index.php', $this->vars);
    }
}

/* End of file Config.php */
/* Location: ./application/controllers/Config.php */