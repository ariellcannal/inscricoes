<?php
if ($this->get_var('custom_head') != false){
    require (XCRUD_PATH . '/' . Xcrud_config::$themes_path . $this->get_var('custom_head'));
}
if($this->is_inner){
    ?><div class="container content_block col-md-12 col-lg-9 p-0"><?php 
}
?>
<div class="content_block">
    <div class="navbar navbar-light bg-light d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 px-3">
    	<?php echo $this->render_table_name('list',array('tag'=>'h2','class'=>'h2')); ?>
    	<div class="btn-toolbar mb-2 mb-md-0 justify-content-end col-md-8 px-0">
    		<div class="d-flex flex-column">
    			<div class="btn-group mb-1">
        			<?php echo $this->render_custom_filter('esquerda')?>
                    <?php echo $this->render_custom_buttons();?>
        			<?php echo $this->print_button ( 'btn btn-sm btn-info', 'btn-icon fas fa-print' ); ?>
                    <?php echo $this->csv_button ( 'btn btn-sm btn-info', 'btn-icon fas fa-file' );?>
                    <?php echo $this->refresh_button ( 'btn btn-sm btn-info', 'btn-icon fas fa-sync' );?>
                    <?php echo $this->add_button('btn btn-sm btn-info','btn-icon fas fa-plus'); ?>
                    <?php echo $this->search_button ('Buscar');?>
                </div>
                <?php echo $this->render_search(); ?>
    		</div>
    	</div>
    </div>
    <div class="navbar navbar-light bg-light p-0 m-0">
        <div class="d-flex justify-content-start flex-wrap flex-md-nowrap col-md-12 col-lg-3 py-2 px-3">
            <?php echo $this->render_mass_actions();?>
        </div>
        <div class="col-md-12 px-3">
            <?php echo $this->render_mass_edit_form();?>
        </div>
    </div>
    <main class="xcrud-list-container table-responsive">
    	<table class="xcrud-list table table-hover table-striped table-sm">
    		<thead>
                    <?php echo $this->render_grid_head('tr', 'th', array('asc' =>'<i class="fas fa-arrow-up"></i> &nbsp;', 'desc' => '<i class="fas fa-arrow-down"></i> &nbsp;')); ?>
                </thead>
    		<tbody>
                    <?php echo $this->render_grid_body('tr', 'td'); ?>
                </tbody>
    		<tfoot>
                    <?php echo $this->render_grid_footer('tr', 'td'); ?>
                </tfoot>
    	</table>
    </main>
    <?php if($this->get_var('footer_note') || $this->result_total > $this->limit){?>
    <nav class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center  p-3" aria-label="Pagination">
    	<div><?php echo $this->get_var('footer_note')?></div>
    	<div class="d-flex justify-content-end">
    	<?php echo $this->render_limitlist(); ?>
    	<?php echo $this->render_pagination(7,1); ?>
    	</div>
    </nav>
    <?php }?>
</div>
<?php 
if($this->is_inner){
    ?></div><?php 
}
?>