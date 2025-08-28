<?php

class Inscricoes extends SYS_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library('controllers/InscricoesLib', null, 'inscricoes');
    }

    public function index()
    {
        $this->checkLogin();
        $this->assets->js('inscricoes_admin.js');

        $this->load->model('grupos_model');
        $xcrud = xcrud_get_instance();
        $xcrud->table('inscricoes');
        $xcrud->table_name('Inscrições');

        $xcrud->set_var('after_task', 'list');

        $xcrud->set_var('custom_head', '/inscricao/modal_whatsAppMsg.php');

        $xcrud->set_var('replace_title', '#{ins_id}');

        $xcrud->subselect('totalRecebido', 'SELECT IFNULL(SUM(otr_valorBruto),0) FROM operadoras_transacoes WHERE otr_inscricao = {ins_id} AND otr_confirmada = 1');
        $xcrud->subselect('transacaoConfirmada', 'SELECT IFNULL(otr_confirmada,0) FROM operadoras_transacoes WHERE otr_inscricao = {ins_id} ORDER BY otr_confirmada DESC LIMIT 1');

        $xcrud->label('ins_grupo', 'Grupo');
        $xcrud->label('ins_aluno', 'Aluno');
        $xcrud->label('ins_status', 'Status');
        $xcrud->label('ins_data', 'Data');
        $xcrud->label('ins_valorModulo', 'Valor Módulo');
        $xcrud->label('ins_valorDesconto', 'Desconto');
        $xcrud->label('ins_valorDevido', 'Saldo');
        $xcrud->label('ins_valorTotalPago', 'Pago');
        $xcrud->label('ins_motivoDesconto', 'Motivo do Desconto');
        $xcrud->label('ins_valorDevido', 'Valor Devido');
        $xcrud->label('ins_comentario', 'Comentário');
        $xcrud->label('ins_IP', 'IP Inscrição');
        $xcrud->label('ins_forma', 'Forma');
        $xcrud->label('alunos.alu_nome', 'Nome Completo');
        $xcrud->label('alunos.alu_nomeArtistico', 'Nome Artístico');
        $xcrud->label('alunos.alu_nascimento', 'Data de Nascimento');
        $xcrud->label('alunos.alu_drt', 'DRT');
        $xcrud->label('alunos.alu_email', 'E-mail');
        $xcrud->label('alunos.alu_cpf', 'CPF');
        $xcrud->label('alunos.alu_cv', 'Currículo');
        $xcrud->label('alunos.alu_celular', 'Celular');
        $xcrud->label('grupos.whatsGrupo', 'Convidar P/ Whats');
        $xcrud->label('ins_user', 'Usuário');
        $xcrud->label('totalRecebido', 'Total Recebido');
        $xcrud->label('ins_id', 'ID');
        $xcrud->label('ins_tempData', 'Temp');

        $xcrud->join('ins_aluno', 'alunos', 'alu_id', false, true);
        $xcrud->join('ins_grupo', 'grupos', 'grp_id', false, true);

        $xcrud->validation_required('ins_forma', 1);

        $ativos = $this->grupos_model->getAtivos();
        if ($ativos) {
            foreach ($ativos as $g) {
                $g_ativos[$g['grp_nome']] = 'ins_grupo = ' . $g['grp_id'];
                $ids[] = $g['grp_id'];
            }
            $filtro['Grupos Ativos'] = 'ins_grupo IN (' . implode(',', $ids) . ')';
            $xcrud->custom_filter('esquerda', array_merge($filtro, $g_ativos), 'Grupos Ativos');
        }

        $xcrud->disabled('ins_id,ins_IP,ins_data,ins_tempData');
        $xcrud->disabled('ins_grupo,ins_aluno', 'edit');

        $xcrud->highlight_row('ins_valorModulo', '=', '0', null, 'table-info');
        $xcrud->highlight_row('ins_valorDevido', '>', '0', null, 'table-warning');
        $xcrud->highlight_row('ins_valorDevido', '<=', '0', null, 'table-success');
        $xcrud->highlight_row('ins_status', '=', 'Cancelada', null, 'table-danger');

        $xcrud->columns('ins_id,ins_status,ins_grupo,ins_data,alunos.alu_nomeArtistico,alunos.alu_email,alunos.alu_celular,ins_forma,ins_comentario,ins_valorModulo,ins_valorTotalPago,ins_valorDevido');
        $xcrud->sum('ins_valorTotalPago,ins_valorModulo,ins_valorDevido');

        $xcrud->fields('ins_grupo,ins_forma,ins_aluno,ins_valorDesconto,ins_motivoDesconto,ins_comentario', null, null, 'create');
        $xcrud->fields('ins_id,ins_grupo,ins_aluno,ins_status,ins_grupo,ins_forma,ins_valorDesconto,ins_motivoDesconto,ins_comentario,ins_IP,ins_data,ins_tempData', null, null, 'edit');

        $xcrud->custom_button('#', 'Mensagem', 'btn-icon fab fa-whatsapp', 'btn btn-sm btn-info', [
            'data-bs-toggle' => 'modal',
            'data-bs-target' => '#whatsAppMsg'
        ]);

        $xcrud->create_action('reenviar_confirmacao', 'reenviar_confirmacao', 'inscricoes_helper.php', 'fas fa-check-double');
        $xcrud->button('#', "Reenviar Confirmação", 'fas fa-check-double', 'btn btn-info xcrud-action', [
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
        $xcrud->create_action('whatsAppMsg', 'whatsAppMsg', 'inscricoes_helper.php', 'fab fa-whatsapp');
        $xcrud->button('#', "Enviar Mensagem", 'fab fa-whatsapp', 'btn btn-info xcrud-action whatsAppMsgRow', [
            'data-primary' => '{ins_id}',
            'data-task' => 'action',
            'data-action' => 'whatsAppMsg',
            'data-msg' => ''
        ]);

        $xcrud->create_action('solicitar_aprovacao_admin', 'solicitar_aprovacao_admin', 'inscricoes_helper.php', 'fa fa-envelope');
        $xcrud->button('#', "Solicitar Aprovação", 'fa fa-envelope', 'btn btn-info xcrud-action', [
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

        $xcrud->create_action('aprovar_admin', 'aprovar_admin', 'inscricoes_helper.php', 'fas fa-thumbs-up');
        $xcrud->button('#', "Aprovar", 'fas fa-thumbs-up', 'btn btn-info xcrud-action', [
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

        $xcrud->create_action('enviar_declaracao', 'enviar_declaracao', 'inscricoes_helper.php', 'fa fa-envelope');
        $xcrud->button('#', "Enviar declaração", 'fas fa-file', 'btn btn-default btn-sm btn-info xcrud-action', [
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

        $xcrud->create_action('sincronizarInscricao', 'sincronizarInscricao', 'inscricoes_helper.php', 'fas fa-sync');
        $xcrud->button('#', "Atualizar Totais", 'fas fa-sync', 'btn btn-info xcrud-action', [
            'data-primary' => '{ins_id}',
            'data-task' => 'action',
            'data-action' => 'sincronizarInscricao'
        ]);

        $xcrud->relation('ins_grupo', 'grupos', 'grp_id', 'grp_nome', 'grp_ativo=1');
        $xcrud->relation('ins_forma', 'grupos_formas', 'gfp_id', 'gfp_descricao', null, null, null, null, null, null, 'gfp_grupo', 'ins_grupo');
        $xcrud->relation('ins_aluno', 'alunos', 'alu_id', 'alu_nomeArtistico');

        $xcrud->change_type('ins_valorTotalPago,ins_valorModulo,ins_valorDevido,ins_valorDesconto', 'price', null, array(
            'decimals' => '2',
            'separator' => '.',
            'point' => ','
        ));
        $xcrud->change_type('ins_motivoDesconto,ins_comentario', 'text');
        $xcrud->change_type('ins_tempData', 'textarea');

        $xcrud->unset_print();
        $xcrud->unset_csv();
        $xcrud->unset_view();
        $xcrud->unset_numbers(false);

        $xcrud->order_by('ins_grupo', 'ASC');
        $xcrud->order_by('ins_valorModulo', 'DESC');
        $xcrud->order_by('ins_valorDevido', 'DESC');
        $xcrud->order_by('ins_valorTotalPago', 'ASC');
        $xcrud->order_by('alunos.alu_nomeArtistico', 'ASC');

        $xcrud->no_quotes('ins_data');
        $xcrud->pass_var('ins_data', 'NOW()', 'create');
        $xcrud->pass_var('ins_user', $this->session->userdata('usr_id'), 'create');
        $xcrud->pass_var('ins_IP', $_SERVER['REMOTE_ADDR'], 'create');

        $xcrud->pass_default('ins_grupo', $this->session->userdata('last_grupo'));
        $xcrud->pass_default('ins_aluno', $this->session->userdata('last_aluno'));

        $xcrud->before_insert('BI_inscricao_admin', 'inscricoes_helper.php');
        $xcrud->after_insert('AI_inscricao_admin', 'inscricoes_helper.php');
        $xcrud->after_update('AU_inscricao_admin', 'inscricoes_helper.php');

        $xcrud->replace_remove('INS_replace_remove', 'inscricoes_helper.php');

        $xcrud->mask('alunos.alu_celular', '(00) 00000-0000');
        $xcrud->mask('alunos.alu_cpf', '000.000.000-000');
        $xcrud->validation_pattern('alunos.alu_email', 'email');
        $xcrud->validation_required('alunos.alu_cpf', 1);

        /* RECEBÍVEIS */
        $rec = $xcrud->nested_table('Recebíveis', 'ins_id', 'recebiveis', 'rec_inscricao');

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

    public function inscricao($grp_id_slug = null, $ins_id = null)
    {
        $this->assets->css('../../assets/plugins/card/card.css');
        $this->assets->js('../../assets/plugins/card/jquery.card.js');
        $this->assets->js('inscricoes_aluno.js');
        $this->assets->inline('recaptcha', config_item('recaptcha_key'));
        
        $this->load->model('grupos_model');
        $this->load->model('alunos_model');
        $this->load->model('operadoras_model');
        $this->load->model('inscricoes_model');
        $this->load->helper('inscricoes_helper');

        if (! empty($ins_id) && ! $ins = $this->inscricoes_model->getInscricaoCompleta($ins_id)) {
            return set_status_header(401,'Inscrição não encontrada');
        }
        if (! empty($ins['ins_valorDevido']) && (int) $ins['ins_valorDevido'] <= 0) {
            return set_status_header(401,'Inscrição quitada');
        }

        global $processoSeletivo;
        $processoSeletivo = false;

        $grp_id_slug = urlencode(urldecode($grp_id_slug));
        $this->vars['grp'] = $this->grupos_model->getBySlugOrID($grp_id_slug);
        
        $semana[0] = 'Domingos';
        $semana[1] = 'Segundas';
        $semana[2] = 'Terças';
        $semana[3] = 'Quartas';
        $semana[4] = 'Quintas';
        $semana[5] = 'Sextas';
        $semana[6] = 'Sábados';
        $this->vars['grp']['grp_diaSemana'] = explode(',', $this->vars['grp']['grp_diaSemana']);
        foreach ($this->vars['grp']['grp_diaSemana'] as $dia) {
            $grp_diaSemana[] = $semana[$dia];
        }
        $this->vars['grp']['grp_diaSemana'] = implode(' e ', $grp_diaSemana);
        
        if (! $this->vars['grp']) {
            return set_status_header(404,'Últimas vagas, tem muita gente por aqui. Tente novamente em instantes');
        } elseif (! $ins_id && ($this->vars['grp']['grp_ativo'] != '1' || $this->vars['grp']['grp_inscricoesAbertas'] != '1')) {
            return set_status_header(401,'Inscrições encerradas');
        }

        if (! $ins_id && $this->vars['grp']['grp_maximoInscricoes'] > 0 && $this->vars['grp']['grp_maximoInscricoes'] <= count($this->grupos_model->getAlunosInscritos($this->vars['grp']['grp_id']))) {
            return set_status_header(401,'Todas as vagas para essa turma já foram preenchidas');
        }

        if ($this->vars['grp']['grp_processoSeletivo'] == '1') {
            $processoSeletivo = true;
        }

        if ($grp_id_slug == $this->vars['grp']['grp_id']) {
            redirect('/inscricao/' . $this->vars['grp']['grp_slug'], 301);
        }

        if (empty($_GET['utm_content'])) {
            $_GET['utm_content'] = null;
        }

        foreach ($this->grupos_model->getFormas($this->vars['grp']['grp_id'], $_GET['utm_content']) as $r) {
            $exibe = true;
            if ($r['gfp_parcelas'] > 1) {
                $r['gfp_aceitaCartao'] = 1;
            }
            $gfp_valorTotal_original = $r['gfp_valorTotal'];
            $parcela_minima = ($gfp_valorTotal_original / 2) / $r['gfp_parcelas'];

            if (! empty($ins['ins_valorDesconto'])) {
                $r['gfp_valorTotal'] = $r['gfp_valorTotal'] - $ins['ins_valorDesconto'];
            }
            if (! empty($ins['ins_valorDevido']) && ! empty($ins['ins_valorTotalPago']) && (int) $ins['ins_valorTotalPago'] > 0 && (int) $ins['ins_valorDevido'] > 0) {
                $exibe = false;
                $r['gfp_valorTotal'] = (int) $ins['ins_valorDevido'];

                if ($r['gfp_parcelas'] == 1) {
                    $exibe = true;
                } else if ($r['gfp_parcelas'] > 1) {
                    for ($i = $r['gfp_parcelas']; $i >= $r['gfp_parcelas']; $i --) {
                        if (($r['gfp_valorTotal'] / $r['gfp_parcelas']) <= $parcela_minima) {
                            $exibe = false;
                        } else {
                            $r['gfp_parcelas'] = $i;
                            $exibe = true;
                            break;
                        }
                    }
                }
            }
            if ($exibe) {
                for ($i = 1; $i <= $r['gfp_parcelas']; $i ++) {
                    $formas_temp[$i][$r['gfp_id'] . '_' . $i . '_' . $r['gfp_aceitaCartao'] . '_' . ($r['gfp_valorTotal'] / $i)] = $i . ' parcela' . (($i > 1) ? 's' : '') . ' de R$ ' . number_format($r['gfp_valorTotal'] / $i, 2, ',', '.') . ' ' . ($r['gfp_aceitaCartao'] ? "no cartão de crédito" : "no PIX") . ($r['gfp_comentario'] != "" ? ' (' . $r['gfp_comentario'] . ')' : '');
                }
            }
            if (! empty($formas_temp)) {
                ksort($formas_temp);
                foreach ($formas_temp as $i) {
                    foreach ($i as $k => $v) {
                        $formas[$k] = $v;
                    }
                }
            }
        }
        if (empty($formas) && empty($_GET['redirect'])) {
            redirect('/inscricao/' . $this->vars['grp']['grp_slug'] . '?redirect=true', 301);
        }

        $mes = [];
        $ano = [];
        for ($i = 1; $i <= 12; $i ++) {
            $v = str_pad($i, 2, '0', STR_PAD_LEFT);
            $mes[$v] = $v;
        }
        for ($i = 0; $i <= 12; $i ++) {
            $v = date('Y', strtotime('+' . $i . ' years'));
            $ano[$v] = $v;
        }

        $xcrud = xcrud_get_instance('inscricao_form');
        $xcrud->table('inscricoes');
        $xcrud->table_name('Inscrição');

        $xcrud->set_var('grp', $this->vars['grp']['grp_id']);
        if (empty($ins_id)) {
            $xcrud->set_var('grp_slug', $this->vars['grp']['grp_slug']);
        }

        $xcrud->set_var('after_task', 'view');
        $xcrud->set_lang('save_edit', 'Confirmar Inscrição');
        $xcrud->set_lang('add_image', 'Selecionar Foto');

        $xcrud->create_field('fop', 'select', null, $formas);
        $xcrud->create_field('rec_cartao', 'text', null, [
            'inputmode' => 'numeric'
        ]);
        $xcrud->create_field('rec_cartaoCodigo', 'text', null, [
            'inputmode' => 'numeric'
        ]);
        $xcrud->create_field('rec_cartaoValidade', 'text');
        $xcrud->create_field('rec_cartaoValidadeMes', 'select', date('m'), $mes);
        $xcrud->create_field('rec_cartaoValidadeAno', 'select', date('Y'), $ano);
        $xcrud->create_field('rec_cartaoNome', 'text');
        $xcrud->create_field('rec_cartaoCPF', 'text');
        $xcrud->create_field('alu_cartoes', 'select', "novo", [
            "novo" => 'Inserir dados do cartão'
        ]);

        $xcrud->set_var('aviso_valor', $this->vars['grp']['grp_valorDescricao']);

        $xcrud->join('ins_aluno', 'alunos', 'alu_id', 'a', true);

        $xcrud->validation_required('a.alu_nascimento', 1);

        $xcrud->change_type('a.alu_enderecoEstado', 'select', null, $this->inscricoes->estadosBrasileiros);
        $xcrud->change_type('a.alu_enderecoCep', 'text');

        $xcrud->label('a.alu_nome', 'Nome Completo (como no CPF)');
        $xcrud->label('a.alu_nomeArtistico', 'Nome Artístico');
        $xcrud->label('a.alu_email', 'E-mail');
        $xcrud->label('a.alu_celular', 'Celular / WhatsApp');
        $xcrud->label('a.alu_cpf', 'CPF');
        $xcrud->label('a.alu_nascimento', 'Data de Nascimento');
        $xcrud->label('a.alu_drt', 'DRT');
        $xcrud->label('a.alu_endereco', 'Endereço');
        $xcrud->label('a.alu_enderecoNumero', 'Número');
        $xcrud->label('a.alu_enderecoComplemento', 'Complemento');
        $xcrud->label('a.alu_enderecoBairro', 'Bairro');
        $xcrud->label('a.alu_enderecoCidade', 'Cidade');
        $xcrud->label('a.alu_enderecoEstado', 'Estado');
        $xcrud->label('a.alu_enderecoCep', 'CEP');
        $xcrud->label('a.alu_cv', 'Currículo');
        $xcrud->label('alu_cartoes', 'Selecione um cartão de crédito salvo');
        $xcrud->label('rec_cartao', 'Número do Cartão');
        $xcrud->label('rec_cartaoCodigo', 'CVV');
        $xcrud->label('rec_cartaoValidadeMes', 'Validade - Mês');
        $xcrud->label('rec_cartaoValidadeAno', 'Validade - Ano');
        $xcrud->label('rec_cartaoNome', 'Nome (Como Impresso no Cartão)');
        $xcrud->label('rec_cartaoCPF', 'CPF do Titular');
        $xcrud->label('fop', 'Forma de Pagamento');
        $xcrud->label('a.alu_foto', 'Foto');
        $xcrud->label('a.alu_cv', 'Currículo Artístico');

        $xcrud->fields('ins_id', true);

        $xcrud->mask('a.alu_celular', '(00) 00000-0000');
        $xcrud->mask('a.alu_cpf,rec_cartaoCPF', '000.000.000-00');
        $xcrud->mask('a.alu_enderecoCep', '00000-000');

        $xcrud->set_attr('a.alu_cpf,a.alu_nome,a.alu_nomeArtistico,a.alu_email,a.alu_cv,a.alu_celular,rec_cartaoCPF,a.alu_nascimento,a.alu_drt', array(
            'autocomplete' => 'off'
        ));

        $xcrud->set_attr('a.alu_cpf', array(
            'id' => 'alu_cpf'
        ));
        $xcrud->set_attr('a.alu_nome', array(
            'id' => 'alu_nome'
        ));
        $xcrud->set_attr('a.alu_nomeArtistico', array(
            'id' => 'alu_nomeArtistico'
        ));
        $xcrud->set_attr('a.alu_email', array(
            'id' => 'alu_email'
        ));
        $xcrud->set_attr('a.alu_celular', array(
            'id' => 'alu_celular'
        ));
        $xcrud->set_attr('a.alu_nascimento', array(
            'id' => 'alu_nascimento'
        ));
        $xcrud->set_attr('alu_cartoes', array(
            'id' => 'alu_cartoes'
        ));
        $xcrud->set_attr('a.alu_drt', array(
            'id' => 'alu_drt'
        ));
        $xcrud->set_attr('rec_cartao', array(
            'autocomplete' => 'cc-number',
            'id' => 'rec_cartao'
        ));
        $xcrud->set_attr('rec_cartaoCodigo', array(
            'autocomplete' => 'cc-csc',
            'id' => 'rec_cartaoCodigo'
        ));
        $xcrud->set_attr('rec_cartaoValidade', array(
            'id' => 'rec_cartaoValidade',
            'id' => 'rec_cartaoValidade'
        ));
        $xcrud->set_attr('rec_cartaoValidadeMes', array(
            'class' => 'not_select2 form-control',
            'autocomplete' => 'cc-exp-month',
            'id' => 'rec_cartaoValidadeMes'
        ));
        $xcrud->set_attr('rec_cartaoValidadeAno', array(
            'class' => 'not_select2 form-control',
            'autocomplete' => 'cc-exp-year',
            'id' => 'rec_cartaoValidadeAno'
        ));
        $xcrud->set_attr('rec_cartaoNome', array(
            'autocomplete' => 'cc-name',
            'id' => 'rec_cartaoNome'
        ));
        $xcrud->set_attr('fop', array(
            'id' => 'fop'
        ));
        $xcrud->set_attr('a.alu_endereco', array(
            'id' => 'alu_endereco',
            'consulta-cep' => 'endereco'
        ));
        $xcrud->set_attr('a.alu_enderecoNumero', array(
            'id' => 'alu_enderecoNumero',
            'consulta-cep' => 'numero'
        ));
        $xcrud->set_attr('a.alu_enderecoComplemento', array(
            'id' => 'alu_enderecoComplemento',
            'consulta-cep' => 'complemento'
        ));
        $xcrud->set_attr('a.alu_enderecoCidade', array(
            'id' => 'alu_enderecoCidade',
            'consulta-cep' => 'cidade'
        ));
        $xcrud->set_attr('a.alu_enderecoEstado', array(
            'id' => 'alu_enderecoEstado',
            'consulta-cep' => 'estado'
        ));
        $xcrud->set_attr('a.alu_enderecoBairro', array(
            'id' => 'alu_enderecoBairro',
            'consulta-cep' => 'bairro'
        ));
        $xcrud->set_attr('a.alu_enderecoCep', array(
            'id' => 'alu_enderecoCep',
            'consulta-cep' => 'cep',
            'onblur' => 'consultaCEP(this)'
        ));

        $xcrud->change_type('a.alu_foto', 'image', '', array(
            'not_rename' => true,
            'width' => 200,
            'height' => 200,
            'crop' => true,
            'path' => $_SERVER['DOCUMENT_ROOT'] .DIR_IMAGEM_ALUNOS
        ));
        $xcrud->change_type('a.alu_cv', 'file', '', array(
            'not_rename' => true,
            'path' => $_SERVER['DOCUMENT_ROOT'] .DIR_IMAGEM_ALUNOS
        ));

        if (ENVIRONMENT == "development" || $this->session->userdata('usr_id')) {
            $xcrud->pass_default('a.alu_cpf', '350.624.628-35');
        }

        $xcrud->unset_list();
        $xcrud->unset_edit();
        $xcrud->unset_print();
        $xcrud->unset_search();
        $xcrud->unset_csv();
        $xcrud->unset_sortable();
        $xcrud->unset_remove();

        $xcrud->before_insert('BI_inscricao_aluno', 'inscricoes_helper.php');
        $xcrud->before_update('BU_inscricao_aluno', 'inscricoes_helper.php');
        $xcrud->after_insert('AI_inscricao_aluno', 'inscricoes_helper.php');
        $xcrud->after_update('AU_inscricao_aluno', 'inscricoes_helper.php');

        $xcrud->load_view('view', 'inscricao/inscricao_form_view.php');
        $xcrud->load_view('create', 'inscricao/inscricao_form.php');
        $xcrud->load_view('edit', 'inscricao/inscricao_form.php');

        $xcrud->no_quotes('ins_data');
        $xcrud->pass_var('ins_grupo', $this->vars['grp']['grp_id']);
        $xcrud->pass_var('ins_data', 'NOW()');
        $xcrud->pass_var('ins_IP', $_SERVER['REMOTE_ADDR']);

        // $xcrud->change_type('ins_forma','select',false,$formas);

        $xcrud->where('ins_IP', $_SERVER['REMOTE_ADDR']);

        if ($this->vars['grp']['grp_drtObrigatorio'] == '1') {
            $xcrud->validation_required('a.alu_drt', 1);
        }
        if ($processoSeletivo) {
            $xcrud->validation_required('a.alu_cv', 1);
        }

        $xcrud->validation_required('a.alu_cpf,a.alu_celular,a.alu_endereco,a.alu_enderecoNumero,a.alu_enderecoBairro,a.alu_enderecoCidade,a.alu_enderecoEstado,a.alu_enderecoCep', 1);
        $xcrud->validation_pattern('alu_email', 'email');

        if (! empty($ins_id)) {
            $xcrud->unset_edit(false);
            $this->vars['conteudo'] = $xcrud->render('edit', $ins_id);
        } else {
            $this->vars['conteudo'] = $xcrud->render('create');
        }
        $this->load->view('inscricao/index.php', $this->vars);
    }

    public function aprovar($ins_id)
    {
        return $this->inscricoes->aprovar($ins_id);
    }

    public function reprovar($ins_id)
    {
        return $this->inscricoes->reprovar($ins_id);
    }

    public function totalizar()
    {
        $this->checkLogin();
        return $this->inscricoes->totalizar();
    }

    public function whatsAppMsg()
    {
        $this->checkLogin();
        return $this->inscricoes->whatsAppMsg();
    }
}

/* End of file Inscricoes.php */
/* Location: ./application/controllers/Inscricoes.php */