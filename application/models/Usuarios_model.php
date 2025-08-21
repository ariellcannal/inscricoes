<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Usuarios_model extends SYS_Model
{

    protected string $prefix = 'usr_';

    protected string $table = 'usuarios';

    /**
     * Construtor da classe Usuarios
     */
    public function __construct()
    {
        parent::__construct();
        return $this;
    }

    function criptPass($pass)
    {
        return crypt($pass, $this->config->item('encryption_key'));
    }

    function checkLogin($usuario, $senha)
    {
        $this->db->where('usr_email', $usuario);
        $this->db->where('usr_senha', $this->criptPass($senha));
        $r = $this->db->get('usuarios');
        //echo $this->db->last_query();exit;
        if ($r->num_rows()) {
            $id = $r->row_array();
        } else
            $id = false;

        return $id;
    }

    function getUsuario($id)
    {
        $r = $this->db->get_where('usuarios', array(
            'usr_id' => $id
        ));
        return $r->row_array();
    }

    function update($usr_id, $data)
    {
        return $this->db->update('usuarios', $data, array(
            'usr_id' => $usr_id
        ));
    }

    function updateSenha($id, $nova_senha)
    {
        $set['usr_senha'] = $this->criptPass($nova_senha);
        $r = $this->db->update('usuarios', $set, array(
            'usr_id' => $id
        ), 1);
        return $set;
    }

    function senhaTemporaria($tamanho = 10)
    {
        $maius = "ABCDEFGHIJKLMNOPQRSTUWXYZ";
        $minus = "abcdefghijklmnopqrstuwxyz";
        $numer = "0123456789";
        $outros = "$@!?-";
        $caracteres = $maius . $minus . $numer . $outros;
        $max = strlen($caracteres) - 1;
        $senha = null;
        for ($i = 0; $i < $tamanho; $i ++) {
            $t = mt_rand(0, $max);
            $senha .= $caracteres[$t];
        }
        return $senha;
    }

    function setSenhaTemporaria($id)
    {
        $temp = $this->senhaTemporaria();
        $set['usr_senha'] = $this->criptPass($temp);
        $set['usr_senhaTemporaria'] = 1;
        $r = $this->db->update('usuarios', $set, array(
            'usr_id' => $id
        ), 1);
        return $temp;
    }

    function getPreferencia(int $usr_id, string $preferencia)
    {
        $usr_preferencias = $this->getPreferencias($usr_id);
        if ($usr_preferencias) {
            $usr_preferencias = json_decode($usr_preferencias, true);
            if (array_key_exists($preferencia, $usr_preferencias)) {
                return $usr_preferencias[$preferencia];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function getPreferencias(int $usr_id)
    {
        $this->db->select('usr_preferencias');
        $usr = $this->db->get_where('usuarios', [
            'usr_id' => $usr_id
        ])->row_array();
        if (count($usr)) {
            return $usr['usr_preferencias'];
        } else {
            return $usr;
        }
    }

    function setPreferencia(int $usr_id, string $nome, string $valor)
    {
        if ($usr_preferencias = $this->getPreferencias($usr_id)) {
            $usr_preferencias = json_decode($usr_preferencias, true);
        } else {
            $usr_preferencias = [];
        }
        $usr_preferencias = array_merge($usr_preferencias, [
            $nome => $valor
        ]);
        $this->updatePreferencias($usr_id, $usr_preferencias);
    }

    function updatePreferencias(int $usr_id, array $usr_preferencias)
    {
        $usr_preferencias = json_encode($usr_preferencias);
        $this->update($usr_id, [
            'usr_preferencias' => $usr_preferencias
        ]);
    }
}
