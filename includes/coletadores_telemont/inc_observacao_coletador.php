<?php 

include_once "../../includes/funcoes.php";
$conexao =  RetornaConexaoMysql('local', 'modulo_coletadores'); 

$get = trataGet($conexao);

$qry_obs = "Select observacao from tbl_cadastro_sistemas where id = '{$get['id']}'";
$rs_obs = mysqli_query($conexao, $qry_obs);

$row = mysqli_fetch_assoc($rs_obs);

?>


<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; CHARSET=iso-8859-1" />
	<meta http-equiv="pragma" content="no-cache" />
	<meta http-equiv="expires" content="-1" />
	
	<link rel="shortcut icon" href="<?php echo $caminho_raiz?>favicon.ico" type="image/x-icon" />
	
	<link href="<?php echo $caminho_raiz?>css/index.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo $caminho_raiz?>css/menu.css" rel="stylesheet" type="text/css" />
	<link type="text/css" rel="stylesheet" href="<?php echo $caminho_raiz?>css/dhtmlgoodies_calendar.css" />
	
	<script src="<?php echo $caminho_raiz?>js/funcoes.js" type=text/javascript></script>
	<script src="<?php echo $caminho_raiz?>js/padrao.js" type=text/javascript></script>
	<script src="<?php echo $caminho_raiz?>js/recursos.js" type=text/javascript></script>
	<script src="<?php echo $caminho_raiz?>js/dhtmlgoodies_calendar.js" type=text/javascript></script>
	
	<title>OBSERVAÇÃO COLETADOR</title>
	
	<style type="text/css" >
	
	</style>	
</head>
<body>
	<table class="box_relatorio" width="100%" >
		<tr class="tr_cor_branco" >
			<td><?php echo $row['observacao']; ?></td>
		</tr>				
	</table>
	
	<br />
	
	<div id="div_resposta" ></div>
</body>
</html>
<?php
@mysqli_close($conexao); 
?>