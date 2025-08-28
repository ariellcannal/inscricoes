<?php
use CANNALInscricoes\Entities\OperadorasTransacoesEntity;

class Webhook extends SYS_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Validate the signature sent by the provider.
     *
     * @param string $payload  Raw request body
     * @param string|null $signatureHeader Header value from X-Hub-Signature
     *
     * @return bool
     */
    private function validateSignature(string $payload, ?string $signatureHeader): bool
    {
        $secret = getenv('PAGARME_WEBHOOK_SECRET');

        if (empty($secret) || empty($signatureHeader)) {
            return false;
        }

        $expected = 'sha1=' . hash_hmac('sha1', $payload, $secret);

        return hash_equals($expected, $signatureHeader);
    }

    function pagarme($wh = null)
    {
        $payload = $this->input->raw_input_stream;
        $headers = $this->input->request_headers(TRUE);

        if (! $this->validateSignature($payload, $headers['X-Hub-Signature'] ?? null)) {
            set_status_header(401);
            return;
        }

        $post = $this->input->post(NULL, TRUE);
        $get  = $this->input->get(NULL, TRUE);

        if (! $wh) {
            $this->logs->setLogDir('Webhook/pagarme');
            // $this->logs->setLogName('webhook_' . date('Y-m-d_H:i:s'));
            $this->logs->write('DEBUG', 'HEADERS' . PHP_EOL . print_r($headers, true));
            $this->logs->write('DEBUG', 'POST' . PHP_EOL . print_r($post, true));
            $this->logs->write('DEBUG', 'GET' . PHP_EOL . print_r($get, true));
            $this->logs->write('DEBUG', 'INPUT' . PHP_EOL . $payload);
            $wh = json_decode($payload, true);
        }

        if (! empty($wh['data'])) {
            $this->load->model('operadoras_model');
            $this->load->model('recebiveis_model');
            $this->load->model('inscricoes_model');
            $this->load->model('alunos_model');
            $this->load->model('grupos_model');

            if (strpos($wh['type'], 'charge') !== null) {
                if (! $transacoes = $this->operadoras_model->getTransacaoPorOperadoraId($wh['data']['id'])) {
                    return set_status_header(400,'Transação não localizada');
                } else {
                    $this->load->library('controllers/TransacoesLib', null, 'transacoes');
                    $this->load->library('controllers/InscricoesLib', null, 'inscricoes');
                    foreach ($transacoes as $otr) {
                        $transacao = new OperadorasTransacoesEntity($otr);
                        if (! $this->transacoes->sincronizar($transacao->getId())) {
                            return set_status_header(400,'Transação não localizada');
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
                    return set_status_header(400,'Recebível não localizado');
                } else {
                    $this->load->library('controllers/RecebiveisLib', null, 'recebiveis');
                    foreach ($recebiveis as $rec) {
                        $this->recebiveis->sincronizar($rec['rec_id']);
                        $this->inscricoes_model->setTotaisInscricao($rec['rec_inscricao']);
                    }
                }
            }
        }
        return set_status_header(200,'OK');
    }
}