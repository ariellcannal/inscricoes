<?php
if ($this->get_var('custom_head') != false) {
    require (XCRUD_PATH . '/' . Xcrud_config::$themes_path . $this->get_var('custom_head'));
}
?>
<?php if (ENVIRONMENT === 'production' && ! empty($grp['grp_pixel'])) :?>
<script>
  fbq('track', 'Purchase');
</script>
<noscript>
	<img height="1" width="1" style="display: none" src="https://www.facebook.com/tr?id=<?php echo $grp['grp_pixel']?>noscript=1" />
</noscript>
<!-- End Meta Pixel Code -->
<?php
endif;

global $processoSeletivo, $capture, $transacao;
if ($processoSeletivo && ! $capture) {?>
<div class="alert alert-success" role="alert">A sua inscrição foi enviada para análise. Você receberá um e-mail informando o resultado do processo seletivo.</div>
<?php
} else if ($transacao->getTipo() == 'pix') {
    ?>
<div class="col-md-8 col-sm-12 mx-auto">
	<div class="card text-center border-primary mb-5">
		<div class="card-header text-white bg-primary">Quase lá!</div>
		<div class="card-body">
			<h5 class="card-title">Sua inscrição estará confirmada após a realização do PIX.</h5>
			<p class="card-text">Basta escanear o código abaixo através do aplicativo do seu banco:</p>
			<img src="<?php echo $transacao->getPixQrCodeUrl()?>" />
			<p class="card-text">ou realizar o pagamento com este código através da opção Pix Copia e Cola:</p>
			<div class="input-group input-group-lg mb-3">
				<input type="text" class="form-control" id="pix_code" value="<?php echo $transacao->getPixQrCode()?>">
				<button class="btn btn-secondary" type="button" id="pix_code_button">Copiar Código</button>
			</div>
		</div>
		<div class="card-footer text-white bg-primary">
			<strong>Válido até <?php echo date('d/m/Y H:i', strtotime($transacao->getDataExpiracao()))?></strong>. Depois deste prazo sua inscrição será desconsiderada.
		</div>
	</div>
</div>
<?php }else if($transacao->getTipo() == 'cartao'){?>
<div class="alert alert-success" role="alert">Inscrição realizada. Você receberá um e-mail quando o pagamento estiver confirmado.</div>
<?php }?>