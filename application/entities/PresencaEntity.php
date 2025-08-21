<?php
namespace CANNALInscricoes\Entities;

class PresencaEntity extends _Entity
{

    protected $prefix = 'prs_';

    protected $table = 'presenca';

    protected $id;

    protected $data;

    protected $dataAula;

    protected $grupo;

    protected $aluno;

    protected $criador;

    /**
     * Construtor da classe Presenca_model
     */
    public function __construct(?array $array = null)
    {
        if ($array) {
            $this->importArray($array);
        }
        return $this;
    }

    /**
     * Obtém o ID da presença.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Obtém a data da presença.
     *
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * Obtém a data da aula associada.
     *
     * @return string|null
     */
    public function getDataAula(): ?string
    {
        return $this->dataAula;
    }

    /**
     * Obtém o ID do grupo associado.
     *
     * @return int
     */
    public function getGrupo(): int
    {
        return $this->grupo;
    }

    /**
     * Obtém o ID do aluno associado.
     *
     * @return int
     */
    public function getAluno(): int
    {
        return $this->aluno;
    }

    /**
     * Obtém o ID do criador da presença.
     *
     * @return int|null
     */
    public function getCriador(): ?int
    {
        return $this->criador;
    }

    /**
     * Define o ID da presença.
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
     * Define a data da presença.
     *
     * @param string $data
     * @return self
     */
    public function setData(string $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Define a data da aula associada.
     *
     * @param string|null $dataAula
     * @return self
     */
    public function setDataAula(?string $dataAula): self
    {
        $this->dataAula = $dataAula;
        return $this;
    }

    /**
     * Define o ID do grupo associado.
     *
     * @param int $grupo
     * @return self
     */
    public function setGrupo(int $grupo): self
    {
        $this->grupo = $grupo;
        return $this;
    }

    /**
     * Define o ID do aluno associado.
     *
     * @param int $aluno
     * @return self
     */
    public function setAluno(int $aluno): self
    {
        $this->aluno = $aluno;
        return $this;
    }

    /**
     * Define o ID do criador da presença.
     *
     * @param int|null $criador
     * @return self
     */
    public function setCriador(?int $criador): self
    {
        $this->criador = $criador;
        return $this;
    }
}
