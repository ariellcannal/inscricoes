<?php
namespace CANNALInscricoes\Entities;

class GruposDistribuicaoEntity extends _Entity
{

    protected $prefix = 'dst_';

    protected $table = 'grupos_distribuicao';

    protected int $id = 0;

    protected int $grupo = 0;

    protected int $usuario = 0;

    protected int $porcentagem = 0;

    /**
     * Construtor da classe GruposDistribuicao_model.
     */
    public function __construct(?array $array = null)
    {
        if ($array) {
            $this->importArray($array);
        }
        return $this;
    }

    /**
     * Obtém o ID da distribuição.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Define o ID da distribuição.
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
     * Obtém o ID do usuário.
     *
     * @return int
     */
    public function getUsuario(): int
    {
        return $this->usuario;
    }

    /**
     * Define o ID do usuário.
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
     * Obtém a porcentagem.
     *
     * @return int
     */
    public function getPorcentagem(): int
    {
        return $this->porcentagem;
    }

    /**
     * Define a porcentagem.
     *
     * @param int $porcentagem
     * @return self
     */
    public function setPorcentagem(int $porcentagem): self
    {
        $this->porcentagem = $porcentagem;
        return $this;
    }
}
