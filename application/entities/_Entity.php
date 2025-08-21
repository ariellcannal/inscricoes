<?php
namespace CANNALInscricoes\Entities;

class _Entity
{

    /**
     * Exporta as variáveis um array.
     *
     * @return array
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
     * @return array
     */
    public function toArray($include_null = true): array
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

    public function import(self|array $obj, $include_null = false)
    {
        if (! is_array($obj)) {
            $obj = $obj->toArray($include_null);
        }
        $this->importArray($obj);
        return $this;
    }
}

/* End of file _Entity.php */
/* Location: ./applicaion/core/_Entity.php */