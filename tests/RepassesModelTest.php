<?php
// Define BASEPATH constant to satisfy CodeIgniter checks
if (!defined('BASEPATH')) {
    define('BASEPATH', __DIR__ . '/');
}

// Minimal stubs for CI_Model and dependencies
class CI_Model {}
class SYS_Model extends CI_Model {}

require_once __DIR__ . '/../application/models/Repasses_model.php';

// Dummy database to record where calls
class DummyDB {
    public array $queries = [];
    private array $current = [];

    public function select($s, $e = null){ return $this; }
    public function from($t){ return $this; }
    public function join($t, $c, $type = ''){ return $this; }
    public function where($k, $v = null, $e = null){
        $this->current[] = [$k, $v];
        return $this;
    }
    public function get(){
        $this->queries[] = $this->current;
        $this->current = [];
        return new class {
            public function row_array(){
                return [
                    'totalGrupo' => 0,
                    'repassado' => 0,
                    'aRepassar' => 0,
                    'dst_porcentagem' => 0,
                ];
            }
        };
    }
}

// Testable subclass overriding data retrieval
class TestRepassesModel extends Repasses_model {
    public $db;
    public function __construct($db){
        $this->db = $db;
    }
    public function getRepassesPorUsuario($usr_id, $months = null, $retornar_grupos = false, $order = 'ASC'){
        return [
            ['grp_id' => '2 OR 1=1', 'grp_nome' => 'Grupo']
        ];
    }
}

$db = new DummyDB();
$model = new TestRepassesModel($db);
$model->getRelatorioPorUsuario('5 OR 1=1', null);

$expected = [
    [['ins_grupo', 2]],
    [['rre_usuario', 5], ['ins_grupo', 2]],
    [['dst_usuario', 5], ['ins_grupo', 2]],
];

if ($db->queries !== $expected) {
    throw new Exception('Injection test failed: parameters not sanitized');
}

echo "All injection tests passed\n";
