<?php
$envFile = __DIR__ . '/.env';
if (is_readable($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if ($line[0] === '#')
            continue;
        [
            $k,
            $v
        ] = array_map('trim', explode('=', $line, 2));
        if ($k !== '') {
            putenv("$k=$v");
            $_ENV[$k] = $v;
            $_SERVER[$k] = $v;
        }
    }
}

error_reporting(E_ALL);
ini_set('display_errors', 'On');

$secret = getenv('GITHUB_WEBHOOK_SECRET') ?: '';
$log = __DIR__ . '/application/logs/deploy.log'; // gitignore!
$cmd = __DIR__ . '/deploy.sh';

// ===== Regras básicas =====
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$event = $_SERVER['HTTP_X_GITHUB_EVENT'] ?? '';

if (! $secret || ! $signature) {
    http_response_code(403);
    error_log(date('[Y-m-d H:i:s] ') . "Faltando secret/assinatura\n", 3, $log);
    exit('Forbidden');
}

// ===== Validação HMAC =====
$calc = 'sha256=' . hash_hmac('sha256', $payload, $secret);
if (! hash_equals($calc, $signature)) {
    http_response_code(403);
    error_log(date('[Y-m-d H:i:s] ') . "Assinatura inválida\n", 3, $log);
    exit('Invalid signature');
}

// ===== Valida evento e branch =====
$data = json_decode($payload, true);
$ref = $data['ref'] ?? '';
if ($event !== 'push' || $ref !== 'refs/heads/master') {
    http_response_code(200);
    exit('Ignored');
}

// ===== Lock para evitar concorrência =====
$lockFile = __DIR__ . '/deploy.lock'; // gitignore!
$lock = fopen($lockFile, 'c');
if (! flock($lock, LOCK_EX | LOCK_NB)) {
    http_response_code(202);
    echo 'Deploy em andamento';
    exit();
}

// ===== Dispara em background =====
if (! is_executable($cmd)) {
    @chmod($cmd, 0755);
}
$logDir = dirname($log);
if (! is_dir($logDir)) {
    @mkdir($logDir, 0775, true);
}
exec('bash ' . escapeshellarg($cmd) . ' >> ' . escapeshellarg($log) . ' 2>&1 &');

http_response_code(200);
echo 'OK';
