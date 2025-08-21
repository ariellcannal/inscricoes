<?php
namespace CANNALInscricoes\Entities;

class GruposDatasEntity extends _Entity
{

    protected $prefix = 'grd_';

    protected $table = 'grupos_datas';

    protected int $grupo = 0;

    protected string $data = '';

    /**
     * Construtor da classe GruposDatas_model.
     */
    public function __construct(?array $array = null)
    {
        if ($array) {
            $this->importArray($array);
        }
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
     * Obtém a data.
     *
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * Define a data.
     *
     * @param string $data
     * @return self
     */
    public function setData(string $data): self
    {
        $this->data = $data;
        return $this;
    }
}
