<?php

class SYS_Model extends CI_Model
{

    public $filtro = array();

    public $labels = array();

    public $customTables = array();

    public $customTypes = array();

    protected string $table;

    protected string $prefix;

    function __construct()
    {
        parent::__construct();
        
        $this->checkFunctions();
        
        if ($timeZone = config_item('time_zone_db')) {
            $this->db->query("SET time_zone = '{$timeZone}'");
        }
    }
    
    function checkFunctions()
    {
        $this->db->query('DROP FUNCTION IF EXISTS EXTRACT_NUMBERS;');
        $this->db->query("CREATE FUNCTION EXTRACT_NUMBERS(field VARCHAR(50)) RETURNS BIGINT
                 NO SQL
                 BEGIN
            DECLARE ls INTEGER;
            DECLARE i INTEGER;
            DECLARE str varchar(100);
            SET ls  = (select length(field));
            SET i   = 1;
            SET str = \"\";
            WHILE i <= ls DO
                IF ((substring(field, i,1) REGEXP '[0-9]') <> 0) THEN
                    SET str = CONCAT(str, convert(substring(field, i,1) USING UTF8));
                END IF;
                SET i = i  + 1;
            END WHILE;
            RETURN str;
        END;");
        
        $this->db->query('DROP FUNCTION IF EXISTS DATA_RECEBIMENTO;');
        $this->db->query("CREATE FUNCTION DATA_RECEBIMENTO(
            dataTransacao DATE,
            dataRecebimento DATE
        )
        RETURNS DATE
        DETERMINISTIC
        BEGIN
            RETURN CASE 
                WHEN DATEDIFF(dataRecebimento, dataTransacao) >= 7 THEN 
                    dataRecebimento
                ELSE 
                    DATE_ADD(dataTransacao, INTERVAL 7 DAY)
            END;
        END;");
    }

    function remover($reg_id)
    {
        if (! isset($this->table) || ! isset($this->prefix))
            return false;
        $this->db->delete($this->table, array(
            $this->prefix . 'id' => $reg_id
        ));
        return $this->db->affected_rows();
    }

    function getRow($id)
    {
        if (! isset($this->table) || ! isset($this->prefix)) {
            return false;
        }
        $r = $this->db->get_where($this->table, array(
            $this->prefix . 'id' => $id
        ));
        return $r->row_array();
    }

    function update($id, $set)
    {
        if (! $id) {
            return null;
        }
        $this->db->update($this->table, $set, array(
            $this->prefix . 'id' => $id
        ));
        return $this->db->affected_rows();
    }

    function inserirFeriados($feriados)
    {
        $this->db->insert_batch('feriados', $feriados);
    }

    function getFeriados($tipo, $dataLimite)
    {
        $this->db->where('fer_data <=', $dataLimite);
        $this->db->where_in('fer_tipoCodigo', $tipo);
        foreach ($this->db->get('feriados')->result_array() as $row) {
            $r[] = $row['fer_data'];
        }
        return $r;
    }

    function getNow()
    {
        $r = $this->db->query("SELECT NOW()");
        var_dump($r->row_array());
    }
}

/* End of file SYS_Model.php */
/* Location: ./applicaion/libraries/SYS_Model.php */