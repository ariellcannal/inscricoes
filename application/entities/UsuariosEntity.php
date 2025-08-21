<?php
namespace CANNALInscricoes\Entities;

class UsuariosEntity extends _Entity
{

    protected string $prefix = 'usr_';

    protected string $table = 'usuarios';

    protected ?int $id = null;

    protected ?string $nome = null;

    protected ?string $senha = null;

    protected ?string $cv = null;

    protected ?string $foto = null;

    protected ?string $email = null;

    protected ?bool $recebeInscricoes = null;

    protected ?bool $coordenador = null;

    protected ?bool $recebeRepasse = null;

    protected ?bool $alertaRepasse = null;

    protected ?string $chavePIX = null;
    
    protected ?string $preferencias = null;

    /**
     * Construtor da classe Usuarios
     */
    public function __construct(?array $array = null)
    {
        if ($array) {
            $this->importArray($array);
        }
        return $this;
    }

    /**
     * Obtém o ID do usuário.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Obtém o nome do usuário.
     *
     * @return string|null
     */
    public function getNome(): ?string
    {
        return $this->nome;
    }

    /**
     * Obtém a senha do usuário.
     *
     * @return string|null
     */
    public function getSenha(): ?string
    {
        return $this->senha;
    }

    /**
     * Obtém o currículo do usuário.
     *
     * @return string|null
     */
    public function getCv(): ?string
    {
        return $this->cv;
    }

    /**
     * Obtém a foto do usuário.
     *
     * @return string|null
     */
    public function getFoto(): ?string
    {
        return $this->foto;
    }

    /**
     * Obtém o email do usuário.
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Verifica se o usuário recebe inscrições.
     *
     * @return bool|null
     */
    public function recebeInscricoes(): ?bool
    {
        return $this->recebeInscricoes;
    }

    /**
     * Verifica se o usuário é coordenador.
     *
     * @return bool|null
     */
    public function isCoordenador(): ?bool
    {
        return $this->coordenador;
    }

    /**
     * Verifica se o usuário recebe repasses.
     *
     * @return bool|null
     */
    public function recebeRepasse(): ?bool
    {
        return $this->recebeRepasse;
    }

    /**
     * Verifica se o usuário recebe alerta de repasse.
     *
     * @return bool|null
     */
    public function alertaRepasse(): ?bool
    {
        return $this->alertaRepasse;
    }
    
    /**
     * Obtém a chave PIX do usuário.
     *
     * @return string|null
     */
    public function getChavePIX(): ?string
    {
        return $this->chavePIX;
    }
    
    /**
     * Obtém as preferencias do usuário
     *
     * @return string|null
     */
    public function getPreferencias(): ?string
    {
        return $this->preferencias;
    }

    /**
     * Define o ID do usuário.
     *
     * @param int|null $id
     * @return self
     */
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Define o nome do usuário.
     *
     * @param string|null $nome
     * @return self
     */
    public function setNome(?string $nome): self
    {
        $this->nome = $nome;
        return $this;
    }

    /**
     * Define a senha do usuário.
     *
     * @param string|null $senha
     * @return self
     */
    public function setSenha(?string $senha): self
    {
        $this->senha = $senha;
        return $this;
    }

    /**
     * Define o currículo do usuário.
     *
     * @param string|null $cv
     * @return self
     */
    public function setCv(?string $cv): self
    {
        $this->cv = $cv;
        return $this;
    }

    /**
     * Define a foto do usuário.
     *
     * @param string|null $foto
     * @return self
     */
    public function setFoto(?string $foto): self
    {
        $this->foto = $foto;
        return $this;
    }

    /**
     * Define o email do usuário.
     *
     * @param string|null $email
     * @return self
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Define se o usuário recebe inscrições.
     *
     * @param bool|null $recebeInscricoes
     * @return self
     */
    public function setRecebeInscricoes(?bool $recebeInscricoes): self
    {
        $this->recebeInscricoes = $recebeInscricoes;
        return $this;
    }

    /**
     * Define se o usuário é coordenador.
     *
     * @param bool|null $coordenador
     * @return self
     */
    public function setCoordenador(?bool $coordenador): self
    {
        $this->coordenador = $coordenador;
        return $this;
    }

    /**
     * Define se o usuário recebe repasses.
     *
     * @param bool|null $recebeRepasse
     * @return self
     */
    public function setRecebeRepasse(?bool $recebeRepasse): self
    {
        $this->recebeRepasse = $recebeRepasse;
        return $this;
    }

    /**
     * Define se o usuário recebe alerta de repasse.
     *
     * @param bool|null $alertaRepasse
     * @return self
     */
    public function setAlertaRepasse(bool|null $alertaRepasse): self
    {
        $this->alertaRepasse = $alertaRepasse;
        return $this;
    }
    
    /**
     * Define a chave PIX do usuário.
     *
     * @param string|null $chavePIX
     * @return self
     */
    public function setChavePIX(?string $chavePIX): self
    {
        $this->chavePIX = $chavePIX;
        return $this;
    }
    
    /**
     * Define as preferencias do usuário
     *
     * @param string|null $preferencias
     * @return self
     */
    public function setPreferencias(?string $preferencias): self
    {
        $this->preferencias = $preferencias;
        return $this;
    }
}
