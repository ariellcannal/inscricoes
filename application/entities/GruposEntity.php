<?php
namespace CANNALInscricoes\Entities;

class GruposEntity extends _Entity
{

    protected string $table = 'grupos';

    protected string $prefix = 'grp_';

    protected int $id = 0;

    protected string $nome = '';

    protected string $slug = '';

    protected string $nomePublico = '';

    protected bool $drtObrigatorio = false;

    protected ?string $dataAulaAberta = null;

    protected string $dataInicio = '';

    protected ?string $dataFim = null;

    protected string $diaSemana = '';

    protected string $horaInicio = '';

    protected string $horaFim = '';

    protected ?string $horario = null;

    protected ?string $dias = null;

    protected ?int $encontros = null;

    protected string $descricao = '';

    protected ?string $descricaoDetalhes = null;

    protected string $coordenadores = '';

    protected float $valor = 0;

    protected string $valorDescricao = '';

    protected ?string $imagem = null;

    protected bool $exibeSite = false;

    protected bool $inscricoesAbertas = false;

    protected bool $processoSeletivo = false;

    protected bool $ativo = false;

    protected bool $repasseAtivado = false;

    protected ?string $atualizacao = null;

    protected ?string $linkWhats = null;

    protected ?string $idFaturaCartao = null;

    protected ?string $operadora = null;

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
     * Obtém o identificador do grupo.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Obtém o nome do grupo.
     *
     * @return string
     */
    public function getNome(): string
    {
        return $this->nome;
    }

    /**
     * Define o nome do grupo.
     *
     * @param string $nome
     *            O novo nome do grupo.
     * @return self
     */
    public function setNome(string $nome): self
    {
        $this->nome = $nome;
        return $this;
    }

    /**
     * Obtém o slug do grupo.
     *
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Define o slug do grupo.
     *
     * @param string $slug
     *            O novo slug do grupo.
     * @return self
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * Obtém o nome público do grupo.
     *
     * @return string
     */
    public function getNomePublico(): string
    {
        return $this->nomePublico;
    }

    /**
     * Define o nome público do grupo.
     *
     * @param string $nomePublico
     *            O novo nome público do grupo.
     * @return self
     */
    public function setNomePublico(string $nomePublico): self
    {
        $this->nomePublico = $nomePublico;
        return $this;
    }

    /**
     * Obtém se o DRT é obrigatório.
     *
     * @return bool
     */
    public function isDrtObrigatorio(): bool
    {
        return $this->drtObrigatorio;
    }

    /**
     * Define se o DRT é obrigatório.
     *
     * @param bool $drtObrigatorio
     *            Novo valor para DRT obrigatório.
     * @return self
     */
    public function setDrtObrigatorio(bool $drtObrigatorio): self
    {
        $this->drtObrigatorio = $drtObrigatorio;
        return $this;
    }

    /**
     * Obtém a data da aula aberta.
     *
     * @return string|null
     */
    public function getDataAulaAberta(): ?string
    {
        return $this->dataAulaAberta;
    }

    /**
     * Define a data da aula aberta.
     *
     * @param string|null $dataAulaAberta
     *            A nova data da aula aberta.
     * @return self
     */
    public function setDataAulaAberta(?string $dataAulaAberta): self
    {
        $this->dataAulaAberta = $dataAulaAberta;
        return $this;
    }

    /**
     * Obtém a data de início do grupo.
     *
     * @return string
     */
    public function getDataInicio(): string
    {
        return $this->dataInicio;
    }

    /**
     * Define a data de início do grupo.
     *
     * @param string $dataInicio
     *            A nova data de início.
     * @return self
     */
    public function setDataInicio(string $dataInicio): self
    {
        $this->dataInicio = $dataInicio;
        return $this;
    }

    /**
     * Obtém a data de fim do grupo.
     *
     * @return string|null
     */
    public function getDataFim(): ?string
    {
        return $this->dataFim;
    }

    /**
     * Define a data de fim do grupo.
     *
     * @param string|null $dataFim
     *            A nova data de fim.
     * @return self
     */
    public function setDataFim(?string $dataFim): self
    {
        $this->dataFim = $dataFim;
        return $this;
    }

    /**
     * Obtém o dia da semana do grupo.
     *
     * @return string
     */
    public function getDiaSemana(): string
    {
        return $this->diaSemana;
    }

