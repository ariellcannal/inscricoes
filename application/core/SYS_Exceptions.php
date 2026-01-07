<?php
/**
 * Custom exception handler.
 *
 * @package    CannalInscricoes
 */
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use PHPMailer\PHPMailer\PHPMailer;

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
        $this->logFileName = 'exceptions/' . date('Y.m.d_Hi') . '.log';
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
                $this->logger->error(print_r($data,true));
                break;
            case strstr(strtolower($type), 'warning'):
                $this->logger->warning(print_r($data,true));
                break;
            case strstr(strtolower($type), 'notice'):
                $this->logger->notice(print_r($data,true));
                break;
            default:
                $this->logger->info(print_r($data,true));
                break;
        }

        if (ENVIRONMENT === 'production') {
            //$this->sendExceptionMail($data);
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
        $this->initMail();
        $CI = &get_instance();
        $CI->load->helper('url');

        $body = $CI->load->view('emails/_layout', [
            'conteudo' => 'O ambiente de produção gerou uma exception:<br/><pre>'.print_r($data,true).'</pre>'
        ], true);

        $CI->mail->clearAllRecipients();
        $CI->mail->Subject = '[Exception] CANNAL Inscrições';
        $CI->mail->Body = $body;
        $CI->mail->addAddress(config_item('email_dev'));
        $CI->mail->send();
    }
    
    public function initMail()
    {
        $CI = &get_instance();
        $CI->load->helper('url');
        $CI->load->config('email');
        $CI->mail = new class() extends PHPMailer {
            
            var $default_from = '';
            
            var $default_from_name = '';
            
            var $email_dev = '';
            
            public function __construct()
            {
                parent::__construct();
                if (config_item('from')) {
                    $this->default_from = config_item('from');
                }
                if (config_item('from_name')) {
                    $this->default_from_name = config_item('from_name');
                }
                $this->setFrom($this->default_from, $this->default_from_name);
                
                $this->email_dev = config_item('email_dev');
                
                $this->setLanguage(config_item('language'));
                $this->CharSet = $this::CHARSET_UTF8;
                $this->SMTPDebug = 0;
                $this->isSMTP();
                $this->Host = config_item('smtp_host');
                $this->SMTPAuth = true;
                $this->Username = config_item('smtp_user');
                $this->Password = config_item('smtp_pass');
                $this->SMTPSecure = config_item('smtp_crypto');
                $this->Port = config_item('smtp_port');
                
                if (ENVIRONMENT == 'development') {
                    $this->SMTPOptions = array(
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    );
                    $this->SMTPDebug = 0;
                }
            }
            
            private function recipientFilter($emails)
            {
                if (ENVIRONMENT != "production" && $this->email_dev !== false) {
                    return $this->email_dev;
                }
                return $emails;
            }
            
            public function addAddress($to, $name = '')
            {
                $to = $this->recipientFilter($to);
                if (is_array($to)) {
                    foreach ($to as $val) {
                        parent::addAddress($val, $name);
                    }
                } else {
                    parent::addAddress($to, $name);
                }
                
                return $this;
            }
            
            public function addCC($cc, $name = '')
            {
                $cc = $this->recipientFilter($cc);
                if (is_array($cc)) {
                    foreach ($cc as $val) {
                        parent::addCC($val, $name);
                    }
                } else {
                    parent::addCC($cc, $name);
                }
                
                return $this;
            }
            
            public function addBCC($bcc, $name = '')
            {
                $bcc = $this->recipientFilter($bcc);
                if (is_array($bcc)) {
                    foreach ($bcc as $val) {
                        parent::addCC($val, $name);
                    }
                } else {
                    parent::addCC($bcc, $name);
                }
                
                return $this;
            }
            
            public function subject($subject)
            {
                $this->Subject = $subject;
                return $this;
            }
            
            public function message($conteudo)
            {
                ob_start();
                require APPPATH . 'views' . DIRECTORY_SEPARATOR . 'emails' . DIRECTORY_SEPARATOR . '_layout.php';
                $body = ob_get_clean();
                $this->isHTML(true);
                $this->Body = $body;
                return $this;
            }
            
            public function attach($file, $disposition = '', $newname = NULL, $mime = '')
            {
                if (is_file($file) && $fp = @fopen($file, 'rb')) {
                    $file_content = stream_get_contents($fp);
                    if (is_null($newname)) {
                        $newname = pathinfo($file, PATHINFO_BASENAME);
                    }
                    $mime = $this->_mime_types(pathinfo($file, PATHINFO_EXTENSION));
                    fclose($fp);
                } else {
                    $file_content = &$file; // buffered file
                    if (is_null($newname)) {
                        $newname = "Attachment.tmp";
                    }
                }
                $this->addStringAttachment($file_content, $newname, 'base64', $mime);
                
                return $this;
            }
        };
    }
}
