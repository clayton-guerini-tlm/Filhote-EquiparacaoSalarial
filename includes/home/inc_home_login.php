<?php
/*include "includes/funcoes.php";*/

/*
$conecta = RetornaConexaoMysql('local', 'sigo_integrado');

$session_id = Verificar_id_session_php();

if(!$session_id){
	$msg_home = "Não foi possivel recuperar o PHPSESSID. Favor contactar o administrador do sistema"; 
}else{
	$Sql_delete_session_id = "UPDATE tbl_log_acesso 
									SET lga_session_id = NULL, 
										lga_session_descricao = NULL, 
										lga_tempo_ini_sessao = NULL, 
										lga_tempo_fim_sessao = NULL, 
										lga_tempo_atual_sessao = NULL 
								WHERE lga_session_id = '$session_id'";
	@mysqli_query($conecta, $Sql_delete_session_id);
	
	session_start();
}*/
?>

<script type="text/javascript">
function ValidaLogin(){
	var login = document.getElementById('login');
	var senha = document.getElementById('senha');
	var retorno =  true;
	var msg;
	msg = "Erros encontrados:\n\n";
	
	if (login.value == ""){
		msg+="- Campo LOGIN não pode ser vazio.\n"
		retorno = false;
	}
	
	if (senha.value == ""){
		msg+="- Campo SENHA não pode ser vazio.\n"
		retorno = false;
	}
	
	if(!retorno){
		alert(msg);
		return false;
	}else{
		document.getElementById('div_login').style.display = "none";
		document.getElementById('div_carregando').style.display = "block";
		
		return true;
	}
}
</script>
<link  href="css/login.css" rel="stylesheet" type="text/css" />
<table class="box_relatorio" width="100%" border="0">
	<tr class="cabecalho_tr">
		<td nowrap><span class="cabecalho_tr">ACESSO AO PORTAL</span></td>
	</tr>
</table>
<form id="form_login" action="includes/valida_login.php" method="POST" onsubmit="return ValidaLogin();">
<div align="center" id="div_login" class="div_login" style="display:block">
	<span class="txt_login">USU&Aacute;RIO</span>
	<div style="margin-top:5px"></div>
	<input type="text" name="login" id="login" value="<?php echo (isset($_GET['login']) ? $_GET['login'] : ''); ?>" size="20" maxlength="20">
	<div style="margin-top:5px"></div>
	<span class="txt_login">SENHA</span>
	<div style="margin-top:5px"></div>
	<input type="password" name="senha"  id="senha" size="20" maxlength="20">
	<div style="margin-top:5px"></div>
	<input type="submit" value="ENTRAR">
	<input type="button" value="LIMPAR" onclick="window.location.href='index.php'">
	<div style="margin-top:5px"></div>			
</div>
</form>
<div align="center" id="div_carregando" class="div_carregando_login" style="display:none">
	<img src="imagens/loading.gif" /><br />
	<span class="span_carregando_relatorio" id="span_carregando_relatorio">AUTENTICANDO O LOGIN<br /> AGUARDE...</span>			
</div>
<div align="center" class="div_msg_login" style="display:<?php echo $display_msg ?>">
	<?php echo $msg_home ?>
</div>


 <table class="box_relatorio fim" width="100%" border="0">
	<tr class="cabecalho_tr">
		<td nowrap><span class="cabecalho_tr">CONTROLE DE USU&Aacute;RIOS</span></td>
	</tr>
	<tr>
		<td align="center" nowrap>NOVO USU&Aacute;RIO? CADASTRE-SE <a href="./index.php?mainapp=home&app=cadastro">AQUI</a>.</td>
	</tr>
	<tr>
		<td align="center" nowrap>ESQUECEU A SENHA? CLIQUE <a href="./index.php?mainapp=home&app=recadastro_senha">AQUI</a>.</td>
	</tr> 
	<tr class="cabecalho_tr">
		<td nowrap><span class="cabecalho_tr">MELHOR VISUALIZADO COM 1024 X 768 - INTERNET EXPLORER 7.0</span></td>
	</tr>	
</table>