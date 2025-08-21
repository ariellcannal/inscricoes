<?php

class Transacoes extends SYS_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->checkLogin();
        $this->load->library('controllers/TransacoesLib', null, 'transacoes');
    }

    public function index()
    {
        $xcrud = xcrud_get_instance();
        $this->assets->js('transacoes.js');

        $xcrud->table('operadoras_transacoes');
        $xcrud->table_name('Transações');

        $xcrud->set_var('custom_head', '/transacoes/modal_estornos.php');

        $xcrud->set_var('replace_title', 'Transação #{otr_id}');
        
        $xcrud->set_var('after_task', 'list');

        $xcrud->unset_print();
        $xcrud->unset_csv();
        $xcrud->unset_edit();
        $xcrud->unset_remove();
        // $xcrud->duplicate_button(true);

        $xcrud->label('otr_id', '#');
        $xcrud->label('otr_inscricao', 'Inscrição');
        $xcrud->label('otr_forma', 'Forma de Pgto');
        $xcrud->label('otr_tipo', 'Tipo de Pgto');
        $xcrud->label('otr_cartao', 'Cartão');
        $xcrud->label('otr_parcelas', 'Qtd. Parcelas');
        $xcrud->label('otr_valorBruto', 'Valor Bruto');
        $xcrud->label('otr_valorLiquido', 'Valor Líquido');
        $xcrud->label('otr_valorCancelado', 'Valor Cancelado');
        $xcrud->label('otr_dataCancelamento', 'Cancelamento');
        $xcrud->label('otr_dataTransacao', 'Transação');
        $xcrud->label('otr_dataExpiracao', 'Expiração');
        $xcrud->label('otr_pixQrCode', 'QRCode Pix');
        $xcrud->label('otr_pixQrCodeUrl', 'URL QRCode Pix');
        $xcrud->label('otr_confirmada', 'Confirmada?');
        $xcrud->label('otr_descricaoFatura', 'Descrição na Fatura');
        $xcrud->label('otr_operadora', 'Operadora');
        $xcrud->label('otr_operadoraResposta', 'Resposta da Operadora');
        $xcrud->label('otr_operadoraErros', 'Erros');
        $xcrud->label('otr_operadoraStatus', 'Status');
        $xcrud->label('otr_operadoraID', 'ID na Operadora');
        $xcrud->label('otr_operadoraData', 'Data da Resposta');
        $xcrud->label('otr_criacao', 'Criação');
        $xcrud->label('otr_tid', 'TID');
        $xcrud->label('otr_nsu', 'NSU');
        $xcrud->label('otr_authorizationCode', 'Authorization Code');
        $xcrud->label('otr_cardBin', 'Card Bin');
        $xcrud->label('otr_cardLast4', 'Card Last Four');
        $xcrud->label('alu.alu_nomeArtistico', 'Aluno');
        $xcrud->label('grp.grp_nome', 'Grupo');

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
        $xcrud->relation('otr_inscricao', 'inscricoes', 'ins_id', [
            'ins_id',
            'alu_nomeArtistico',
            'grp_nome'
        ], [], false, false, ' - ', $join);
        $xcrud->relation('otr_forma', 'operadoras_formas', 'ofo_forma', 'ofo_forma');
        $xcrud->relation('otr_operadora', 'operadoras', 'opr_nome', 'opr_nome');
        
        $xcrud->after_insert('AI_transacao', 'transacoes_helper.php');

        $xcrud->columns('otr_id,otr_dataTransacao,otr_dataExpiracao,otr_inscricao,otr_forma,otr_parcelas,otr_valorBruto,otr_valorLiquido,otr_dataExpiracao,otr_confirmada,otr_operadora,otr_operadoraStatus,otr_operadoraID');

        $xcrud->fields('otr_id,otr_confirmada,otr_forma,otr_cartao,otr_parcelas,otr_dataTransacao,otr_valorBruto,otr_valorLiquido,otr_dataCancelamento,otr_valorCancelado,otr_operadora,otr_operadoraID,otr_operadoraStatus,otr_operadoraErros,otr_operadoraResposta', false, false, 'view');
        $xcrud->fields('otr_inscricao,otr_operadora,otr_operadoraID', false, false, 'create');

        $xcrud->change_type('otr_valorBruto,otr_valorLiquido', 'price', null, array(
            'prefix' => '',
            'separator' => '.',
            'point' => ','
        ));
        $xcrud->sum('otr_valorBruto,otr_valorLiquido');

        $xcrud->button('#', "Estornar", 'fas fa-hand-holding-usd', 'btn btn-info', [
            'data-bs-target' => '#estornar_{otr_id}',
            'data-bs-toggle' => 'modal'
        ], [
            [
                'otr_operadoraID',
                '!=',
                ''
            ],
            [
                'otr_valorLiquido',
                '>',
                '0'
            ],
            [
                'otr_confirmada',
                '=',
                '1'
            ],
            [
                'otr_operadoraStatus',
                'IN',
                'paid,captured'
            ]
        ], null);
        $xcrud->button('#', "Sincronizar transação com a operadora", 'fas fa-sync', 'btn btn-info sincronizar', [
            'data-primary' => '{otr_id}'
        ]);
        $filtro['Confirmadas'] = 'otr_confirmada = 1';
        $filtro['Não Confirmadas'] = 'otr_confirmada = 0';
        // $xcrud->custom_filter('esquerda', $filtro, 'Não Confirmadas');

        $xcrud->order_by('otr_id', 'DESC');

        /* RECEBÍVEIS */
        $rec = $xcrud->nested_table('Recebiveis', 'otr_id', 'recebiveis', 'rec_transacao');
        $rec->table_name('Recebíveis da Transação');

        $rec->label('rec_id', 'Pgto #');
        $rec->label('rec_parcela', 'Parcela');
        $rec->label('rec_dataTransacao', 'Data Transação');
        $rec->label('rec_forma', 'Forma');
        $rec->label('rec_valor', 'Bruto');
        $rec->label('rec_valorLiquido', 'Líquido');
        $rec->label('rec_dataRecebimento', 'Recebimento');
        $rec->label('rec_operadoraID', 'ID na operadora');
        $rec->label('grp.grp_nome', 'Grupo');
        $rec->label('alu.alu_nomeArtistico', 'Aluno');

        $rec->join('rec_inscricao', 'inscricoes', 'ins_id', 'ins');
        $rec->join('ins.ins_aluno', 'alunos', 'alu_id', 'alu');
        $rec->join('ins.ins_grupo', 'grupos', 'grp_id', 'grp');

        $rec->columns('rec_id,rec_forma,rec_dataTransacao,rec_dataRecebimento,rec_parcela,rec_valor,rec_valorLiquido,rec_operadoraID');
        $rec->fields('rec_id,rec_valor,rec_valorLiquido,rec_creditoUtilizado,rec_forma,rec_dataTransacao,rec_dataRecebimento,rec_recebido');

        $rec->change_type('rec_valor,rec_valorLiquido', 'price', null, array(
            'prefix' => 'R$ ',
            'separator' => '.',
            'point' => ','
        ));
        $rec->sum('rec_valor,rec_valorLiquido');

        $rec->unset_print();
        $rec->unset_csv();
        $rec->unset_edit();
        $rec->unset_add();
        $rec->unset_search();
        $rec->unset_pagination();
        $rec->unset_limitlist();

        /* REPASSES */
        $rre = $rec->nested_table('Repasses', 'rec_id', 'recebiveis_repasses', 'rre_recebivel');

        $rre->table_name('Repasses');
        $rre->label('rre_usuario', 'Usuário');
        $rre->label('rre_valor', 'Valor');
        $rre->label('repasses.rep_data', 'Consolidação');
        $rre->label('repasses.rep_efetivado', 'PIX');

        $rre->relation('rre_usuario', 'usuarios', 'usr_id', 'usr_nome');

        $rre->join('rre_repasse', 'repasses', 'rep_id');

        $rre->columns('rre_usuario, rre_valor, repasses.rep_data, repasses.rep_efetivado');

        $rre->sum('rre_valor');

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

    function estornar()
    {
        return $this->transacoes->estornar();
    }

    function cancelar()
    {
        return $this->transacoes->cancelar();
    }

    function sincronizar($otr_id = null)
    {
        return $this->transacoes->sincronizar($otr_id);
    }

    function sincronizaTransacoesVencidas()
    {
        return $this->transacoes->sincronizaTransacoesVencidas();
    }
}

/* End of file Transacoes.php */
/* Location: ./application/controllers/Transacoes.php */