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
     * Importa o banco de produção para desenvolvimento.
     *
     * Abre túnel SSH, gera dump e importa no banco de desenvolvimento.
     *
     * @return void
     */
    public function importDatabase(): void
    {
        // Garante execução apenas em desenvolvimento
        if (ENVIRONMENT !== 'development') {
            show_404();
        }

        // Carrega configurações dos bancos
        $this->config->load('database', true);
        $prod = $this->config->item('production', 'database');
        $dev  = $this->config->item('development', 'database');

        // Define nome e caminho do arquivo de dump
        $fileName = date('Y.m.d-H.i-') . $prod['database'] . '.sql';
        $filePath = FCPATH . 'sql/' . $fileName;

        // Abre túnel SSH para o banco de produção
        $sshCmd = sprintf(
            'ssh -i %s -p %s -L %s:%s:%s %s -N >/dev/null 2>&1 & echo $!',
            escapeshellarg($prod['ssh_key']),
            escapeshellarg($prod['ssh_port']),
            escapeshellarg($prod['port']),
            escapeshellarg($prod['ssh_remote_host']),
            escapeshellarg($prod['ssh_remote_port']),
            escapeshellarg($prod['ssh_user'] . '@' . $prod['ssh_host'])
        );
        $tunnelPid = trim(shell_exec($sshCmd));

        // Caso o túnel não seja estabelecido, aborta
        if ($tunnelPid === '') {
            $_SESSION['alert_error'][] = 'Falha ao estabelecer túnel SSH.';
            redirect('/config/acoes');
            return;
        }

        // Aguarda estabilização do túnel
        sleep(1);

        // Realiza dump do banco de produção através do túnel
        $dumpCmd = sprintf(
            'mysqldump -h%s -P%s -u%s --password=%s %s > %s',
            escapeshellarg($prod['hostname']),
            escapeshellarg($prod['port']),
            escapeshellarg($prod['username']),
            escapeshellarg($prod['password']),
            escapeshellarg($prod['database']),
            escapeshellarg($filePath)
        );
        exec($dumpCmd);

        // Encerra túnel SSH
        exec('kill ' . escapeshellarg($tunnelPid) . ' 2>/dev/null');

        // Importa dump no banco de desenvolvimento
        $importCmd = sprintf(
            'mysql -h%s -u%s --password=%s %s < %s',
            escapeshellarg($dev['hostname']),
            escapeshellarg($dev['username']),
            escapeshellarg($dev['password']),
            escapeshellarg($dev['database']),
            escapeshellarg($filePath)
        );
        exec($importCmd);

        // Informa sucesso e retorna à tela de ações
        $_SESSION['alert_success'][] = 'Banco de produção importado.';
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