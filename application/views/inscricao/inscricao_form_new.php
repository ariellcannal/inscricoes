<?php
global $processoSeletivo;
if ($this->get_var('custom_head') != false) {
    require (XCRUD_PATH . '/' . Xcrud_config::$themes_path . $this->get_var('custom_head'));
}

$taxasAdicionais = json_decode($this->get_var('taxasAdicionais'), true) ?: [];
$taxasPrimeiraParcela = json_decode($this->get_var('taxasPrimeiraParcela'), true) ?: [];
$taxasPosteriores = json_decode($this->get_var('taxasPosteriores'), true) ?: [];
?>

<h3>Para confirmar a sua inscrição preencha os dados abaixo:</h3>
<div class="row">
	<h4 class="col-md-12">Dados do aluno</h4>
</div>
<div class="row inscricao_form">
	<input type="hidden" id="grp" value="<?php echo $this->get_var('grp')?>">
	<input type="hidden" id="grp_slug" value="<?php echo $this->get_var('grp_slug')?>">
	<div class="col-md-6">
		<div class="mb-3 form-group">
			<?php echo $this->open_label_tag('a.alu_cpf','label').$this->fields_output['a.alu_cpf']['label'].$this->close_tag('label')?> <small id="alu_cpf_erro">CPF inválido</small>
			<?php echo $this->fields_output['a.alu_cpf']['field']?>
		</div>
		<div class="mb-3 form-group">
			<?php echo $this->open_label_tag('a.alu_email','label').$this->fields_output['a.alu_email']['label'].$this->close_tag('label')?>
			<?php echo $this->fields_output['a.alu_email']['field']?>
		</div>
		<div class="mb-3 form-group">
			<?php echo $this->open_label_tag('a.alu_nome','label').$this->fields_output['a.alu_nome']['label'].$this->close_tag('label')?>
			<?php echo $this->fields_output['a.alu_nome']['field']?>
		</div>
		<div class="row">
			<div class="col-md-6 mb-3 form-group">
    			<?php echo $this->open_label_tag('a.alu_nomeArtistico','label').$this->fields_output['a.alu_nomeArtistico']['label'].$this->close_tag('label')?>
    			<?php echo $this->fields_output['a.alu_nomeArtistico']['field']?>
    		</div>
			<div class="col-md-6 mb-3 form-group">
        		<?php echo $this->open_label_tag('a.alu_nascimento','label').$this->fields_output['a.alu_nascimento']['label'].$this->close_tag('label')?>
    			<?php echo $this->fields_output['a.alu_nascimento']['field']?>
    		</div>
		</div>
		<div class="row">
			<div class="col-md-6 mb-3 form-group">
    			<?php echo $this->open_label_tag('a.alu_celular','label').$this->fields_output['a.alu_celular']['label'].$this->close_tag('label')?>
    			<?php echo $this->fields_output['a.alu_celular']['field']?>
			</div>
			<div class="col-md-6 mb-3 form-group">
    			<?php echo $this->open_label_tag('a.alu_drt','label').$this->fields_output['a.alu_drt']['label'].$this->close_tag('label')?>
    			<?php echo $this->fields_output['a.alu_drt']['field']?>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 mb-3 form-group">
    			<?php echo $this->open_label_tag('a.alu_cv','label').$this->fields_output['a.alu_cv']['label'].$this->close_tag('label')?>
    			<div id="cv_wrapper">
    			<?php echo $this->fields_output['a.alu_cv']['field']?>
    			</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="row">
			<div class="col-md-3 mb-3 form-group">
    			<?php echo $this->open_label_tag('a.alu_enderecoCep','label').$this->fields_output['a.alu_enderecoCep']['label'].$this->close_tag('label')?>
    			<?php echo $this->fields_output['a.alu_enderecoCep']['field']?>
			</div>
			<div class="col-md-9 mb-3 form-group">
    			<?php echo $this->open_label_tag('a.alu_endereco','label').$this->fields_output['a.alu_endereco']['label'].$this->close_tag('label')?>
    			<?php echo $this->fields_output['a.alu_endereco']['field']?>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3 mb-3 form-group">
    			<?php echo $this->open_label_tag('a.alu_enderecoNumero','label').$this->fields_output['a.alu_enderecoNumero']['label'].$this->close_tag('label')?>
    			<?php echo $this->fields_output['a.alu_enderecoNumero']['field']?>
			</div>
			<div class="col-md-3 mb-3 form-group">
    			<?php echo $this->open_label_tag('a.alu_enderecoComplemento','label').$this->fields_output['a.alu_enderecoComplemento']['label'].$this->close_tag('label')?>
    			<?php echo $this->fields_output['a.alu_enderecoComplemento']['field']?>
			</div>
			<div class="col-md-6 mb-3 form-group">
    			<?php echo $this->open_label_tag('a.alu_enderecoBairro','label').$this->fields_output['a.alu_enderecoBairro']['label'].$this->close_tag('label')?>
    			<?php echo $this->fields_output['a.alu_enderecoBairro']['field']?>
			</div>
		</div>
		<div class="row">
			<div class="col-md-8 mb-3 form-group">
    			<?php echo $this->open_label_tag('a.alu_enderecoCidade','label').$this->fields_output['a.alu_enderecoCidade']['label'].$this->close_tag('label')?>
    			<?php echo $this->fields_output['a.alu_enderecoCidade']['field']?>
			</div>
			<div class="col-md-4 mb-3 form-group">
    			<?php echo $this->open_label_tag('a.alu_enderecoEstado','label').$this->fields_output['a.alu_enderecoEstado']['label'].$this->close_tag('label')?>
    			<?php echo $this->fields_output['a.alu_enderecoEstado']['field']?>
			</div>
		</div>
		<div class="mb-3 foto form-group d-none">
			<?php echo $this->open_label_tag('a.alu_foto','label').$this->fields_output['a.alu_foto']['label'].$this->close_tag('label')?><small><br>Selecione uma foto de rosto. Somente para uso interno. Não será publicada.</small>
			<div id="foto_wrapper">
				<?php echo $this->fields_output['a.alu_foto']['field']?>
			</div>
		</div>
	</div>
