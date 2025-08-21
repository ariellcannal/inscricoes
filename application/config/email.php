<?php
$config['protocol'] = 'smtp';

$config['smtp_host'] = 'email-smtp.sa-east-1.amazonaws.com';
$config['smtp_user'] = 'AKIA2IS6ERFO66JBTTY5';
$config['smtp_pass'] = 'BOYMm2xrlmGKRZvFvZmuh52JrZjW9m+dgRLwEmSdhIZv';

$config['smtp_port'] = 587;
$config['smtp_crypto'] = 'tls';
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