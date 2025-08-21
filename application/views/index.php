<?php $this->load->view('header');?>
<body>
	<nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-primary" aria-label="Menu Principal">
		<div class="container-fluid">
			<a class="navbar-brand" href="/recebiveis"><img src="<?php echo site_url('/writable/logos/CANNAL_NEG_SM.png');?>" alt="" height="32"></a>
			<button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#menu" aria-controls="menu" aria-expanded="false" aria-label="Alternar navegação">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="navbar-collapse offcanvas-collapse collapse" id="menu">
				<ul class="navbar-nav me-auto mb-2 mb-lg-0">
					<?php foreach($menu as $k=>$v) { ?>
					<li class="nav-item"><a href="/<?php echo $k?>" class="nav-link px-2 <?php echo strpos('/'.$this->uri->segment(1),'/'.$k)===0?'active':''?>" <?php echo strpos('/'.$this->uri->segment(1),'/'.$k)===0?'aria-current="page"':''?>><?php echo $v?></a></li>
					<?php }?>
				</ul>
				<div class="d-flex">
					<a href="/sair" class="btn btn-danger me-2">Sair</a>
				</div>
			</div>
		</div>
	</nav>
	
	<?php if(!empty($submenu)){?>
	<div class="nav-scroller bg-body shadow-sm">
		<nav class="nav" aria-label="Secondary navigation">
    		<?php foreach($submenu as $link=>$label){?>
        	<a href="/<?php echo $link?>" class="nav-link <?php echo $this->uri->uri_string() == $link?'active':''?>"><?php echo $label?></a>
    		<?php }?>
		</nav>
	</div>
	<?php }?>
<?php
$alerts = [
    'success',
    'warning'
];
foreach ($alerts as $alert) {
    if (isset($_SESSION['alert_' . $alert]) && is_array($_SESSION['alert_' . $alert])) {
        foreach ($_SESSION['alert_' . $alert] as $k => $texto) {
            ?>
                	        <div class="alert alert-<?php echo $alert?>" role="alert"><?php echo $texto?></div>
                	        <?php
            unset($_SESSION['alert_' . $alert][$k]);
        }
    }
}
?>
        <main class="col-md-12 ms-sm-auto col-lg-12 p-4">
    	<?php echo $conteudo?>
    	</main>
    	<?php $this->assets->print_view_footer('js')?>
<?php $this->load->view('footer')?>