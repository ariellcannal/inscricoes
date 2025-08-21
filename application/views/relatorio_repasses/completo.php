<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="<?=$this->config->item('language')?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Relatório de Pagamento - <?php echo $usr['usr_nome']?> - Grupos de Estudos - Grupo TAPA</title>
<style>
$theme-colors: (
  primary: #FFCC00
);
@page{
margin: 0px;
}
</style>
<link type="text/css" href="<?php echo site_url('/assets/plugins/bootstrap/bootstrap.min.css')?>" rel="stylesheet" />
<style>
*,h1,h2,th{
    font-size: 96%;
    font-family: sans-serif!important;
}
th{
text-transform: uppercase;
}
</style>
</head>
<body class="">    
    	<table class="table table-borderless" style="width: 100%;margin-bottom: 20px;">
          <thead>
            <tr>
              <th style="text-align: center; font-size: 150%">Relatório de Repasses<br/><?php echo $usr['usr_nome']?><br/><span style="font-size: 70%!important">Atualizado até <?php echo date('d/m/Y \à\s H:i:s')?></span></th>
            </tr>
          </thead>
        </table>
        <?php if(count($grupos) && 1!=1){
            ?>
            <table class="table table-sm text-right">
              <tbody>
              	<tr class="table-info">
              		<th colspan="6" class="text-center">Resumo dos Grupos - Últimos <?php echo $months?> meses</th>
              	</tr>
              		<tr>
                  		<th class="text-right">Grupo</th>
                  		<td>Arrecadação Líquida</td>
                  		<td>Porcentagem</td>
                  		<td>Repassado</td>
                  		<td>A Repassar</td>
                  		<td>Repasse Total</td>
                  	</tr>
              	<?php foreach($grupos as $grp_id=>$i){
              	    $total_data = 0;
              	    ?>
                  	<tr>
                  		<th class="text-right"><?php echo $i['grupo']?></th>
                  		<td><?php echo number_format($i['total_grupo'],2,',','.')?></td>
                  		<td><?php echo number_format($i['porcentagem'],0,',','.')?>%</td>
                  		<td><?php echo number_format($i['repassado'],2,',','.')?></td>
                  		<td><?php echo number_format($i['a_repassar'],2,',','.')?></td>
                  		<td><?php echo number_format($i['repassado']+$i['a_repassar'],2,',','.')?></td>
                  	</tr>
                <?php } ?>
              </tbody>
            </table>
        <?php }?>
        
        <?php if(count($pendentes)){
            $this->load->view('relatorio_repasses/pendentes.php', ['pendentes'=>$pendentes]);
        }?>
        
        <?php if(count($previstos)){
            $this->load->view('relatorio_repasses/previstos.php', ['previstos'=>$previstos]);
        }?>
        
        <?php if(count($pagos)){
            $this->load->view('relatorio_repasses/pagos.php', ['pagos'=>$pagos]);
        }?>
</body>
</html>