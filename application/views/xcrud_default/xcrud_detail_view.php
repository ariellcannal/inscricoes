<?php
if ($this->get_var('custom_head') != false) {
    require (XCRUD_PATH . '/' . Xcrud_config::$themes_path . $this->get_var('custom_head'));
}
require '_blocos/replace_title.php';
?>

<div class="container content_block col-md-12 col-lg-9 p-0">
	<div class="header_actions header_actions d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 px-3">
		<?php echo $this->render_table_name('list',['tag'=>'h2','class'=>'h2'],false,$title); ?>
		<div class="btn-toolbar mb-2 mb-md-0">
			<div class="btn-group me-2">
				<?php
                echo $this->render_button('return', 'list', '', 'btn btn-sm btn-info', 'far fa-list-alt');
                require $_SERVER['DOCUMENT_ROOT'] . '/application/views/xcrud_default/_blocos/buttons_links.php';
                echo $this->render_button('save_edit', 'save', ($this->get_var('after_task') != "") ? $this->get_var('after_task') : 'edit', 'btn btn-sm btn-outline btn-primary', 'fas fa-check', 'create,edit');
                ?>
			</div>
		</div>
	</div>
	<div class="card-body main-container xcrud-view tabs-alpha px-3">
		<?php
        /* DEFAULTS */
        $container = 'form';
        
        $row = 'div';
        $label = ['tag'=>'label','class'=>'col-sm-2'];
        $field = 'div';
        
        $tabs_block = 'div';
        $tabs_content = 'div';
        $tabs_pane = 'div';
        $tabs_head = array(
            'tag' => 'ul',
            'class'=>'nav nav-underline p-0'
        );
        $tabs_row = array(
            'tag' => 'li',
            'class' => 'nav-item'
        );
        $tabs_link = array(
            'tag' => 'a',
            'class' => 'nav-link',
            'aria-current' => 'page'
        );
        echo $this->render_fields_list($mode, $container, $row, $label, $field, $tabs_block, $tabs_head, $tabs_row, $tabs_link, $tabs_content, $tabs_pane);
        ?>
	</div>
	<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-top">
		<h2>&nbsp;</h2>
		<div class="btn-toolbar mb-2 mb-md-0">
			<div class="btn-group me-2">
				<?php
                echo $this->render_button('save_edit', 'save', ($this->get_var('after_task') != "") ? $this->get_var('after_task') : 'edit', 'btn btn-sm btn-outline btn-success', 'fas fa-check', 'create,edit');
                ?>
			</div>
		</div>
	</div>
</div>

	<?php echo $this->render_benchmark(); ?>
</div>
<?php ?>