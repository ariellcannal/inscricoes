<?php
use CANNALInscricoes\Entities\RecebiveisEntity;
use CANNALInscricoes\Entities\OperadorasEntity;
use CANNALInscricoes\Entities\OperadorasTransacoesEntity;
use CANNALPagamentos\Entities\Transacao;

class TransacoesLib
{

    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    public function sincronizar(array|int|null $otr_id = null, int $dias = 7, bool $echo = true): int|null|false
    {
        $transacoes_atualizadas = 0;
        $recebiveis_atualizados = 0;
        $transacoes = [];
        $transacao = null;

        $this->CI->load->model('recebiveis_model');
        $this->CI->load->model('operadoras_model');
        $this->CI->load->model('inscricoes_model');

        if (is_array($otr_id)) {
            foreach ($otr_id as $otr) {
                if(is_array($id)){
                    $transacoes[] = $otr;
                }
                else if (is_int($id) && $otr = $this->CI->operadoras_model->getTransacao($otr)) {
                    $transacoes[] = $otr;
                }
            }
        } else if (is_int($otr_id) && $otr = $this->CI->operadoras_model->getTransacao($otr_id)) {
            $transacoes[] = $otr;
        } else if (is_int($otr_id)) {
            return false;
        } else if ($this->CI->input->post('ins_id')) {
            $transacoes = $this->CI->operadoras_model->getTransacoesPorInscricao($this->CI->input->post('ins_id'));
        } else if ($this->CI->input->post('dias')) {
            $transacoes = $this->CI->operadoras_model->getTransacoesRecentes($this->CI->input->post('dias'));
        } else {
            $transacoes = $this->CI->operadoras_model->getTransacoesRecentes($dias);
        }
        if (! count($transacoes)) {
            return null;
        }
        foreach ($transacoes as $otr) {
            $transacao = new OperadorasTransacoesEntity($otr);
            $valorLiquido = 0;

            $operadora = new OperadorasEntity($this->CI->operadoras_model->getRow($transacao->getOperadora()));
            $class = ucfirst($operadora->getInterface());
            $class = "CANNALPagamentos\\Interfaces\\" . $class;
            if (ENVIRONMENT == 'production' || FORCE_OPERADORA_PRODUCTION === TRUE) {
                $interface = new $class($operadora->getProductionKey(), $operadora->getNome());
            } else {
                $interface = new $class($operadora->getDevelopmentKey(), $operadora->getNome());
            }

            $transacao_operadora = $interface->getCharge($transacao->getOperadoraId());
            if ($transacao_operadora instanceof Transacao) {
                $transacao_operadora = new OperadorasTransacoesEntity($transacao_operadora->toArray(true));
                $transacao->import($transacao_operadora);
                $transacao->setForma($this->selectForma($transacao));

                // busca recebíveis para checar o valor líquido da transação
                $recebiveis = $interface->getReceivables($transacao->getOperadoraID());
                if ($recebiveis) {
                    foreach ($recebiveis as $recebivel) {
                        $recebivel = new RecebiveisEntity($recebivel->toArray(true));
                        $valorLiquido += $recebivel->getValorLiquido();

                        $rec = $this->CI->recebiveis_model->getRecebiveisPorTransacao($transacao->getId(), $recebivel->getParcela());
                        if ($rec) {
                            // já existe, atualiza.
                            unset($rec[0]['rec_valor'], $rec[0]['rec_valorLiquido']);
                            $recebivel->importArray(array_merge($rec[0], $recebivel->toArray(false)));
                            $recebivel->setInscricao($transacao->getInscricao());
                            $recebivel->setTransacao($transacao->getId());
                            $recebivel->setDataTransacao($transacao->getDataTransacao());
                            $r = $this->CI->recebiveis_model->update($recebivel->getId(), $recebivel->toArray());
                            $recebiveis_atualizados += $r;
                        } else {
                            // não existe, cria
                            $recebivel->setInscricao($transacao->getInscricao());
                            $recebivel->setForma($transacao->getForma());
                            $recebivel->setOperadora($transacao->getOperadora());
                            $recebivel->setTransacao($transacao->getId());
                            $recebivel->setDataTransacao($transacao->getDataTransacao());
                            $r = $this->CI->recebiveis_model->incluirRecebivel($recebivel->toArray(false));
                            if ($r) {
                                $recebivel->setId($r);
                                $recebiveis_atualizados ++;
                            }
                        }
                        if (in_array($recebivel->getOperadoraStatus(), [
                            'paid'
                        ])) {
                            $this->CI->recebiveis_model->confirmar($recebivel->getId(), $recebivel->getValorLiquido(), $recebivel->getDataRecebimento());
                        } else {
                            $this->CI->recebiveis_model->desconfirmar($recebivel->getId());
                        }
                    }
                }
                $transacao->setValorLiquido($valorLiquido);
                $r = $this->CI->operadoras_model->updateTransacao($transacao->getId(), $transacao->toArray(false));

                $this->CI->inscricoes_model->setTotaisInscricao($transacao->getInscricao());

                $transacoes_atualizadas += $r;
            }
        }
        if ($transacoes_atualizadas || $recebiveis_atualizados) {
            $t_atualizadas = $transacoes_atualizadas . ' transaç' . ($transacoes_atualizadas > 1 ? 'ões' : 'ão') . ' atualizada' . ($transacoes_atualizadas > 1 ? 's' : '');
            $r_atualizados = $recebiveis_atualizados . ' recebíve' . ($recebiveis_atualizados > 1 ? 'is' : 'l') . ' atualizado' . ($recebiveis_atualizados > 1 ? 's' : '');
            if ($echo) {
                echo $t_atualizadas . PHP_EOL . $r_atualizados;
            }
        } else if ($echo) {
            echo 'Sem atualizações pendentes';
        }
        return $transacoes_atualizadas;
    }

