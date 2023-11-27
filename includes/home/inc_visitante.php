<script type="text/javascript">

function EnviarSolicitacaoVisitante(){
	var id = document.getElementById('input_id');
	var email = document.getElementById('email_solicitacao');
	var telefone = document.getElementById('telefone');
	var nome = document.getElementById('nome');
	var registro = document.getElementById('registro');
	var observacao = document.getElementById('observacao_solicitacao');
	var retorno =  true;
	var msg;
	msg = "Erros encontrados:\n\n";
	
	if (nome.value.length <1){
		msg+="- Campo NOME não pode ser vazio.\n"
		retorno = false;
	}
	
	if (nome.value.length <5){
		msg+="- Campo REGISTRO inválido.\n"
		retorno = false;
	}
	
	if (telefone.value.length <13){
		msg+="- Campo TELEFONE inválido.\n"
		retorno = false;
	}
	
	if(!email.value.match(/\b(^(\S+@).+((\.com)|(\.net)|(\.edu)|(\.mil)|(\.gov)|(\.org)|(\..{2,2}))$)\b/gi)) {
		msg+="- Formato de e-mail inválido.\n"
		retorno = false;
	}
	
	if(!retorno){
		alert(msg);
		return false;
	}
	
	document.getElementById('div_solicitacao').style.display = "none";
	document.getElementById('span_carregando_relatorio').innerHTML = "CADASTRANDO SOLICITAÇÃO<br /> AGUARDE...";
	document.getElementById('div_carregando').style.display = "block";
	
	var campos = "funcao_ajax=AjaxEnviarSolicitacao&tipo=2&email="+email.value+"&observacao="+observacao.value+"&telefone="+telefone.value+"&nome="+nome.value+"&registro="+registro.value;
		
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
					email.value = "";
					nome.value = "";
					registro.value = "";
					telefone.value = "";
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

<table class="box_relatorio" width="1024" border="0">
	<tr class="cabecalho_tr">
		<td nowrap><span class="cabecalho_tr">NOVO USU&Aacute;RIO SIGO</span></td>
	</tr>
</table>
<br /><br />

<div id="div_solicitacao" style="display:block">
<form id="form1" onsubmit="return EnviarSolicitacaoVisitante();">
	<table class="box_relatorio" width="400" align="center" border="1">
		<tr class="subcabecalho_tr">
			<td colspan="2">CADASTRO DE VISITANTES</td>
		</tr>
		<tr class="tr_cor_cinza">
			<td colspan="2"><br /><div align="justify">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;O CADASTRO DE VISITANTE PASSARÁ POR UM RIGOROSO PROCESSO ONDE SER&Aacute; VERIFICADO SE O SOLICITANTE REALMENTE DEVE TER ACESSO AO SIGO. <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;PORTANTO, FORNE&Ccedil;A SEUS DADOS REAIS DE FORMA A FACILITAR SUA IDENTIFICA&Ccedil;&Atilde;O.</div>  <br /></td>
		</tr>
		<tr class="tr_cor_branco">
			<td>NOME</td>
			<td><input name="nome" type="text" id="nome" size="40" maxlength="100" /></td>
		</tr>
		<tr class="tr_cor_cinza">
			<td nowrap>REGISTRO Oi</td>
			<td><input name="nome" type="text" id="registro" size="40" maxlength="10" value="<?php echo $_GET['usuario'] ?>" /></td>
		</tr>
		<tr class="tr_cor_branco">
			<td>TELEFONE</td>
			<td><input name="telefone" type="text" id="telefone" size="40" maxlength="13" onKeyPress="return txtBoxFormat(document.form1,'telefone','(99)9999-9999', event);" /></td>
		</tr>
		<tr class="tr_cor_cinza">
			<td>E-MAIL</td>
			<td><input name="email_solicitacao" type="text" id="email_solicitacao" size="40" maxlength="100" /></td>
		</tr>
		<tr class="tr_cor_branco">
			<td>DADOS ADICIONAIS</td>
			<td><textarea id="observacao_solicitacao" rows="5" style="width:325px"></textarea></td>
		</tr>
	<tr class="subcabecalho_tr">
		<td colspan="2"><input type="submit" value="SOLICITAR"><input type="button" value="LIMPAR" onclick="window.location.href='?mainapp=home&app=visitante';"><input type="button" value="VOLTAR" onclick="window.location.href='index.php';"></td>
	</tr>
	</table>
</form>
</div>
<div id="div_carregando" class="div_carregando_relatorio">
	<div align="center"><img src="imagens/loading.gif" /></div>
	<span class="span_carregando_relatorio" id="span_carregando_relatorio">????<br /> AGUARDE...</span>
</div>