<?php
defined('BASEPATH') or exit('No direct script access allowed');
$this->load->view('header');
?>
<body class="bg-light p-md-5">
	<div class="container">
		<div class="col-md-12 form" style="display: none;">
			<div class="alert alert-warning text-center" role="alert">
				<h2>ATENÇÃO: Você só pode registrar presença uma vez por dispositivo, por aula.</h2>
				<h4>Para sua própria segurança seus dados ficarão salvos em cookie.</h4>
			</div>
        	<?php echo $form?>
	    </div>
		<div class="alert alert-danger text-center longe" role="alert" style="display: none;">
			<h2>Não é possível registrar a presença</h2>
			<h4>Você só pode registrar a presença quando está num raio de 200 metros do galpão.</h4>
		</div>
	</div>
<?php $this->load->view('footer')?>