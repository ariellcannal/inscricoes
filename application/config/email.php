<?php
$config['protocol'] = 'smtp';

$config['smtp_host'] = getenv('smtp.host');
$config['smtp_user'] = getenv('smtp.user');
$config['smtp_pass'] = getenv('smtp.pass');

$config['smtp_port'] = getenv('smtp.port');
$config['smtp_crypto'] = getenv('smtp.crypto');

$config['smtp_keepalive'] = true;

$config['mailtype'] = 'html';
$config['newline'] = PHP_EOL;
$config['crlf'] = PHP_EOL;
$config['charset'] = 'utf-8';
$config['wordwrap'] = FALSE;

$config['mailpath'] = $_SERVER['DOCUMENT_ROOT'].'/vendor/phpmailer/phpmailer/src/';

$config['useragent'] = 'Cannal Produções';

$config['from'] = 'contato@cannal.com.br';
$config['from_name'] = 'CANNAL Produções';

$config['validate'] = false;

$config['email_dev'] = 'ariell@cannal.com.br';
?>