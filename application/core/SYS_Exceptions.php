<?php
/**
 * Custom exception handler.
 *
 * @package    CannalInscricoes
 */
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

/**
 * Class SYS_Exceptions
 *
 * Manipula exceptions geradas pela aplicação.
 */
class SYS_Exceptions extends CI_Exceptions
{

    /**
     * Instância do logger de exceptions.
     *
     * @var Logger
     */
    private Logger $logger;

    /**
     * Diretório e arquivo dos logs de exceptions.
     *
     * @var string
     */
    private string $logFileName;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        parent::__construct();
        $this->logFileName = 'exceptions/' . date('Y.m.d') . '.log';
        $this->logger = new Logger('exceptions', [
            new StreamHandler(APPPATH . 'logs/' . $this->logFileName, Level::Debug)
        ]);
    }

    /**
     * Registra detalhes da exception e envia e-mail em produção.
     *
     * @param mixed $severity
     *            Nível do erro.
     * @param string $message
     *            Mensagem da exception.
     * @param string $filepath
     *            Arquivo onde ocorreu.
     * @param int $line
     *            Linha do arquivo.
     *            
     * @return void
     */
    public function log_exception($severity, $message, $filepath, $line): void
    {
        $type = is_int($severity) && isset($this->levels[$severity]) ? $this->levels[$severity] : (string) $severity;
        $vars = $GLOBALS;
        unset($vars['GLOBALS']);
        $data = [
            'type' => $type,
            'code' => $severity,
            'message' => $message,
            'backtrace' => debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT),
            'input' => file_get_contents('php://input'),
            'variables' => $vars,
            '_SERVER' => $_SERVER
        ];
        switch ($type) {
            case strstr(strtolower($type), 'error'):
                $this->logger->error($message);
                break;
            case strstr(strtolower($type), 'warning'):
                $this->logger->warning($message);
                break;
            case strstr(strtolower($type), 'notice'):
                $this->logger->notice($message);
                break;
            default:
                $this->logger->info($message);
                break;
        }

        if (ENVIRONMENT === 'production') {
            $this->sendExceptionMail($type, $this->logFileName);
        }
    }

    /**
     * Envia e-mail informando sobre a exception.
     *
     * @param string $type
     *            Tipo do erro.
     * @param string $logPath
     *            Caminho do log.
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
        $body = $CI->load->view('emails/_layout', [
            'conteudo' => $conteudo
        ], true);

        $CI->mail->clearAllRecipients();
        $CI->mail->Subject = '[' . $type . '] CANNAL Inscrições';
        $CI->mail->Body = $body;
        $CI->mail->addAddress(config_item('email_dev'));
        $CI->mail->send();
    }
}
