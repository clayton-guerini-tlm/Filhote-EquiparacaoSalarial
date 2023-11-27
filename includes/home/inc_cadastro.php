<script type="text/javascript">
function ValidaFuncionario(){

	var cpf = document.getElementById('cpf').value;
	var retorno =  true;
	var msg;
	
	msg = "Erros encontrados:\n"
	
	if(cpf.length < 11){
		msg += "- CPF inválido.\n";
		retorno = false;
	}
	
	if (! retorno){
		alert(msg);
		return false;
		}else{
			document.getElementById('span_carregando_relatorio').innerHTML = "BUSCANDO DADOS DO FUNCIONÁRIO<br /> AGUARDE...";
			document.getElementById('div_busca').style.display = "none";
			document.getElementById('div_carregando').style.display = "block";
			BuscaDadosFuncionario(cpf);
			return false;
			
	}
	return false;
}

function BuscaDadosFuncionario(cpf){
	var campos = "funcao_ajax=AjaxBuscaDadosFuncionario&cpf="+cpf;
	
	document.getElementById('div_dados_novo').style.display = "none";
	document.getElementById('div_solicitacao').style.display = "none";
		
	var AjaxBuscaDadosFuncionario = getAjax();
	if (AjaxBuscaDadosFuncionario != null) {
		AjaxBuscaDadosFuncionario.open("POST", "ajax/ajax_funcoes.php", true);
		AjaxBuscaDadosFuncionario.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		AjaxBuscaDadosFuncionario.setRequestHeader("Content-length", campos.length);
		AjaxBuscaDadosFuncionario.setRequestHeader("Connection", "close");		
		AjaxBuscaDadosFuncionario.send(campos);
		AjaxBuscaDadosFuncionario.onreadystatechange = function(){
			if (AjaxBuscaDadosFuncionario.readyState == 4 ){
				if(AjaxBuscaDadosFuncionario.responseText == "naoencontrado"){
					document.getElementById('div_carregando').style.display = "none";
					document.getElementById('div_busca').style.display = "block";
					alert('Funcionário não encontrado!');
					}else if (AjaxBuscaDadosFuncionario.responseText == "erro"){
						document.getElementById('div_carregando').style.display = "none";
						document.getElementById('div_busca').style.display = "block";
						alert('Erro ao retornar funcionário!');
						}else{
							ProcessaXMLDadosFuncionario(AjaxBuscaDadosFuncionario.responseXML);
				}
			}
		}
	}
}

function ProcessaXMLDadosFuncionario(obj){

	var dataArray  = obj.getElementsByTagName("funcionario");
	var quant = dataArray.length;
	var campo;
	var id;
	var nome;
	var existe_usuario;
	
	if(dataArray.length > 0){
		document.getElementById('div_carregando').style.display = "none";
	 	for(var i = 0 ; i < quant ; i++) {
	 		campo = dataArray[i];
	 		existe_usuario = campo.getElementsByTagName('existe_usuario')[0].firstChild.nodeValue;
	 		id  	= campo.getElementsByTagName('id')[0].firstChild.nodeValue;
	 		nome  = campo.getElementsByTagName('nome')[0].firstChild.nodeValue;
	 		
	 		document.getElementById('input_id').value  	= id;		 		
	 		if(existe_usuario == "SIM"){		 			
	 			document.getElementById('div_solicitacao').style.display = "block";
			 	document.getElementById('email_solicitacao').focus();
			 	alert('ATENÇÃO: \nEste funcionário já possui um usuário cadastrado no sistema.\nPara liberar a criação do usuário preencha a solicitação.');
		 		}else{
	 				document.getElementById('span_nome').innerHTML = nome;
		 			document.getElementById('div_dados_novo').style.display = "block";
				 	document.getElementById('usuario').focus();
		 	}
	 	}		 	
	}
}

