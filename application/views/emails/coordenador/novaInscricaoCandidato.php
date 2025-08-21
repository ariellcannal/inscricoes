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
<h1>Novo Candidato</h1>
<p>
	Um novo candidato se inscreveu no grupos de estudos <strong><?php echo $grp['grp_nomePublico']?></strong>. O currículo está em anexo.
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
<p>Selecione uma das opções abaixo:</p>
<table style="width: 100%">
	<tr>
		<td style="text-align: center; font-size: 14px; text-transform: uppercase;">
			<a href="<?php echo site_url('inscricoes/aprovar/'.$ins['ins_id'])?>" target="_blank">Aprovar</a>
		</td>
		<td style="text-align: center; font-size: 14px; text-transform: uppercase;">
			<a href="<?php echo site_url('inscricoes/aprovar/'.$ins['ins_id'])?>" target="_blank">Reprovar</a>
		</td>
	</tr>
</table>