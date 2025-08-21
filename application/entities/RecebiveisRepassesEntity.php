<?php
namespace CANNALInscricoes\Entities;

class RecebiveisRepassesEntity extends _Entity
{

    protected string $prefix = 'rre_';

    protected string $table = 'recebiveis_repasses';

    protected ?int $id = null;

    protected ?int $recebivel = null;

    protected ?int $repasse = null;

    protected ?int $usuario = null;

    protected ?float $porcentagemUsuario = null;

    protected ?float $valor = null;

    /**
     * Construtor da classe RecebiveisRepasses_model
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
     * Obtém o ID do recebivel associado.
     *
     * @return int
     */
    public function getRecebivel(): int
    {
        return $this->recebivel;
    }

    /**
     * Obtém o ID do repasse associado.
     *
     * @return int
     */
    public function getRepasse(): int
    {
        return $this->repasse;
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
     * Obtém a porcentagem do usuário associado ao repasse.
     *
     * @return float
     */
    public function getPorcentagemUsuario(): float
    {
        return $this->porcentagemUsuario;
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
     * Define o ID do repasse.
     *
     * @param int|null $id
     * @return self
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Define o ID do recebivel associado.
     *
     * @param int|null $recebivel
     * @return self
     */
    public function setRecebivel(int $recebivel): self
    {
        $this->recebivel = $recebivel;
        return $this;
    }

    /**
     * Define o ID do recebivel associado.
     *
     * @param int|null $repasse
     * @return self
     */
    public function setRepasse(int $repasse): self
    {
        $this->repasse = $repasse;
        return $this;
    }

    /**
     * Define o ID do usuário associado ao repasse.
     *
     * @param int|null $usuario
     * @return self
     */
    public function setUsuario(int $usuario): self
    {
        $this->usuario = $usuario;
        return $this;
    }

    /**
     * Define a porcentagem do usuário associado ao repasse.
     *
     * @param float|null $porcentagemUsuario
     * @return self
     */
    public function setPorcentagemUsuario(?float $porcentagemUsuario): self
    {
        $this->porcentagemUsuario = $porcentagemUsuario;
        return $this;
    }

    /**
     * Define o valor do repasse.
     *
     * @param float|null $valor
     * @return self
     */
    public function setValor(float $valor): self
    {
        $this->valor = $valor;
        return $this;
    }
}
