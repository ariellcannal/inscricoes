<?php

class Config extends SYS_Controller
{

    public $submenu = [
        'config/acoes' => 'Ações',
        'config/operadoras' => 'Operadoras',
        'config/taxas' => 'Taxas'
    ];

    function __construct()
    {
        parent::__construct();
        $this->checkLogin();
    }

    public function index()
    {
        redirect('/config/acoes');
    }
    
    public function acoes()
    {
        return $this->_view($this->load->view('config/acoes.php', $this->vars, true));
    }
    
    public function taxas()
    {
        redirect('/config/acoes');
    }
    
    public function operadoras()
    {
        redirect('/config/acoes');
    }

    public function _view($conteudo)
    {
        $this->vars['submenu'] = $this->submenu;
        $this->vars['conteudo'] = $conteudo;
        $this->load->view('index.php', $this->vars);
    }
}

/* End of file Config.php */
/* Location: ./application/controllers/Config.php */