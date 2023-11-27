<script type="text/javascript">
function TrocarSenha(){

	var senha = document.getElementById('senha');
	var nova_senha = document.getElementById('nova_senha');
	var conf_nova_senha = document.getElementById('conf_nova_senha');
	var retorno =  true;
	var msg;
	msg = "Erros encontrados:\n\n"
	if (senha.value.length <1){
		msg+="- Campo SENHA não pode ser vazio.\n"
		retorno = false;
	}
	
	if(nova_senha.value.length <1) {
		msg+="- Campo NOVA SENHA não pode ser vazio.\n"
		retorno = false;
	}
	
	if(nova_senha.value != conf_nova_senha.value) {
		msg+="- Campo NOVA SENHA e CONFRIME A NOVA SENHA devem ser iguais.\n"
		retorno = false;
	}
	
	
	if(!retorno){
		alert(msg);
		return false;
	}
	

	
	document.getElementById('div_busca').style.display = "none";
	document.getElementById('span_carregando_relatorio').innerHTML = "ALTERANDO SENHA<br /> AGUARDE...";
	document.getElementById('div_carregando').style.display = "block";
		
	var campos = "funcao_ajax=AjaxTrocarSenha&senha="+escape(senha.value)+"&nova_senha="+escape(nova_senha.value);
	var AjaxTrocarSenha = getAjax();

	if (AjaxTrocarSenha != null) {
		AjaxTrocarSenha.open("POST", "<?php echo $caminho_raiz?>ajax/ajax_funcoes.php", true);
		AjaxTrocarSenha.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		AjaxTrocarSenha.setRequestHeader("Content-length", campos.length);
		AjaxTrocarSenha.setRequestHeader("Connection", "close");		
		AjaxTrocarSenha.send(campos);
		AjaxTrocarSenha.onreadystatechange = function(){
			if (AjaxTrocarSenha.readyState == 4 ){
				if(AjaxTrocarSenha.status == 200){
					if(AjaxTrocarSenha.responseText == 'alterou'){
						document.getElementById('div_carregando').style.display = "none";
						alert('Sua senha foi alterada com êxito.');
						window.location.href = '../';
						return false;
					}else{
						document.getElementById('div_busca').style.display = "block";
						document.getElementById('div_carregando').style.display = "none";
						alert(AjaxTrocarSenha.responseText);
						return false;
					}
				}
			}
		}
	}
	return false;
}
</script>
<table class="box_relatorio" width="1024" border="0">
	<tr class="cabecalho_tr">
		<td nowrap><span class="cabecalho_tr">ALTERA&Ccedil;&Atilde;O DE SENHA</span></td>
	</tr>
</table>
<br />
<div id="div_busca" style="display:block">
<form onsubmit="return TrocarSenha();">
<table class="box_relatorio" width="420" align="center" border="1">
	<tr class="subcabecalho_tr">
		<td colspan="2">PREENCHA OS SEGUINTES DADOS</td>
	</tr>
	<tr class="tr_cor_cinza">
		<td>SENHA ATUAL</td>
		<td><div align="left"><input name="senha" type="password" id="senha" size="20" maxlength="20" /></div></td>
	</tr>
	<tr class="tr_cor_cinza">
		<td>NOVA SENHA</td>
		<td><div align="left"><input name="nova_senha" type="password" id="nova_senha" size="20" maxlength="20" /></div></td>
	</tr>
	<tr class="tr_cor_cinza">
		<td>CONFIRME A NOVA SENHA</td>
		<td><div align="left"><input name="conf_nova_senha" type="password" id="conf_nova_senha" size="20" maxlength="20" /></div></td>
	</tr>

	<tr class="subcabecalho_tr">
		<td colspan="2"><input type="submit" value="ENVIAR"><input type="button" value="LIMPAR" onclick="window.location.href='?mainapp=sistema&app=trocar_senha';"></td>
	</tr>
</table>
</form>

<div align="center" style="font-weight:bold;font-size:12px;color:red;" style="display:<?php echo $display_msg ?>">
	<?php echo $msg_trocar_senha ?>
</div>

</div>

<div id="div_carregando" class="div_carregando_relatorio">
	<div align="center"><img src="<?php echo $caminho_raiz?>imagens/loading.gif" /></div>
	<span class="span_carregando_relatorio" id="span_carregando_relatorio">????<br /> AGUARDE...</span>
</div>