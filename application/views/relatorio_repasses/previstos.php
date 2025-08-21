<?php 

$repasses = [];
foreach($previstos as $i){
    $repasses[$i['proximoRepasse']][] = $i;
}
?>
<table class="table table-sm text-right mt-5" style="width: 100%; margin-bottom: 30px;">
  <tbody>
  	<tr class="table-warning">
  		<th colspan="5" style=" text-align: center; font-size: 130%;">PROXÍMOS REPASSES PREVISTOS</th>
  	</tr>
  	<?php foreach($repasses as $data=>$i){
  	    $total_data = 0;
  	    ?>
      	<tr class="table-warning">
      		<th colspan="5" style="text-align: left; border-bottom: 1px solid black;">Previsto para <?php echo $data?></th>
      	</tr>
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
        <tr class="table-secondary" style="border-top: 1px solid black">
      		<th colspan="4" style=" text-align: right;">Total previsto para <?php echo $data?></th>
      		<th colspan="1" style=" text-align: right;">R$ <?php echo number_format($total_data,2,',','.')?></th>
      	</tr>
    <?php } ?>
  </tbody>
</table>