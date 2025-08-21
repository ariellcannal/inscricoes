<h1>Novo Repasse</h1>
<p>Oi, <?php echo explode(' ', $rep['usr_nome'])[0]?>!</p>
<p>
	Você acabou de receber um novo repasse de <strong>R$ <?php echo number_format($rep['rep_valor'],2,',','.')?></strong>
	referente aos grupos de estudos do TAPA, através da chave PIX <strong><?php echo $rep['usr_chavePIX']?></strong>.
</p>