    public function sincronizaTransacoesVencidas()
    {
        $recebiveis_removidos = 0;
        $this->CI->load->model('recebiveis_model');
        $this->CI->load->model('operadoras_model');

        $transacoes = $this->CI->operadoras_model->getTransacoesVencidas();
        if ($transacoes) {
            foreach ($transacoes as $otr) {
                $transacao = new OperadorasTransacoesEntity($otr);
                $this->sincronizar($transacao->getId());
                $transacao = new OperadorasTransacoesEntity($this->CI->operadoras_model->getTransacao($transacao->getId()));
                if (! $transacao->getConfirmada()) {
                    $r = $this->CI->recebiveis_model->removerFromTransacao($transacao->getId());
                    $recebiveis_removidos += $r;
                }
            }
            if ($recebiveis_removidos) {
                $recebiveis_removidos = $recebiveis_removidos . ' recebível' . ($recebiveis_removidos > 1 ? 's' : '') . ' atualizado' . ($recebiveis_removidos > 1 ? 's' : '');
                echo $recebiveis_removidos;
            }
            return true;
        }
        return false;
    }

    public function estornar()
    {
        if ($this->CI->input->post('otr_id') && $this->CI->input->post('otr_valorCancelamento') && $this->CI->input->is_ajax_request()) {
            $this->CI->load->model('operadoras_model');
            $this->CI->load->model('inscricoes_model');
            $this->CI->load->helper('inscricoes_helper');
            $this->CI->load->helper('recebiveis_helper');
            $otr = $this->CI->operadoras_model->getTransacao($this->CI->input->post('otr_id'));
            $opr = $this->CI->operadoras_model->getRow($otr['otr_operadora']);
            $ins = $this->CI->inscricoes_model->getInscricaoCompleta($otr['otr_inscricao']);

            $this->CI->logs->setLogName('ALU_' . $ins['alu_id'] . '_' . time(), true);
            $this->CI->logs->write('INFO', 'ESTORNO Valor: ' . $this->CI->input->post('otr_valorCancelamento'));

            $valor = (float) $this->valorLiquidoCancelamento($otr, str_replace(',', '.', str_replace('.', '', str_replace('R$ ', '', $this->CI->input->post('otr_valorCancelamento')))));

            if (ENVIRONMENT == 'production') {
                if ($otr['otr_confirmada'] == 0) {
                    return set_status_header(400,'Não é possível estornar essa transação.');
                }
                if (round($valor, 2) <= 0) {
                    return set_status_header(400,'Informe o valor corretamente');
                }
                if (round($otr['otr_valorBruto'], 2) < round($valor, 2)) {
                    return set_status_header(400,'O valor informado é maior que a transação.');
                }
                if ($otr['otr_operadoraID'] == "") {
                    return set_status_header(400,'Transação não identificada.');
                }
            }

            $this->CI->logs->write('INFO', 'Vai estornar...');

            $opr = new OperadorasEntity($opr);
            $class = "CANNALPagamentos\\Interfaces\\" . ucfirst($opr->getInterface());
            if (ENVIRONMENT == 'production') {
                $interface = new $class($opr->getProductionKey(), $opr->getNome());
            } else {
                $interface = new $class($opr->getDevelopmentKey(), $opr->getNome());
            }

            $transacao_operadora = $interface->refund($otr['otr_operadoraID'], $valor);
            if ($transacao_operadora instanceof Transacao) {
                $transacao_operadora = new OperadorasTransacoesEntity($transacao_operadora->toArray(true));
                $transacao = new OperadorasTransacoesEntity($otr);
                $transacao->import($transacao_operadora);

                $this->CI->operadoras_model->registraEstorno($this->CI->input->post('otr_id'), $transacao->toArray(), $valor);
                $this->sincronizar($this->CI->input->post('otr_id'));
                $this->CI->load->library('controllers/InscricoesLib', null, 'inscricoes');
                $this->CI->inscricoes->email_inscricao($otr['otr_inscricao'], 'confirmar_estorno', $transacao);
            } else {
                return set_status_header(400,'Não foi possível estornar esse pagamento ' . $this->CI->logs->getLogName());
            }
        } else {
            show_404();
        }
    }

