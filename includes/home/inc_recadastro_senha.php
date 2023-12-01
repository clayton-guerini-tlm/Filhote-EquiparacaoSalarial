<script type="text/javascript">
function RecadastraSenha(){
	var usuario = document.getElementById('usuario');
	var email = document.getElementById('email');
	var retorno =  true;
	var msg;
	msg = "Erros encontrados:\n\n"
	
	var email_tmp = email.value;
	
	if (usuario.value.length <6){
		msg+="Campo USUÁRIO deve ter conter: \n- Registro Oi: 6 caracteres\n- Registro BrT: 8 caracteres\n\n"
		retorno = false;
	}
	
	if(!email_tmp.match(/\b(^(\S+@).+((\.com)|(\.net)|(\.edu)|(\.mil)|(\.gov)|(\.org)|(\..{2,2}))$)\b/gi)) {
		msg+="- Formato de e-mail inválido.\n"
		retorno = false;
	}
	
	if(!retorno){
		alert(msg);
		return false;
	}
	
	document.getElementById('div_busca').style.display = "none";
	document.getElementById('span_carregando_relatorio').innerHTML = "BUSCANDO DADOS DO FUNCIONÁRIO<br /> AGUARDE...";
	document.getElementById('div_carregando').style.display = "block";
	
	
	var campos = "funcao_ajax=AjaxRecadastraSenha&usuario="+usuario.value+"&email="+email_tmp;
		
	var AjaxRecadastraSenha = getAjax();
	if (AjaxRecadastraSenha != null) {
		AjaxRecadastraSenha.open("POST", "ajax/ajax_funcoes.php", true);
		AjaxRecadastraSenha.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		AjaxRecadastraSenha.setRequestHeader("Content-length", campos.length);
		AjaxRecadastraSenha.setRequestHeader("Connection", "close");		
		AjaxRecadastraSenha.send(campos);
		AjaxRecadastraSenha.onreadystatechange = function(){
			if (AjaxRecadastraSenha.readyState == 4 ){
				if(AjaxRecadastraSenha.responseText == 'alterou'){
					document.getElementById('div_carregando').style.display = "none";
					alert('Sua senha foi recadastrada. Verifique seu e-mail para informações de acesso ao SIGO.');
					window.location.href = 'index.php';
					return false;
					}else{
						document.getElementById('div_busca').style.display = "block";
						document.getElementById('div_carregando').style.display = "none";
						alert(AjaxRecadastraSenha.responseText);
						return false;
				}
			}
		}
	}
	return false;
}
</script>

<link href="css/login.css" rel="stylesheet" type="text/css" />
<table class="box_relatorio" width="100%" border="0">
	<tr class="cabecalho_tr">
		<td nowrap><span class="cabecalho_tr">RECADASTRO DE SENHA</span></td>
	</tr>
</table>
<br />
<div id="div_busca" style="display:block">
<form onsubmit="return RecadastraSenha();">
<table class="box_relatorio" width="420" align="center" border="1">
	<tr class="subcabecalho_tr">
		<td colspan="2">PREENCHA OS SEGUINTES DADOS</td>
	</tr>
	<tr class="tr_cor_cinza">
		<td>USU&Aacute;RIO SIGO</td>
		<td><div align="center"><input name="usuario" type="text" id="usuario" size="40" maxlength="8" /></div></td>
	</tr>
	<tr class="tr_cor_branco">
		<td>E-MAIL</td>
		<td><div align="center"><input name="email" type="text" id="email" size="40" maxlength="50" value="" /></div></td>
	</tr>
	<tr class="subcabecalho_tr">
		<td colspan="2"><input type="submit" value="BUSCAR"><input type="button" value="LIMPAR" onclick="window.location.href='?mainapp=home&app=recadastro_senha';"><input type="button" value="VOLTAR" onclick="window.location.href='index.php';"></td>
	</tr>
</table>
</form>
</div>

<div id="div_carregando" class="div_carregando_relatorio">
	<div align="center"><img src="imagens/loading.gif" /></div>
	<span class="span_carregando_relatorio" id="span_carregando_relatorio">????<br /> AGUARDE...</span>
</div>