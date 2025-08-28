<?php
use CANNALInscricoes\Entities\RecebiveisEntity;
use CANNALInscricoes\Entities\OperadorasEntity;

class RecebiveisLib
{

    private $CI;

    function __construct()
    {
        $this->CI = &get_instance();
    }

    function confirmar($rec_id = null, $valorLiquido = null, $dataRecebimento = null)
    {
        $rec_id = $this->CI->input->post('rec_id') ? $this->CI->input->post('rec_id') : $rec_id;
        if ($rec_id) {
            $this->CI->load->model('recebiveis_model');
            $ret = $this->CI->recebiveis_model->confirmar($rec_id, $valorLiquido, $dataRecebimento);
            if ($ret['status'] == true) {
                echo $ret['message'];
            } else {
                return set_status_header(400, $ret['message']);
            }
        } else {
            return set_status_header(400,'Recebível não identificado');
        }
    }

    function desconfirmar()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 'On');
        if ($this->CI->input->post('rec_id') && $this->CI->input->is_ajax_request()) {
            $this->CI->load->model('recebiveis_model');
            if (! $this->CI->recebiveis_model->checkRepasse($this->CI->input->post('rec_id'))) {
                exit($this->CI->recebiveis_model->desconfirmar($this->CI->input->post('rec_id')));
            } else {
                return set_status_header(400,'Impossível desconfirmar, há repasses consolidados');
            }
        } else {
            show_404();
        }
    }

    function sincronizar($rec_id = null)
    {
        $recebiveis_atualizados = 0;
        $recebiveis = [];

        $this->CI->load->model('recebiveis_model');
        $this->CI->load->model('operadoras_model');
        $this->CI->load->model('inscricoes_model');

        if ($rec_id && $rec = $this->CI->recebiveis_model->getRow($rec_id)) {
            $recebiveis[] = $rec;
        } else if ($rec_id) {
            return set_status_header(400,'Recebível não localizado');
        } else {
            $recebiveis = $this->CI->recebiveis_model->getRecebiveisPendentes();
        }
        foreach ($recebiveis as $rec) {
            unset($rec['rec_valor'], $rec['rec_valorLiquido']);
            $charge_id = null;
            $recebivel = new RecebiveisEntity($rec);

            $otr_id = $recebivel->getTransacao();
            if ($otr_id) {
                $transacao = $this->CI->operadoras_model->getTransacao($otr_id);
                $charge_id = $transacao['otr_operadoraID'];
                $recebivel->setDataTransacao($transacao['otr_dataTransacao']);
            }

            $operadora = new OperadorasEntity($this->CI->operadoras_model->getRow($recebivel->getOperadora()));
            $class = "CANNALPagamentos\\Interfaces\\" . ucfirst($operadora->getInterface());
            if (ENVIRONMENT == 'production' || FORCE_OPERADORA_PRODUCTION === TRUE) {
                $interface = new $class($operadora->getProductionKey(), $operadora->getNome());
            } else {
                $interface = new $class($operadora->getDevelopmentKey(), $operadora->getNome());
            }
            $recebiveis_operadora = $interface->getReceivables($charge_id ? $charge_id : $recebivel->getOperadoraID(), $recebivel->getParcela());
            if ($recebiveis_operadora) {
                foreach ($recebiveis_operadora as $recebivel_operadora) {
                    $recebivel->import($recebivel_operadora->toArray(false));
                    $recebivel->setValor($recebivel_operadora->getValor())
                        ->setValorLiquido($recebivel_operadora->getValorLiquido());
                    $r = $this->CI->recebiveis_model->update($recebivel->getId(), $recebivel->toArray(false));
                    $recebiveis_atualizados += $r;

                    if ($recebivel->isRecebido()) {
                        $this->confirmar($recebivel->getId(), $recebivel->getValorLiquido(), $recebivel->getDataRecebimento());
                    } else {
                        $this->CI->recebiveis_model->desconfirmar($recebivel->getId());
                    }
                }
            }
            $this->CI->inscricoes_model->setTotaisInscricao($recebivel->getInscricao());
        }
        if ($recebiveis_atualizados && ! $rec_id) {
            $recebiveis_atualizados = $recebiveis_atualizados . ' recebíve' . ($recebiveis_atualizados > 1 ? 'is' : 'l') . ' atualizado' . ($recebiveis_atualizados > 1 ? 's' : '');
            echo $recebiveis_atualizados . PHP_EOL;
        } elseif ($recebiveis_atualizados && $rec_id) {
            $recebiveis_atualizados = 'Recebível ' . $rec_id . ' atualizado';
            echo $recebiveis_atualizados . PHP_EOL;
        } else {
            echo 'Sem atualizações pendentes' . PHP_EOL;
        }
        return $recebiveis_atualizados;
    }

    function sincronizarPrevistosAteHoje($data = null)
    {
        $this->CI->load->model('recebiveis_model');
        if (! $data) {
            $data = date('Y-m-d');
        }
        foreach ($this->CI->recebiveis_model->getRecebiveisPrevistos($data) as $rec) {
            $this->sincronizar($rec['rec_id']);
        }
    }

    function valorLiquido($valor, $forma, $qtd_parcelas = 1)
    {
        /*
         * @todo o que fazer quando é inserido um recebível a partir de uma operadora que é uma maquininha física (POS)? E o parcelamento?
         * E como fica a transação?
         */
        return $valor;
        if (! is_array($forma)) {
            $this->CI->load->model('recebiveis_model');
            $forma = $this->CI->recebiveis_model->getRow($forma);
            if (! $forma) {
                return $valor;
            }
        }
        if ($qtd_parcelas == 1) {
            $taxa = $forma['ofo_taxa'];
        } elseif ($qtd_parcelas >= 2 && $qtd_parcelas <= 3) {
            $taxa = $forma['ofo_taxaParcelamento23'];
        } elseif ($qtd_parcelas >= 4 && $qtd_parcelas <= 6) {
            $taxa = $forma['ofo_taxaParcelamento46'];
        } elseif ($qtd_parcelas >= 7 && $qtd_parcelas <= 12) {
            $taxa = $forma['ofo_taxaParcelamento712'];
        }
        return (($valor - ($forma['ofo_custoFixo'] / $qtd_parcelas)) * (100 - $taxa)) / 100;
    }
}