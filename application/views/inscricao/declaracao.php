<?php
defined('BASEPATH') or exit('No direct script access allowed');
setlocale(LC_ALL, 'pt_BR');
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
		<p>São Paulo, <?php echo date('d \d\e F \d\e Y')?></p>
		<h1>DECLARAÇÃO</h1>
	</header>
	<section>
		<p>
			Declaramos que <strong><?php echo $alu['alu_nomeArtistico']?></strong>, CPF <strong><?php echo $alu['alu_cpf']?></strong> participou do grupo
			de estudo de "<strong><?php echo $grp['grp_nomePublico']?></strong>", com <?php echo $grp_coordenadores?>, no Grupo TAPA, por <?php echo $grp['grp_encontros']?> encontros, de <?php echo $grp['grp_dataInicio']?> até <?php echo $grp['grp_dataFim']?>, perfazendo uma carga total de <strong><?php echo $carga_horaria?> horas</strong>.
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