</div>

<!-- Seção de Pagamento do Curso -->
<div class="row inscricao_form">
	<h4 class="col-md-12 mt-3">
		Pagamento do Curso<br>
	<?php if($processoSeletivo){?>
	<small>O seu pagamento só será processado após sua aprovação no processo seletivo.</small>
	<?php }?>
	</h4>
</div>

<div class="row mb-3">
	<div class="col-md-6 form-group">
        <?php echo $this->open_label_tag('inscricoes.fop','label').$this->fields_output['inscricoes.fop']['label'].$this->close_tag('label')?>
		<?php echo $this->fields_output['inscricoes.fop']['field']?>
	</div>
	<div class="mesmo_cartao col-md-6 form-group" style="display: none;">
		<?php echo $this->open_label_tag('inscricoes.alu_cartoes','label').$this->fields_output['inscricoes.alu_cartoes']['label'].$this->close_tag('label')?><br>
		<?php echo $this->fields_output['inscricoes.alu_cartoes']['field']?>
    </div>
</div>

<div class="row dados_cartao">
	<div class="col-md-12 ">
		<h5 class="mt-3">Dados do Cartão de Crédito</h5>
	</div>
	<div class="col-md-8 row">
		<div class="col-md-6 mb-3 form-group">
    		<?php echo $this->open_label_tag('inscricoes.rec_cartaoNome','label').$this->fields_output['inscricoes.rec_cartaoNome']['label'].$this->close_tag('label')?>
    		<?php echo $this->fields_output['inscricoes.rec_cartaoNome']['field']?>
		</div>
		<div class="col-md-6 mb-3 form-group">
			<?php echo $this->open_label_tag('inscricoes.rec_cartao','label').$this->fields_output['inscricoes.rec_cartao']['label'].$this->close_tag('label')?>
    		<?php echo $this->fields_output['inscricoes.rec_cartao']['field']?>
		</div>
		<div class="col-md-4 mb-3 form-group">
			<label>Validade <small>(MM / AAAA)</small></label>
			<div class="input-group">
    			<?php echo $this->fields_output['inscricoes.rec_cartaoValidadeMes']['field']?>
    			<?php echo $this->fields_output['inscricoes.rec_cartaoValidadeAno']['field']?>
			</div>
			<?php echo $this->fields_output['inscricoes.rec_cartaoValidade']['field']?>
		</div>
		<div class="col-md-2 mb-3 form-group">
    		<?php echo $this->open_label_tag('inscricoes.rec_cartaoCodigo','label').$this->fields_output['inscricoes.rec_cartaoCodigo']['label'].$this->close_tag('label')?>
    		<?php echo $this->fields_output['inscricoes.rec_cartaoCodigo']['field']?>
    	</div>
		<div class="col-md-3 mb-3 form-group">
            <?php echo $this->open_label_tag('inscricoes.rec_cartaoCPF','label').$this->fields_output['inscricoes.rec_cartaoCPF']['label'].$this->close_tag('label')?>
    		<?php echo $this->fields_output['inscricoes.rec_cartaoCPF']['field']?>
        </div>
	</div>
	<div class="col-md-4">
		<div class="row cartao"></div>
	</div>
