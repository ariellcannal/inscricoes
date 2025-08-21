<h1>Pagamento Confirmado</h1>
<p>Oi, <?php echo explode(' ', $alu['alu_nomeArtistico'])[0]?>!</p>
<p>
	O pagamento da sua inscrição no grupos de estudos <strong><?php echo $grp['grp_nomePublico']?></strong> está confirmado.
</p>
<p>
	O seu pagamento de <strong>R$ <?php echo number_format($transacao->getValorBruto(),2,',','.')?></strong> foi efetivado com sucesso em <strong><?php echo $transacao->getParcelas().' parcela'.( $transacao->getParcelas()>1?'s':'' )?></strong> através do <strong><?php echo $transacao->getCartao()?></strong>.
</p>
<p>
	O pagamento estará identificado na sua fatura como <strong><?php echo $transacao->getDescricaoFatura()?></strong>.
</p>
<?php if (! empty($grp['grp_linkWhats'])) {?>
	<center>
    	<a href="<?php echo $grp['grp_linkWhats']?>" style="font-family:Arial, Helvetica, sans-serif; font-size:18px; border-radius:4px; padding:15px 30px; color:#ffffff; background-color:#25d366; outline:none; border:none; cursor:pointer; display:inline-block; text-decoration: none;">Entrar no Grupo do WhatsApp</a><br>
        <?php echo $grp['grp_linkWhats']?>
    </center>
<?php }?>