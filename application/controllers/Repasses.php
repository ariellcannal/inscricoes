<?php

class Repasses extends SYS_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->checkLogin();
        $this->load->library('controllers/RepassesLib', null, 'repasses');
    }

    public function index()
    {
        $this->assets->js('repasses.js');
        $this->assets->js('https://cdn.jsdelivr.net/gh/linways/table-to-excel@v1.0.4/dist/tableToExcel.js', true);

        $this->load->model('repasses_model');
        $repassesEmRetencao = $this->repasses_model->getRepassesEmRetencao(true);

        $xcrud = xcrud_get_instance();
        $xcrud->table('repasses');
        $xcrud->table_name('Repasses');

        if ($repassesEmRetencao) {
            $xcrud->set_var('footer_note', number_format($repassesEmRetencao, 2, ',', '.') . " com repasse retido.");
        }
        $xcrud->set_var('after_task', 'list');

        $xcrud->set_lang('add', 'Consolidar');

        $xcrud->subselect('desc_pix', 'SELECT CONCAT("GRUPOS ",usr_nome)');

        $xcrud->label('rep_id', '#');
        $xcrud->label('rep_usuario', 'Nome');
        $xcrud->label('rep_valor', 'Valor');
        $xcrud->label('rep_data', 'Data');
        $xcrud->label('rep_efetivado', 'Efetivado');
        $xcrud->label('rep_comentario', 'Comentário');
        $xcrud->label('usr.usr_chavePIX', 'Chave PIX');
        $xcrud->label('desc_pix', 'Desc. PIX');
        $xcrud->label('mesclar', 'Mesclar com');

        $xcrud->join('rep_usuario', 'usuarios', 'usr_id', 'usr', true);

        $xcrud->columns('rep_id,usr.usr_chavePIX,rep_valor,rep_data,desc_pix,rep_efetivado,rep_comentario');
        $xcrud->fields('rep_data,rep_usuario,rep_valor,rep_efetivado,rep_comentario');
        $xcrud->fields('rep_comentario', null, null, 'edit');

        $filtro['Efetivados'] = 'rep_efetivado IS NOT NULL';
        $filtro['Não Efetivados'] = 'rep_efetivado IS NULL';
        $xcrud->custom_filter('esquerda', $filtro, 'Não Efetivados');

        $xcrud->change_type('rep_valor', 'price', null, array(
            'prefix' => '',
            'separator' => '',
            'point' => ','
        ));

        $xcrud->mass_merge('mergeRepasse', 'repasses_helper.php');

        $xcrud->custom_button('#', 'Gerar Lote', 'btn-icon fas fa-file-csv', 'btn btn-sm btn-info gerar_lote');

        $xcrud->button(site_url() . 'repasses/relatorio/{rep_usuario}', "Relatório de Recebiveis", 'fas fa-dollar-sign', 'btn btn-default btn-inverse btn-sm btn-info', [
            'target' => '_blank'
        ]);

        $xcrud->button('#', "Efetivado", 'fas fa-thumbs-up', 'btn btn-info efetivar', array(
            'data-primary' => '{rep_id}',
            'data-confirm' => 'Tem certeza?'
        ), array(
            array(
                'rep_efetivado',
                '=',
                null
            )
        ));
        $xcrud->button('#', "Remover efetivação", 'fas fa-thumbs-down', 'btn btn-danger desefetivar', array(
            'data-primary' => '{rep_id}',
            'data-confirm' => 'Tem certeza?'
        ), array(
            array(
                'rep_efetivado',
                '!=',
                null
            )
        ));

        $xcrud->relation('rep_usuario', 'usuarios', 'usr_id', 'usr_nome');
        $xcrud->sum('rep_valor');

        $xcrud->column_callback('rep_data', 'repasse_data', 'repasses_helper.php');

        $xcrud->unset_print();
        $xcrud->unset_csv();

        $xcrud->disabled('rep_data,rep_usuario,rep_valor,rep_efetivado');

        $xcrud->order_by('rep_data', 'DESC');
        $xcrud->order_by('rep_nome', 'DESC');

        /* REPASSES */
        $rep = $xcrud->nested_table('Recebiveis', 'rep_id', 'recebiveis_repasses', 'rre_repasse');
        $rep->table_name('Recebiveis no Repasse');

        $rep->label('rec.rec_id', 'REC#');
        $rep->label('rec.rec_dataTransacao', 'Data Transação');
        $rep->label('rec.rec_forma', 'Forma');
        $rep->label('rec.rec_valor', 'Pagamento');
        $rep->label('rre_valor', 'Repasse');
        $rep->label('rre_porcentagemUsuario', '%');
        $rep->label('rec.rec_parcela', 'Parcela');
        $rep->label('rec.rec_dataRecebimento', 'Recebimento');
        $rep->label('grp.grp_nome', 'Grupo');
        $rep->label('alu.alu_nomeArtistico', 'Aluno');

        $rep->join('rre_recebivel', 'recebiveis', 'rec_id', 'rec');
        $rep->join('rec.rec_inscricao', 'inscricoes', 'ins_id', 'ins');
        $rep->join('ins.ins_aluno', 'alunos', 'alu_id', 'alu');
        $rep->join('ins.ins_grupo', 'grupos', 'grp_id', 'grp');

        $rep->columns('rec.rec_id,grp.grp_nome,alu.alu_nomeArtistico,rec.rec_dataTransacao,rec.rec_forma,rec.rec_parcela,rec.rec_valor,rre_porcentagemUsuario,rre_valor,rec.rec_dataRecebimento');

        $rep->change_type('rec.rec_valor,rre_valor', 'price', null, array(
            'prefix' => 'R$ ',
            'separator' => '.',
            'point' => ','
        ));
        $rep->sum('rec.rec_valor,rre_valor');

        $rep->unset_print();
        $rep->unset_csv();
        $rep->unset_edit();
        $rep->unset_add();
        $rep->unset_view();
        $rep->unset_search();
        $rep->unset_pagination();
        $rep->unset_limitlist();

        $this->vars['conteudo'] = $xcrud->render();
        $this->load->view('index.php', $this->vars);
    }

    function consolidar()
    {
        return $this->repasses->consolidar();
    }

    function efetivar()
    {
        return $this->repasses->efetivar();
    }

    function desefetivar()
    {
        return $this->repasses->desefetivar();
    }

    function relatorio($usr_id, $return = false)
    {
        return $this->repasses->relatorio($usr_id, $return);
    }
}

/* End of file Repasses.php */
/* Location: ./application/controllers/Repasses.php */