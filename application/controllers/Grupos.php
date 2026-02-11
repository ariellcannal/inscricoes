<?php

class Grupos extends SYS_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library('controllers/GruposLib', null, 'grupos');
    }

    public function index()
    {
        $this->checkLogin();
        $this->assets->js('inscricoes_admin.js');

        $this->load->model('operadoras_model');
        $this->load->model('grupos_model');

        $xcrud = xcrud_get_instance();
        $xcrud->table('grupos');
        $xcrud->table_name('Grupos de Estudos');

        $xcrud->set_var('after_task', 'edit');
        $xcrud->set_var('replace_title', '{grp_nome}');

        $xcrud->label('grp_id', 'ID');
        $xcrud->label('grp_nome', 'Nome Interno');
        $xcrud->label('grp_dataAulaAberta', 'Aula Aberta');
        $xcrud->label('grp_dataInicio', 'Início');
        $xcrud->label('grp_dataFim', 'Fim');
        $xcrud->label('grp_encontros', 'Qtd. Encontros');
        $xcrud->label('grp_descricao', 'Descrição Curta');
        $xcrud->label('grp_descricaoDetalhes', 'Descrição Detalhada');
        $xcrud->label('grp_coordenadores', 'Coordenadores');
        $xcrud->label('grp_imagem', 'Imagem');
        $xcrud->label('grp_inscricoesAbertas', 'Inscrições Abertas');
        $xcrud->label('grp_nomePublico', 'Nome Público');
        $xcrud->label('grp_ativo', 'Ativo?');
        $xcrud->label('grp_repasseAtivado', 'Repasse Ativado?');
        $xcrud->label('grp_horario', 'Horário');
        $xcrud->label('grp_dias', 'Dias');
        $xcrud->label('grp_diaSemana', 'Dias da Semana');
        $xcrud->label('grp_horaInicio', 'Hora Início');
        $xcrud->label('grp_horaFim', 'Hora Fim');
        $xcrud->label('grp_processoSeletivo', 'Processo Seletivo?');
        $xcrud->label('grp_linkWhats', 'Link do Grupo no WhatsApp');
        $xcrud->label('grp_emailFaleConosco', 'E-mail Fale Conosco');
        $xcrud->label('grp_idFaturaCartao', 'Identificação na Fatura');
        $xcrud->label('grp_drtObrigatorio', 'DRT Obrigatório');
        $xcrud->label('grp_exibeSite', 'Exibir no Site');
        $xcrud->label('grp_operadora', 'Operadora');
        $xcrud->label('grp_maximoInscricoes', 'Máximo de Inscrições');
        $xcrud->label('grp_slug', 'Slug');
        $xcrud->label('grp_pixel', 'Meta Pixel');
        $xcrud->label('grp_analytics', 'Google Analytics');

        $xcrud->columns('grp_id,grp_nome,grp_maximoInscricoes,grp_encontros,grp_dataInicio,grp_dataFim,grp_diaSemana,grp_horaInicio,grp_horaFim,grp_inscricoesAbertas,grp_exibeSite,grp_operadora,grp_ativo');
        $xcrud->fields('grp_imagem,grp_nome,grp_nomePublico,grp_slug,grp_dataAulaAberta,grp_dataInicio,grp_dataFim,grp_diaSemana,grp_horaInicio,grp_horaFim,grp_maximoInscricoes,grp_exibeSite,grp_repasseAtivado,grp_ativo,grp_inscricoesAbertas,grp_processoSeletivo,grp_drtObrigatorio,grp_encontros,grp_idFaturaCartao,grp_coordenadores,grp_descricao,grp_descricaoDetalhes,grp_linkWhats,grp_emailFaleConosco,grp_operadora,grp_pixel,grp_analytics', false, 'Dados Principais');

        $semana[1] = 'Segundas';
        $semana[2] = 'Terças';
        $semana[3] = 'Quartas';
        $semana[4] = 'Quintas';
        $semana[5] = 'Sextas';
        $semana[6] = 'Sábados';
        $semana[0] = 'Domingos';
        $xcrud->change_type('grp_diaSemana', 'multiselect', null, $semana);

        $xcrud->change_type('grp_linkWhats', 'text');
        $xcrud->change_type('grp_emailFaleConosco', 'email');

        $xcrud->change_type('grp_imagem', 'image', '', array(
            'width' => 1600,
            'height' => 1200,
            'crop' => false,
            'path' => $_SERVER['DOCUMENT_ROOT'] . DIR_IMAGEM_GRUPOS
        ));

        $xcrud->pass_default('grp_operadora', $this->operadoras_model->getDefault()['opr_nome'], 'create');
        $xcrud->relation('grp_operadora', 'operadoras', 'opr_nome', 'opr_nome');

        $xcrud->relation('grp_coordenadores', 'usuarios', 'usr_id', 'usr_nome', 'usr_coordenador = 1', true, ',');

        $xcrud->button(site_url() . 'inscricao/{grp_slug}', "Formulário de Inscrição", 'fas fa-file-import', 'btn btn-default btn-inverse btn-sm btn-info', [
            'target' => 'Inscricao'
        ]);
        $xcrud->button(site_url('/grupos/csv/{grp_id}'), "CSV", 'fas fa-file-csv', 'btn btn-default btn-inverse btn-sm btn-info');
        $xcrud->button(site_url('/grupos/lista_presenca/{grp_id}'), "Lista de Presença", 'fas fa-th-list', 'btn btn-default btn-inverse btn-sm btn-info');

        $filtro['Ativos'] = 'grp_ativo = 1';
        $filtro['Inativos'] = 'grp_ativo = 0';
        $xcrud->custom_filter('esquerda', $filtro, 'Ativos');

        $xcrud->no_quotes('grp_atualizacao');
        $xcrud->pass_var('grp_atualizacao', 'NOW()');

        $xcrud->unset_print();
        $xcrud->unset_csv();
        
        $xcrud->before_update('BU_grupo', 'grupos_helper.php');
        $xcrud->before_insert('BI_grupo', 'grupos_helper.php');
        $xcrud->after_clone('AC_grupo', 'grupos_helper.php');

        $xcrud->duplicate_button(true);

        $xcrud->order_by('grp_nome', 'ASC');
        $xcrud->no_editor('grp_descricao,grp_descricaoDetalhes');

        /*
         * INSCRIÇÕES
         */
        $ins = $xcrud->nested_table('Inscrições', 'grp_id', 'inscricoes', 'ins_grupo');
        $ins->table_name('Inscrições');

        $ins->set_var('after_task', 'list');

        $ins->set_var('custom_head', '/inscricao/modal_whatsAppMsg.php');

        $ins->set_var('replace_title', '#{ins_id}');

        $ins->subselect('totalRecebido', 'SELECT IFNULL(SUM(otr_valorBruto),0) FROM operadoras_transacoes WHERE otr_inscricao = {ins_id} AND otr_confirmada = 1');
        $ins->subselect('transacaoConfirmada', 'SELECT IFNULL(otr_confirmada,0) FROM operadoras_transacoes WHERE otr_inscricao = {ins_id} ORDER BY otr_confirmada DESC LIMIT 1');

        $ins->label('ins_grupo', 'Grupo');
        $ins->label('ins_aluno', 'Aluno');
        $ins->label('ins_status', 'Status');
        $ins->label('ins_data', 'Data');
        $ins->label('ins_valorModulo', 'Valor Módulo');
        $ins->label('ins_valorDesconto', 'Desconto');
        $ins->label('ins_valorDevido', 'Saldo');
        $ins->label('ins_valorTotalPago', 'Pago');
        $ins->label('ins_motivoDesconto', 'Motivo do Desconto');
        $ins->label('ins_valorDevido', 'Valor Devido');
        $ins->label('ins_comentario', 'Comentário');
        $ins->label('ins_IP', 'IP Inscrição');
        $ins->label('ins_forma', 'Forma');
        $ins->label('alunos.alu_nome', 'Nome Completo');
        $ins->label('alunos.alu_nomeArtistico', 'Nome Artístico');
        $ins->label('alunos.alu_nascimento', 'Data de Nascimento');
        $ins->label('alunos.alu_drt', 'DRT');
        $ins->label('alunos.alu_email', 'E-mail');
        $ins->label('alunos.alu_cpf', 'CPF');
        $ins->label('alunos.alu_cv', 'Currículo');
        $ins->label('alunos.alu_celular', 'Celular');
        $ins->label('grupos.whatsGrupo', 'Convidar P/ Whats');
        $ins->label('ins_user', 'Usuário');
        $ins->label('totalRecebido', 'Total Recebido');
        $ins->label('ins_id', 'ID');
        $ins->label('ins_tempData', 'Temp');

        $ins->join('ins_aluno', 'alunos', 'alu_id', false, true);
        $ins->join('ins_grupo', 'grupos', 'grp_id', false, true);

        $ins->validation_required('ins_forma', 1);

        $ativos = $this->grupos_model->getAtivos();
        if ($ativos) {
            foreach ($ativos as $g) {
                $g_ativos[$g['grp_nome']] = 'ins_grupo = ' . $g['grp_id'];
                $ids[] = $g['grp_id'];
            }
            $filtro['Grupos Ativos'] = 'ins_grupo IN (' . implode(',', $ids) . ')';
            $ins->custom_filter('esquerda', array_merge($filtro, $g_ativos), 'Grupos Ativos');
        }

        $ins->disabled('ins_id,ins_IP,ins_data,ins_tempData');
        $ins->disabled('ins_grupo,ins_aluno', 'edit');

        $ins->highlight_row('ins_valorModulo', '=', '0', null, 'table-info');
        $ins->highlight_row('ins_valorDevido', '>', '0', null, 'table-warning');
        $ins->highlight_row('ins_valorDevido', '<=', '0', null, 'table-success');
        $ins->highlight_row('ins_status', '=', 'Cancelada', null, 'table-danger');

        $ins->columns('ins_id,ins_status,ins_grupo,ins_data,alunos.alu_nomeArtistico,alunos.alu_email,alunos.alu_celular,ins_forma,ins_valorDesconto,ins_motivoDesconto,ins_valorModulo,ins_valorTotalPago,ins_valorDevido');
        $ins->sum('ins_valorTotalPago,ins_valorModulo,ins_valorDevido');

        $ins->fields('ins_grupo,ins_forma,ins_aluno,ins_valorDesconto,ins_motivoDesconto,ins_comentario', null, null, 'create');
        $ins->fields('ins_id,ins_grupo,ins_aluno,ins_status,ins_grupo,ins_forma,ins_valorDesconto,ins_motivoDesconto,ins_comentario,ins_IP,ins_data,ins_tempData', null, null, 'edit');

        $ins->custom_button('#', 'Mensagem', 'btn-icon fab fa-whatsapp', 'btn btn-sm btn-info', [
            'data-bs-toggle' => 'modal',
            'data-bs-target' => '#whatsAppMsg'
        ]);

        $ins->create_action('reenviar_confirmacao', 'reenviar_confirmacao', 'inscricoes_helper.php', 'fas fa-check-double');
        $ins->button('#', "Reenviar Confirmação", 'fas fa-check-double', 'btn btn-info xcrud-action', [
            'data-primary' => '{ins_id}',
            'data-task' => 'action',
            'data-action' => 'reenviar_confirmacao'
        ], [
            [
                'ins_status',
                '=',
                'Confirmada'
            ]
        ]);
        $ins->create_action('whatsAppMsg', 'whatsAppMsg', 'inscricoes_helper.php', 'fab fa-whatsapp');
        $ins->button('#', "Enviar Mensagem", 'fab fa-whatsapp', 'btn btn-info xcrud-action whatsAppMsgRow', [
            'data-primary' => '{ins_id}',
            'data-task' => 'action',
            'data-action' => 'whatsAppMsg',
            'data-msg' => ''
        ]);

        $ins->create_action('solicitar_aprovacao_admin', 'solicitar_aprovacao_admin', 'inscricoes_helper.php', 'fa fa-envelope');
        $ins->button('#', "Solicitar Aprovação", 'fa fa-envelope', 'btn btn-info xcrud-action', [
            'data-primary' => '{ins_id}',
            'data-task' => 'action',
            'data-action' => 'solicitar_aprovacao_admin'
        ], [
            [
                'ins_status',
                '=',
                'Confirmada'
            ],
            [
                'ins_aprovada',
                '=',
                ''
            ],
            [
                'grupos.grp_processoSeletivo',
                '=',
                '1'
            ]
        ]);

        $ins->create_action('aprovar_admin', 'aprovar_admin', 'inscricoes_helper.php', 'fas fa-thumbs-up');
        $ins->button('#', "Aprovar", 'fas fa-thumbs-up', 'btn btn-info xcrud-action', [
            'data-primary' => '{ins_id}',
            'data-task' => 'action',
            'data-action' => 'aprovar_admin',
            'data-confirm' => 'Deseja aprovar {alunos.alu_nomeArtistico}, processar o pagamento e enviar a notificação para o e-mail do aluno?'
        ], [
            [
                'ins_status',
                '=',
                'Confirmada'
            ],
            [
                'ins_aprovada',
                '=',
                ''
            ],
            [
                'grupos.grp_processoSeletivo',
                '=',
                '1'
            ]
        ]);

        $ins->create_action('enviar_declaracao', 'enviar_declaracao', 'inscricoes_helper.php', 'fa fa-envelope');
        $ins->button('#', "Enviar declaração", 'fas fa-file', 'btn btn-default btn-sm btn-info xcrud-action', [
            'data-primary' => '{ins_id}',
            'data-task' => 'action',
            'data-action' => 'enviar_declaracao',
            'data-confirm' => 'Confirma o envio dessa declaração para {alunos.alu_nomeArtistico}'
        ], [
            [
                'grupos.grp_dataFim',
                '<=',
                date('Y-m-d')
            ]
        ]);

        $ins->create_action('sincronizarInscricao', 'sincronizarInscricao', 'inscricoes_helper.php', 'fas fa-sync');
        $ins->button('#', "Atualizar Totais", 'fas fa-sync', 'btn btn-info xcrud-action', [
            'data-primary' => '{ins_id}',
            'data-task' => 'action',
            'data-action' => 'sincronizarInscricao'
        ]);

        $ins->relation('ins_grupo', 'grupos', 'grp_id', 'grp_nome', 'grp_ativo=1');
        $ins->relation('ins_forma', 'grupos_formas', 'gfp_id', [
            'gfp_valorTotal',
            'gfp_comentario'
        ], null, null, null, ' ', null, null, 'gfp_grupo', 'ins_grupo');
        $ins->relation('ins_aluno', 'alunos', 'alu_id', 'alu_nomeArtistico');

        $ins->change_type('ins_valorTotalPago,ins_valorModulo,ins_valorDevido,ins_valorDesconto', 'price', null, array(
            'decimals' => '2',
            'separator' => '.',
            'point' => ','
        ));
        $ins->change_type('ins_motivoDesconto,ins_comentario', 'text');
        $ins->change_type('ins_tempData', 'textarea');

        $ins->unset_print();
        $ins->unset_csv();
        $ins->unset_view();
        $ins->unset_numbers(false);

        $ins->order_by('ins_grupo', 'ASC');
        $ins->order_by('ins_valorModulo', 'DESC');
        $ins->order_by('ins_valorDevido', 'DESC');
        $ins->order_by('ins_valorTotalPago', 'ASC');
        $ins->order_by('alunos.alu_nomeArtistico', 'ASC');

        $ins->no_quotes('ins_data');
        $ins->pass_var('ins_data', 'NOW()', 'create');
        $ins->pass_var('ins_user', $this->session->userdata('usr_id'), 'create');
        $ins->pass_var('ins_IP', $_SERVER['REMOTE_ADDR'], 'create');

        $ins->pass_default('ins_grupo', $this->session->userdata('last_grupo'));
        $ins->pass_default('ins_aluno', $this->session->userdata('last_aluno'));

        $ins->before_insert('BI_inscricao_admin', 'inscricoes_helper.php');
        $ins->after_insert('AI_inscricao_admin', 'inscricoes_helper.php');
        $ins->after_update('AU_inscricao_admin', 'inscricoes_helper.php');

        $ins->replace_remove('INS_replace_remove', 'inscricoes_helper.php');

        $ins->mask('alunos.alu_celular', '(00) 00000-0000');
        $ins->mask('alunos.alu_cpf', '000.000.000-000');
        $ins->validation_pattern('alunos.alu_email', 'email');
        $ins->validation_required('alunos.alu_cpf', 1);

        /*
         * RECEBÍVEIS
         */
        $rec = $ins->nested_table('Recebíveis', 'ins_id', 'recebiveis', 'rec_inscricao');

        $rec->set_var('replace_title', 'Recebível #{rec_id}');

        $rec->table_name('Recebiveis');
        $rec->label('rec_valor', 'Valor Bruto');
        $rec->label('rec_valorLiquido', 'Valor Líquido');
        $rec->label('rec_creditoUtilizado', 'Crédito Utilizado');
        $rec->label('rec_forma', 'Forma de Pgto');
        $rec->label('rec_dataTransacao', 'Data');
        $rec->label('rec_dataRecebimento', 'Recebimento');
        $rec->label('rec_recebido', 'Recebível Recebido');
        $rec->label('rec_id', '#');
        $rec->label('rec_parcela', 'Parcela');
        $rec->label('rec_estornoValor', 'Total Estornado');
        $rec->label('rec_operadoraID', 'Operadora ID');
        $rec->label('rec_operadoraStatus', 'Operadora Status');

        $rec->columns('rec_id,rec_dataTransacao,rec_creditoUtilizado,rec_forma,rec_valor,rec_valorLiquido,rec_estornoValor,rec_dataRecebimento,rec_operadoraID,rec_operadoraStatus');
        $rec->fields('rec_id,rec_valor,rec_valorLiquido,rec_creditoUtilizado,rec_forma,rec_dataTransacao,rec_dataRecebimento,rec_recebido');

        $rec->change_type('rec_valor,rec_valorLiquido', 'price', '', array(
            'decimals' => '2',
            'separator' => '.',
            'point' => ','
        ));

        $rec->relation('rec_creditoUtilizado', 'alunos_creditos', 'alc_id', array(
            'alc_id',
            'alc_motivo'
        ), null, null, null, ' - ', null, null);

        $rec->unset_print();
        $rec->unset_csv();
        $rec->unset_add();
        $rec->unset_edit();
        $rec->unset_search();
        $rec->unset_pagination();
        $rec->unset_limitlist();
        $rec->unset_remove();

        /*
         * REPASSES
         */
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

        /*
         * DISTRIBUÇÕES
         */
        $dist = $xcrud->nested_table('Distribuição de Repasse do Grupo', 'grp_id', 'grupos_distribuicao', 'dst_grupo');
        $dist->table_name('Distribuição de Repasse');

        $dist->subselect('totalGrupo', 'SELECT SUM(rec_valorLiquido) FROM recebiveis JOIN inscricoes ON ins_id = rec_inscricao WHERE ins_grupo = {dst_grupo}');
        $dist->subselect('totalRepassado', 'SELECT SUM(rre_valor) FROM recebiveis_repasses JOIN recebiveis ON rre_recebivel = rec_id JOIN inscricoes ON ins_id = rec_inscricao WHERE rre_usuario = {dst_usuario} AND ins_grupo = {dst_grupo}');
        $dist->subselect('totalARepassar', '(SELECT SUM(rec_valorLiquido*({dst_porcentagem}/100)) FROM recebiveis JOIN inscricoes ON ins_id = rec_inscricao WHERE ins_grupo = {dst_grupo})-(SELECT SUM(rre_valor) FROM recebiveis_repasses JOIN recebiveis ON rre_recebivel = rec_id JOIN inscricoes ON ins_id = rec_inscricao WHERE rre_usuario = {dst_usuario} AND ins_grupo = {dst_grupo})');

        $dist->label('dst_usuario', 'Usuário');
        $dist->label('dst_porcentagem', 'Porcentagem');
        $dist->label('totalGrupo', 'Faturamento Líquido');
        $dist->label('totalRepassado', 'Repassado');
        $dist->label('totalARepassar', 'A Repassar');

        $dist->set_var('after_task', 'create');

        $dist->where('dst_forma IS NULL');

        $dist->columns('dst_usuario,dst_porcentagem,totalGrupo,totalRepassado,totalARepassar');
        $dist->fields('dst_usuario,dst_porcentagem');
        $dist->relation('dst_usuario', 'usuarios', 'usr_id', 'usr_nome', 'usr_recebeRepasse = 1');
        $dist->sum('dst_porcentagem,totalRepassado,totalARepassar');

        $dist->change_type('totalGrupo,totalRepassado,totalARepassar', 'price', null, array(
            'prefix' => 'R$ ',
            'separator' => '.',
            'point' => ','
        ));

        $dist->unset_print();
        $dist->unset_csv();
        $dist->unset_view();
        $dist->unset_remove(true, 'totalRepassado', '>', '0');
        $dist->unset_search();
        $dist->unset_pagination();
        $dist->unset_limitlist();

        /*
         * FORMAS DE PAGAMENTO
         */
        $gfp = $xcrud->nested_table('Opções de Pagamento', 'grp_id', 'grupos_formas', 'gfp_grupo');
        $gfp->table_name('Opções de Pagamento');
        $gfp->label('gfp_valorTotal', 'Valor Total');
        $gfp->label('gfp_parcelas', 'Parcelamento Máximo');
        $gfp->label('gfp_ordem', 'Ordem de Exibição');
        $gfp->label('gfp_aceitaCartao', 'Aceita Cartão de Crédito?');
        $gfp->label('gfp_publico', 'Público?');
        $gfp->label('gfp_linkOculto', 'Link Oculto');
        $gfp->label('gfp_linkOcultoValidade', 'Validade do Link Oculto');
        $gfp->label('gfp_comentario', 'Comentário');
        $gfp->label('gfp_descricao', 'Descrição');

        $gfp->join('gfp_grupo', 'grupos', 'grp_id', false, true);

        $gfp->set_var('after_task', 'list');

        $gfp->columns('gfp_comentario,gfp_descricao,gfp_aceitaCartao,gfp_parcelas,gfp_ordem,gfp_publico,gfp_linkOculto,gfp_linkOcultoValidade');
        $gfp->fields('gfp_parcelas,gfp_valorTotal,gfp_comentario,gfp_aceitaCartao,gfp_ordem,gfp_publico,gfp_linkOculto,gfp_linkOcultoValidade');

        $gfp->change_type('gfp_valorTotal', 'price', null, array(
            'prefix' => 'R$ ',
            'separator' => '.',
            'point' => ','
        ));

        $gfp->button(site_url('/inscricao/{grupos.grp_slug}?utm_content={gfp_linkOculto}'), "Link Oculto", 'fas fa-file-import', 'btn btn-default btn-inverse btn-sm btn-info', [
            'target' => 'LinkOculto'
        ], array(
            array(
                'gfp_linkOculto',
                '!=',
                ''
            )
        ));

        $gfp->before_insert('BI_gfp', 'grupos_helper.php');
        $gfp->before_update('BU_gfp', 'grupos_helper.php');

        $gfp->unset_print();
        $gfp->unset_csv();
        $gfp->unset_view();
        $gfp->unset_search();
        $gfp->unset_pagination();
        $gfp->unset_limitlist();

        /*
         * DISTRIBUÇÃO DA FORMA DE PAGAMENTO
         */
        $dist_gfp = $gfp->nested_table('Distribuição de Repasse', 'gfp_id', 'grupos_distribuicao', 'dst_forma');
        $dist_gfp->table_name('Distribuição de Repasse da Forma');

        $dist_gfp->subselect('totalGrupo', 'SELECT SUM(rec_valorLiquido) FROM recebiveis JOIN inscricoes ON ins_id = rec_inscricao WHERE ins_grupo = {dst_grupo}');
        $dist_gfp->subselect('totalRepassado', 'SELECT SUM(rre_valor) FROM recebiveis_repasses JOIN recebiveis ON rre_recebivel = rec_id JOIN inscricoes ON ins_id = rec_inscricao WHERE rre_usuario = {dst_usuario} AND ins_grupo = {dst_grupo}');
        $dist_gfp->subselect('totalARepassar', '(SELECT SUM(rec_valorLiquido*({dst_porcentagem}/100)) FROM recebiveis JOIN inscricoes ON ins_id = rec_inscricao WHERE ins_grupo = {dst_grupo})-(SELECT SUM(rre_valor) FROM recebiveis_repasses JOIN recebiveis ON rre_recebivel = rec_id JOIN inscricoes ON ins_id = rec_inscricao WHERE rre_usuario = {dst_usuario} AND ins_grupo = {dst_grupo})');

        $dist_gfp->label('dst_usuario', 'Usuário');
        $dist_gfp->label('dst_porcentagem', 'Porcentagem');
        $dist_gfp->label('totalGrupo', 'Faturamento Líquido');
        $dist_gfp->label('totalRepassado', 'Repassado');
        $dist_gfp->label('totalARepassar', 'A Repassar');

        $dist_gfp->set_var('after_task', 'create');

        $dist_gfp->columns('dst_usuario,dst_porcentagem,totalGrupo,totalRepassado,totalARepassar');
        $dist_gfp->fields('dst_usuario,dst_porcentagem');
        $dist_gfp->relation('dst_usuario', 'usuarios', 'usr_id', 'usr_nome', 'usr_recebeRepasse = 1');
        $dist_gfp->sum('dst_porcentagem,totalRepassado,totalARepassar');

        $dist_gfp->change_type('totalGrupo,totalRepassado,totalARepassar', 'price', null, array(
            'prefix' => 'R$ ',
            'separator' => '.',
            'point' => ','
        ));

        $dist_gfp->before_insert('callback_set_grupo', 'grupos_helper.php');
        $dist_gfp->before_update('callback_set_grupo', 'grupos_helper.php');

        $dist_gfp->unset_print();
        $dist_gfp->unset_csv();
        $dist_gfp->unset_view();
        $dist_gfp->unset_remove(true, 'totalRepassado', '>', '0');
        $dist_gfp->unset_search();
        $dist_gfp->unset_pagination();
        $dist_gfp->unset_limitlist();

        /*
         * TAXAS ADICIONAIS
         */
        $gtx = $xcrud->nested_table('Taxas Adicionais', 'grp_id', 'grupos_taxas_adicionais', 'gtx_grupo');
        $gtx->table_name('Taxas Adicionais');
        $gtx->label('gtx_valorTotal', 'Valor Total');
        $gtx->label('gtx_parcelas', 'Parcelamento Máximo');
        $gtx->label('gtx_ordem', 'Ordem de Exibição');
        $gtx->label('gtx_aceitaCartao', 'Aceita Cartão de Crédito?');
        $gtx->label('gtx_descricao', 'Descrição');
        $gtx->label('gtx_primeiraParcela', 'Cobrar na Primeira Parcela/PIX?');

        $gtx->join('gtx_grupo', 'grupos', 'grp_id', false, true);

        $gtx->set_var('after_task', 'list');

        $gtx->columns('gtx_descricao,gtx_primeiraParcela,gtx_aceitaCartao,gtx_parcelas');
        $gtx->fields('gtx_descricao,gtx_parcelas,gtx_valorTotal,gtx_aceitaCartao,gtx_primeiraParcela');

        $gtx->change_type('gtx_valorTotal', 'price', null, array(
            'prefix' => 'R$ ',
            'separator' => '.',
            'point' => ','
        ));

        $gtx->before_insert('BI_gtx', 'grupos_helper.php');
        $gtx->before_update('BU_gtx', 'grupos_helper.php');

        $gtx->unset_print();
        $gtx->unset_csv();
        $gtx->unset_view();
        $gtx->unset_search();
        $gtx->unset_pagination();
        $gtx->unset_limitlist();

        $this->vars['conteudo'] = $xcrud->render();
        $this->load->view('index.php', $this->vars);
    }

    public function ws()
    {
        return $this->grupos->ws();
    }

    public function csv($grp_id)
    {
        $this->checkLogin();
        return $this->grupos->csv($grp_id);
    }

    public function lista_presenca($grp_id)
    {
        $this->checkLogin();
        return $this->grupos->lista_presenca($grp_id);
    }

    public function presenca()
    {
        return $this->grupos->presenca();
    }
}

/* End of file Grupos.php */
/* Location: ./application/controllers/Grupos.php */