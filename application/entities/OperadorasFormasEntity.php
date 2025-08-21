<?php
namespace CANNALInscricoes\Entities;

class OperadorasFormasEntity extends _Entity
{

    protected $prefix = 'ofo_';

    protected $table = 'operadoras_formas';

    protected $forma;

    protected $operadora;

    protected $parcelamento;

    protected $antecipacao;

    protected $prazoEstornoTaxa;

    protected $custoFixo;

    protected $taxa;

    protected $taxaParcelamento23;

    protected $taxaParcelamento46;

    protected $taxaParcelamento712;

    /**
     * Construtor da classe RecebiveisFormas
     */
    public function __construct(?array $array = null)
    {
        if ($array) {
            $this->importArray($array);
        }
        return $this;
    }

    /**
     * Obtém a forma de pagamento.
     *
     * @return string
     */
    public function getForma(): string
    {
        return $this->forma;
    }

    /**
     * Obtém a operadora.
     *
     * @return string
     */
    public function getOperadora(): string
    {
        return $this->operadora;
    }

    /**
     * Obtém o status de parcelamento.
     *
     * @return int
     */
    public function getParcelamento(): int
    {
        return $this->parcelamento;
    }

    /**
     * Obtém o status de antecipação.
     *
     * @return int|null
     */
    public function getAntecipacao(): ?int
    {
        return $this->antecipacao;
    }

    /**
     * Obtém o prazo de estorno da taxa.
     *
     * @return int
     */
    public function getPrazoEstornoTaxa(): int
    {
        return $this->prazoEstornoTaxa;
    }

    /**
     * Obtém o custo fixo.
     *
     * @return float|null
     */
    public function getCustoFixo(): ?float
    {
        return $this->custoFixo;
    }

    /**
     * Obtém a taxa.
     *
     * @return float|null
     */
    public function getTaxa(): ?float
    {
        return $this->taxa;
    }

    /**
     * Obtém a taxa de parcelamento em 2 a 3 vezes.
     *
     * @return float|null
     */
    public function getTaxaParcelamento23(): ?float
    {
        return $this->taxaParcelamento23;
    }

    /**
     * Obtém a taxa de parcelamento em 4 a 6 vezes.
     *
     * @return float|null
     */
    public function getTaxaParcelamento46(): ?float
    {
        return $this->taxaParcelamento46;
    }

    /**
     * Obtém a taxa de parcelamento em 7 a 12 vezes.
     *
     * @return float|null
     */
    public function getTaxaParcelamento712(): ?float
    {
        return $this->taxaParcelamento712;
    }

    /**
     * Define a forma de pagamento.
     *
     * @param string $forma
     * @return self
     */
    public function setForma(string $forma): self
    {
        $this->forma = $forma;
        return $this;
    }

    /**
     * Define a operadora.
     *
     * @param string $operadora
     * @return self
     */
    public function setOperadora(string $operadora): self
    {
        $this->operadora = $operadora;
        return $this;
    }

    /**
     * Define o status de parcelamento.
     *
     * @param int $parcelamento
     * @return self
     */
    public function setParcelamento(int $parcelamento): self
    {
        $this->parcelamento = $parcelamento;
        return $this;
    }

    /**
     * Define o status de antecipação.
     *
     * @param int|null $antecipacao
     * @return self
     */
    public function setAntecipacao(?int $antecipacao): self
    {
        $this->antecipacao = $antecipacao;
        return $this;
    }

    /**
     * Define o prazo de estorno da taxa.
     *
     * @param int $prazoEstornoTaxa
     * @return self
     */
    public function setPrazoEstornoTaxa(int $prazoEstornoTaxa): self
    {
        $this->prazoEstornoTaxa = $prazoEstornoTaxa;
        return $this;
    }

    /**
     * Define o custo fixo.
     *
     * @param float|null $custoFixo
     * @return self
     */
    public function setCustoFixo(?float $custoFixo): self
    {
        $this->custoFixo = $custoFixo;
        return $this;
    }

    /**
     * Define a taxa.
     *
     * @param float|null $taxa
     * @return self
     */
    public function setTaxa(?float $taxa): self
    {
        $this->taxa = $taxa;
        return $this;
    }

    /**
     * Define a taxa de parcelamento em 2 a 3 vezes.
     *
     * @param float|null $taxaParcelamento23
     * @return self
     */
    public function setTaxaParcelamento23(?float $taxaParcelamento23): self
    {
        $this->taxaParcelamento23 = $taxaParcelamento23;
        return $this;
    }

    /**
     * Define a taxa de parcelamento em 4 a 6 vezes.
     *
     * @param float|null $taxaParcelamento46
     * @return self
     */
    public function setTaxaParcelamento46(?float $taxaParcelamento46): self
    {
        $this->taxaParcelamento46 = $taxaParcelamento46;
        return $this;
    }

    /**
     * Define a taxa de parcelamento em 7 a 12 vezes.
     *
     * @param float|null $taxaParcelamento712
     * @return self
     */
    public function setTaxaParcelamento712(?float $taxaParcelamento712): self
    {
        $this->taxaParcelamento712 = $taxaParcelamento712;
        return $this;
    }
}
