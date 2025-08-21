<?php
defined('BASEPATH') or exit('No direct script access allowed');
$this->load->view('header');?>
<body class="bg-light">
	<center>
		<img src="<?php echo site_url('/writable/logos/CANNAL_POS_SM.png');?>" class="mb-5" height="100"/>
	</center>
	<?php echo $conteudo?>
<?php $this->load->view('footer')?>