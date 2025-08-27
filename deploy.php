<?php

/**
 * Manipulador de deploy acionado por webhook do GitHub.
 *
 * Comentários em português conforme convenção.
 */
class DeployHandler
{
    /**
     * Caminho raiz do projeto.
     *
     * @var string
     */
    private string $rootPath;

    /**
     * Caminho para o arquivo de ambiente.
     *
     * @var string
     */
    private string $envPath;

    /**
     * Caminho para o script de deploy.
     *
     * @var string
     */
    private string $deployScript;

    /**
     * Caminho para o arquivo de log.
     *
     * @var string
     */
    private string $logPath;

    /**
     * Construtor.
     *
     * @param string $rootPath Diretório raiz do projeto.
     */
    public function __construct(string $rootPath)
    {
        $this->rootPath = $rootPath;
        $this->envPath = $rootPath . '/.env';
        $this->deployScript = $rootPath . '/deploy.sh';
        $this->logPath = $rootPath . '/application/logs/deploy.log';
    }

    /**
     * Inicia o processamento do webhook.
     *
     * @return void
     */
    public function handle(): void
    {
        // Carrega variáveis de ambiente
        $this->loadEnvironment();

        // Valida método HTTP
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            http_response_code(405);
            exit('Method Not Allowed');
        }

        $payload = file_get_contents('php://input');

        // Valida assinatura
        $secret = getenv('GITHUB_WEBHOOK_SECRET') ?: '';
        $signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256']
            ?? $_SERVER['HTTP_X_HUB_SIGNATURE']
            ?? '';

        if ($secret === '' || $signature === '' || ! $this->isValidSignature($payload, $secret, $signature)) {
            http_response_code(403);
            error_log(date('[Y-m-d H:i:s] ') . "Assinatura inválida\n", 3, $this->logPath);
            exit('Forbidden');
        }

        // Valida evento e branch
        $event = $_SERVER['HTTP_X_GITHUB_EVENT'] ?? '';
        $data = json_decode($payload, true);
        $ref = $data['ref'] ?? '';
        $allowedRefs = ['refs/heads/master', 'refs/heads/main'];
        if ($event !== 'push' || ! in_array($ref, $allowedRefs, true)) {
            http_response_code(200);
            exit('Ignored');
        }

        // Lock para evitar concorrência
        $lockFile = $this->rootPath . '/deploy.lock';
        $lock = fopen($lockFile, 'c');
        if (! flock($lock, LOCK_EX | LOCK_NB)) {
            http_response_code(202);
            echo 'Deploy em andamento';
            exit();
        }

        // Dispara script em background
        if (! is_executable($this->deployScript)) {
            @chmod($this->deployScript, 0755);
        }
        $logDir = dirname($this->logPath);
        if (! is_dir($logDir)) {
            @mkdir($logDir, 0775, true);
        }
        exec('bash ' . escapeshellarg($this->deployScript) . ' >> '
            . escapeshellarg($this->logPath) . ' 2>&1 &');

        http_response_code(200);
        echo 'OK';
    }

    /**
     * Carrega variáveis de ambiente do arquivo .env.
     *
     * @return void
     */
    private function loadEnvironment(): void
    {
        if (! is_readable($this->envPath)) {
            return;
        }

        foreach (file($this->envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            if ($line[0] === '#') {
                continue;
            }
            [$key, $value] = array_map('trim', explode('=', $line, 2));
            if ($key !== '') {
                putenv("$key=$value");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }

    /**
     * Verifica se a assinatura enviada é válida.
     *
     * @param string $payload  Dados recebidos do GitHub.
     * @param string $secret   Chave compartilhada do webhook.
     * @param string $signature Assinatura recebida.
     *
     * @return bool
     */
    private function isValidSignature(string $payload, string $secret, string $signature): bool
    {
        $expected = 'sha256=' . hash_hmac('sha256', $payload, $secret);
        return hash_equals($expected, $signature);
    }
}

$handler = new DeployHandler(__DIR__);
$handler->handle();
