<?php
use CANNALLogs\Logs;
use PHPMailer\PHPMailer\PHPMailer;

defined('DIR_IMAGEM_USUARIOS') or define('DIR_IMAGEM_USUARIOS',  '/writable/usuarios/');
defined('DIR_IMAGEM_ALUNOS') or define('DIR_IMAGEM_ALUNOS',  '/writable/alunos/');
defined('DIR_IMAGEM_GRUPOS') or define('DIR_IMAGEM_GRUPOS',  '/writable/grupos/');
defined('URI_INSCRICOES') or define('URI_INSCRICOES', '/inscricao/');

class SYS_Controller extends CI_Controller
{

    public ?array $vars = [];

    function __construct()
    {
        parent::__construct();
        global $html_errors;
        $this->load->library('session');
        date_default_timezone_set(config_item('time_reference'));

        $this->load->database();

        $this->vars['menu'] = [
            'transacoes' => 'Transações',
            'recebiveis' => 'Recebíveis',
            'inscricoes' => 'Inscrições',
            'repasses' => 'Repasses',
            'grupos' => 'Grupos',
            'alunos' => 'Alunos',
            'presenca' => 'Presença',
            'usuarios' => 'Usuários',
            'config' => 'Config'
        ];

        if ($this->session->userdata('usr_id') && array_key_exists($this->uri->segment(1), $this->vars['menu'])) {
            $this->usuarios_model->setPreferencia($this->session->userdata('usr_id'), 'ultima_aba', $this->uri->segment(1));
        }

        if (! is_cli()) {
            $this->load->helper('xcrud');
            $this->updateUserSession();
            $this->setAssets();
        }

        if (ENVIRONMENT == 'development') {
            define('FORCE_OPERADORA_PRODUCTION', false);
        }

        if ($this->input->is_ajax_request()) {
            $html_errors = false;
        } else {
            $html_errors = true;
        }

        $this->logs = new Logs();
    }

    public function initMail()
    {
        $this->load->config('email');
        $this->mail = new class() extends PHPMailer {

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

    function checkLogin()
    {
        if (! $this->session->userdata('usr_id')) {
            if ($this->input->is_ajax_request()) {
                exit('<script>window.location="/sair"</script>');
            } else {
                redirect('/sair');
            }
        }
    }

    function updateUserSession()
    {
        if ($this->session->userdata('usr_id')) {
            $dados = $this->usuarios_model->getUsuario($this->session->userdata('usr_id'));
            $this->session->set_userdata($dados);
        }
    }

    function setAssets()
    {
        $this->assets->inline('environment', ENVIRONMENT);

        /*
         * BOOTSTRAP
         */
        $this->assets->css('https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', true);
        $this->assets->css('bootstrap.min.css');
        $this->assets->js('https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', true);
        $this->assets->js('https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js', true);

        /*
         * JQUERY
         */
        $this->assets->js('https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js', true);

        /*
         * SELECT2
         */
        $this->assets->css('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', true);
        $this->assets->js('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', true);
        $this->assets->css('/assets/css/themes/select2.css', true);

        /*
         * DATETIME
         */
        $this->assets->css('../../assets/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.css');
        $this->assets->js('../../assets/plugins/moment/moment.min.js');
        $this->assets->js('../../assets/plugins/moment/locales.min.js');
        $this->assets->js('../../assets/plugins/_language/' . $this->config->item('language') . '/moment.js');
        $this->assets->js('../../assets/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js');
        $this->assets->js('../../assets/plugins/_language/' . $this->config->item('language') . '/bootstrap-datetimepicker.js');

        /*
         * MASKED
         */
        $this->assets->js('https://cdn.jsdelivr.net/npm/jquery-mask-plugin@1.14.16/dist/jquery.mask.min.js', true);

        /*
         * ALERTIFY
         */
        $this->assets->css('//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/alertify.min.css', true);
        $this->assets->css('//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/themes/bootstrap.min.css', true);
        $this->assets->js('//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js', true);

        /*
         * SUMOSELECT
         */
        $this->assets->css('../../assets/plugins/sumo-select/sumoselect.min.css');
        $this->assets->js('../../assets/plugins/sumo-select/jquery.sumoselect.min.js');

        /*
         * Chart.js
         */
        $this->assets->js('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js', true);

        /*
         * FONTES
         */
        $this->assets->css('../fonts/font-awesome/css/all.css');

        /*
         * XCRUD
         */
        $this->assets->css('xcrud.css');

        /*
         * reCAPTCHA
         */
        if (config_item('recaptcha_key') && ENVIRONMENT == 'production') {
            $this->assets->js('https://www.google.com/recaptcha/enterprise.js?render=' . config_item('recaptcha_key'), true);
        }

        $this->assets->css('app.css');
        $this->assets->js('meta.js');
        $this->assets->js('app.js');
    }
}

/* End of file SYS_Controller.php */
/* Location: ./applicaion/core/SYS_Controller.php */