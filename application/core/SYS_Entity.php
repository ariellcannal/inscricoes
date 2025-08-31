<?php

class SYS_Entity
{

    /**
     * Importa os valores do array para as propriedades da entidade.
     *
     * @param array $array Dados a serem importados
     * @return self
     */
    public function importArray(array $array): self
    {
        foreach ($array as $key => $value) {
            $key = str_replace($this->prefix, '', $key);
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
        return $this;
    }

    /**
     * Exporta as variáveis um array.
     *
     * @param bool $include_null Incluir valores nulos no resultado
     * @return array
     */
    public function toArray(bool $include_null = true): array
    {
        $return = [];
        $reflect = new \ReflectionClass($this);
        foreach ($reflect->getProperties() as $prop) {
            $key = $prop->getName();
            if (! in_array($key, [
                'prefix',
                'table'
            ])) {
                if (! $include_null && $this->$key === null) {
                    continue;
                } else if (!is_null($this->$key) && substr($this->$key, 0, strlen($this->prefix)) === $this->prefix) {
                    $return[$key] = $this->$key;
                } else {
                    $return[$this->prefix . $key] = $this->$key;
                }
            }
        }
        return $return;
    }

    /**
     * Importa os dados de outra entidade ou array.
     *
     * @param self|array $obj Objeto ou array de origem
     * @param bool $include_null Incluir valores nulos na importação
     * @return self
     */
    public function import(self|array $obj, bool $include_null = false): self
    {
        if (! is_array($obj)) {
            $obj = $obj->toArray($include_null);
        }
        $this->importArray($obj);
        return $this;
    }
}

/* End of file SYS_Entity.php */
/* Location: ./application/core/SYS_Entity.php */