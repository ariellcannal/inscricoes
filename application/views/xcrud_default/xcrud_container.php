<?php
if (in_array($this->task, [
    'create',
    'edit'
])) {
    $tag = 'form';
} else {
    $tag = 'div';
}
?>
<<?php echo $tag?> class="xcrud<?php echo $this->is_rtl ? ' xcrud_rtl' : ''?>">
    <?php echo $this->render_table_name(false, 'div', true)?>
    <div class="xcrud-container" <?php echo ($this->start_minimized) ? ' style="display:none;"' : '' ?>>
		<div class="xcrud-ajax">
            <?php echo $this->render_view() ?>
        </div>
    	<div class="xcrud-overlay d-flex justify-content-center align-items-center">
        	<div class="spinner-border" role="status">
              <span class="visually-hidden">Aguarde...</span>
            </div>
    	</div>
	</div>
</<?php echo $tag?>>