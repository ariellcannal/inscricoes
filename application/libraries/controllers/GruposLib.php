<?php

class GruposLib
{

    private $CI;
    
    private $site_url = 'https://inscricoes.cannal.com.br/';

    function __construct()
    {
        $this->CI = &get_instance();
    }

    public function ws()
    {
        error_on();
        if ($this->CI->input->get('token') == 'Q0FOTkFMSW5zY3JpY29lc19ncnVwb3RhcGE') {
            $this->CI->load->model('grupos_model');
            $lastDate = $this->CI->grupos_model->getLastDate();
            if ($lastDate != $this->CI->input->get('lastDate')) {
                $r = $this->CI->grupos_model->getWS();
                foreach ($r as $k => $grp) {
                    $r[$k]['grp_url'] = $this->site_url.URI_INSCRICOES . '/' . $grp['grp_slug'];
                    foreach ($grp['grp_coordenadores'] as $k2 => $coordenador) {
                        $r[$k]['grp_coordenadores'][$k2]['usr_imagem'] = $this->site_url.DIR_IMAGEM_USUARIOS . '/' . $coordenador['usr_foto'];
                    }
                }
                exit(json_encode([
                    'lastDate' => $lastDate,
                    'grupos' => $r
                ]));
            }
            return set_status_header(204);
        }
        show_404();
    }

    public function csv($grp_id)
    {
        $this->CI->load->model('grupos_model');
        $csv = $this->CI->grupos_model->CSV($grp_id);
        $this->CI->load->helper('download');
        force_download('CSV_Grupo_' . $grp_id . '.csv', $csv);
    }

    public function lista_presenca($grp_id)
    {
        $this->CI->load->model('grupos_model');
        $this->CI->vars['alunos'] = $this->CI->grupos_model->getAlunosInscritos($grp_id);
        $this->CI->vars['grp'] = $this->CI->grupos_model->getBySlugOrID($grp_id);
        $this->CI->load->view('grupos/lista_presenca.php', $this->CI->vars);
    }

    public function presenca()
    {
        $this->CI->load->model('grupos_model');
        $this->CI->load->model('alunos_model');

        $grp = $this->CI->grupos_model->getGrupoPresenca();
        if (! empty($grp)) {
            $load_view = false;

            if (! empty($_COOKIE['tapa_presenca'])) {
                $alu = $this->CI->alunos_model->getRow($_COOKIE['tapa_presenca']);
                if (! empty($alu)) {
                    $presencas = $this->CI->alunos_model->getPresencas($alu['alu_id'], $grp['grp_id']);
                    if (! empty($presencas) && date('Y-m-d') == date('Y-m-d', strtotime($presencas[0]['prs_data']))) {
                        $load_view = true;
                    }
                }
            }

            $this->CI->assets->js('registra_presenca.js');

            $xcrud = xcrud_get_instance();
            $xcrud->table('presenca');
            $xcrud->table_name('Presença');
            $xcrud->load_view('view', 'presenca/presenca_view.php');

            if (! $load_view) {
                $xcrud->set_lang('add', 'Registrar');
                $xcrud->set_lang('save_edit', 'Registrar');

                $xcrud->set_var('after_task', 'view');

                $xcrud->create_field('alu_cpf', 'text');
                $xcrud->create_field('alu_nome', 'text');
                $xcrud->create_field('alu_nomeArtistico', 'text');
                $xcrud->create_field('alu_email', 'text');
                $xcrud->create_field('alu_celular', 'text');
                $xcrud->create_field('alu_id', 'hidden');

                $xcrud->label('prs_grupo', 'Grupo de Estudos');
                $xcrud->label('prs_aluno', 'Aluno');
                $xcrud->label('alu_nome', 'Nome (como no CPF)');
                $xcrud->label('alu_nomeArtistico', 'Nome Artístico');
                $xcrud->label('alu_cpf', 'CPF');
                $xcrud->label('alu_email', 'E-mail');
                $xcrud->label('alu_celular', 'Celular / WhatsApp');

                $xcrud->fields('prs_grupo,alu_cpf,alu_nome,alu_nomeArtistico,alu_email,alu_celular,alu_id');

                $xcrud->relation('prs_grupo', 'grupos', 'grp_id', 'grp_nomePublico', 'grp_ativo = 1');

                $xcrud->validation_required('alu_cpf', 1);
                $xcrud->validation_required('alu_celular', 1);
                $xcrud->validation_required('alu_email', 1);
                $xcrud->validation_required('alu_nomeArtistico', 1);
                $xcrud->validation_required('alu_nome', 1);
                $xcrud->validation_pattern('alu_email', 'email');

                $xcrud->mask('presenca.alu_cpf', '000.000.000-00');
                $xcrud->mask('presenca.alu_celular', '(00) 00000-0000');

                $xcrud->set_attr('alu_cpf', array(
                    'autocomplete' => 'off',
                    'id' => 'alu_cpf'
                ));
                $xcrud->set_attr('alu_nomeArtistico', array(
                    'id' => 'alu_nomeArtistico'
                ));
                $xcrud->set_attr('alu_nome', array(
                    'id' => 'alu_nome'
                ));
                $xcrud->set_attr('alu_email', array(
                    'id' => 'alu_email'
                ));
                $xcrud->set_attr('alu_celular', array(
                    'id' => 'alu_celular'
                ));
                $xcrud->set_attr('alu_id', array(
                    'id' => 'alu_id'
                ));

                if (! empty($alu)) {
                    $xcrud->pass_default('alu_id', $alu['alu_id']);
                    $xcrud->pass_default('alu_nome', $alu['alu_nome']);
                    $xcrud->pass_default('alu_nomeArtistico', $alu['alu_nomeArtistico']);
                    $xcrud->pass_default('alu_email', $alu['alu_email']);
                    $xcrud->pass_default('alu_celular', $alu['alu_celular']);
                    $xcrud->pass_default('alu_cpf', $alu['alu_cpf']);
                }

                $xcrud->before_insert('BI_presenca', 'grupos_helper.php');

                $xcrud->no_quotes('prs_data,prs_dataAula');
                $xcrud->pass_var('prs_data', 'NOW()');
                $xcrud->pass_var('prs_dataAula', 'NOW()');
                $xcrud->pass_var('prs_grupo', $grp['grp_id']);
                $xcrud->pass_default('prs_grupo', $grp['grp_id']);
                $xcrud->disabled('prs_grupo');

                $xcrud->unset_list();
                $xcrud->unset_edit();
                $xcrud->unset_print();
                $xcrud->unset_search();
                $xcrud->unset_csv();
                $xcrud->unset_sortable();
                $xcrud->unset_remove();
                $this->CI->vars['form'] = $xcrud->render('create');
            } else {
                $this->CI->vars['form'] = $xcrud->render('view', $presencas[0]['prs_id']);
            }
            $this->CI->load->view('presenca/presenca.php', $this->CI->vars);
        } else {
            return set_status_header(200,'Não existe nenhum grupo acontecendo agora');
        }
    }
}