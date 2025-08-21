<?php

class Alunos extends SYS_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library('controllers/AlunosLib', null, 'alunos');
    }

    public function index()
    {
        $this->checkLogin();
        $this->assets->js('consultaCEP.js');
        $this->assets->js('alunos.js');

        $xcrud = xcrud_get_instance();
        $xcrud->table('alunos');
        $xcrud->table_name('Alunos');

        $xcrud->set_var('after_task', 'list');

        $xcrud->label('alu_id', '#');
        $xcrud->label('alu_nome', 'Nome Completo');
        $xcrud->label('alu_nomeArtistico', 'Nome Artístico');
        $xcrud->label('alu_cpf', 'CPF');
        $xcrud->label('alu_nascimento', 'Data de Nascimento');
        $xcrud->label('alu_drt', 'DRT');
        $xcrud->label('alu_email', 'E-mail');
        $xcrud->label('alu_celular', 'Celular');
        $xcrud->label('alu_cv', 'CV');
        $xcrud->label('alu_foto', 'Foto');
        $xcrud->label('alu_criacao', 'Criação');
        $xcrud->label('alu_modificacao', 'Modificação');
        $xcrud->label('mesclar', 'Mesclar Com');
        $xcrud->label('alu_endereco', 'Endereço');
        $xcrud->label('alu_enderecoNumero', 'Número');
        $xcrud->label('alu_enderecoComplemento', 'Complemento');
        $xcrud->label('alu_enderecoBairro', 'Bairro');
        $xcrud->label('alu_enderecoCidade', 'Cidade');
        $xcrud->label('alu_enderecoEstado', 'Estado');
        $xcrud->label('alu_enderecoCep', 'CEP');

        $xcrud->columns('alu_id,alu_foto,alu_nomeArtistico,alu_nome,alu_email,alu_celular,alu_cpf,alu_nascimento,alu_drt,alu_cv');
        $xcrud->fields('alu_nome,alu_nomeArtistico,alu_cpf,alu_email,alu_celular,alu_drt,alu_nascimento', null, 'Dados Principais');
        $xcrud->fields('alu_enderecoCep,alu_endereco,alu_enderecoNumero,alu_enderecoComplemento,alu_enderecoBairro,alu_enderecoCidade,alu_enderecoEstado', null, 'Endereço');
        $xcrud->fields('alu_id,alu_criacao,alu_modificacao', null, 'Detalhes', 'edit');

        $xcrud->disabled('alu_id,alu_criacao,alu_modificacao');

        $xcrud->change_type('alu_foto', 'image', '', array(
            'width' => 200,
            'height' => 200,
            'crop' => true,
            'path' => $_SERVER['DOCUMENT_ROOT'] .DIR_IMAGEM_ALUNOS
        ));

        $xcrud->button('https://api.whatsapp.com/send?phone=55{alu_celular}&msg=Olá', "WhatsApp", 'fab fa-whatsapp', 'btn btn-info', [
            'target' => 'TAPAwhats'
        ]);

        $xcrud->custom_button('#', 'Enviar Formulário', 'btn-icon fab fa-wpforms', 'btn btn-sm btn-info formulario', [
            'data-msg' => 'Por favor, faça o seu cadastro utilizando o link: ' . site_url('/alunos/cadastrar')
        ]);
        $xcrud->button('#', "Solicitar Atualização de Cadastro", 'fab fa-wpforms', 'btn btn-info formulario', [
            'data-primary' => '{alu_id}',
            'data-tel' => '{alu_celular}',
            'data-msg' => 'Por favor, atualize o seu cadastro utilizando o link: ' . site_url('/alunos/atualizar')
        ]);

        $xcrud->mask('alu_celular', '(00) 00000-0000');
        $xcrud->mask('alu_cpf', '000.000.000-00');
        $xcrud->validation_pattern('alu_email', 'email');
        $xcrud->validation_required('alu_cpf', 1);

        $xcrud->mass_merge('mergeAluno','alunos_helper.php');

        $xcrud->after_insert('AI_aluno', 'alunos_helper.php');
        $xcrud->after_update('AU_aluno', 'alunos_helper.php');
        
        $xcrud->no_quotes('alu_criacao,alu_modificacao');
        $xcrud->pass_var('alu_criacao', 'NOW()', 'create');
        $xcrud->pass_var('alu_modificacao', 'NOW()', 'edit');

        $xcrud->no_editor('alu_cv');

        $xcrud->mask('alu_enderecoCep', '00000-000');
        $xcrud->set_attr('alu_endereco', array(
            'consulta-cep' => 'endereco'
        ));
        $xcrud->set_attr('alu_enderecoNumero', array(
            'consulta-cep' => 'numero'
        ));
        $xcrud->set_attr('alu_enderecoComplemento', array(
            'consulta-cep' => 'complemento'
        ));
        $xcrud->set_attr('alu_enderecoCidade', array(
            'consulta-cep' => 'cidade'
        ));
        $xcrud->set_attr('alu_enderecoEstado', array(
            'consulta-cep' => 'estado'
        ));
        $xcrud->set_attr('alu_enderecoBairro', array(
            'consulta-cep' => 'bairro'
        ));
        $xcrud->set_attr('alu_enderecoCep', array(
            'consulta-cep' => 'cep',
            'onblur' => 'consultaCEP(this)'
        ));
        $xcrud->unset_print();
        $xcrud->unset_csv();
        $xcrud->unset_view();
        $xcrud->unset_print();
        $xcrud->unset_csv();

        $xcrud->order_by('alu_nome', 'ASC');

        /* CRÉDITOS */
        $alc = $xcrud->nested_table('Créditos', 'alu_id', 'alunos_creditos', 'alc_aluno');

        $alc->table_name('Créditos');

        $alc->set_var('after_task', 'list');
        $alc->subselect('saldo', '{alc_valorInicial}-{alc_valorUtilizado}');

        $alc->label('alc_valorInicial', 'Valor do Crédito');
        $alc->label('alc_valorUtilizado', 'Valor Utilizado');
        $alc->label('alc_motivo', 'Motivo');
        $alc->label('alc_inscricao', 'Referente à Inscrição');
        $alc->label('saldo', 'Saldo');

        $alc->columns('alc_inscricao,alc_valorInicial,alc_motivo,alc_valorUtilizado,saldo');
        $alc->fields('alc_inscricao,alc_valorInicial,alc_motivo');
        $alc->change_type('alc_valorInicial,alc_valorUtilizado,saldo', 'price', null, array(
            'prefix' => 'R$ ',
            'separator' => '.',
            'point' => ','
        ));

        $join = [
            [
                'lfield' => 'ins_aluno',
                'table' => 'alunos',
                'rfield' => 'alu_id'
            ],
            [
                'lfield' => 'ins_grupo',
                'table' => 'grupos',
                'rfield' => 'grp_id'
            ]
        ];
        $alc->relation('alc_inscricao', 'inscricoes', 'ins_id', [
            'ins_id',
            'alu_nomeArtistico',
            'grp_nome'
        ], [], false, false, ' - ', $join);

        $alc->no_editor('alc_motivo');

        $alc->unset_search();
        $alc->unset_print();
        $alc->unset_csv();
        $alc->unset_edit(true, 'alc_valorUtilizado', '>', '0');
        $alc->unset_remove(true, 'alc_valorUtilizado', '>', '0');

        $utl = $alc->nested_table('Utilizações', 'alc_id', 'recebiveis', 'rec_creditoUtilizado');
        $utl->table_name('Utilizações');

        $utl->label('rec_dataTransacao', 'Data');
        $utl->label('rec_valor', 'Valor');
        $utl->label('g.grp_nome', 'Grupo');
        $utl->label('g.grp_dataInicio', 'Início');
        $utl->label('g.grp_dataFim', 'Fim');

        $utl->columns('rec_dataTransacao,rec_valor,g.grp_nome,g.grp_dataInicio,g.grp_dataFim');
        $utl->join('rec_inscricao', 'inscricoes', 'ins_id', 'i', true);
        $utl->join('i.ins_grupo', 'grupos', 'grp_id', 'g', true);

        $utl->change_type('rec_valor', 'price', null, array(
            'prefix' => 'R$ ',
            'separator' => '.',
            'point' => ','
        ));

        $utl->unset_add();
        $utl->unset_edit();
        $utl->unset_view();
        $utl->unset_remove();
        $utl->unset_search();
        $utl->unset_pagination();
        $utl->unset_print();
        $utl->unset_csv();
        
        $ins = $xcrud->nested_table('Inscrições', 'alu_id', 'inscricoes', 'ins_aluno');
        
        $ins->unset_add();
        $ins->unset_edit();
        $ins->unset_view();
        $ins->unset_remove();
        $ins->unset_search();
        $ins->unset_pagination();
        $ins->unset_print();
        $ins->unset_csv();
        
        $ins->columns('ins_id,ins_status,ins_grupo,ins_data,ins_forma,ins_valorModulo,ins_valorTotalPago,ins_valorDevido');
        $ins->relation('ins_grupo', 'grupos', 'grp_id', 'grp_nome', 'grp_ativo=1');
        $ins->relation('ins_forma', 'grupos_formas', 'gfp_id', 'gfp_descricao', null, null, null, null, null, null, 'gfp_grupo', 'ins_grupo');
        $ins->order_by('ins_id','DESC');

        $this->vars['conteudo'] = $xcrud->render();
        $this->load->view('index.php', $this->vars);
    }

    function presenca()
    {
        $xcrud = xcrud_get_instance();
        $xcrud->table('presenca');
        $xcrud->table_name('Presença');

        $xcrud->set_var('after_task', 'create');

        $xcrud->label('prs_grupo', 'Grupo de Estudos');
        $xcrud->label('prs_aluno', 'Aluno');
        $xcrud->label('prs_data', 'Data Registro');
        $xcrud->label('prs_dataAula', 'Data Aula');
        $xcrud->label('prs_criador', 'Criador');

        $xcrud->columns('prs_aluno,prs_grupo,prs_dataAula,prs_criador');
        $xcrud->fields('prs_aluno,prs_dataAula,prs_grupo');
        $xcrud->relation('prs_grupo', 'grupos', 'grp_id', 'grp_nome', 'grp_ativo = 1');
        $xcrud->relation('prs_aluno', 'alunos', 'alu_id', 'alu_nomeArtistico');
        $xcrud->relation('prs_criador', 'usuarios', 'usr_id', 'usr_nome');

        $xcrud->no_quotes('prs_data,prs_dataAula');
        $xcrud->pass_var('prs_data', 'NOW()');
        $xcrud->pass_var('prs_criador', $this->session->userdata('usr_id'), 'create');

        $xcrud->after_insert('AI_presenca', 'alunos_helper.php');
        $xcrud->after_update('AU_presenca', 'alunos_helper.php');

        $xcrud->pass_default('prs_grupo', $this->session->userdata('last_grupo'));
        $xcrud->pass_default('prs_dataAula', $this->session->userdata('last_dataAula'));

        $xcrud->order_by('prs_dataAula', 'DESC');
        $xcrud->order_by('prs_aluno', 'DESC');

        $xcrud->unset_print();
        $xcrud->unset_csv();
        $this->vars['conteudo'] = $xcrud->render();
        $this->load->view('index.php', $this->vars);
    }

    public function check_cpf()
    {
        return $this->alunos->check_cpf();
    }

    public function atualizar($token)
    {
        return $this->_formulario($token, 'atualizar');
    }

    public function cadastrar($token)
    {
        return $this->_formulario($token, 'cadastrar');
    }

    public function _formulario($token, $modo = 'cadastrar')
    {
        $token = explode(':', base64_decode($token));
        $alu_id = $token[1];
        $token = $token[0];

        $this->assets->js('consultaCEP.js');

        $xcrud = xcrud_get_instance();
        $xcrud->table('alunos');

        $xcrud->set_var('custom_head', '/alunos/header_aluno.php');
        $xcrud->set_var('replace_title', 'Atualizar {alu_nomeArtistico}');

        $xcrud->label('alu_id', '#');
        $xcrud->label('alu_nome', 'Nome Completo');
        $xcrud->label('alu_nomeArtistico', 'Nome Artístico');
        $xcrud->label('alu_cpf', 'CPF');
        $xcrud->label('alu_nascimento', 'Data de Nascimento');
        $xcrud->label('alu_drt', 'DRT');
        $xcrud->label('alu_email', 'E-mail');
        $xcrud->label('alu_celular', 'Celular');
        $xcrud->label('alu_cv', 'CV');
        $xcrud->label('alu_foto', 'Foto');
        $xcrud->label('alu_criacao', 'Criação');
        $xcrud->label('alu_modificacao', 'Modificação');
        $xcrud->label('mesclar', 'Mesclar Com');
        $xcrud->label('alu_endereco', 'Endereço');
        $xcrud->label('alu_enderecoNumero', 'Número');
        $xcrud->label('alu_enderecoComplemento', 'Complemento');
        $xcrud->label('alu_enderecoBairro', 'Bairro');
        $xcrud->label('alu_enderecoCidade', 'Cidade');
        $xcrud->label('alu_enderecoEstado', 'Estado');
        $xcrud->label('alu_enderecoCep', 'CEP');

        $xcrud->fields('alu_cpf,alu_nome,alu_nomeArtistico,alu_email,alu_celular,alu_drt,alu_nascimento', null, 'Dados Principais');
        $xcrud->fields('alu_enderecoCep,alu_endereco,alu_enderecoNumero,alu_enderecoComplemento,alu_enderecoBairro,alu_enderecoCidade,alu_enderecoEstado', null, 'Endereço');

        $xcrud->change_type('alu_foto', 'image', '', array(
            'width' => 200,
            'height' => 200,
            'crop' => true,
            'path' => $_SERVER['DOCUMENT_ROOT'] .DIR_IMAGEM_ALUNOS
        ));

        $xcrud->mask('alu_celular', '(00) 00000-0000');
        $xcrud->mask('alu_cpf', '000.000.000-00');
        $xcrud->validation_pattern('alu_email', 'email');
        $xcrud->validation_required('alu_cpf', 1);

        $xcrud->no_quotes('alu_criacao,alu_modificacao');
        $xcrud->pass_var('alu_criacao', 'NOW()', 'create');
        $xcrud->pass_var('alu_modificacao', 'NOW()', 'edit');

        $xcrud->no_editor('alu_cv');

        $xcrud->mask('alu_enderecoCep', '00000-000');

        $xcrud->set_attr('alu_endereco', array(
            'consulta-cep' => 'endereco'
        ));
        $xcrud->set_attr('alu_enderecoNumero', array(
            'consulta-cep' => 'numero'
        ));
        $xcrud->set_attr('alu_enderecoComplemento', array(
            'consulta-cep' => 'complemento'
        ));
        $xcrud->set_attr('alu_enderecoCidade', array(
            'consulta-cep' => 'cidade'
        ));
        $xcrud->set_attr('alu_enderecoEstado', array(
            'consulta-cep' => 'estado'
        ));
        $xcrud->set_attr('alu_enderecoBairro', array(
            'consulta-cep' => 'bairro'
        ));
        $xcrud->set_attr('alu_enderecoCep', array(
            'consulta-cep' => 'cep',
            'onblur' => 'consultaCEP(this)'
        ));

        $xcrud->unset_print();
        $xcrud->unset_csv();
        $xcrud->unset_list();
        $xcrud->unset_print();
        $xcrud->unset_csv();

        $xcrud->set_var('after_task', 'edit');
        switch ($modo) {
            case 'cadastrar':
                $xcrud->table_name('Cadastro de Alunos');
                $this->vars['conteudo'] = $xcrud->render('create');
                $this->load->view('alunos/formulario.php', $this->vars);
                break;

            case 'atualizar':
                $xcrud->table_name('Atualização de Cadastro');
                $xcrud->unset_add();
                $this->vars['conteudo'] = $xcrud->render('edit', $alu_id);
                $this->load->view('alunos/formulario.php', $this->vars);
                break;
        }
    }
}

/* End of file Alunos.php */
/* Location: ./application/controllers/Alunos.php */