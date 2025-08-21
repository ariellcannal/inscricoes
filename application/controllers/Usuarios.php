<?php

class Usuarios extends SYS_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->checkLogin();
    }

    public function index()
    {
        $xcrud = xcrud_get_instance();
        $xcrud->table('usuarios');
        $xcrud->table_name('Usuários');

        $xcrud->set_var('after_task', 'list');

        $xcrud->label('usr_id', 'ID');
        $xcrud->label('usr_nome', 'Nome');
        $xcrud->label('usr_cv', 'CV');
        $xcrud->label('usr_foto', 'Foto');
        $xcrud->label('usr_coordenador', 'Coordenador?');
        $xcrud->label('usr_recebeRepasse', 'Recebe Repasses?');
        $xcrud->label('usr_chavePIX', 'Chave PIX');
        $xcrud->label('usr_recebeInscricoes', 'Notificação de Inscrição');
        $xcrud->label('usr_alertaRepasse', 'Alerta Repasse?');
        $xcrud->label('usr_email', 'E-mail');

        $xcrud->columns('usr_foto,usr_nome,usr_cv,usr_recebeInscricoes,usr_email');
        $xcrud->fields('usr_foto,usr_nome,usr_cv,usr_coordenador,usr_recebeRepasse,usr_chavePIX,usr_alertaRepasse,usr_recebeInscricoes,usr_email');

        $xcrud->change_type('usr_foto', 'image', '', array(
            'width' => 200,
            'height' => 200,
            'crop' => true,
            'path' => $_SERVER['DOCUMENT_ROOT'] .DIR_IMAGEM_USUARIOS
        ));

        $xcrud->no_editor('usr_cv');

        $xcrud->unset_print();
        $xcrud->unset_csv();
        $xcrud->unset_view();

        $xcrud->order_by('usr_nome', 'ASC');

        $this->vars['conteudo'] = $xcrud->render();
        $this->load->view('index.php', $this->vars);
    }
}

/* End of file Usuarios.php */
/* Location: ./application/controllers/Usuarios.php */