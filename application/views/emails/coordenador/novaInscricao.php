<?php
$campos['alu_nomeArtistico'] = 'Nome Artístico';
$campos['alu_email'] = 'E-mail';
$campos['alu_celular'] = 'WhatsApp / Celular';
$campos['alu_nascimento_br'] = 'Data de Nascimento';
if ($alu['alu_nascimento'] != "") {
    $campos['idade'] = 'Idade';
    $this->load->helper('dates_helper');
    $alu['idade'] = diferencaDatas(date('Y-m-d'), $alu['alu_nascimento'], 'y') . ' anos';
    $alu['alu_nascimento_br'] = date('d/m/Y', strtotime($alu['alu_nascimento']));
}
if (isset($alu['alu_celular'])) {
    $link = 'https://api.whatsapp.com/send?phone=55' . preg_replace('/[^0-9]/', '', $alu['alu_celular']) . '&text=';
    $alu['alu_celular'] = '<a href=' . $link . ' target="_blank" style="text-decoration:none;">' . $alu['alu_celular'] . '</a>';
}
?>
<h1>Nova Inscrição Confirmada</h1>
<p>
	Uma nova inscrição foi confirmada no grupos de estudos <strong><?php echo $grp['grp_nomePublico']?></strong>.
</p>
<table>
	<?php foreach($campos as $campo=>$label){?>
		<?php if(!empty($alu[$campo])){?>
		<tr>
		<th style="text-align: right;"><?php echo $label?></th>
		<td><?php echo $alu[$campo]?></td>
		</tr>
		<?php }?>
	<?php }?>
</table>