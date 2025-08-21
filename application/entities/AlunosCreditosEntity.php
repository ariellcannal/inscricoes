<?php
namespace CANNALInscricoes\Entities;

class AlunosCreditosEntity extends _Entity
{

    protected string $table = 'alunos_creditos';

    protected string $prefix = 'alc_';

    protected int $id = 0;

    protected int $aluno = 0;

    protected ?int $inscricao = null;

    protected float $valorInicial;

    protected ?float $valorUtilizado = null;

    protected string $motivo = '';

    /**
     * Construtor da classe.
     */
    public function __construct(?array $array = null)
    {
        if ($array) {
            $this->importArray($array);
        }
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAluno(): int
    {
        return $this->aluno;
    }

    public function setAluno(int $aluno): self
    {
        $this->aluno = $aluno;
        return $this;
    }

    public function getInscricao(): ?int
    {
        return $this->inscricao;
    }

    public function setInscricao(?int $inscricao): self
    {
        $this->inscricao = $inscricao;
        return $this;
    }

    public function getValorInicial(): float
    {
        return $this->valorInicial;
    }

    public function setValorInicial(float $valorInicial): self
    {
        $this->valorInicial = $valorInicial;
        return $this;
    }

    public function getValorUtilizado(): ?float
    {
        return $this->valorUtilizado;
    }

    public function setValorUtilizado(?float $valorUtilizado): self
    {
        $this->valorUtilizado = $valorUtilizado;
        return $this;
    }

    public function getMotivo(): string
    {
        return $this->motivo;
    }

    public function setMotivo(string $motivo): self
    {
        $this->motivo = $motivo;
        return $this;
    }
}
