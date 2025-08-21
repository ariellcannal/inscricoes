<?php
use CANNALInscricoes\Entities\RecebiveisEntity;
use CANNALInscricoes\Entities\OperadorasEntity;

class Recebiveis extends SYS_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->checkLogin();
        $this->load->library('controllers/RecebiveisLib', null, 'recebiveis');
    }

    public function index()
    {
        $xcrud = xcrud_get_instance();
        $this->assets->js('recebiveis.js');

        $xcrud->table('recebiveis');
        $xcrud->table_name('Recebíveis');

        $xcrud->set_var('after_task', 'list');

        $xcrud->label('rec_inscricao', 'Grupo');
        $xcrud->label('rec_valor', 'Valor Bruto');
        $xcrud->label('rec_valorLiquido', 'Valor Líquido');
        $xcrud->label('rec_forma', 'Forma de Pagamento');
        $xcrud->label('rec_dataTransacao', 'Transação');
        $xcrud->label('rec_dataRecebimento', 'Recebimento');
        $xcrud->label('i.ins_grupo', 'Grupo de Estudos');
        $xcrud->label('i.ins_aluno', 'Aluno');
        $xcrud->label('g.grp_nome', 'Grupo');
        $xcrud->label('a.alu_nomeArtistico', 'Aluno');
        $xcrud->label('rec_id', '#');
        $xcrud->label('repassado', 'Repassado');
        $xcrud->label('rec_creditoUtilizado', 'Utilizar Crédito');
        $xcrud->label('rec_parcela', 'Parcela');
        $xcrud->label('rec_estornoValor', 'Estorno: Valor');
        $xcrud->label('rec_estornoData', 'Estorno: Data');
        $xcrud->label('rec_operadora', 'Operadora');
        $xcrud->label('rec_operadora', 'Operadora');
        $xcrud->label('rec_operadoraStatus', 'Status');
        $xcrud->label('rec_operadoraID', 'ID do Recebível');
        $xcrud->label('rec_transacao', 'ID da Transacação');
        $xcrud->label('rec_recebido', 'Recebido?');

        $xcrud->join('rec_inscricao', 'inscricoes', 'ins_id', 'i', true);
        $xcrud->join('i.ins_aluno', 'alunos', 'alu_id', 'a', true);
        $xcrud->join('i.ins_grupo', 'grupos', 'grp_id', 'g', true);

        $xcrud->sum('rec_valor,rec_valorLiquido');

        $xcrud->columns('rec_id,g.grp_nome,a.alu_nomeArtistico,rec_dataTransacao,rec_valor,rec_forma,rec_parcela,rec_valorLiquido,rec_dataRecebimento,rec_estornoValor,rec_operadora,rec_operadoraStatus,rec_operadoraID,rec_transacao,rec_recebido');
        $xcrud->fields('i.ins_grupo,i.ins_aluno,rec_parcela,rec_creditoUtilizado,rec_dataTransacao,rec_valor,rec_valorLiquido,rec_forma,rec_estornoValor,rec_estornoData,rec_operadoraID,rec_operadoraStatus,rec_recebido,rec_operadoraResposta', false);

        $xcrud->disabled('rec_operadoraResposta,rec_operadora,rec_estornoValor,rec_estornoData,rec_transacao');

        $xcrud->before_insert('BI_recebivel', 'recebiveis_helper.php');
        $xcrud->before_update('BU_recebivel', 'recebiveis_helper.php');
        $xcrud->before_remove('BR_recebivel', 'recebiveis_helper.php');

        $xcrud->after_update('AU_recebivel', 'recebiveis_helper.php');
        $xcrud->after_insert('AI_recebivel', 'recebiveis_helper.php');
        $xcrud->after_remove('AR_recebivel', 'recebiveis_helper.php');

        $xcrud->change_type('rec_valor,rec_valorLiquido,rec_estornoValor', 'price', null, array(
            'prefix' => '',
            'separator' => '.',
            'point' => ','
        ));

        $xcrud->relation('rec_operadora', 'operadoras', 'opr_nome', 'opr_nome');
        $xcrud->relation('rec_forma', 'operadoras_formas', 'ofo_forma', 'ofo_forma');
        $xcrud->relation('rec_transacao', 'operadoras_transacoes', 'otr_id', 'otr_operadoraID');
        $xcrud->relation('i.ins_aluno', 'alunos', 'alu_id', 'alu_nomeArtistico');
        $xcrud->relation('i.ins_grupo', 'grupos', 'grp_id', 'grp_nome', 'grp_ativo = 1');
        $xcrud->relation('rec_creditoUtilizado', 'alunos_creditos', 'alc_id', [
            'alc_valorInicial',
            'alc_motivo'
        ], 'IFNULL(alc_valorUtilizado,0) < alc_valorInicial', null, null, ' - ', null, null, 'alc_aluno', 'i.ins_aluno');

        $xcrud->button('#', "Sincronizar recebível com a operadora", 'fas fa-sync', 'btn btn-info sincronizar', array(
            'data-primary' => '{rec_id}'
        ), array(
            array(
                'rec_recebido',
                '=',
                '0'
            )
        ));

        $xcrud->button('#', "Confirmar", 'fas fa-thumbs-up', 'btn btn-info confirmar', array(
            'data-primary' => '{rec_id}'
        ), array(
            array(
                'rec_recebido',
                '=',
                '0'
            ),
            array(
                'g.grp_repasseAtivado',
                '=',
                '1'
            )
        ));

        $xcrud->button('#', "Desconfirmar", 'fas fa-thumbs-down', 'btn btn-info desconfirmar', array(
            'data-primary' => '{rec_id}'
        ), array(
            array(
                'rec_recebido',
                '=',
                '1'
            )
        ));

        $xcrud->highlight_row('rec_recebido', '=', '1', null, 'table-success');

        $filtro['Recebidos'] = 'rec_recebido = 1';
        $filtro['Pendentes'] = 'rec_recebido = 0';
        $xcrud->custom_filter('esquerda', $filtro, 'Pendentes');

        $xcrud->pass_default('rec_dataTransacao', date('Y-m-d H:i:s'));
        $xcrud->pass_default('rec_dataRecebimento', date('Y-m-d'));
        $xcrud->pass_default('i.ins_grupo', $this->session->userdata('last_grupo'));
        $xcrud->pass_default('i.ins_aluno', $this->session->userdata('last_aluno'));
        $xcrud->pass_default('rec_forma', 'PIX / Depósito Direto');

        $xcrud->no_quotes('rec_criacao');
        $xcrud->pass_var('rec_criacao', 'NOW()', 'create');

        $xcrud->unset_print();
        $xcrud->unset_csv();
        $xcrud->unset_edit(true, 'rec_recebido', '=', '1');
        $xcrud->unset_remove(true, 'rec_recebido', '=', '1');

        $xcrud->order_by('rec_dataRecebimento', 'ASC');
        $xcrud->order_by('rec_forma', 'ASC');
        $xcrud->order_by('rec_aluno', 'ASC');

        /* REPASSES */
        $rre = $xcrud->nested_table('Repasses', 'rec_id', 'recebiveis_repasses', 'rre_recebivel');

        $rre->subselect('rep_data', 'SELECT rep_data FROM repasses WHERE rep_id = {rre_repasse}');
        $rre->subselect('rep_efetivado', 'SELECT rep_efetivado FROM repasses WHERE rep_id = {rre_repasse}');

        $rre->table_name('Repasses');
        $rre->label('rre_usuario', 'Usuário');
        $rre->label('rre_valor', 'Valor');
        $rre->label('rre_porcentagemUsuario', '%');
        $rre->label('rep_data', 'Consolidação');
        $rre->label('rep_efetivado', 'PIX');

        $rre->relation('rre_usuario', 'usuarios', 'usr_id', 'usr_nome');

        $rre->columns('rre_usuario, rre_porcentagemUsuario, rre_valor, rep_data, rep_efetivado');

        $rre->sum('rre_valor');

        $rre->change_type('rep_data,rep_efetivado', 'date');
        $rre->change_type('rre_valor', 'price', '', array(
            'decimals' => '2',
            'separator' => '.',
            'point' => ','
        ));

        $rre->unset_print();
        $rre->unset_csv();
        $rre->unset_add();
        $rre->unset_edit();
        $rre->unset_search(true);
        $rre->unset_pagination();
        $rre->unset_limitlist();
        $rre->unset_remove();
        $rre->unset_view();

        $this->vars['conteudo'] = $xcrud->render();
        $this->load->view('index.php', $this->vars);
    }

    function confirmar()
    {
        return $this->recebiveis->confirmar();
    }

    function desconfirmar()
    {
        return $this->recebiveis->desconfirmar();
    }

    function sincronizar($rec_id = null)
    {
        return $this->recebiveis->sincronizar($rec_id);
    }
}

/* End of file recebiveis.php */
/* Location: ./application/controllers/recebiveis.php */