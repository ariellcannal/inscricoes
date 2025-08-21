<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Operadoras_model extends SYS_Model
{

    protected string $prefix = 'opr_';

    protected string $table = 'operadoras';

    /**
     * Construtor da classe Operadoras_model.
     */
    public function __construct()
    {
        parent::__construct();
        return $this;
    }

    function getDefault()
    {
        $this->db->where('opr_default', '1', false);
        $r = $this->db->get('operadoras');
        return $r->row_array();
    }

    function getRow($opr_nome)
    {
        $this->db->where('opr_nome', $opr_nome);
        $r = $this->db->get('operadoras');
        return $r->row_array();
    }

    function getLike($opr_nome)
    {
        $this->db->where('opr_nome LIKE "%' . $opr_nome . '%"', null, false);
        $r = $this->db->get('operadoras');
        return $r->result_array();
    }

    function getFormas($ofo_operadora, $ofo_forma_like = null)
    {
        $this->db->where('ofo_operadora', $ofo_operadora);
        if ($ofo_forma_like) {
            $this->db->where('ofo_forma LIKE "%' . $ofo_forma_like . '%"', null, false);
        }
        $this->db->order_by('ofo_parcelamento', 'ASC');
        $r = $this->db->get('operadoras_formas');
        return $r->result_array();
    }

    function getForma($ofo_forma, $ofo_operadora)
    {
        $this->db->where('ofo_forma', $ofo_forma);
        $this->db->where('ofo_operadora', $ofo_operadora);
        $r = $this->db->get('operadoras_formas');
        return $r->row_array();
    }

    function inserirTransacao(array $otr)
    {
        $this->db->insert('operadoras_transacoes', $otr);
        return $this->db->insert_id();
    }

    function getTransacao($otr_id)
    {
        $this->db->where('otr_id', $otr_id);
        $r = $this->db->get('operadoras_transacoes');
        return $r->row_array();
    }

    function updateTransacao($otr_id, $set)
    {
        $this->db->update('operadoras_transacoes', $set, array(
            'otr_id' => $otr_id
        ));
        return $this->db->affected_rows();
    }

    function getTransacaoPorOperadoraId($otr_operadoraId)
    {
        $this->db->where('otr_operadoraID', $otr_operadoraId);
        $r = $this->db->get('operadoras_transacoes');
        return $r->result_array();
    }

    function getTransacoesPorInscricao($otr_inscricao, $where = null)
    {
        if ($where) {
            $this->db->where($where);
        }
        $this->db->where('otr_inscricao', $otr_inscricao);
        $r = $this->db->get('operadoras_transacoes');
        return $r->result_array();
    }

    function getTransacoesRecentes(int $dias = null)
    {
        if ($dias) {
            $this->db->where('otr_dataTransacao >=', date('Y-m-d H:i:s', strtotime('-' . $dias . 'days')));
        }
        $r = $this->db->get('operadoras_transacoes');
        return $r->result_array();
    }

    function getTransacoesVencidas()
    {
        $this->db->where('otr_dataExpiracao <', 'NOW()', false);
        $this->db->where('otr_dataExpiracao IS NOT NULL', null, false);
        return $this->db->get('operadoras_transacoes')->result_array();
    }

    function registraEstorno($otr_id, $otr, $valorCancelado)
    {
        $i_est['tes_transacao'] = $otr_id;
        $i_est['tes_valor'] = $valorCancelado;
        $i_est['tes_operadoraResposta'] = $otr['otr_operadoraResposta'];
        $i_est['tes_operadoraStatus'] = $otr['otr_operadoraStatus'];
        $i_est['tes_operadoraID'] = $otr['otr_operadoraID'];
        $i_est['tes_criacao'] = date('Y-m-d H:i:s');
        $this->db->insert('operadoras_transacoes_estornos', $i_est);

        $this->db->where('otr_id', $otr_id);
        $this->db->set($otr);
        $this->db->update('operadoras_transacoes');
        return $this->db->affected_rows();
    }
}
