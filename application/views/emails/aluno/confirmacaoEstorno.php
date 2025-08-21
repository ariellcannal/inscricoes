<h1>Confirmação de Estorno</h1>
<p>Oi, <?php echo explode(' ', $alu['alu_nomeArtistico'])[0]?>!</p>
<p>
	Confirmamos o estorno de <strong>R$ <?php echo number_format($transacao->getValorCancelado(),2,',','.')?></strong> referente ao grupo de estudos <strong><?php echo $grp['grp_nomePublico']?></strong>.<br/>
</p>
<?php if(strpos($transacao->getTipo(), "pix")){?>
<p>O estorno será creditado na sua conta bancária em até 24 horas úteis.</p>
<?php }else if($transacao->getTipo()=='cartao'){?>
<p>O crédito será feito no <strong><?php echo $transacao->getCartao()?></strong> e poderá aparecer em até duas faturas.</p>
<?php }?>
