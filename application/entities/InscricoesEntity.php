<?php
namespace CANNALInscricoes\Entities;

class InscricoesEntity extends _Entity
{

    protected string $table = 'inscricoes';

    protected int $id = 0;

    protected int $grupo = 0;

    protected int $aluno = 0;

    protected int $status = 1;

    protected ?float $valorModulo = null;

    protected float $valorDesconto = 0;

    protected ?string $motivoDesconto = null;

    protected ?float $valorTotalPago = null;

    protected ?float $valorDevido = null;

    protected ?string $comentario = null;

    protected ?string $tempData = null;

    protected ?string $aprovada = null;

    protected string $IP = '';

    protected ?string $forma = null;

    protected string $data = '';

    protected ?int $user = null;

    /**
     * Get the value of id
     */
    public function __construct(?array $array = null)
    {
        if ($array) {
            $this->importArray($array);
        }
        return $this;
    }
    /**
     * Set the value of id
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get the value of grupo
     */
    public function getGrupo(): int
    {
        return $this->grupo;
    }

    /**
     * Set the value of grupo
     */
    public function setGrupo(int $grupo): self
    {
        $this->grupo = $grupo;
        return $this;
    }

    /**
     * Get the value of aluno
     */
    public function getAluno(): int
    {
        return $this->aluno;
    }

    /**
     * Set the value of aluno
     */
    public function setAluno(int $aluno): self
    {
        $this->aluno = $aluno;
        return $this;
    }

    /**
     * Get the value of status
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Set the value of status
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get the value of valorModulo
     */
    public function getValorModulo(): ?float
    {
        return $this->valorModulo;
    }

    /**
     * Set the value of valorModulo
     */
    public function setValorModulo(?float $valorModulo): self
    {
        $this->valorModulo = $valorModulo;
        return $this;
    }

    /**
     * Get the value of valorDesconto
     */
    public function getValorDesconto(): float
    {
        return $this->valorDesconto;
    }

    /**
     * Set the value of valorDesconto
     */
    public function setValorDesconto(float $valorDesconto): self
    {
        $this->valorDesconto = $valorDesconto;
        return $this;
    }

    /**
     * Get the value of motivoDesconto
     */
    public function getMotivoDesconto(): ?string
    {
        return $this->motivoDesconto;
    }

    /**
     * Set the value of motivoDesconto
     */
    public function setMotivoDesconto(?string $motivoDesconto): self
    {
        $this->motivoDesconto = $motivoDesconto;
        return $this;
    }

    /**
     * Get the value of valorTotalPago
     */
    public function getValorTotalPago(): ?float
    {
        return $this->valorTotalPago;
    }

    /**
     * Set the value of valorTotalPago
     */
    public function setValorTotalPago(?float $valorTotalPago): self
    {
        $this->valorTotalPago = $valorTotalPago;
        return $this;
    }

    /**
     * Get the value of valorDevido
     */
    public function getValorDevido(): ?float
    {
        return $this->valorDevido;
    }

    /**
     * Set the value of valorDevido
     */
    public function setValorDevido(?float $valorDevido): self
    {
        $this->valorDevido = $valorDevido;
        return $this;
    }

    /**
     * Get the value of comentario
     */
    public function getComentario(): ?string
    {
        return $this->comentario;
    }

    /**
     * Set the value of comentario
     */
    public function setComentario(?string $comentario): self
    {
        $this->comentario = $comentario;
        return $this;
    }

    /**
     * Get the value of tempData
     */
    public function getTempData(): ?string
    {
        return $this->tempData;
    }

    /**
     * Set the value of tempData
     */
    public function setTempData(?string $tempData): self
    {
        $this->tempData = $tempData;
        return $this;
    }

    /**
     * Get the value of aprovada
     */
    public function getAprovada(): ?string
    {
        return $this->aprovada;
    }

    /**
     * Set the value of aprovada
     */
    public function setAprovada(?string $aprovada): self
    {
        $this->aprovada = $aprovada;
        return $this;
    }

    /**
     * Get the value of IP
     */
    public function getIP(): string
    {
        return $this->IP;
    }

    /**
     * Set the value of IP
     */
    public function setIP(string $IP): self
    {
        $this->IP = $IP;
        return $this;
    }

    /**
     * Get the value of forma
     */
    public function getForma(): ?string
    {
        return $this->forma;
    }

    /**
     * Set the value of forma
     */
    public function setForma(?string $forma): self
    {
        $this->forma = $forma;
        return $this;
    }

    /**
     * Get the value of data
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * Set the value of data
     */
    public function setData(string $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get the value of user
     */
    public function getUser(): ?int
    {
        return $this->user;
    }

    /**
     * Set the value of user
     */
    public function setUser(?int $user): self
    {
        $this->user = $user;
        return $this;
    }
}
