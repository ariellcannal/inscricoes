<?php
namespace CANNALInscricoes\Entities;

class FeriadosEntity extends _Entity
{

    protected string $table = 'feriados';

    protected string $prefix = 'fer_';

    protected int $id = 0;

    // Identificador do feriado
    protected string $data = '';

    // Data do feriado
    protected ?string $nome = null;

    // Nome do feriado
    protected ?string $link = null;

    // Link relacionado ao feriado
    protected ?string $tipo = null;

    // Tipo do feriado
    protected ?string $tipoCodigo = null;

    // Código do tipo do feriado
    protected ?string $descricao = null;

    // Descrição do feriado

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

    /**
     * Obtém o identificador do feriado.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Obtém a data do feriado.
     *
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * Define a data do feriado.
     *
     * @param string $data
     *            A nova data do feriado.
     * @return self
     */
    public function setData(string $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Obtém o nome do feriado.
     *
     * @return string|null
     */
    public function getNome(): ?string
    {
        return $this->nome;
    }

    /**
     * Define o nome do feriado.
     *
     * @param string|null $nome
     *            O novo nome do feriado.
     * @return self
     */
    public function setNome(?string $nome): self
    {
        $this->nome = $nome;
        return $this;
    }

    /**
     * Obtém o link relacionado ao feriado.
     *
     * @return string|null
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * Define o link relacionado ao feriado.
     *
     * @param string|null $link
     *            O novo link do feriado.
     * @return self
     */
    public function setLink(?string $link): self
    {
        $this->link = $link;
        return $this;
    }

    /**
     * Obtém o tipo do feriado.
     *
     * @return string|null
     */
    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    /**
     * Define o tipo do feriado.
     *
     * @param string|null $tipo
     *            O novo tipo do feriado.
     * @return self
     */
    public function setTipo(?string $tipo): self
    {
        $this->tipo = $tipo;
        return $this;
    }

    /**
     * Obtém o código do tipo do feriado.
     *
     * @return string|null
     */
    public function getTipoCodigo(): ?string
    {
        return $this->tipoCodigo;
    }

    /**
     * Define o código do tipo do feriado.
     *
     * @param string|null $tipoCodigo
     *            O novo código do tipo do feriado.
     * @return self
     */
    public function setTipoCodigo(?string $tipoCodigo): self
    {
        $this->tipoCodigo = $tipoCodigo;
        return $this;
    }

    /**
     * Obtém a descrição do feriado.
     *
     * @return string|null
     */
    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    /**
     * Define a descrição do feriado.
     *
     * @param string|null $descricao
     *            A nova descrição do feriado.
     * @return self
     */
    public function setDescricao(?string $descricao): self
    {
        $this->descricao = $descricao;
        return $this;
    }
}
