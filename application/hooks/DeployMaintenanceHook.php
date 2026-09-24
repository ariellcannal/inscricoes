<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DeployMaintenanceHook
{
    private string $rootPath;
    private string $maintenancePath;
    private int $staleAfterSeconds = 43200;

    public function __construct()
    {
        $root = realpath(APPPATH . '../');
        $this->rootPath = $root !== false ? rtrim($root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR : rtrim(APPPATH . '../', DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->maintenancePath = $this->rootPath . 'writable' . DIRECTORY_SEPARATOR . 'deploy.maintenance.json';
    }

    public function handle(): void
    {
        if (! is_file($this->maintenancePath)) {
            return;
        }

        if ($this->shouldBypass()) {
            return;
        }

        $state = $this->readState();
        if ($state !== null && isset($state['enabled']) && ! $state['enabled']) {
            @unlink($this->maintenancePath);
            return;
        }

        if ($this->isStale($state)) {
            @unlink($this->maintenancePath);
            return;
        }

        $this->renderMaintenancePage();
    }

    private function shouldBypass(): bool
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $path = parse_url($uri, PHP_URL_PATH);
        if (! is_string($path) || $path === '') {
            return false;
        }

        $path = ltrim($path, '/');
        if (strpos($path, 'index.php/') === 0) {
            $path = substr($path, strlen('index.php/'));
        }

        return $path === 'deploy'
            || strpos($path, 'deploy/') === 0
            || $path === 'webhook'
            || strpos($path, 'webhook/') === 0;
    }

    private function readState(): ?array
    {
        $raw = @file_get_contents($this->maintenancePath);
        if ($raw === false || trim($raw) === '') {
            return null;
        }

        $state = json_decode($raw, true);
        return is_array($state) ? $state : null;
    }

    private function isStale(?array $state): bool
    {
        $fileMtime = @filemtime($this->maintenancePath);
        $lastUpdated = null;

        if (is_array($state)) {
            $candidate = $state['updated_at'] ?? ($state['started_at'] ?? null);
            if (is_string($candidate) && $candidate !== '') {
                $parsed = strtotime($candidate);
                if ($parsed !== false) {
                    $lastUpdated = $parsed;
                }
            }
        }

        if ($lastUpdated === null) {
            $lastUpdated = $fileMtime !== false ? $fileMtime : time();
        }

        if ((time() - $lastUpdated) < $this->staleAfterSeconds) {
            return false;
        }

        if (! is_array($state)) {
            return true;
        }

        $pid = isset($state['pid']) ? (int) $state['pid'] : 0;
        if ($pid > 0 && function_exists('posix_kill')) {
            return ! @posix_kill($pid, 0);
        }

        return true;
    }

    private function renderMaintenancePage(): void
    {
        if (! headers_sent()) {
            header('HTTP/1.1 503 Service Unavailable');
            header('Content-Type: text/html; charset=UTF-8');
            header('Retry-After: 300');
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');
            header('X-Robots-Tag: noindex, nofollow');
        }

        echo '<!doctype html><html lang="pt-BR"><head><meta charset="utf-8">';
        echo '<meta name="viewport" content="width=device-width,initial-scale=1">';
        echo '<title>Em Manutenção</title>';
        echo '<style>body{margin:0;font-family:Arial,sans-serif;background:#f6f7f9;color:#1f2937;display:flex;min-height:100vh;align-items:center;justify-content:center;text-align:center}.box{max-width:640px;padding:32px}h1{font-size:2rem;margin:0 0 12px}p{margin:0;font-size:1.1rem;line-height:1.5}</style>';
        echo '</head><body><div class="box"><h1>Em Manutenção</h1><p>Em Manutenção. tente novamente em alguns minutos</p></div></body></html>';
        exit;
    }
}
