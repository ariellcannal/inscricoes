<h1>Inscrição Aprovada</h1>
<p>Oi, <?php echo explode(' ', $alu['alu_nomeArtistico'])[0]?>!</p>
<p>
	Sua inscrição para o grupo <strong><?php echo $grp['grp_nomePublico']?></strong> foi aprovada. Por gentileza acesse o link abaixo para
	confirmar a sua forma de pagamento e finalizar a sua inscrição.
</p>
<p>
	<a href="<?php echo site_url('/inscricao/'.$grp['grp_slug'].'/'.$ins['ins_id'])?>" target="_blank"><?php echo site_url('/inscricao/'.$grp['grp_slug'].'/'.$ins['ins_id'])?></a>
</p>