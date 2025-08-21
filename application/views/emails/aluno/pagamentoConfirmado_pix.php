<h1>Pagamento Confirmado</h1>
<p>Oi, <?php echo explode(' ', $alu['alu_nomeArtistico'])[0]?>!</p>
<p>
	O pagamento da sua inscrição no grupos de estudos <strong><?php echo $grp['grp_nomePublico']?></strong> está confirmado.
</p>
<p>
	O seu pagamento foi efetivado com sucesso por PIX.
</p>
<?php if (! empty($grp['grp_linkWhats'])) {?>
	<center>
    	<a href="<?php echo $grp['grp_linkWhats']?>" style="font-family:Arial, Helvetica, sans-serif; font-size:18px; border-radius:4px; padding:15px 30px; color:#ffffff; background-color:#25d366; outline:none; border:none; cursor:pointer; display:inline-block; text-decoration: none;">Entrar no Grupo do WhatsApp</a><br>
		<?php echo $grp['grp_linkWhats']?>
    </center>
<?php }?>