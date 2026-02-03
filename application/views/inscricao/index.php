<?php $this->load->view('header', ['closeHead'=>false,'title'=>$grp['grp_nomePublico'].' - CANNAL Produções']);?>
<?php
$subtitulo = $grp['grp_encontros'].' encontros';
if($grp['grp_dataInicio']!="" && $grp['grp_dataFim'] !=""){
    $subtitulo .= ' entre '.date('d/m/Y',strtotime($grp['grp_dataInicio'])).' e '.date('d/m/Y',strtotime($grp['grp_dataFim']));
}
else if($grp['grp_dataInicio']!="" && $grp['grp_dataFim'] ==""){
    $subtitulo .= ' a partir de '.date('d/m/Y',strtotime($grp['grp_dataInicio']));
}
else if($grp['grp_dataInicio']=="" && $grp['grp_dataFim'] !=""){
    $subtitulo .= ' até '.date('d/m/Y',strtotime($grp['grp_dataFim']));
}
?>
<meta property="og:url" content="<?php echo site_url('/inscricao/'.$grp['grp_slug'])?>" />
<meta property="og:type" content="website" />
<meta property="og:title" content="<?php echo $grp['grp_nomePublico']?>" />
<meta property="og:image" content="<?php echo site_url('/writable/grupos/'.$grp['grp_imagem'])?>" />
<meta property="og:description" content="<?php echo $grp['grp_descricao']?>" />
</head>
<body class="inscricao pt-0">
    <?php 
    // Noscript tags de tracking
    if (ENVIRONMENT === 'production') {
        echo get_tracking_noscript($grp);
    }
    ?>
    
    <header class="jumbotron d-flex align-items-end text-light" style="background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),  url('<?php echo site_url('/writable/grupos/'.$grp['grp_imagem'])?>'); background-position: center; background-size: cover;">
		<h1 class="py-5 my-5 m-auto"><?php echo $grp['grp_nomePublico']?></h1>
	</header>

	<main class="container">
		<div class="alert alert-primary" role="alert">
          <?php echo $subtitulo?>. <?php echo $grp['grp_diaSemana'].', das '.$grp['grp_horaInicio'].' às '.$grp['grp_horaFim']?>.
          <?php echo $grp['grp_descricao']!=''?'<br/>'.nl2br($grp['grp_descricao']):''?>
          <?php echo $grp['grp_emailFaleConosco']!=''?'<br/>'.'Se precisa de maiores informações você pode nos contatar através do e-mail <a href="mailto:'.$grp['grp_emailFaleConosco'].'">'.$grp['grp_emailFaleConosco'].'</a>':''?>
        </div>
	<?php echo $conteudo?>
	</main>
<?php $this->load->view('footer')?>
    