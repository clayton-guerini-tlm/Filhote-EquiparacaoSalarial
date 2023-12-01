<?php 

$options_usuario_area = RetornaOptionUsuarioFilial();

?>

<script type="text/javascript">
function InserirVisitante(){
	var usuario = document.getElementById('usuario');
	var nome = document.getElementById('nome');
    var cpf = document.getElementById('cpf');
	var area = document.getElementById('area');
	var email = document.getElementById('email');
	var email_confirmacao = document.getElementById('email_confirmacao');
	var observacao = document.getElementById('observacao');
	var retorno =  true;
	var msg;
	msg = "Erros encontrados:\n\n";
	
	var email_tmp = email.value;
	var email_confirmacao_tmp = email_confirmacao.value;
	
	if (nome.value==""){
		msg+="- Campo NOME não pode ser vazio.\n\n";
		retorno = false;
	}
    
    if (!validaCPF(cpf.id)){
        msg+="- Campo CPF inválido.\n\n";
        retorno = false;
    }
	
	if (area.value==""){
		msg+="- Selecione o campo ÁREA.\n\n";
		retorno = false;
	}
	
	if (usuario.value == ""){
		msg+="- Campo USUÁRIO não pode ser vazio\n\n";
		retorno = false;
	}
	
	if(!email_tmp.match(/\b(^(\S+@).+((\.com)|(\.net)|(\.edu)|(\.mil)|(\.gov)|(\.org)|(\..{2,2}))$)\b/gi)) {
		msg+="- Formato de e-mail inválido.\n\n";
		retorno = false;
	}
	
	if (email_tmp != email_confirmacao_tmp){
		msg+="- Campos E-MAIL e CONFIRME O E-MAIL diferentes.\n\n";
		retorno = false;
	}
	
	if (observacao.value.length <1){
		msg+="- Campo OBSERVAÇÕES não pode ser vazio.\n";
		retorno = false;
	}
	
	if(!retorno){
		alert(msg);
		return false;
	}
	
	document.getElementById('div_dados_novo').style.display = "none";
	document.getElementById('span_carregando_relatorio').innerHTML = "CADASTRANDO VISITANTE<br /> AGUARDE...";
	document.getElementById('div_carregando').style.display = "block";
	
	var campos = "funcao_ajax=AjaxInserirVisitante&usuario="+usuario.value+"&nome="+nome.value+"&area="+area.value+"&email="+email_tmp+"&observacao="+observacao.value+"&cpf="+cpf.value;
		
	var AjaxInserirVisitante = getAjax();
	if (AjaxInserirVisitante != null) {
		AjaxInserirVisitante.open("POST", "ajax/ajax_funcoes.php", true);
		AjaxInserirVisitante.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		AjaxInserirVisitante.setRequestHeader("Content-length", campos.length);
		AjaxInserirVisitante.setRequestHeader("Connection", "close");		
		AjaxInserirVisitante.send(campos);
		AjaxInserirVisitante.onreadystatechange = function(){
			if (AjaxInserirVisitante.readyState == 4 ){
				if(AjaxInserirVisitante.responseText == 'inseriu'){
					document.getElementById('div_dados_novo').style.display = "block";
					document.getElementById('div_carregando').style.display = "none";
					usuario.value = "";
					nome.value = "";
                    cpf.value = "";
					area.value = "";
					email.value = "";
					email_confirmacao.value = "";
					observacao.value = "";
					alert('Visitante cadastrado com êxito!');
					return false;
				}else{
					document.getElementById('div_dados_novo').style.display = "block";
					document.getElementById('div_carregando').style.display = "none";
					alert(AjaxInserirVisitante.responseText);
					return false;

				}
			}
		}
	}else{
		alert('Ocorreu um erro inesperado. Não foi possivel instanciar o objeto ajax.\nTente novamente ou entre em contato com o administrador do sistema.');
	}
	
	return false;
}
</script>



<table class="box_relatorio" width="1024" border="0">
	<tr class="cabecalho_tr">
		<td nowrap><span class="cabecalho_tr">NOVO VISITANTE SIGO</span></td>
	</tr>
</table>
<br /><br />
<div id="div_dados_novo" style="display:block">
<form id="formNovo" onsubmit="return InserirVisitante();">
	<table class="box_relatorio" width="420" align="center" border="1">
		<tr class="subcabecalho_tr">
			<td colspan="2">DADOS DO VISITANTE</td>
		</tr>
		<tr class="tr_cor_cinza">
			<td>NOME</td>
			<td><div align="left"><input name="nome" type="text" id="nome" size="50" maxlength="100" value="<?php echo $_GET['valor_get'] ?>" /></div></td>
		</tr>
        <tr class="tr_cor_branco">
            <td>CPF</td>
            <td><div align="left"><input name="cpf" type="text" id="cpf" size="20" maxlength="11" onKeyPress="return txtBoxFormat(document.form1,'cpf','99999999999', event);" /></div></td>
        </tr>
		<tr class="tr_cor_cinza">
			<td>&Aacute;REA</td>
			<td><div align="left"><select name="area" id="area">
			<?php echo $options_usuario_area ?>			
			</select></div></td>
		</tr>
		<tr class="tr_cor_branco">
			<td>LOGIN</td>
			<td><div align="left"><input name="usuario" type="text" id="usuario" size="20" maxlength="8" /></div></td>
		</tr>
		<tr class="tr_cor_cinza">
			<td>E-MAIL</td>
			<td><div align="left"><input name="email" type="text" id="email" size="40" maxlength="50" value="" /></div></td>
		</tr>
		<tr class="tr_cor_branco">
			<td>CONFIRME O E-MAIL</td>
			<td><div align="left"><input name="email_confirmacao" type="text" id="email_confirmacao" size="40" maxlength="50" value="" /></div></td>
		</tr>
		<tr class="tr_cor_cinza">
			<td>OBSERVA&Ccedil;&Otilde;ES</td>
			<td><textarea id="observacao" rows="5" style="width:290px"></textarea></td>
		</tr>
	<tr class="subcabecalho_tr">
		<td colspan="2"><input type="submit" value="CADASTRAR"><input type="button" value="LIMPAR" onclick="window.location.href='?mainapp=cadastro&app=visitante';"></td>
	</tr>
	</table>
</form>	
</div>
<div id="div_carregando" class="div_carregando_relatorio">
	<div align="center"><img src="imagens/loading.gif" /></div>
	<span class="span_carregando_relatorio" id="span_carregando_relatorio">????<br /> AGUARDE...</span>
</div>