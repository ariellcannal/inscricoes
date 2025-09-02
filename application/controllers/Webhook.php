<?php
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use CANNALInscricoes\Entities\OperadorasTransacoesEntity;

class Webhook extends SYS_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Verifica usuário e senha enviados via HTTP Basic Auth.
     *
     * @return bool
     */
    private function isAuthorized(): bool
    {
        $expectedUser = getenv('PAGARME_WEBHOOK_USER') ?: '';
        $expectedPass = getenv('PAGARME_WEBHOOK_PASS') ?: '';
        $user = $this->input->server('PHP_AUTH_USER') ?? '';
        $pass = $this->input->server('PHP_AUTH_PW') ?? '';

        return $expectedUser !== '' && $expectedPass !== ''
            && hash_equals($expectedUser, $user)
            && hash_equals($expectedPass, $pass);
    }

    /**
     * Processa notificações do Pagar.me.
     *
     * @param array|null $wh Dados de webhook para testes.
     * @return void
     */
    public function pagarme(?array $wh = null): void
    {
        $payload = $this->input->raw_input_stream;
        $headers = $this->input->request_headers(TRUE);

        $post = $this->input->post(NULL, TRUE);
        $get  = $this->input->get(NULL, TRUE);

        if (! $wh) {
            $logDir = APPPATH . 'logs/Webhook/pagarme/';
            if (! is_dir($logDir)) {
                mkdir($logDir, 0777, true);
            }
            $logFile = $logDir . 'webhook_' . date('Y-m-d_H:i:s') . '.log';
            $logger = new Logger('webhook');
            $handler = new StreamHandler($logFile, Level::Debug);
            $handler->setFormatter(new LineFormatter(null, null, true, true));
            $logger->pushHandler($handler);
            $logger->debug('HEADERS' . PHP_EOL . print_r($headers, true));
            $logger->debug('POST' . PHP_EOL . print_r($post, true));
            $logger->debug('GET' . PHP_EOL . print_r($get, true));
            $logger->debug('INPUT' . PHP_EOL . $payload);
            $wh = json_decode($payload, true);
        }
        
        if (! $this->isAuthorized()) {
            set_status_header(401, lang('error_credenciais_invalidas'));
            return;
        }

        if (! empty($wh['data'])) {
            $this->load->model('operadoras_model');
            $this->load->model('recebiveis_model');
            $this->load->model('inscricoes_model');
            $this->load->model('alunos_model');
            $this->load->model('grupos_model');

            if (strpos($wh['type'], 'charge') !== null) {
                if (! $transacoes = $this->operadoras_model->getTransacaoPorOperadoraId($wh['data']['id'])) {
                    set_status_header(400, lang('error_transacao_nao_localizada'));
                    return;
                } else {
                    $this->load->library('controllers/TransacoesLib', null, 'transacoes');
                    $this->load->library('controllers/InscricoesLib', null, 'inscricoes');
                    foreach ($transacoes as $otr) {
                        $transacao = new OperadorasTransacoesEntity($otr);
                        if (! $this->transacoes->sincronizar($transacao->getId())) {
                            set_status_header(400, lang('error_transacao_nao_localizada'));
                            return;
                        }
                        if ($wh['type'] == 'charge.paid') {
                            $this->inscricoes->email_inscricao($transacao->getInscricao(), 'pagamento_confirmado', $transacao);
                        }
                    }
                }
            } else if ($wh['type'] == 'order.canceled') {
                $wh['type'] = 'charge.canceled';
                $wh['data'] = $wh['data']['charges'][0];
                $this->pagarme($wh);
            } else if ($wh['type'] == 'payable.paid') {
                // RECEBIVEL RECEBIDO
                if (! $recebiveis = $this->recebiveis_model->getRecebiveisPorOperadoraId($wh['data']['id']) && ! $recebiveis = $this->recebiveis_model->getRecebiveisPorOperadoraId($wh['data']['gateway_id'])) {
                    set_status_header(400, lang('error_recebivel_nao_localizado'));
                    return;
                } else {
                    $this->load->library('controllers/RecebiveisLib', null, 'recebiveis');
                    foreach ($recebiveis as $rec) {
                        $this->recebiveis->sincronizar($rec['rec_id']);
                        $this->inscricoes_model->setTotaisInscricao($rec['rec_inscricao']);
                    }
                }
            }
        }
        set_status_header(200, lang('general_ok'));
        return;
    }
}
