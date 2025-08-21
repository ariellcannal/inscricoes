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
        $this->load->model('operadoras_model');

        $xcrud = xcrud_get_instance();
        $xcrud->table('grupos');
        $xcrud->table_name('Grupos de Estudos');

        $xcrud->set_var('after_task', 'list');
        $xcrud->set_var('replace_title', '{grp_nome}');

        $xcrud->label('grp_id', 'ID');
        $xcrud->label('grp_nome', 'Nome Interno');
        $xcrud->label('grp_dataAulaAberta', 'Aula Aberta');
        $xcrud->label('grp_dataInicio', 'Início');
        $xcrud->label('grp_dataFim', 'Fim');
        $xcrud->label('grp_encontros', 'Qtd. Encontros');
        $xcrud->label('grp_descricao', 'Descrição');
        $xcrud->label('grp_descricaoDetalhes', 'Detalhes');
        $xcrud->label('grp_coordenadores', 'Coordenadores');
        $xcrud->label('grp_valor', 'Valor');
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
        $xcrud->label('grp_valorDescricao', 'Descrição do Valor');
        $xcrud->label('grp_processoSeletivo', 'Processo Seletivo?');
        $xcrud->label('grp_linkWhats', 'Link do Grupo no WhatsApp');
        $xcrud->label('grp_idFaturaCartao', 'Identificação na Fatura');
        $xcrud->label('grp_drtObrigatorio', 'DRT Obrigatório');
        $xcrud->label('grp_exibeSite', 'Exibir no Site');
        $xcrud->label('grp_operadora', 'Operadora');
        $xcrud->label('grp_maximoInscricoes', 'Máximo de Inscrições');
        $xcrud->label('grp_slug', 'Slug');
        $xcrud->label('grp_pixel', 'Meta Pixel');
        $xcrud->label('grp_analytics', 'Google Analytics');

        $xcrud->columns('grp_id,grp_nome,grp_valor,grp_maximoInscricoes,grp_encontros,grp_dataInicio,grp_dataFim,grp_diaSemana,grp_horaInicio,grp_horaFim,grp_inscricoesAbertas,grp_exibeSite,grp_operadora,grp_ativo');
        $xcrud->fields('grp_imagem,grp_nome,grp_nomePublico,grp_slug,grp_dataAulaAberta,grp_dataInicio,grp_dataFim,grp_diaSemana,grp_horaInicio,grp_horaFim,grp_maximoInscricoes,grp_exibeSite,grp_repasseAtivado,grp_ativo,grp_inscricoesAbertas,grp_processoSeletivo,grp_drtObrigatorio,grp_encontros,grp_valor,grp_valorDescricao,grp_idFaturaCartao,grp_coordenadores,grp_descricao,grp_descricaoDetalhes,grp_linkWhats,grp_operadora,grp_pixel,grp_analytics', false, 'Dados Principais');

        $semana[1] = 'Segundas';
        $semana[2] = 'Terças';
        $semana[3] = 'Quartas';
        $semana[4] = 'Quintas';
        $semana[5] = 'Sextas';
        $semana[6] = 'Sábados';
        $semana[0] = 'Domingos';
        $xcrud->change_type('grp_diaSemana', 'multiselect', null, $semana);

        $xcrud->change_type('grp_linkWhats', 'text');

        $xcrud->change_type('grp_valor', 'price', null, array(
            'prefix' => 'R$ ',
            'separator' => '.',
            'point' => ','
        ));
        $xcrud->change_type('grp_imagem', 'image', '', array(
            'width' => 1600,
            'height' => 1200,
            'crop' => false,
            'path' => $_SERVER['DOCUMENT_ROOT'] .DIR_IMAGEM_GRUPOS
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
        $xcrud->unset_view();

        $xcrud->before_update('BU_grupo', 'grupos_helper.php');
        $xcrud->before_insert('BI_grupo', 'grupos_helper.php');
        $xcrud->after_clone('AC_grupo', 'grupos_helper.php');

        $xcrud->duplicate_button(true);

        $xcrud->order_by('grp_nome', 'ASC');
        $xcrud->no_editor('grp_valorDescricao,grp_descricao,grp_descricaoDetalhes');

        $dist = $xcrud->nested_table('Distribuição de Repasse', 'grp_id', 'grupos_distribuicao', 'dst_grupo');
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

        $gfp->columns('gfp_descricao,gfp_aceitaCartao,gfp_parcelas,gfp_ordem,gfp_publico,gfp_linkOculto,gfp_linkOcultoValidade');
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