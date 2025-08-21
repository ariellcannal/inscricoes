<?php
defined('BASEPATH') or exit('No direct script access allowed');
$this->load->view('header');
?>
<body class="bg-light">
    <div class="container">
    	<table class="table table-borderless">
          <thead>
            <tr>
              <th colspan="3">
              	<h1 class="text-center"><?php echo $grp['grp_nomePublico']?></h1>
              </th>
            </tr>
            <tr>
            	<th><?php echo $grp['grp_encontros'].' encontros  entre '.date('d/m/Y',strtotime($grp['grp_dataInicio'])).' e '.date('d/m/Y',strtotime($grp['grp_dataFim']))?></th>
            	<th rowspan="2" style="width: 40%; vertical-align: top;">Data:</th>
            </tr>
            <tr>
            	<th><?php echo $grp['grp_dias'].', '.$grp['grp_horario']?></th>
            </tr>
          </thead>
        </table>
        <div style="column-count:2;">
        <table class="table table-striped">
          <tbody>
          	<tr>
          	<?php
          	$i=1;
          	foreach($alunos as $a){
          	    
          	?>
            <tr>
              <th scope="row" style="width: 5%"><?php echo $i?></th>
              <td style="width: 50%; white-space: nowrap;"><?php echo $a['alu_nomeArtistico']?></td>
              <td style="width: 45%"></td>
            </tr>
            <?php
            $i++;
            }
            ?>
          </tbody>
        </table>
        </div>
    </div>
<?php $this->load->view('footer')?>