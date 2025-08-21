<?php
$ci = &get_instance();

$msg[] = 'Oi, {alu_primeiroNome}! A sua inscrição está confirmada no grupo *{grp_nomePublico}*! Clique no link a seguir para entrar no grupo de trabalho do WhatsApp: {grp_linkWhats}';
$msg[] = 'Oi, {alu_primeiroNome}! A sua inscrição está confirmada no grupo *{grp_nomePublico}*! Clique no link a seguir, revise seus dados e complete a inscrição escolhendo a forma de pagamento. Qualquer dúvida, pode responder essa mensagem aqui. ' . site_url('inscricao/') . '{grp_slug}/{ins_id}';

$default = $ci->session->has_userdata('whatsAppMsg')?$ci->session->userdata('whatsAppMsg'):$msg[0];

$ci->load->model('inscricoes_model');
$tags = array_keys($ci->inscricoes_model->getInscricaoCompleta(3));
?>
<script>
	msgWhatsApp = "<?php echo $default?>"; 
</script>
<div class="modal fade" id="whatsAppMsg" tabindex="-1" aria-labelledby="whatsAppMsg" aria-hidden="true">
	<div class="modal-dialog modal-xl modal-fullscreen-sm-down modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Selecione a Mensagem</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form>
					<?php foreach ($msg as $m){?>
					<div class="form-group input-group mb-2">
						<textarea class="form-control form-control-sm" disabled="disabled" readonly="readonly"><?php echo $m?></textarea>
						<button type="button" class="btn btn-primary btn-lg btn-block whatsAppMsg" data-msg="<?php echo $m?>">Selecionar</button>
					</div>
					<?php }?>
					<div class="input-group mb-2">
						<textarea class="form-control form-control-sm" id="customWhatsAppMsg"><?php echo $ci->session->has_userdata('whatsAppMsg')?$ci->session->userdata('whatsAppMsg'):''?></textarea>
						<button type="button" class="btn btn-primary btn-lg btn-block whatsAppMsg" data-msg="custom">Selecionar</button>
					</div>
				</form>
			</div>
			<div class="modal-footer overflow-y-auto" style="height: 200px">
    			<?php foreach($tags as $t){
    			    print '<span class="ins_tag m-1 p-1 border d-inline-flex bg-secondary-subtle">{'.$t.'}</span>';
    			}?>
			</div>
		</div>
	</div>
</div>