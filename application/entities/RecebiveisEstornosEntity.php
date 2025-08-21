<?php
namespace CANNALInscricoes\Entities;

class RecebiveisEstornosEntity extends _Entity
{

    protected $prefix = 'res_';

    protected $table = 'recebiveis_estornos';

    protected $id;

    protected $recebivel;

    protected $valor;

    protected $criacao;

    protected $returnCode;

    protected $returnMessage;

    protected $tid;

    protected $nsu;

    protected $refundId;

    protected $refundDateTime;

    protected $cancelId;

    protected $operadoraID;

    protected $operadoraStatus;

    protected $operadoraResposta;

    /**
     * Construtor da classe RecebiveisEstornos_model
     */
    public function __construct(?array $array = null)
    {
        if ($array) {
            $this->importArray($array);
        }
        return $this;
    }

    /**
     * Obtém o ID do estorno.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Obtém o ID do recebivel relacionado.
     *
     * @return int
     */
    public function getRecebivel(): int
    {
        return $this->recebivel;
    }

    /**
     * Obtém o valor do estorno.
     *
     * @return float
     */
    public function getValor(): float
    {
        return $this->valor;
    }

    /**
     * Obtém a data de criação do estorno.
     *
     * @return string
     */
    public function getCriacao(): string
    {
        return $this->criacao;
    }

    /**
     * Obtém o código de retorno.
     *
     * @return string|null
     */
    public function getReturnCode(): ?string
    {
        return $this->returnCode;
    }

    /**
     * Obtém a mensagem de retorno.
     *
     * @return string|null
     */
    public function getReturnMessage(): ?string
    {
        return $this->returnMessage;
    }

    /**
     * Obtém o TID da transação.
     *
     * @return string|null
     */
    public function getTid(): ?string
    {
        return $this->tid;
    }

    /**
     * Obtém o NSU da transação.
     *
     * @return string|null
     */
    public function getNsu(): ?string
    {
        return $this->nsu;
    }

    /**
     * Obtém o ID do reembolso.
     *
     * @return string|null
     */
    public function getRefundId(): ?string
    {
        return $this->refundId;
    }

    /**
     * Obtém a data e hora do reembolso.
     *
     * @return string|null
     */
    public function getRefundDateTime(): ?string
    {
        return $this->refundDateTime;
    }

    /**
     * Obtém o ID do cancelamento.
     *
     * @return string|null
     */
    public function getCancelId(): ?string
    {
        return $this->cancelId;
    }

    /**
     * Obtém o ID da operadora.
     *
     * @return string|null
     */
    public function getOperadoraID(): ?string
    {
        return $this->operadoraID;
    }

    /**
     * Obtém o status da operadora.
     *
     * @return string|null
     */
    public function getOperadoraStatus(): ?string
    {
        return $this->operadoraStatus;
    }

    /**
     * Obtém a resposta da operadora.
     *
     * @return string|null
     */
    public function getOperadoraResposta(): ?string
    {
        return $this->operadoraResposta;
    }

    /**
     * Define o ID do estorno.
     *
     * @param int $id
     * @return self
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Define o ID do recebivel relacionado.
     *
     * @param int $recebivel
     * @return self
     */
    public function setRecebivel(int $recebivel): self
    {
        $this->recebivel = $recebivel;
        return $this;
    }

    /**
     * Define o valor do estorno.
     *
     * @param float $valor
     * @return self
     */
    public function setValor(float $valor): self
    {
        $this->valor = $valor;
        return $this;
    }

    /**
     * Define a data de criação do estorno.
     *
     * @param string $criacao
     * @return self
     */
    public function setCriacao(string $criacao): self
    {
        $this->criacao = $criacao;
        return $this;
    }

    /**
     * Define o código de retorno.
     *
     * @param string|null $returnCode
     * @return self
     */
    public function setReturnCode(?string $returnCode): self
    {
        $this->returnCode = $returnCode;
        return $this;
    }

    /**
     * Define a mensagem de retorno.
     *
     * @param string|null $returnMessage
     * @return self
     */
    public function setReturnMessage(?string $returnMessage): self
    {
        $this->returnMessage = $returnMessage;
        return $this;
    }

    /**
     * Define o TID da transação.
     *
     * @param string|null $tid
     * @return self
     */
    public function setTid(?string $tid): self
    {
        $this->tid = $tid;
        return $this;
    }

    /**
     * Define o NSU da transação.
     *
     * @param string|null $nsu
     * @return self
     */
    public function setNsu(?string $nsu): self
    {
        $this->nsu = $nsu;
        return $this;
    }

    /**
     * Define o ID do reembolso.
     *
     * @param string|null $refundId
     * @return self
     */
    public function setRefundId(?string $refundId): self
    {
        $this->refundId = $refundId;
        return $this;
    }

    /**
     * Define a data e hora do reembolso.
     *
     * @param string|null $refundDateTime
     * @return self
     */
    public function setRefundDateTime(?string $refundDateTime): self
    {
        $this->refundDateTime = $refundDateTime;
        return $this;
    }

    /**
     * Define o ID do cancelamento.
     *
     * @param string|null $cancelId
     * @return self
     */
    public function setCancelId(?string $cancelId): self
    {
        $this->cancelId = $cancelId;
        return $this;
    }

    /**
     * Define o ID da operadora.
     *
     * @param string|null $operadoraID
     * @return self
     */
    public function setOperadoraID(?string $operadoraID): self
    {
        $this->operadoraID = $operadoraID;
        return $this;
    }

    /**
     * Define o status da operadora.
     *
     * @param string|null $operadoraStatus
     * @return self
     */
    public function setOperadoraStatus(?string $operadoraStatus): self
    {
        $this->operadoraStatus = $operadoraStatus;
        return $this;
    }

    /**
     * Define a resposta da operadora.
     *
     * @param string|null $operadoraResposta
     * @return self
     */
    public function setOperadoraResposta(?string $operadoraResposta): self
    {
        $this->operadoraResposta = $operadoraResposta;
        return $this;
    }
}
