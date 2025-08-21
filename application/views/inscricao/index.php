<?php $this->load->view('header', ['closeHead'=>false,'title'=>$grp['grp_nomePublico'].' - CANNAL Produções']);?>
    <meta property="og:url" content="<?php echo site_url('/inscricao/'.$grp['grp_slug'])?>" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="<?php echo $grp['grp_nomePublico']?>" />
    <meta property="og:image" content="<?php echo site_url('/writable/grupos/'.$grp['grp_imagem'])?>" />
    <meta property="og:description" content="<?php echo $grp['grp_descricao']?>" />
</head>
<body class="inscricao pt-0">
    <?php if (ENVIRONMENT === 'production' && !empty($grp['grp_pixel'])): ?>
    <!-- Meta Pixel Code -->
	<noscript>
		<img height="1" width="1" style="display: none" src="https://www.facebook.com/tr?id=<?= htmlspecialchars($grp['grp_pixel']) ?>&ev=PageView&noscript=1" />
	</noscript>
	<!-- End Meta Pixel Code -->
    <?php endif; ?>
    
    <header class="jumbotron d-flex align-items-end text-light" style="background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),  url('<?php echo site_url('/writable/grupos/'.$grp['grp_imagem'])?>'); background-position: center; background-size: cover;">
		<h1 class="py-5 my-5 m-auto"><?php echo $grp['grp_nomePublico']?></h1>
	</header>
	
	<main class="container">	
		<div class="alert alert-primary" role="alert">
          <?php echo $grp['grp_encontros'].' encontros  entre '.date('d/m/Y',strtotime($grp['grp_dataInicio'])).' e '.date('d/m/Y',strtotime($grp['grp_dataFim']))?>. <?php echo $grp['grp_diaSemana'].', das '.$grp['grp_horaInicio'].' às '.$grp['grp_horaFim']?>.
        </div>
	<?php echo $conteudo?>
	</main>
<?php $this->load->view('footer')?>
    