    /**
     * Define o dia da semana do grupo.
     *
     * @param string $diaSemana
     *            O novo dia da semana.
     * @return self
     */
    public function setDiaSemana(string $diaSemana): self
    {
        $this->diaSemana = $diaSemana;
        return $this;
    }

    /**
     * Obtém a hora de início do grupo.
     *
     * @return string
     */
    public function getHoraInicio(): string
    {
        return $this->horaInicio;
    }

    /**
     * Define a hora de início do grupo.
     *
     * @param string $horaInicio
     *            A nova hora de início.
     * @return self
     */
    public function setHoraInicio(string $horaInicio): self
    {
        $this->horaInicio = $horaInicio;
        return $this;
    }

    /**
     * Obtém a hora de fim do grupo.
     *
     * @return string
     */
    public function getHoraFim(): string
    {
        return $this->horaFim;
    }

    /**
     * Define a hora de fim do grupo.
     *
     * @param string $horaFim
     *            A nova hora de fim.
     * @return self
     */
    public function setHoraFim(string $horaFim): self
    {
        $this->horaFim = $horaFim;
        return $this;
    }

    /**
     * Obtém o horário adicional do grupo.
     *
     * @return string|null
     */
    public function getHorario(): ?string
    {
        return $this->horario;
    }

    /**
     * Define o horário adicional do grupo.
     *
     * @param string|null $horario
     *            O novo horário adicional.
     * @return self
     */
    public function setHorario(?string $horario): self
    {
        $this->horario = $horario;
        return $this;
    }

    /**
     * Obtém os dias adicionais do grupo.
     *
     * @return string|null
     */
    public function getDias(): ?string
    {
        return $this->dias;
    }

    /**
     * Define os dias adicionais do grupo.
     *
     * @param string|null $dias
     *            Os novos dias adicionais.
     * @return self
     */
    public function setDias(?string $dias): self
    {
        $this->dias = $dias;
        return $this;
    }

    /**
     * Obtém o número de encontros do grupo.
     *
     * @return int|null
     */
    public function getEncontros(): ?int
    {
        return $this->encontros;
    }

    /**
     * Define o número de encontros do grupo.
     *
     * @param int|null $encontros
     *            O novo número de encontros.
     * @return self
     */
    public function setEncontros(?int $encontros): self
    {
        $this->encontros = $encontros;
        return $this;
    }

    /**
     * Obtém a descrição do grupo.
     *
     * @return string
     */
    public function getDescricao(): string
    {
        return $this->descricao;
    }

    /**
     * Define a descrição do grupo.
     *
     * @param string $descricao
     *            A nova descrição.
     * @return self
     */
    public function setDescricao(string $descricao): self
    {
        $this->descricao = $descricao;
        return $this;
    }

    /**
     * Obtém a descrição detalhada do grupo.
     *
     * @return string|null
     */
    public function getDescricaoDetalhes(): ?string
    {
        return $this->descricaoDetalhes;
    }

    /**
     * Define a descrição detalhada do grupo.
     *
     * @param string|null $descricaoDetalhes
     *            A nova descrição detalhada.
     * @return self
     */
    public function setDescricaoDetalhes(?string $descricaoDetalhes): self
    {
        $this->descricaoDetalhes = $descricaoDetalhes;
        return $this;
    }

    /**
     * Obtém os coordenadores do grupo.
     *
     * @return string
     */
    public function getCoordenadores(): string
    {
        return $this->coordenadores;
    }

    /**
     * Define os coordenadores do grupo.
     *
     * @param string $coordenadores
     *            Os novos coordenadores.
     * @return self
     */
    public function setCoordenadores(string $coordenadores): self
    {
        $this->coordenadores = $coordenadores;
        return $this;
    }

    /**
     * Obtém o valor do grupo.
     *
     * @return float
     */
    public function getValor(): float
    {
        return $this->valor;
    }

    /**
     * Define o valor do grupo.
     *
     * @param float $valor
     *            O novo valor.
     * @return self
     */
    public function setValor(float $valor): self
    {
        $this->valor = $valor;
        return $this;
    }

    /**
     * Obtém a descrição do valor do grupo.
     *
     * @return string
     */
    public function getValorDescricao(): string
    {
        return $this->valorDescricao;
    }

    /**
     * Define a descrição do valor do grupo.
     *
     * @param string $valorDescricao
     *            A nova descrição do valor.
     * @return self
     */
    public function setValorDescricao(string $valorDescricao): self
    {
        $this->valorDescricao = $valorDescricao;
        return $this;
    }

