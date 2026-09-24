<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Deploy extends SYS_Controller
{
    private string $rootPath;
    private string $envPath;
    private string $storagePath;
    private string $lockPath;
    private string $maintenancePath;
    private string $composerLockBackupPath;
    private string $allowedRef = 'refs/heads/master';
    private string $allowedRepo = 'ariellcannal/inscricoes';
    private bool $deploySucceeded = false;
    private bool $rollbackSucceeded = false;
    private bool $maintenanceShouldPersist = true;
    private $lockHandle = null;

    public function index(): void
    {
        $this->handle();
    }

    public function handle(): void
    {
        $this->bootstrapPaths();
        $this->loadEnvironment();

        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            http_response_code(405);
            exit('Method Not Allowed');
        }

        @ignore_user_abort(true);
        $this->logger->debug('[DEPLOY] DEPLOY INICIADO');

        $payload = file_get_contents('php://input') ?: '';
        $secret = getenv('github.wh_secret') ?: '';
        $signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? ($_SERVER['HTTP_X_HUB_SIGNATURE'] ?? '');

        if ($secret === '' || $signature === '' || ! $this->isValidSignature($payload, $secret, $signature)) {
            http_response_code(403);
            $this->logger->warning('[DEPLOY] Assinatura invalida ou ausente');
            exit('Forbidden');
        }

        $this->logger->debug('[DEPLOY] Assinatura do GitHub validada');

        $data = $this->decodePayload($payload);
        $event = $_SERVER['HTTP_X_GITHUB_EVENT'] ?? '';
        $ref = $data['ref'] ?? '';
        $repo = $data['repository']['full_name'] ?? '';

        if ($event !== 'push' || $ref !== $this->allowedRef || $repo !== $this->allowedRepo) {
            $this->logger->debug("[DEPLOY] Evento ignorado: event={$event}, ref={$ref}, repo={$repo}");
            http_response_code(200);
            exit('Ignored');
        }

        $this->logger->debug("[DEPLOY] Payload aceito: ref={$ref}, repo={$repo}");

        if (! $this->acquireLock()) {
            http_response_code(202);
            echo 'Deploy already running';
            return;
        }

        register_shutdown_function([$this, 'shutdownCleanup']);

        $repoDir = $this->rootPath;
        $snapshot = $this->createRollbackSnapshot($repoDir);

        try {
            @set_time_limit(3600);
            $this->enableMaintenanceMode('starting');
            $this->setExecutionEnvironment();

            $this->logger->debug("[DEPLOY] Repositorio de trabalho: {$repoDir}");
            $this->refreshMaintenanceMode('preparing-repository');
            $this->ensureGitRepository($repoDir, $data);

            $this->refreshMaintenanceMode('syncing-git');
            $this->runCommand('git config --global --add safe.directory ' . escapeshellarg($repoDir), $repoDir, true);
            $this->runCommand('git fetch --prune origin', $repoDir);
            $this->runCommand('git checkout -B master origin/master', $repoDir);
            $this->runCommand('git reset --hard origin/master', $repoDir);
            $this->runCommand('git clean -fd', $repoDir);

            $this->refreshMaintenanceMode('validating-dependencies');
            $phpBinary = getenv('deploy.php_binary') ?: PHP_BINARY;
            $composerBinary = getenv('deploy.composer_binary') ?: '/usr/local/bin/composer';
            $composerCmd = escapeshellarg($phpBinary) . ' ' . escapeshellarg($composerBinary);

            $this->runCommand($composerCmd . ' validate --no-check-lock', $repoDir);

            $devFlag = (ENVIRONMENT === 'development') ? '' : '--no-dev';
            $this->refreshMaintenanceMode('running-composer-update');
            $this->logger->debug('[DEPLOY] Iniciando composer update');
            $this->runCommand(
                $composerCmd . ' update --no-interaction --prefer-dist --no-progress ' . $devFlag . ' --optimize-autoloader',
                $repoDir
            );

            $this->deploySucceeded = true;
            $this->maintenanceShouldPersist = false;
            $this->clearMaintenanceMode();
            $this->cleanupRollbackArtifacts();
            $this->logger->debug('[DEPLOY] Deploy finalizado com sucesso');
            http_response_code(200);
            echo 'OK';
        } catch (\Throwable $e) {
            $msg = sprintf('Erro no deploy: %s em %s:%d', $e->getMessage(), $e->getFile(), $e->getLine());
            $this->logger->error('[DEPLOY] ' . $msg);
            $this->logger->error('[DEPLOY] ' . $e->getTraceAsString());

            try {
                $this->rollbackDeployment($repoDir, $snapshot);
                $this->rollbackSucceeded = true;
                $this->maintenanceShouldPersist = false;
                $this->clearMaintenanceMode();
                $this->cleanupRollbackArtifacts();
                $this->logger->debug('[DEPLOY] Rollback concluido com sucesso');
            } catch (\Throwable $rollbackException) {
                $this->maintenanceShouldPersist = true;
                $this->logger->error(
                    sprintf(
                        '[DEPLOY] Rollback falhou: %s em %s:%d',
                        $rollbackException->getMessage(),
                        $rollbackException->getFile(),
                        $rollbackException->getLine()
                    )
                );
                $this->logger->error('[DEPLOY] ' . $rollbackException->getTraceAsString());
            }

            http_response_code(500);
            echo 'Erro no deploy';
        } finally {
            $this->releaseLock();
        }
    }

    private function bootstrapPaths(): void
    {
        $root = realpath(APPPATH . '../');
        if ($root === false) {
            throw new \RuntimeException('Nao foi possivel determinar o caminho raiz do projeto.');
        }

        $this->rootPath = rtrim($root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->storagePath = $this->rootPath . 'writable' . DIRECTORY_SEPARATOR;
        $this->envPath = $this->rootPath . '.env';
        $this->lockPath = $this->storagePath . 'deploy.lock';
        $this->maintenancePath = $this->storagePath . 'deploy.maintenance.json';
        $this->composerLockBackupPath = $this->storagePath . 'deploy.composer.lock.bak';

        $this->ensureDirectory($this->storagePath);
    }

    private function ensureDirectory(string $path): void
    {
        if (is_dir($path)) {
            return;
        }

        if (! @mkdir($path, 0775, true) && ! is_dir($path)) {
            throw new \RuntimeException('Nao foi possivel criar o diretorio: ' . $path);
        }
    }

    private function setExecutionEnvironment(): void
    {
        $home = $this->storagePath . 'home';
        $composerHome = $home . DIRECTORY_SEPARATOR . '.composer';

        $this->ensureDirectory($home);
        $this->ensureDirectory($composerHome);

        putenv('HOME=' . $home);
        putenv('COMPOSER_HOME=' . $composerHome);
        putenv('COMPOSER_NO_INTERACTION=1');
        putenv('COMPOSER_PROCESS_TIMEOUT=0');
        putenv('COMPOSER_MEMORY_LIMIT=-1');
        putenv('GIT_TERMINAL_PROMPT=0');
    }

    private function acquireLock(): bool
    {
        $this->lockHandle = fopen($this->lockPath, 'c+');

        if ($this->lockHandle === false) {
            $this->logger->error('[DEPLOY] Falha ao abrir lockfile: ' . $this->lockPath);
            return false;
        }

        if (! flock($this->lockHandle, LOCK_EX | LOCK_NB)) {
            $this->logger->warning('[DEPLOY] Ja existe um deploy em andamento (lock ativo)');
            fclose($this->lockHandle);
            $this->lockHandle = null;
            return false;
        }

        $this->writeLockState('lock-acquired');
        $this->logger->debug('[DEPLOY] Lock adquirido com sucesso');
        return true;
    }

    private function writeLockState(string $phase): void
    {
        if (! is_resource($this->lockHandle)) {
            return;
        }

        $state = array(
            'phase' => $phase,
            'pid' => function_exists('getmypid') ? getmypid() : null,
            'started_at' => date('c'),
            'updated_at' => date('c')
        );

        $json = json_encode($state, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if ($json === false) {
            throw new \RuntimeException('Nao foi possivel serializar o estado do deploy.');
        }

        rewind($this->lockHandle);
        if (ftruncate($this->lockHandle, 0) === false || fwrite($this->lockHandle, $json . PHP_EOL) === false) {
            throw new \RuntimeException('Nao foi possivel gravar o estado do lockfile.');
        }

        fflush($this->lockHandle);
    }

    private function enableMaintenanceMode(string $phase): void
    {
        $payload = array(
            'enabled' => true,
            'phase' => $phase,
            'pid' => function_exists('getmypid') ? getmypid() : null,
            'message' => 'Em Manutenção. tente novamente em alguns minutos',
            'started_at' => date('c'),
            'updated_at' => date('c')
        );

        $this->writeMaintenanceState($payload);
        $this->logger->debug('[DEPLOY] Modo de manutencao ativado');
    }

    private function refreshMaintenanceMode(string $phase): void
    {
        if (! is_file($this->maintenancePath)) {
            return;
        }

        $payload = $this->readJsonFile($this->maintenancePath);
        if ($payload === null) {
            $payload = array();
        }

        $payload['enabled'] = true;
        $payload['phase'] = $phase;
        $payload['pid'] = function_exists('getmypid') ? getmypid() : null;
        $payload['message'] = 'Em Manutenção. tente novamente em alguns minutos';
        $payload['updated_at'] = date('c');
        if (! isset($payload['started_at'])) {
            $payload['started_at'] = date('c');
        }

        $this->writeMaintenanceState($payload);
        $this->writeLockState($phase);
        $this->logger->debug('[DEPLOY] Modo de manutencao atualizado: ' . $phase);
    }

    private function clearMaintenanceMode(): void
    {
        if (is_file($this->maintenancePath)) {
            @unlink($this->maintenancePath);
        }

        $this->logger->debug('[DEPLOY] Modo de manutencao desativado');
    }

    private function writeMaintenanceState(array $payload): void
    {
        $json = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if ($json === false) {
            throw new \RuntimeException('Nao foi possivel serializar o estado de manutencao.');
        }

        if (file_put_contents($this->maintenancePath, $json . PHP_EOL, LOCK_EX) === false) {
            throw new \RuntimeException('Nao foi possivel gravar o arquivo de manutencao.');
        }
    }

    private function readJsonFile(string $path): ?array
    {
        if (! is_file($path) || ! is_readable($path)) {
            return null;
        }

        $raw = file_get_contents($path);
        if ($raw === false || trim($raw) === '') {
            return null;
        }

        $data = json_decode($raw, true);
        return is_array($data) ? $data : null;
    }

    private function cleanupRollbackArtifacts(): void
    {
        if (is_file($this->composerLockBackupPath)) {
            @unlink($this->composerLockBackupPath);
        }
    }

    private function createRollbackSnapshot(string $cwd): array
    {
        $snapshot = array(
            'git_head' => null,
            'composer_lock_backed_up' => false
        );

        if ($this->isGitRepository($cwd)) {
            [$status, $output] = $this->runCommand('git rev-parse HEAD', $cwd, false, true);
            if ($status === 0 && isset($output[0]) && trim($output[0]) !== '') {
                $snapshot['git_head'] = trim($output[0]);
            }
        }

        $composerLockPath = $cwd . 'composer.lock';
        if (is_file($composerLockPath)) {
            if (! copy($composerLockPath, $this->composerLockBackupPath)) {
                throw new \RuntimeException('Nao foi possivel criar backup do composer.lock.');
            }
            $snapshot['composer_lock_backed_up'] = true;
            $this->logger->debug('[DEPLOY] Backup do composer.lock criado');
        } else {
            $this->logger->debug('[DEPLOY] composer.lock nao encontrado; rollback parcial');
        }

        return $snapshot;
    }

    private function rollbackDeployment(string $cwd, array $snapshot): void
    {
        $this->logger->debug('[DEPLOY] Iniciando rollback do deploy');

        if (! empty($snapshot['git_head'])) {
            $this->runCommand('git reset --hard ' . escapeshellarg($snapshot['git_head']), $cwd);
            $this->runCommand('git clean -fd', $cwd);
        } else {
            $this->logger->debug('[DEPLOY] Snapshot Git indisponivel; rollback de codigo nao executado');
        }

        $composerLockPath = $cwd . 'composer.lock';
        if (! empty($snapshot['composer_lock_backed_up']) && is_file($this->composerLockBackupPath)) {
            if (! copy($this->composerLockBackupPath, $composerLockPath)) {
                throw new \RuntimeException('Nao foi possivel restaurar o composer.lock.');
            }

            $phpBinary = getenv('deploy.php_binary') ?: PHP_BINARY;
            $composerBinary = getenv('deploy.composer_binary') ?: '/usr/local/bin/composer';
            $composerCmd = escapeshellarg($phpBinary) . ' ' . escapeshellarg($composerBinary);
            $devFlag = (ENVIRONMENT === 'development') ? '' : '--no-dev';

            $this->runCommand(
                $composerCmd . ' install --no-interaction --prefer-dist --no-progress ' . $devFlag . ' --optimize-autoloader',
                $cwd
            );
            $this->logger->debug('[DEPLOY] composer install executado para restauracao');
        } else {
            $this->logger->debug('[DEPLOY] Sem backup do composer.lock; restauracao de dependencias nao executada');
        }
    }

    private function isGitRepository(string $cwd): bool
    {
        try {
            $this->runCommand('git rev-parse --is-inside-work-tree', $cwd);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function decodePayload(string $payload): array
    {
        $data = json_decode($payload, true);
        if (! is_array($data)) {
            throw new \RuntimeException('Payload do GitHub invalido.');
        }

        return $data;
    }

    private function isValidSignature(string $payload, string $secret, string $signature): bool
    {
        $expected = 'sha256=' . hash_hmac('sha256', $payload, $secret);
        return hash_equals($expected, $signature);
    }

    private function ensureGitRepository(string $cwd, array $data): void
    {
        if ($this->isGitRepository($cwd)) {
            $this->logger->debug('[DEPLOY] Repositorio Git ja existente neste diretorio.');
            return;
        }

        $remoteUrl = $data['repository']['clone_url'] ?? '';
        if ($remoteUrl === '') {
            throw new \RuntimeException('Nao foi possivel determinar a URL remota do repositorio.');
        }

        $this->logger->debug('[DEPLOY] Inicializando repositorio Git com URL: ' . $remoteUrl);
        $this->runCommand('git init', $cwd);
        $this->runCommand('git remote remove origin', $cwd, true);
        $this->runCommand('git remote add origin ' . escapeshellarg($remoteUrl), $cwd);
        $this->runCommand('git fetch origin', $cwd);

        [$trackedStatus, $tracked] = $this->runCommand('git ls-tree -r --name-only origin/master', $cwd, false, true);
        if ($trackedStatus !== 0) {
            throw new \RuntimeException('Nao foi possivel listar os arquivos rastreados do origin/master.');
        }

        [$untrackedStatus, $untracked] = $this->runCommand('git ls-files --others --exclude-standard', $cwd, false, true);
        if ($untrackedStatus !== 0) {
            throw new \RuntimeException('Nao foi possivel listar os arquivos untracked.');
        }

        $trackedSet = array();
        foreach ($tracked as $path) {
            $path = trim($path);
            if ($path !== '') {
                $trackedSet[$path] = true;
            }
        }

        $conflicting = array();
        foreach ($untracked as $path) {
            $path = trim($path);
            if ($path !== '' && isset($trackedSet[$path])) {
                $conflicting[] = $path;
            }
        }

        foreach ($conflicting as $path) {
            $trimmed = trim($path);
            if ($trimmed === '' || strpos($trimmed, '..') !== false) {
                continue;
            }

            $this->logger->debug('[DEPLOY] Removendo conflito untracked: ' . $trimmed);
            $this->runCommand('rm -rf -- ' . escapeshellarg($trimmed), $cwd, true);
        }

        $this->runCommand('git checkout -B master origin/master', $cwd);
        $this->logger->debug('[DEPLOY] Repositorio Git criado e sincronizado com origin/master.');
    }

    private function runCommand(string $command, string $cwd, bool $allowFailure = false, bool $captureOutput = false): array
    {
        $fullCommand = 'cd ' . escapeshellarg($cwd) . ' && ' . $command . ' 2>&1';
        $this->logger->debug('[DEPLOY] EXEC: ' . $fullCommand);

        $descriptors = array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            2 => array('pipe', 'w')
        );

        $process = proc_open($fullCommand, $descriptors, $pipes);
        if (! is_resource($process)) {
            throw new \RuntimeException('Nao foi possivel iniciar o comando: ' . $command);
        }

        fclose($pipes[0]);
        fclose($pipes[2]);

        $output = array();
        while (! feof($pipes[1])) {
            $line = fgets($pipes[1]);
            if ($line === false) {
                break;
            }

            $line = rtrim($line, "\r\n");
            $this->logger->debug('[DEPLOY] ' . ($line === '' ? 'OUTPUT: (vazio)' : 'OUTPUT: ' . $line));
            if ($captureOutput) {
                $output[] = $line;
            }
        }

        fclose($pipes[1]);
        $status = proc_close($process);
        $this->logger->debug('[DEPLOY] STATUS: ' . $status);

        if ($status !== 0 && ! $allowFailure) {
            throw new \RuntimeException('Comando falhou: ' . $command . ' (status ' . $status . ')');
        }

        return array(
            $status,
            $output
        );
    }

    private function loadEnvironment(): void
    {
        if (! is_readable($this->envPath)) {
            return;
        }

        $lines = file($this->envPath, FILE_IGNORE_NEW_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || strpos($line, '=') === false) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            if ($key === '') {
                continue;
            }

            $first = substr($value, 0, 1);
            $last = substr($value, -1);
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }

            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }

    private function shutdownCleanup(): void
    {
        if (is_resource($this->lockHandle)) {
            @flock($this->lockHandle, LOCK_UN);
            @fclose($this->lockHandle);
            $this->lockHandle = null;
        }

        if (is_file($this->lockPath)) {
            @unlink($this->lockPath);
        }

        if (! $this->maintenanceShouldPersist && is_file($this->maintenancePath)) {
            @unlink($this->maintenancePath);
        }
    }

    private function releaseLock(): void
    {
        if (is_resource($this->lockHandle)) {
            flock($this->lockHandle, LOCK_UN);
            fclose($this->lockHandle);
            $this->lockHandle = null;
        }

        if (is_file($this->lockPath)) {
            @unlink($this->lockPath);
        }

        if (! $this->maintenanceShouldPersist && is_file($this->maintenancePath)) {
            @unlink($this->maintenancePath);
        }
    }
}
