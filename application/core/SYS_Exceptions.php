<?php
/**
 * Custom exception handler.
 *
 * @package    CannalInscricoes
 */

use CANNALLogs\Logs;

/**
 * Class SYS_Exceptions
 *
 * Manipula exceptions geradas pela aplicação.
 */
class SYS_Exceptions extends CI_Exceptions
{
    /**
     * Objeto de log.
     *
     * @var Logs|null
     */
    private ?Logs $logger = null;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        parent::__construct();
        $this->logger = new Logs('exceptions');
    }

    /**
     * Registra detalhes da exception e envia e-mail em produção.
     *
     * @param mixed  $severity Nível do erro.
     * @param string $message  Mensagem da exception.
     * @param string $filepath Arquivo onde ocorreu.
     * @param int    $line     Linha do arquivo.
     *
     * @return void
     */
    public function log_exception($severity, $message, $filepath, $line): void
    {
        $type = is_int($severity) && isset($this->levels[$severity]) ? $this->levels[$severity] : (string) $severity;
        $timestamp = date('Y.m.d-H:i:s');
        $fileName = $timestamp . '-' . preg_replace('/[^A-Za-z0-9-_.]/', '_', $message) . '.log';
        $this->logger->setLogName($fileName);

        $vars = $GLOBALS;
        unset($vars['GLOBALS']);
        $data = [
            'type'      => $type,
            'code'      => $severity,
            'message'   => $message,
            'file'      => $filepath,
            'line'      => $line,
            'backtrace' => debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT),
            'variables' => $vars,
            '_SERVER'   => $_SERVER,
            '_ENV'      => $_ENV,
            'input'     => file_get_contents('php://input'),
        ];
        $this->logger->write('ERROR', json_encode($data, JSON_PRETTY_PRINT | JSON_PARTIAL_OUTPUT_ON_ERROR));

        if (ENVIRONMENT === 'production') {
            $this->sendExceptionMail($type, 'exceptions/' . $fileName);
        }
    }

    /**
     * Envia e-mail informando sobre a exception.
     *
     * @param string $type    Tipo do erro.
     * @param string $logPath Caminho do log.
     *
     * @return void
     */
    private function sendExceptionMail(string $type, string $logPath): void
    {
        $CI = &get_instance();
        $CI->load->helper('url');

        $link = site_url('config/show_log/' . $logPath);
        $conteudo = 'O ambiente de produção gerou uma exception que foi registrada em log.<br>';
        $conteudo .= '<a href="' . $link . '" style="display:inline-block;padding:10px 15px;background:#0d6efd;color:#fff;text-decoration:none;">Ver log</a>';
        $body = $CI->load->view('emails/_layout', ['conteudo' => $conteudo], true);

        $CI->mail->clearAllRecipients();
        $CI->mail->Subject = '[' . $type . '] CANNAL Inscrições';
        $CI->mail->Body = $body;
        $CI->mail->addAddress(config_item('email_dev'));
        $CI->mail->send();
    }
}
