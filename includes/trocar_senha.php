<?php 

$caminho_raiz = "../";

include "funcoes.php";
include "valida_sessao.php";

if($_GET['msg'] == 'senha_expirada'){
	$msg_trocar_senha = "ATEN&Ccedil;&Atilde;O! Senha expirada! Favor alterar a mesma para prosseguir...";
}

?>


<html>
<title>:: SIGO - SISTEMA INTEGRADO DE GESTÃO OPERACIONAL - TROCAR SENHA ::</title>

<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; CHARSET=iso-8859-1">
<link rel="shortcut icon" href="<?php echo $caminho_raiz?>imagens/favicon.ico" type="image/x-icon" />
<link href="<?php echo $caminho_raiz?>css/menu.css" rel="stylesheet" type="text/css">
<link href="<?php echo $caminho_raiz?>css/index.css" rel="stylesheet" type="text/css">

<!--Calendario-->
	<link type="text/css" rel="stylesheet" href="<?php echo $caminho_raiz?>css/dhtmlgoodies_calendar.css?random=20051112" media="screen"></LINK>
	<SCRIPT type="text/javascript" src="<?php echo $caminho_raiz?>js/dhtmlgoodies_calendar.js?random=20060118"></script>
<!--Fim Calendario-->
<script src="<?php echo $caminho_raiz?>js/formatacaixadetexto.js"></script>

<script src="<?php echo $caminho_raiz?>js/funcoes.js" type=text/javascript></script>
<script src="<?php echo $caminho_raiz?>js/padrao.js" type=text/javascript></script>
</head>
<body>
	<div id="div_geral">
		<div class="div_topo">
		  	<a href="index.php"><img src="<?php echo $caminho_raiz?>imagens/banner_topo.jpg" border="0" /></a>
		  	<div class="div_menu">
				<ul id="menu">
					<li>
						<a href="logout.php">SAIR E EFETUAR LOGIN</a>
					</li>
				</ul>
			</div>
		</div>
		<div class="div_meio" >
			<?php include "{$caminho_raiz}includes/inc_trocar_senha.php"; ?>
		</div>
	</div>
	
	<script type="text/javascript">
	//<!--
		AlterarCorTrs();
	//-->
	</script>
	
</body>
</html>
