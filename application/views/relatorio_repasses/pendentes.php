<?php $repasses = [];
$total_data = 0;
foreach($pendentes as $i){
    $repasses[$i['data']][] = $i;
}
?>
<table class="table table-sm text-right mt-5" style="width: 100%; margin-bottom: 30px;">
  <tbody>
  	<tr class="table-info">
  		<th colspan="5" style=" text-align: center; font-size: 130%;">Repasses Pendentes</th>
  	</tr>
  	<tr class="table-info">
  		<td colspan="5" style=" text-align: center; border-bottom: 1px solid black; ">Recebiveis que ainda não foram repassados.</td>
  	</tr>
  	<?php foreach($repasses as $data=>$i){?>
  	    <?php foreach($i as $r){
      	    $total_data+=$r['valor'];
          	?>
            <tr>
              <td style="white-space: nowrap;"><?php echo $r['rec_id'].' - '.$r['grupo']?></td>
              <td style="white-space: nowrap; text-align: right;"><?php echo $r['porcentagem']?>%</td>
              <td style="white-space: nowrap;">Parcela <?php echo $r['parcela']?$r['parcela']:'única'?></td>
              <td style="white-space: nowrap;"><?php echo $r['aluno']?></td>
              <td style="white-space: nowrap; text-align: right;"><?php echo number_format($r['valor'],2,',','.')?></td>
            </tr>
        <?php }?>
    <?php } ?>
    <tr class="table-secondary" style="border-top: 1px solid black">
  		<th colspan="4" style="text-align: right;">Total <?php echo $data?></th>
  		<th style="text-align: right;">R$ <?php echo number_format($total_data,2,',','.')?></th>
  	</tr>
  </tbody>
</table>