<?php
namespace CANNALInscricoes\Entities;

class AlunosEntity extends _Entity
{

    protected string $table = 'alunos';

    protected string $prefix = 'alu_';

    protected int $id = 0;

    protected string $nome = '';

    protected string $nomeArtistico = '';

    protected ?string $cpf = '';

    protected ?string $nascimento = null;

    protected ?string $drt = null;

    protected string $email = '';

    protected string $celular = '';

    protected ?string $endereco = null;

    protected ?string $enderecoNumero = null;

    protected ?string $enderecoComplemento = null;

    protected ?string $enderecoBairro = null;

    protected ?string $enderecoCidade = null;

    protected ?string $enderecoEstado = null;

    protected ?string $enderecoCep = null;

    protected ?string $cv = null;

    protected ?string $foto = null;

    protected ?string $criacao = null;

    protected ?string $modificacao = null;

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

    public function getNome(): string
    {
        return $this->nome;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;
        return $this;
    }

    public function getNomeArtistico(): string
    {
        return $this->nomeArtistico;
    }

    public function setNomeArtistico(string $nomeArtistico): self
    {
        $this->nomeArtistico = $nomeArtistico;
        return $this;
    }

    public function getCpf(): ?string
    {
        return $this->cpf;
    }

    public function setCpf(?string $cpf): self
    {
        $this->cpf = $cpf;
        return $this;
    }

    public function getNascimento(): ?string
    {
        return $this->nascimento;
    }

    public function setNascimento(?string $nascimento): self
    {
        $this->nascimento = $nascimento;
        return $this;
    }

    public function getDrt(): ?string
    {
        return $this->drt;
    }

    public function setDrt(?string $drt): self
    {
        $this->drt = $drt;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getCelular(): string
    {
        return $this->celular;
    }

    public function setCelular(string $celular): self
    {
        $this->celular = $celular;
        return $this;
    }

    public function getEndereco(): ?string
    {
        return $this->endereco;
    }

    public function setEndereco(?string $endereco): self
    {
        $this->endereco = $endereco;
        return $this;
    }

    public function getEnderecoNumero(): ?string
    {
        return $this->enderecoNumero;
    }

    public function setEnderecoNumero(?string $enderecoNumero): self
    {
        $this->enderecoNumero = $enderecoNumero;
        return $this;
    }

    public function getEnderecoComplemento(): ?string
    {
        return $this->enderecoComplemento;
    }

    public function setEnderecoComplemento(?string $enderecoComplemento): self
    {
        $this->enderecoComplemento = $enderecoComplemento;
        return $this;
    }

    public function getEnderecoBairro(): ?string
    {
        return $this->enderecoBairro;
    }

    public function setEnderecoBairro(?string $enderecoBairro): self
    {
        $this->enderecoBairro = $enderecoBairro;
        return $this;
    }

    public function getEnderecoCidade(): ?string
    {
        return $this->enderecoCidade;
    }

    public function setEnderecoCidade(?string $enderecoCidade): self
    {
        $this->enderecoCidade = $enderecoCidade;
        return $this;
    }

    public function getEnderecoEstado(): ?string
    {
        return $this->enderecoEstado;
    }

    public function setEnderecoEstado(?string $enderecoEstado): self
    {
        $this->enderecoEstado = $enderecoEstado;
        return $this;
    }

    public function getEnderecoCep(): ?string
    {
        return $this->enderecoCep;
    }

    public function setEnderecoCep(?string $enderecoCep): self
    {
        $this->enderecoCep = $enderecoCep;
        return $this;
    }

    public function getCv(): ?string
    {
        return $this->cv;
    }

    public function setCv(?string $cv): self
    {
        $this->cv = $cv;
        return $this;
    }

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(?string $foto): self
    {
        $this->foto = $foto;
        return $this;
    }

    public function getCriacao(): ?string
    {
        return $this->criacao;
    }

    public function setCriacao(?string $criacao): self
    {
        $this->criacao = $criacao;
        return $this;
    }

    public function getModificacao(): ?string
    {
        return $this->modificacao;
    }

    public function setModificacao(?string $modificacao): self
    {
        $this->modificacao = $modificacao;
        return $this;
    }
}
