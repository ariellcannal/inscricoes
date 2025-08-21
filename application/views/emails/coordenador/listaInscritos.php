<?php 
foreach($inscricoes as $k=>$i){
    if ($i['alu_nascimento'] != "") {
        $this->load->helper('dates_helper');
        $inscricoes[$k]['idade'] = diferencaDatas(date('Y-m-d'), $i['alu_nascimento'], 'y') . ' anos';
        $inscricoes[$k]['alu_nascimento_br'] = date('d/m/Y', strtotime($i['alu_nascimento']));
    }
    if (isset($i['alu_celular'])) {
        $link = 'https://api.whatsapp.com/send?phone=55' . preg_replace('/[^0-9]/', '', $i['alu_celular']) . '&text=';
        $inscricoes[$k]['alu_celular'] = '<a href=' . $link . ' target="_blank" style="text-decoration:none;">' . $i['alu_celular'] . '</a>';
    }
}
?>
<table class="table table-sm text-right mt-5" style="width: 100%;">
  <tbody>
  	<tr class="table-warning">
  		<th colspan="4" style=" text-align: center;">INSCRIÇÕES CONFIRMADAS</th>
  	</tr>
  	<?php
  	$i=1;
  	foreach($inscricoes as $alu){?>
        <tr>
          <td style="white-space: nowrap;"><?php echo $i?></td>
          <td style="white-space: nowrap;"><?php echo $alu['alu_nomeArtistico']?></td>
          <td style="white-space: nowrap;"><?php echo $alu['idade']?></td>
          <td style="white-space: nowrap;"><?php echo $alu['alu_celular']?></td>
        </tr>
    <?php
    $i++; } ?>
  </tbody>
</table>