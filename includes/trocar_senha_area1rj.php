<?php 

$caminho_raiz = "../";

include "{$caminho_raiz}includes/funcoes.php";
include "{$caminho_raiz}includes/valida_sessao.php";

/*if($_SESSION['SIGO']['ACESSO']['USUARIO'] != "049164"){
	die('Em manutenção.');
}*/

if($_GET['msg'] == 'senha_expirada'){
	$msg_trocar_senha = "ATEN&Ccedil;&Atilde;O! Senha expirada! Favor alterar a mesma para prosseguir...";
}

$onload = '';
if($_POST){
	
	$conexao 			= RetornaConexaoMysql('local','sigo_integrado');
	
	$post 				= trataPost($conexao);
	
	$usr_usuario		= $_SESSION['SIGO']['ACESSO']['USUARIO'];
	
	/**
	 * Recupera os dados oriundos do formulario.
	 */
	$senha_atual		= md5($_POST['senha_atual']);
	$nova_senha 		= md5($post['nova_senha']);
	$conf_nova_senha 	= md5($post['conf_nova_senha']);
	
	$qry 	= "select usr_senha from tbl_usuario where usr_usuario = '{$usr_usuario}' ";
	$rs 	= mysqli_query($conexao, $qry) or die('Erro: ' . mysqli_error($conexao));
	$row 	= mysqli_fetch_assoc($rs);
	
	if($row['usr_senha'] != $senha_atual){
		$msg_trocar_senha = "Senha atual inválida.";
	}else{
		
		if($nova_senha != $conf_nova_senha){
			$msg_trocar_senha = "Nova senha e confirmação de nova senha estão diferentes.";
		}else{
			
			if(strpos($nova_senha, ' ')){
				$msg_trocar_senha = "Nova senha não pode conter espaços.";
			}else{
				
				$qry = "update tbl_usuario set usr_senha = '$nova_senha', usr_senha_padrao = 0, usr_data_troca_senha = NOW() where usr_usuario = '$usr_usuario' ";
				mysqli_query($conexao, $qry) or die('Erro: ' . mysqli_error($conexao));
				
				GravaLogSentenca($conexao, $qry);
				
				$onload = 'onload="javascript:alert(\'Senha atualizada com sucesso.\'); window.location.href = \'?a=ok\';"';
				
			}
			
		}
		
	}
	
	mysqli_close($conexao);
	
}

/*echo "<pre>";
print_r($_SESSION);
echo "</pre>";
exit();*/
?>
<html>
<title>:: SIGO - SISTEMA INTEGRADO DE GESTÃO OPERACIONAL - TROCAR SENHA ::</title>

<head>
<meta http-equiv="Content-Type" content="text/html; Charset=iso-8859-1" />

<link rel="shortcut icon" href="<?php echo $caminho_raiz; ?>imagens/favicon.ico" type="image/x-icon" />

<link href="<?php echo $caminho_raiz; ?>css/menu.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $caminho_raiz; ?>css/index.css" rel="stylesheet" type="text/css" />

<script src="<?php echo $caminho_raiz; ?>js/funcoes.js" type="text/javascript" ></script>

<script type="text/javascript" >
//<!--
function validaFormTrocarSenha(){

	var senha 				= document.getElementById('senha_atual').value.trim();
	var nova_senha 			= document.getElementById('nova_senha').value.trim();
	var conf_nova_senha 	= document.getElementById('conf_nova_senha').value.trim();

	if(senha == '' || nova_senha == '' || conf_nova_senha == ''){
		alert('Todos os campos são de preenchimento obrigatorio');
		return false;
	}
	
	return true;
}
//-->
</script>

</head>
<body <?php echo $onload; ?> >

	<?php
	/*echo "<pre>";
	print_r($_SESSION['SIGO']['ACESSO']);
	echo "</pre>"; */
	?>

	<div id="div_geral">
		<div class="div_topo">
		  	<a href="index.php"><img src="<?php echo $caminho_raiz?>imagens/banner_topo.jpg" border="0" /></a>
		  	<div class="div_menu">
				<ul id="menu">
					<li>
						<a href="logout.php?p=trocar_senha_area2">SAIR E EFETUAR LOGIN</a>
					</li>
				</ul>
			</div>
		</div>
		<div class="div_meio" >
			
			
			<table class="box_relatorio" width="1024" border="0">
				<tr class="cabecalho_tr">
					<td nowrap><span class="cabecalho_tr">ALTERA&Ccedil;&Atilde;O DE SENHA</span></td>
				</tr>
			</table>
			
			<br />
			
			<div>ATENÇÃO <?php echo $_SESSION['SIGO']['ACESSO']['NOME']; ?>! As senhas são gravadas na base da matriz em MG e sincronizadas nos outros servidores. O tempo de sincronização pode variar dependendo da quantidade de registros a serem processados no momento da atualização da senha. Portanto, aguarde entre 15 e 20 minutos apos a troca da senha para tentar novamente o acesso ao portal.</div>
			
			<br />
			
			<div id="div_busca" >
			
				<form name="frmTrocarSenha" id="frmTrocarSenha" method="post" action="?a=s" onsubmit="return validaFormTrocarSenha();">
				
					<table class="box_relatorio" width="420" align="center" border="1">
						<tr class="subcabecalho_tr">
							<td colspan="2">PREENCHA OS CAMPOS ABAIXO</td>
						</tr>
						<tr class="tr_cor_cinza">
							<td align="right" >SENHA ATUAL:</td>
							<td align="left" ><input name="senha_atual" type="password" id="senha_atual" size="20" maxlength="20" value="" /></td>
						</tr>
						<tr class="tr_cor_cinza">
							<td align="right" >NOVA SENHA:</td>
							<td align="left" ><input name="nova_senha" type="password" id="nova_senha" size="20" maxlength="20" value="" /></td>
						</tr>
						<tr class="tr_cor_cinza">
							<td align="right" >CONFIRME A NOVA SENHA:</td>
							<td align="left" ><input name="conf_nova_senha" type="password" id="conf_nova_senha" size="20" maxlength="20" value="" /></td>
						</tr>
					
						<tr class="subcabecalho_tr">
							<td colspan="2"><input name="btnEnviar" id="btnEnviar" type="submit" value="ENVIAR" /></td>
						</tr>
					</table>
				</form>
				
				<br />
			
				<div align="center" style="font-weight:bold;font-size:12px;color:red;" >
					<?php echo $msg_trocar_senha; ?>
				</div>
			
			</div>
			
			
		</div>
	</div>
	
</body>
</html>
