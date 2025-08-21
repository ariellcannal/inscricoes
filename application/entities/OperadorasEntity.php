<?php
namespace CANNALInscricoes\Entities;

class OperadorasEntity extends _Entity
{

    protected string $prefix = 'opr_';

    protected string $table = 'operadoras';

    protected string $nome = '';

    protected string $productionKey = '';

    protected string $developmentKey = '';

    protected string $interface = '';

    protected ?bool $default = null;

    /**
     * Construtor da classe Operadoras_model.
     */
    public function __construct(?array $array = null)
    {
        if ($array) {
            $this->importArray($array);
        }
        return $this;
    }

    /**
     * Obtém o nome da operadora.
     *
     * @return string
     */
    public function getNome(): string
    {
        return $this->nome;
    }

    /**
     * Define o nome da operadora.
     *
     * @param string $nome
     * @return self
     */
    public function setNome(string $nome): self
    {
        $this->nome = $nome;
        return $this;
    }

    /**
     * Obtém a chave de produção.
     *
     * @return string
     */
    public function getProductionKey(): string
    {
        return $this->productionKey;
    }

    /**
     * Define a chave de produção.
     *
     * @param string $productionKey
     * @return self
     */
    public function setProductionKey(string $productionKey): self
    {
        $this->productionKey = $productionKey;
        return $this;
    }

    /**
     * Obtém a chave de desenvolvimento.
     *
     * @return string
     */
    public function getDevelopmentKey(): string
    {
        return $this->developmentKey;
    }

    /**
     * Define a chave de desenvolvimento.
     *
     * @param string $developmentKey
     * @return self
     */
    public function setDevelopmentKey(string $developmentKey): self
    {
        $this->developmentKey = $developmentKey;
        return $this;
    }

    /**
     * Obtém a interface.
     *
     * @return string
     */
    public function getInterface(): string
    {
        return $this->interface;
    }

    /**
     * Define a interface.
     *
     * @param string $interface
     * @return self
     */
    public function setInterface(string $interface): self
    {
        $this->interface = $interface;
        return $this;
    }

    /**
     * Verifica se a operadora é padrão.
     *
     * @return bool|null
     */
    public function isDefault(): ?bool
    {
        return $this->default;
    }

    /**
     * Define se a operadora é padrão.
     *
     * @param bool|null $default
     * @return self
     */
    public function setDefault(?bool $default): self
    {
        $this->default = $default;
        return $this;
    }
}
