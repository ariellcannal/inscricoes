<?php
namespace CANNALInscricoes\Entities;

class GruposFormasEntity extends _Entity
{

    protected $prefix = 'gfp_';

    protected $table = 'grupos_formas';

    protected int $id = 0;

    protected int $grupo = 0;

    protected int $parcelas = 0;

    protected float $valorTotal = 0;

    protected int $ordem = 0;

    protected bool $publico = false;

    protected bool $aceitaCartao = false;

    protected ?string $linkOculto = '';

    protected ?string $linkOcultoValidade = '';

    protected ?string $comentario = '';

    protected ?string $descricao = '';

    /**
     * Construtor da classe GruposFormas_model.
     */
    public function __construct(?array $array = null)
    {
        if ($array) {
            $this->importArray($array);
        }
        return $this;
    }

    /**
     * Obtém o ID da forma de pagamento.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Define o ID da forma de pagamento.
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
     * Obtém o ID do grupo.
     *
     * @return int
     */
    public function getGrupo(): int
    {
        return $this->grupo;
    }

    /**
     * Define o ID do grupo.
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
     * Obtém o número de parcelas.
     *
     * @return int
     */
    public function getParcelas(): int
    {
        return $this->parcelas;
    }

    /**
     * Define o número de parcelas.
     *
     * @param int $parcelas
     * @return self
     */
    public function setParcelas(int $parcelas): self
    {
        $this->parcelas = $parcelas;
        return $this;
    }

    /**
     * Obtém o valor total.
     *
     * @return float
     */
    public function getValorTotal(): float
    {
        return $this->valorTotal;
    }

    /**
     * Define o valor total.
     *
     * @param float $valorTotal
     * @return self
     */
    public function setValorTotal(float $valorTotal): self
    {
        $this->valorTotal = $valorTotal;
        return $this;
    }

    /**
     * Obtém a ordem.
     *
     * @return int
     */
    public function getOrdem(): int
    {
        return $this->ordem;
    }

    /**
     * Define a ordem.
     *
     * @param int $ordem
     * @return self
     */
    public function setOrdem(int $ordem): self
    {
        $this->ordem = $ordem;
        return $this;
    }

    /**
     * Obtém se é público.
     *
     * @return bool
     */
    public function isPublico(): bool
    {
        return $this->publico;
    }

    /**
     * Define se é público.
     *
     * @param bool $publico
     * @return self
     */
    public function setPublico(bool $publico): self
    {
        $this->publico = $publico;
        return $this;
    }

    /**
     * Obtém se aceita cartão.
     *
     * @return bool
     */
    public function isAceitaCartao(): bool
    {
        return $this->aceitaCartao;
    }

    /**
     * Define se aceita cartão.
     *
     * @param bool $aceitaCartao
     * @return self
     */
    public function setAceitaCartao(bool $aceitaCartao): self
    {
        $this->aceitaCartao = $aceitaCartao;
        return $this;
    }

    /**
     * Obtém o link oculto.
     *
     * @return string|null
     */
    public function getLinkOculto(): ?string
    {
        return $this->linkOculto;
    }

    /**
     * Define o link oculto.
     *
     * @param string|null $linkOculto
     * @return self
     */
    public function setLinkOculto(?string $linkOculto): self
    {
        $this->linkOculto = $linkOculto;
        return $this;
    }

    /**
     * Obtém a validade do link oculto.
     *
     * @return string|null
     */
    public function getLinkOcultoValidade(): ?string
    {
        return $this->linkOcultoValidade;
    }

    /**
     * Define a validade do link oculto.
     *
     * @param string|null $linkOcultoValidade
     * @return self
     */
    public function setLinkOcultoValidade(?string $linkOcultoValidade): self
    {
        $this->linkOcultoValidade = $linkOcultoValidade;
        return $this;
    }

    /**
     * Obtém o comentário.
     *
     * @return string|null
     */
    public function getComentario(): ?string
    {
        return $this->comentario;
    }

    /**
     * Define o comentário.
     *
     * @param string|null $comentario
     * @return self
     */
    public function setComentario(?string $comentario): self
    {
        $this->comentario = $comentario;
        return $this;
    }

    /**
     * Obtém a descrição.
     *
     * @return string|null
     */
    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    /**
     * Define a descrição.
     *
     * @param string|null $descricao
     * @return self
     */
    public function setDescricao(?string $descricao): self
    {
        $this->descricao = $descricao;
        return $this;
    }
}