    /**
     * Obtém a imagem do grupo.
     *
     * @return string|null
     */
    public function getImagem(): ?string
    {
        return $this->imagem;
    }

    /**
     * Define a imagem do grupo.
     *
     * @param string|null $imagem
     *            A nova imagem do grupo.
     * @return self
     */
    public function setImagem(?string $imagem): self
    {
        $this->imagem = $imagem;
        return $this;
    }

    /**
     * Obtém se o grupo deve ser exibido no site.
     *
     * @return bool
     */
    public function isExibeSite(): bool
    {
        return $this->exibeSite;
    }

    /**
     * Define se o grupo deve ser exibido no site.
     *
     * @param bool $exibeSite
     *            O novo valor.
     * @return self
     */
    public function setExibeSite(bool $exibeSite): self
    {
        $this->exibeSite = $exibeSite;
        return $this;
    }

    /**
     * Obtém se as inscrições estão abertas.
     *
     * @return bool
     */
    public function isInscricoesAbertas(): bool
    {
        return $this->inscricoesAbertas;
    }

    /**
     * Define se as inscrições estão abertas.
     *
     * @param bool $inscricoesAbertas
     *            O novo valor.
     * @return self
     */
    public function setInscricoesAbertas(bool $inscricoesAbertas): self
    {
        $this->inscricoesAbertas = $inscricoesAbertas;
        return $this;
    }

    /**
     * Obtém se há processo seletivo.
     *
     * @return bool
     */
    public function isProcessoSeletivo(): bool
    {
        return $this->processoSeletivo;
    }

    /**
     * Define se há processo seletivo.
     *
     * @param bool $processoSeletivo
     *            O novo valor.
     * @return self
     */
    public function setProcessoSeletivo(bool $processoSeletivo): self
    {
        $this->processoSeletivo = $processoSeletivo;
        return $this;
    }

    /**
     * Obtém se o grupo está ativo.
     *
     * @return bool
     */
    public function isAtivo(): bool
    {
        return $this->ativo;
    }

    /**
     * Define se o grupo está ativo.
     *
     * @param bool $ativo
     *            O novo valor.
     * @return self
     */
    public function setAtivo(bool $ativo): self
    {
        $this->ativo = $ativo;
        return $this;
    }

    /**
     * Obtém se o repasse está ativado.
     *
     * @return bool
     */
    public function isRepasseAtivado(): bool
    {
        return $this->repasseAtivado;
    }

    /**
     * Define se o repasse está ativado.
     *
     * @param bool $repasseAtivado
     *            O novo valor.
     * @return self
     */
    public function setRepasseAtivado(bool $repasseAtivado): self
    {
        $this->repasseAtivado = $repasseAtivado;
        return $this;
    }

    /**
     * Obtém a data da última atualização.
     *
     * @return string|null
     */
    public function getAtualizacao(): ?string
    {
        return $this->atualizacao;
    }

    /**
     * Define a data da última atualização.
     *
     * @param string|null $atualizacao
     *            A nova data de atualização.
     * @return self
     */
    public function setAtualizacao(?string $atualizacao): self
    {
        $this->atualizacao = $atualizacao;
        return $this;
    }

    /**
     * Obtém o link do WhatsApp.
     *
     * @return string|null
     */
    public function getLinkWhats(): ?string
    {
        return $this->linkWhats;
    }

    /**
     * Define o link do WhatsApp.
     *
     * @param string|null $linkWhats
     *            O novo link do WhatsApp.
     * @return self
     */
    public function setLinkWhats(?string $linkWhats): self
    {
        $this->linkWhats = $linkWhats;
        return $this;
    }

    /**
     * Obtém o ID da fatura do cartão.
     *
     * @return string|null
     */
    public function getIdFaturaCartao(): ?string
    {
        return $this->idFaturaCartao;
    }

    /**
     * Define o ID da fatura do cartão.
     *
     * @param string|null $idFaturaCartao
     *            O novo ID da fatura do cartão.
     * @return self
     */
    public function setIdFaturaCartao(?string $idFaturaCartao): self
    {
        $this->idFaturaCartao = $idFaturaCartao;
        return $this;
    }

    /**
     * Obtém a operadora.
     *
     * @return string|null
     */
    public function getOperadora(): ?string
    {
        return $this->operadora;
    }

    /**
     * Define a operadora.
     *
     * @param string|null $operadora
     *            A nova operadora.
     * @return self
     */
    public function setOperadora(?string $operadora): self
    {
        $this->operadora = $operadora;
        return $this;
    }
}
