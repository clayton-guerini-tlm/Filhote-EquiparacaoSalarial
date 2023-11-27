<?php
include_once "../includes/funcoes.php";
$conecta =  RetornaConexaoMysql('local', 'gestao_sistemas');

$sql_sistemas = "Select distinct(area_sistema) from tbl_cadastro_sistemas order by area_sistema asc";
$rs_sistemas = mysqli_query($conecta, $sql_sistemas);
?>


<script type="text/javascript" src="ajax/ajax.js"></script>
<script type="text/javascript" src="ajax/ReqAjax.js"></script>
<link href="../css/index.css" rel="stylesheet" type="text/css" />


<table class="box_relatorio" align="center" >
	<?php while ($row_sistemas = mysqli_fetch_array($rs_sistemas)) {		
				
				$area = $row_sistemas['area_sistema'];
		
		
		
				if ($estilo == "tr_cor_cinza"){
					$estilo = "tr_cor_branco";
				}else{
					$estilo = "tr_cor_cinza";
				}
				
	?> 
				<tr class="<?php echo $estilo; ?>">
					<td><?php echo $area; ?><br />
						<div id="<?php echo $area; ?>"></div>
					</td>
					<td>
						<a onclick="javascript: gerar_area_operacional('<?php echo $area; ?>', '<?php echo $area; ?>');"><img src="../imagens/ico_pesq.png" height="18" width="21"/></a>
					</td>
				</tr>
	<?php } ?>
</table>


