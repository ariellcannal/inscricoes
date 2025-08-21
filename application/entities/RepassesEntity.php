<?php
namespace CANNALInscricoes\Entities;

class RepassesEntity extends _Entity
{

    protected $prefix = 'rep_';

    protected $table = 'repasses';

    protected $id;

    protected $usuario;

    protected $valor;

    protected $data;

    protected $efetivado;

    protected $comentario;

    /**
     * Construtor da classe Repasses_model
     */
    public function __construct(?array $array = null)
    {
        if ($array) {
            $this->importArray($array);
        }
        return $this;
    }

    /**
     * Obtém o ID do repasse.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Obtém o ID do usuário associado ao repasse.
     *
     * @return int
     */
    public function getUsuario(): int
    {
        return $this->usuario;
    }

    /**
     * Obtém o valor do repasse.
     *
     * @return float
     */
    public function getValor(): float
    {
        return $this->valor;
    }

    /**
     * Obtém a data do repasse.
     *
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * Obtém a data de efetivação do repasse.
     *
     * @return string|null
     */
    public function getEfetivado(): ?string
    {
        return $this->efetivado;
    }

    /**
     * Obtém o comentário associado ao repasse.
     *
     * @return string|null
     */
    public function getComentario(): ?string
    {
        return $this->comentario;
    }

    /**
     * Define o ID do repasse.
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
     * Define o ID do usuário associado ao repasse.
     *
     * @param int $usuario
     * @return self
     */
    public function setUsuario(int $usuario): self
    {
        $this->usuario = $usuario;
        return $this;
    }

    /**
     * Define o valor do repasse.
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
     * Define a data do repasse.
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
     * Define a data de efetivação do repasse.
     *
     * @param string|null $efetivado
     * @return self
     */
    public function setEfetivado(?string $efetivado): self
    {
        $this->efetivado = $efetivado;
        return $this;
    }

    /**
     * Define o comentário associado ao repasse.
     *
     * @param string|null $comentario
     * @return self
     */
    public function setComentario(?string $comentario): self
    {
        $this->comentario = $comentario;
        return $this;
    }
}
