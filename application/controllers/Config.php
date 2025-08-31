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

        $prod = array(
            'hostname' => getenv('db.prd.hostname'),
            'username' => getenv('db.prd.username'),
            'password' => getenv('db.prd.password'),
            'database' => getenv('db.prd.database'),
            'ssh_host' => getenv('db.prd.ssh_host'),
            'ssh_user' => getenv('db.prd.ssh_user'),
            'ssh_pass' => getenv('db.prd.ssh_pass'),
            'ssh_port' => getenv('db.prd.ssh_port'),
            'ssh_key'  => getenv('db.prd.ssh_key'),
        );
        
        $dev = array(
            'hostname' => getenv('db.dev.hostname'),
            'username' => getenv('db.dev.username'),
            'password' => getenv('db.dev.password'),
            'database' => getenv('db.dev.database')
        );

        // Define nome e caminho do arquivo de dump
        $fileName = date('Y.m.d-H.i-') . $prod['database'] . '.sql';
        $filePath = FCPATH . 'sql/' . $fileName;

        $sshDumpCmd = sprintf(
            'ssh -i %s -p %s %s@%s ' .
            escapeshellarg(
            // este é o comando que roda NO REMOTO:
            'mysqldump -h127.0.0.1 -P3306 -u' . escapeshellarg($prod['username']) .
            ' --ssl-mode=REQUIRED --set-gtid-purged=OFF --password=' . escapeshellarg($prod['password']) . ' ' .
            escapeshellarg($prod['database'])
            ) .
            ' > %s',
            escapeshellarg(FCPATH . $prod['ssh_key']),
            escapeshellarg($prod['ssh_port']),
            escapeshellarg($prod['ssh_user']),
            escapeshellarg($prod['ssh_host']),
            escapeshellarg($filePath)
            );
        exec($sshDumpCmd, $dumpOutput, $dumpStatus);
        if ($dumpStatus !== 0) {
            throw new Error('Falha ao gerar dump do banco de produção (via SSH).');
        }
        
        // Importa dump no banco de desenvolvimento
        $importCmd = sprintf(
            'mysql -h%s -u%s --password=%s %s < %s',
            escapeshellarg($dev['hostname']),
            escapeshellarg($dev['username']),
            escapeshellarg($dev['password']),
            escapeshellarg($dev['database']),
            escapeshellarg($filePath)
        );
        exec($importCmd, $importOutput, $importStatus);
        if ($importStatus !== 0) {
            throw new Error('Falha ao importar dump no banco de desenvolvimento.');
        }
        
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