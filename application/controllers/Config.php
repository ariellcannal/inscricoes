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
     * Exibe o conteúdo de um arquivo de log.
     *
     * @param string|null $path Caminho relativo do arquivo de log.
     * @return void
     */
    public function show_log(?string $path = null): void
    {
        if ($path === null) {
            show_404();
        }

        $logsDir = APPPATH . 'logs' . DIRECTORY_SEPARATOR;
        $realLogsDir = realpath($logsDir);
        $fullPath = realpath($logsDir . $path);

        if ($realLogsDir === false || $fullPath === false || strpos($fullPath, $realLogsDir) !== 0 || !is_file($fullPath)) {
            show_404();
        }

        $this->load->helper('file');
        header('Content-Type: text/plain; charset=utf-8');
        echo read_file($fullPath);
    }

    /**
     * Importa o banco de produção para desenvolvimento.
     *
     * Abre túnel SSH, gera dump compactado e importa simultaneamente via Pipeline em RAM.
     *
     * @return void
     */
    public function importDatabase(): void
    {
        // Garante execução apenas em desenvolvimento
        if (ENVIRONMENT !== 'development') {
            show_404();
        }

        // Aumenta o tempo limite e memória do PHP para evitar interrupções em bancos grandes
        set_time_limit(0);
        ini_set('memory_limit', '512M');

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

        $fileName = date('Y.m.d-H.i-') . $prod['database'] . '.sql.gz';
        $filePath = FCPATH . 'sql/' . $fileName;

        // =========================================================================
        // OTIMIZAÇÃO EXTREMA: Pipeline Único
        // 1. Gera dump e compacta na origem (nível 3 para focar em velocidade de CPU)
        // 2. Transmite via rede
        // 3. 'tee' intercepta o pacote e salva uma cópia no disco ($filePath)
        // 4. Simultaneamente, descompacta em RAM e injeta direto no MySQL local
        // =========================================================================
        
        $pipelineCmd = sprintf(
            'ssh -i %s -p %s %s@%s ' .
            escapeshellarg(
                'mysqldump -h127.0.0.1 -u' . escapeshellarg($prod['username']) .
                ' --password=' . escapeshellarg($prod['password']) .
                ' --ssl-mode=REQUIRED --set-gtid-purged=OFF --quick --single-transaction --routines --triggers ' . 
                escapeshellarg($prod['database']) . ' | gzip -3 -c'
            ) .
            ' | tee %s | gunzip -c | mysql -h%s -u%s --password=%s %s',
            escapeshellarg(FCPATH . $prod['ssh_key']),
            escapeshellarg($prod['ssh_port']),
            escapeshellarg($prod['ssh_user']),
            escapeshellarg($prod['ssh_host']),
            escapeshellarg($filePath),
            escapeshellarg($dev['hostname']),
            escapeshellarg($dev['username']),
            escapeshellarg($dev['password']),
            escapeshellarg($dev['database'])
        );

        exec($pipelineCmd, $output, $status);

        if ($status !== 0) {
            throw new Error('Falha ao importar o banco de dados em modo Pipeline. Código de Saída: ' . $status);
        }
        
        // Informa sucesso e retorna à tela de ações
        $_SESSION['alert_success'][] = 'Banco de produção importado com sucesso (Alta Velocidade).';
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