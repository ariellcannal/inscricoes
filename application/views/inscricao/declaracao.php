<?php
defined('BASEPATH') or exit('No direct script access allowed');

$meses = array(
    1 => 'janeiro',
    2 => 'fevereiro',
    3 => 'março',
    4 => 'abril',
    5 => 'maio',
    6 => 'junho',
    7 => 'julho',
    8 => 'agosto',
    9 => 'setembro',
    10 => 'outubro',
    11 => 'novembro',
    12 => 'dezembro',
);
$dataCabecalho = date('d') . ' de ' . $meses[(int) date('n')] . ' de ' . date('Y');

$periodo = '';
if ($grp['grp_dataInicio'] != "" && $grp['grp_dataFim'] != "") {
    $periodo .= ', entre ' . $grp['grp_dataInicio'] . ' e ' . $grp['grp_dataFim'];
}

?>
<!DOCTYPE html>
<html lang="<?php echo $this->config->item('language')?>">
<head>
<meta charset="utf-8">
<title><?php echo $title?></title>
<style>
<?php echo file_get_contents(realpath(APPPATH . '/../assets/css/declaracao.css'));?>
</style>
</head>
<body class="declaracao">
	<header>
		<img src="<?php echo site_url('/writable/logos/CANNAL_POS_SM.png')?>" style="height: 60px;">
		<p>São Paulo, <?php echo $dataCabecalho?></p>
		<h1>DECLARAÇÃO</h1>
	</header>
	<section>
		<p>
			Declaramos que <strong><?php echo ucwords($alu['alu_nome'])?></strong>, CPF <strong><?php echo $alu['alu_cpf']?></strong> participou do grupo de estudo de "<strong><?php echo $grp['grp_nomePublico']?></strong>", com <?php echo $grp_coordenadores?>, por <?php echo $grp['grp_encontros']?> encontros<?php echo $periodo?>, perfazendo uma carga total de <strong><?php echo $carga_horaria?> horas</strong>.
		</p>
		<p>Sem mais,</p>
	</section>
	<footer>
		<p>
			<strong>Grupo TAPA</strong>
		</p>
	</footer>
<?php $this->assets->print_view_footer('js')?>
</body>
</html>