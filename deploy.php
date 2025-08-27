<?php
$envFile = '.env';
if (is_readable($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if ($line[0] === '#')
            continue;
        [
            $k,
            $v
        ] = array_map('trim', explode('=', $line, 2));
        $_ENV[$k] = $_SERVER[$k] = $v;
        putenv("$k=$v");
    }
}

$secret = getenv('GITHUB_WEBHOOK_SECRET') ?: '';
$log = 'application/logs/deploy.log';
$cmd = 'deploy.sh';

// ====== Validação da assinatura ======
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$calc = 'sha256=' . hash_hmac('sha256', $payload, $secret);

if (! $secret || ! $signature || ! hash_equals($calc, $signature)) {
    http_response_code(403);
    error_log(date('[Y-m-d H:i:s] ') . "Assinatura inválida\n", 3, $log);
    exit('Invalid signature');
}

// ====== Filtro de evento/branch ======
$event = $_SERVER['HTTP_X_GITHUB_EVENT'] ?? '';
$data = json_decode($payload, true);
$ref = $data['ref'] ?? '';
if ($event !== 'push' || ! preg_match('#^refs/heads/main$#', $ref)) {
    http_response_code(200);
    exit('Ignored');
}

// ====== Lock para evitar concorrência ======
$lockFile = 'webhook.lock';
$lock = fopen($lockFile, 'c');
if (! flock($lock, LOCK_EX | LOCK_NB)) {
    http_response_code(202);
    echo "Deploy já em andamento";
    exit();
}

// ====== Dispara o deploy em background ======
exec("bash " . escapeshellarg($cmd) . " >> " . escapeshellarg($log) . " 2>&1 &");

http_response_code(200);
echo "OK";
