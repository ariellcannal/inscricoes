<!DOCTYPE html>
<html lang="<?php echo $this->config->item('language')?>">
<head>
<meta charset="utf-8">
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#084c6e">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link href="<?php echo site_url('/writable/logos/CANNAL_SIMB_POS192x192.png');?>" rel="icon" media="(prefers-color-scheme: light)">
<link href="<?php echo site_url('/writable/logos/CANNAL_SIMB_NEG192x192.png');?>" rel="icon" media="(prefers-color-scheme: dark)">
<title><?php isset($title)?$title:'CANNAL Produções'?></title>
    <?php $this->assets->print_view_head()?>
    <?php $this->assets->renderInline();?>
<?php if(@$closeHead !== false):?>
</head>
<?php endif;?>