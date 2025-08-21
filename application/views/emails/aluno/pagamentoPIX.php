<?php global $transacao?>
<h1>Inscrição Aguardando Pagamento</h1>
<p>Oi, <?php echo explode(' ', $alu['alu_nomeArtistico'])[0]?>!</p>
<p>
	Sua inscrição para o grupo <strong><?php echo $grp['grp_nomePublico']?></strong> foi aprovada e estará confirmada após o processamento do PIX.
	Basta escanear o código abaixo pelo aplicativo do seu banco:
</p>
<p style="text-align: center">
	<img src="<?php echo $transacao->getPixQrCodeUrl()?>" />
</p>
<p>
	ou realizar o pagamento com este código através da opção Pix Copia e Cola:</br>
</p>
<p>
	<textarea style="color: #494238; border-radius: 8px; display: block; height: 60px; width: 100%; overflow: hidden; resize: none; border: 1px solid #d9d3cc;" readonly ><?php echo $transacao->getPixQrCode()?></textarea>
</p>