</div>

<?php 
// Taxas Adicionais (gtx_primeiraParcela = false)
if (!empty($taxasPosteriores)) {
    foreach ($taxasPosteriores as $taxa) {
        $prefix = 'taxa_' . $taxa['gtx_id'];
?>
<div class="row inscricao_form mt-4">
    <h4 class="col-md-12">
        Pagamento de <?php echo htmlspecialchars($taxa['gtx_descricao'] ?: $taxa['gtx_comentario']); ?><br>
        <small>Taxa adicional - R$ <?php echo number_format($taxa['gtx_valorTotal'], 2, ',', '.'); ?></small>
    </h4>
</div>

<div class="row mb-3">
    <div class="col-md-6 form-group">
        <?php echo $this->open_label_tag('inscricoes.' . $prefix . '_fop','label').$this->fields_output['inscricoes.' . $prefix . '_fop']['label'].$this->close_tag('label')?>
        <?php echo $this->fields_output['inscricoes.' . $prefix . '_fop']['field']?>
    </div>
    <div class="col-md-6 form-group taxa-mesmo-cartao-<?php echo $taxa['gtx_id']; ?>" style="display: none;">
        <?php echo $this->open_label_tag('inscricoes.' . $prefix . '_alu_cartoes','label').$this->fields_output['inscricoes.' . $prefix . '_alu_cartoes']['label'].$this->close_tag('label')?>
        <?php echo $this->fields_output['inscricoes.' . $prefix . '_alu_cartoes']['field']?>
    </div>
</div>

<div class="row dados_cartao_<?php echo $prefix; ?>" style="display: none;">
    <div class="col-md-12">
        <h5 class="mt-3">Dados do Cartão de Crédito</h5>
    </div>
    <div class="col-md-8 row">
        <div class="col-md-6 mb-3 form-group">
            <?php echo $this->open_label_tag('inscricoes.' . $prefix . '_rec_cartaoNome','label').$this->fields_output['inscricoes.' . $prefix . '_rec_cartaoNome']['label'].$this->close_tag('label')?>
            <?php echo $this->fields_output['inscricoes.' . $prefix . '_rec_cartaoNome']['field']?>
        </div>
        <div class="col-md-6 mb-3 form-group">
            <?php echo $this->open_label_tag('inscricoes.' . $prefix . '_rec_cartao','label').$this->fields_output['inscricoes.' . $prefix . '_rec_cartao']['label'].$this->close_tag('label')?>
            <?php echo $this->fields_output['inscricoes.' . $prefix . '_rec_cartao']['field']?>
        </div>
        <div class="col-md-4 mb-3 form-group">
            <label>Validade <small>(MM / AAAA)</small></label>
            <div class="input-group">
                <?php echo $this->fields_output['inscricoes.' . $prefix . '_rec_cartaoValidadeMes']['field']?>
                <?php echo $this->fields_output['inscricoes.' . $prefix . '_rec_cartaoValidadeAno']['field']?>
            </div>
        </div>
        <div class="col-md-2 mb-3 form-group">
            <?php echo $this->open_label_tag('inscricoes.' . $prefix . '_rec_cartaoCodigo','label').$this->fields_output['inscricoes.' . $prefix . '_rec_cartaoCodigo']['label'].$this->close_tag('label')?>
            <?php echo $this->fields_output['inscricoes.' . $prefix . '_rec_cartaoCodigo']['field']?>
        </div>
        <div class="col-md-3 mb-3 form-group">
            <?php echo $this->open_label_tag('inscricoes.' . $prefix . '_rec_cartaoCPF','label').$this->fields_output['inscricoes.' . $prefix . '_rec_cartaoCPF']['label'].$this->close_tag('label')?>
            <?php echo $this->fields_output['inscricoes.' . $prefix . '_rec_cartaoCPF']['field']?>
        </div>
    </div>
</div>
<?php 
    }
}
?>

<div class="row inscricao_form">
	<div class="col-md-12 mb-5">
		<?php echo $this->render_button('save_edit','save','view','btn btn-lg btn-primary col-md-12 spinner_control','','create,edit','',['data-recaptcha'=>'confirmar_inscricao']); ?>
	</div>
</div>
