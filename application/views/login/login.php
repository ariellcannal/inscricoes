<?php
defined('BASEPATH') or exit('No direct script access allowed');
$this->load->view('header');?>
<body class="d-flex align-items-center py-4 bg-body-tertiary text-center">
	<main class="form-signin w-100 m-auto">
    	<form class="auth">
    		<?php
            $alerts_out = '';
            $alerts = $this->session->flashdata('alerts');
            $this->session->unset_userdata('alerts');
            if (! is_null($alerts) && count($alerts)) {
                $alerts_out .= '<div class="page-alerts">';
                foreach ($alerts as $i) {
                    $alerts_out .= '<div class="alert dark alert-icon alert-' . $i['type'] . ' alert-dismissible" role="alert">';
                    $alerts_out .= '<button type="button" class="close ' . (($i['closeable'] == true) ? '' : 'hide') . '" data-dismiss="alert" aria-label="Close">';
                    $alerts_out .= '<span aria-hidden="true">×</span>';
                    $alerts_out .= '</button>';
                    $alerts_out .= '<i class="' . $i['icon'] . '" aria-hidden="true"></i> ' . $i['text'];
                    $alerts_out .= '</div>';
                }
                $alerts_out .= '</div>';
            }
            echo $alerts_out;
            ?>
    		<img class="mb-1" src="<?php echo site_url('/writable/logos/CANNAL_POS_SM.png');?>" alt="" height="100">
    		<label for="user" class="sr-only">Usuário</label>
    		<input type="email" id="user" class="form-control" placeholder="Usuário" required autofocus>
    		
    		<label for="pass" class="sr-only">Senha</label>
    		<input type="password" id="pass" class="form-control" placeholder="Senha" required>
    		
    		<button class="btn btn-lg btn-primary btn-block" type="submit">Entrar</button>
    	</form>
	</main>
<?php $this->load->view('footer',['footer'=>false])?>