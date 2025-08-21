<h1>Falha ao Processar Pagamento</h1>
<p>Oi, <?php echo explode(' ', $alu['alu_nomeArtistico'])[0]?>!</p>
<p>
	Seu cartão não aprovou o pagamento referente ao grupo de estudos <strong><?php echo $grp['grp_nomePublico']?></strong>.<br>
	O seu banco retornou: <strong><?php echo $transacao->getOperadoraErros()?></strong> 
</p>
<p>Você pode tentar novamente com ou mesmo cartão, ou selecionar outra forma de pagamento no link abaixo:</p>
<p><a href="<?php echo site_url('/inscricao/'.$grp['grp_slug'].'/'.$ins['ins_id'])?>" target="_blank"><?php echo site_url('/inscricao/'.$grp['grp_slug'].'/'.$ins['ins_id'])?></a></p>