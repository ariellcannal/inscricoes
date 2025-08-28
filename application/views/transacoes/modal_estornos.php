<?php
$ci = &get_instance();
if (isset($this->result_list)) {
    foreach ($this->result_list as $key => $row) {
        ?>
<div class="modal fade" data-primary="<?php echo $row['primary_key']?>" id="estornar_<?php echo $row['primary_key']?>" aria-hidden="true" aria-labelledby="estornar_<?php echo $row['primary_key']?>_title" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title fs-5" id="estornar_<?php echo $row['primary_key']?>_title">Qual valor deseja estornar?</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
                                <form class="form-horizontal">
                                        <input type="hidden" name="<?=$ci->security->get_csrf_token_name();?>" value="<?=$ci->security->get_csrf_hash();?>">
                                        <div class="form-group form-material form-group">
						<input type="text" class="form-control" value="R$ <?php echo number_format($row['operadoras_transacoes.otr_valorBruto'],2,',','.')?>" id="otr_valorCancelamento_<?php echo $row['primary_key']?>" data-type="price" data-reverse="true" value="" data-mask="#.###.##0,00" prefix="" separator="." point="," maxlength="10" decimals="2" autocomplete="off">
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-primary estornar" data-otr="<?php echo $row['primary_key']?>">Estornar</button>
			</div>
		</div>
	</div>
</div>
<?php
    }
}
?>