function InserirUsuario(){
	var id = document.getElementById('input_id');
	var usuario = document.getElementById('usuario');
	var nome = document.getElementById('span_nome').innerHTML;
	var email = document.getElementById('email');
	var email_confirmacao = document.getElementById('email_confirmacao');
	var observacao = document.getElementById('observacao');
	var retorno =  true;
	var msg;
	msg = "Erros encontrados:\n\n"
	
	var email_tmp = email.value;
	var email_confirmacao_tmp = email_confirmacao.value;
	
	var obs_tmp = observacao.value.replace(/\'/g,'');
	observacao.value = obs_tmp;
	
	if (usuario.value.length <6){
		msg+="Campo USUÁRIO deve ter conter: \n- Registro Oi: 6 caracteres\n- Registro BrT: 8 caracteres\n\n"
		retorno = false;
	}
	
	/*if(!email_tmp.match(/\b(^(\S+@).+((\.com)|(\.net)|(\.edu)|(\.mil)|(\.gov)|(\.org)|(\..{2,2}))$)\b/gi)) {
		msg+="- Formato de e-mail inválido.\n"
		retorno = false;
	}*/
	
	if (email_tmp != email_confirmacao_tmp){
		msg+="- Campos E-MAIL e CONFIRME O E-MAIL diferentes.\n"
		retorno = false;
	}
	
	/*if (observacao.value.length <1){
		msg+="- Campo OBSERVAÇÕES não pode ser vazio.\n"
		retorno = false;
	}*/
	
	if(!retorno){
		alert(msg);
		return false;
	}
	
	document.getElementById('div_dados_novo').style.display = "none";
	document.getElementById('span_carregando_relatorio').innerHTML = "CADASTRANDO USUÁRIO<br /> AGUARDE...";
	document.getElementById('div_carregando').style.display = "block";
	
	var campos = "funcao_ajax=AjaxInserirUsuario&id="+id.value+"&usuario="+usuario.value+"&nome="+nome+"&email="+email_tmp+"&observacao="+observacao.value;
		
	var AjaxInserirUsuario = getAjax();
	if (AjaxInserirUsuario != null) {
		AjaxInserirUsuario.open("POST", "ajax/ajax_funcoes.php", true);
		AjaxInserirUsuario.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		AjaxInserirUsuario.setRequestHeader("Content-length", campos.length);
		AjaxInserirUsuario.setRequestHeader("Connection", "close");		
		AjaxInserirUsuario.send(campos);
		AjaxInserirUsuario.onreadystatechange = function(){
			if (AjaxInserirUsuario.readyState == 4 ){
				if(AjaxInserirUsuario.responseText == 'inseriu'){
					id.value = 0;
					email.value = "";
					email_confirmacao.value = "";
					observacao.value = "";
					document.getElementById('span_nome').innerHTML = "??NOME??";
					document.getElementById('div_dados_novo').style.display = "none";
					document.getElementById('div_carregando').style.display = "none";
					alert('Usuário cadastrado com êxito no SIGO INTEGRADO! Aguarde a liberação de seu acesso pelos administradores. \n\n ATENÇÃO usuários fazendo a aualização do cadastro! Continuem acessando o SIGO normalmente pelo endereço que você já utiliza.');
					<?php if (! isset($_GET['usuario'])){ ?>
					window.location.href = 'index.php';
					<?php }else{ ?>
					window.location.href = 'http://189.74.128.245/';
					<?php }?>
					return false;
					}else{
						document.getElementById('div_dados_novo').style.display = "block";
						document.getElementById('div_carregando').style.display = "none";
						alert(AjaxInserirUsuario.responseText);
						return false;

				}
			}
		}
	}
	return false;		
}

function EnviarSolicitacao(){
	var id = document.getElementById('input_id');
	var email = document.getElementById('email_solicitacao');
	var observacao = document.getElementById('observacao_solicitacao');
	var retorno =  true;
	var msg;
	msg = "Erros encontrados:\n\n"
	
	if(!email.value.match(/\b(^(\S+@).+((\.com)|(\.net)|(\.edu)|(\.mil)|(\.gov)|(\.org)|(\..{2,2}))$)\b/gi)) {
		msg+="- Formato de e-mail inválido.\n"
		retorno = false;
	}
	
	if (observacao.value.length <1){
		msg+="- Campo OBSERVAÇÕES não pode ser vazio.\n"
		retorno = false;
	}
	
	if(!retorno){
		alert(msg);
		return false;
	}
	
	document.getElementById('div_solicitacao').style.display = "none";
	document.getElementById('span_carregando_relatorio').innerHTML = "CADASTRANDO SOLICITAÇÃO<br /> AGUARDE...";
	document.getElementById('div_carregando').style.display = "block";
	
	var campos = "funcao_ajax=AjaxEnviarSolicitacao&tipo=1&id="+id.value+"&email="+email.value+"&observacao="+observacao.value;
		
	var AjaxEnviarSolicitacao = getAjax();
	if (AjaxEnviarSolicitacao != null) {
		AjaxEnviarSolicitacao.open("POST", "ajax/ajax_funcoes.php", true);
		AjaxEnviarSolicitacao.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		AjaxEnviarSolicitacao.setRequestHeader("Content-length", campos.length);
		AjaxEnviarSolicitacao.setRequestHeader("Connection", "close");		
		AjaxEnviarSolicitacao.send(campos);
		AjaxEnviarSolicitacao.onreadystatechange = function(){
			if (AjaxEnviarSolicitacao.readyState == 4 ){
				if(AjaxEnviarSolicitacao.responseText == 'inseriu'){
					id.value = 0;
					email.value = "";
					observacao.value = "";
					document.getElementById('div_solicitacao').style.display = "none";
					document.getElementById('div_carregando').style.display = "none";
					alert('Sua solicitação foi cadastrada com êxito, e será avaliada pelo administrador do sistema.');
					window.location.href = 'index.php';
					return false;
					}else{
						document.getElementById('div_solicitacao').style.display = "block";
						document.getElementById('div_carregando').style.display = "none";
						alert(AjaxEnviarSolicitacao.responseText);
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
		<td nowrap><span class="cabecalho_tr">NOVO USU&Aacute;RIO SIGO</span></td>
	</tr>
</table>
<br /><br />
<div id="div_busca" style="display:block">
<form onsubmit="return ValidaFuncionario();">
<table class="box_relatorio" width="300" align="center" border="1">
	<tr class="subcabecalho_tr">
		<td colspan="2">FUNCION&Aacute;RIOS DA TELEMONT</td>
	</tr>
	<tr class="tr_cor_cinza">
		<td>CPF</td>
		<td><input value="" name="cpf" type="text" id="cpf" size="20" maxlength="11" onKeyPress="return txtBoxFormat(document.form1,'cpf','99999999999', event);"></td>
	</tr>
	<tr class="subcabecalho_tr">
		<td colspan="2"><input type="submit" value="BUSCAR"><input type="button" value="LIMPAR" onclick="window.location.href='?mainapp=home&app=cadastro';"><input type="button" value="VOLTAR" onclick="window.location.href='index.php';"></td>
	</tr>
</table>
</form>

</div>
<br />
<input type="hidden" id="input_id" value="0" />
<div id="div_dados_novo" style="display:none">
<form id="formNovo" onsubmit="return InserirUsuario();">
	<table class="box_relatorio" width="420" align="center" border="1">
		<tr class="subcabecalho_tr">
			<td colspan="2">DADOS DO FUNCION&Aacute;RIO</td>
		</tr>
		<tr class="tr_cor_cinza">
			<td>NOME</td>
			<td><div align="left"><span id="span_nome">??NOME??</span></div></td>
		</tr>
		<tr class="tr_cor_branco">
			<td>REGISTRO Oi</td>
			<td><div align="left"><input name="usuario" type="text" id="usuario" size="20" maxlength="8" value="<?php echo isset($_GET['usuario']) ? $_GET['usuario'] : '' ?>" /></div></td>
		</tr>
		<tr class="tr_cor_cinza">
			<td>E-MAIL</td>
			<td><div align="left"><input name="email" type="text" id="email" size="40" maxlength="50" value="" />
			</div></td>
		</tr>
		<tr class="tr_cor_branco">
			<td>CONFIRME O E-MAIL</td>
			<td><div align="left"><input name="email_confirmacao" type="text" id="email_confirmacao" size="40" maxlength="50" value="" />
			</div></td>
		</tr>
		<tr class="tr_cor_cinza">
			<td>OBSERVA&Ccedil;&Otilde;ES DO FUNCION&Aacute;RIO (SETOR, SUPERIOR, ETC)</td>
			<td><textarea id="observacao" rows="5" style="width:290px"></textarea></td>
		</tr>
	<tr class="subcabecalho_tr">
		<td colspan="2"><input type="submit" value="CADASTRAR"><input type="button" value="LIMPAR" onclick="window.location.href='?mainapp=home&app=cadastro';"></td>
	</tr>
	</table>
</form>	
</div>
<div id="div_solicitacao" style="display:none">
<form onsubmit="return EnviarSolicitacao();">
	<table class="box_relatorio" width="500" align="center" border="1">
		<tr class="subcabecalho_tr">
			<td colspan="2">LIBERA&Ccedil;&Atilde;O DE USU&Aacute;RIO</td>
		</tr>
		<tr class="tr_cor_branco">
			<td>E-MAIL DE RETORNO</td>
			<td><input name="email_solicitacao" type="text" id="email_solicitacao" size="40" maxlength="100" /></td>
		</tr>
		<tr class="tr_cor_cinza">
			<td>OBSERVA&Ccedil;&Otilde;ES DO FUNCION&Aacute;RIO (SETOR, SUPERIOR, ETC)</td>
			<td><textarea id="observacao_solicitacao" rows="5" style="width:325px"></textarea></td>
		</tr>
	<tr class="subcabecalho_tr">
		<td colspan="2"><input type="submit" value="SOLICITAR"><input type="button" value="LIMPAR" onclick="window.location.href='?mainapp=home&app=cadastro';"></td>
	</tr>
	</table>
</form>
</div>
<div id="div_carregando" class="div_carregando_relatorio">
	<div align="center"><img src="imagens/loading.gif" /></div>
	<span class="span_carregando_relatorio" id="span_carregando_relatorio">????<br /> AGUARDE...</span>
</div>