    private function valorLiquidoCancelamento($otr, $valorCancelamento)
    {
        return $valorCancelamento;

        $this->CI->load->model('operadora_transacoes');
        $otr = $this->CI->operadora_transacoes->getTransacao($otr['otr_id']);
        $ofo = $this->CI->operadora_transacoes->getForma($otr['otr_forma']);

        /* Demos descontar as taxas, do cliente? */
        if ((floor(time() - strtotime($otr['otr_dataTransacao']) / (60 * 60 * 24))) > (int) $ofo['ofo_prazoEstornoTaxa']) {
            $this->load->library('controllers/InscricoesLib', null, 'inscricoes');
            return $this->valorLiquido($valorCancelamento, $ofo);
        }
        return $valorCancelamento;
    }

    public function selectForma(OperadorasTransacoesEntity $transacao)
    {
        $faixas_ofo = [];
        if ($transacao->getTipo() == 'pix') {
            $ofo = $this->CI->operadoras_model->getFormas($transacao->getOperadora(), 'PIX');
        } else {
            $ofo = $this->CI->operadoras_model->getFormas($transacao->getOperadora(), explode(' final ', $transacao->getCartao())[0]);
        }
        foreach ($ofo as $row) {
            if ($row['ofo_parcelamento'] == '0') {
                // PIX
                $faixas_ofo[$row['ofo_forma']] = [
                    'min' => 1,
                    'max' => 1
                ];
            } else {
                // Cartão de Crédito
                if ($row['ofo_taxaParcelamento712'] != "") {
                    $parc_min = 7;
                }
                if ($row['ofo_taxaParcelamento46'] != "") {
                    $parc_min = 4;
                }
                if ($row['ofo_taxaParcelamento23'] != "") {
                    $parc_min = 2;
                }
                if ($row['ofo_taxaParcelamento23'] != "") {
                    $parc_max = 3;
                }
                if ($row['ofo_taxaParcelamento46'] != "") {
                    $parc_max = 6;
                }
                if ($row['ofo_taxaParcelamento712'] != "") {
                    $parc_max = 12;
                }
                $faixas_ofo[$row['ofo_forma']] = [
                    'min' => $parc_min,
                    'max' => $parc_max
                ];
            }
        }
        foreach ($faixas_ofo as $ofo_forma => $faixa) {
            if ($transacao->getParcelas() >= $faixa['min'] && $transacao->getParcelas() <= $faixa['max']) {
                break;
            }
        }
        return $ofo_forma;
    }

    public function cancelar()
    {}
}