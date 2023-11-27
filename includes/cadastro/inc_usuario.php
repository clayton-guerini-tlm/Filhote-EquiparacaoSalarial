<?php
$link = RetornaConexaoMysql('local', 'sigo_integrado');

$grupo_id = $_SESSION['SIGO']['ACESSO']['ID_GRUPO'];

if($grupo_id != 2){
	
	$area_id = $_SESSION['SIGO']['ACESSO']['FILIAL'];
	switch ($area_id) {
	 	case 'AREA2':
	 		$area_label = "AREA 2";
	 		break;
	 		
	 	case 'AREA1_MG':
	 		$area_label = "AREA 1 MG";
	 		break;
	 		
	 	case 'AREA1_RJ':
	 		$area_label = "AREA 1 RJ";
	 		break;
	 	
	 	case 'AREA1_ES':
	 		$area_label = "AREA 1 ES";
	 		break;
	 
                case 'AREA3_SP':
	 		$area_label = "AREA 3 SP";
	 		break;
                    
	 	default:
	 		break;
	 }
	
	$where_grupo = " WHERE gru_privilegio < 80 AND gru_descricao LIKE '%$area_label%'";
}else{
	$where_grupo = "";
}

$Sql = "SELECT gru_id, gru_descricao FROM tbl_grupo $where_grupo ORDER BY gru_descricao ASC";
$rs_grupo = mysqli_query($link, $Sql);
$options_grupo = GeraOptionGenerico($rs_grupo,'gru_id','gru_descricao','','');
mysqli_data_seek($rs_grupo,0);
$options_grupo_multiplo = GeraOptionGenerico($rs_grupo,'gru_id','gru_descricao','','Selecione');


$sql_area = "SELECT DISTINCT descricao from tbl_area order by descricao";
$rs_area = mysqli_query($link, $sql_area) or die('erro SQL AREA');
$options_usuario_area = GeraOptionGenerico($rs_area, 'descricao', 'descricao', '', 'Selecione');
//$options_usuario_area = RetornaOptionUsuarioFilial();

$Sql = "SELECT aol_descricao FROM tbl_atuacao_old ORDER BY aol_descricao ASC";
$rs_atuacao_old = mysqli_query($link, $Sql);
$options_atuacao_old = GeraOptionGenerico($rs_atuacao_old,'aol_descricao','aol_descricao','','NENHUM');

$options_grupo_js = str_replace('"',"'",$options_grupo_multiplo);
?>

<script type="text/javascript">
// CADASTRO DE USUARIO //

function BuscaUsuario(valor_get){
	TrocaStatusBotoesUsuario(true);
	var valor 		= document.getElementById('valor');
	var situacao 	= document.getElementById('situacao');
	
	if(valor_get != ""){
		valor.value = valor_get;
	}
	if(valor.value == ""){
		alert('Digite o valor antes de enviar.');
		TrocaStatusBotoesUsuario(false);
		valor.focus();
		return false;
	}
	
	
	var campos = "funcao_ajax=AjaxBuscaUsuario&valor="+valor.value+"&situacao="+situacao.value;
	
	document.getElementById('span_carregando_relatorio').innerHTML = "BUSCANDO USU&Aacute;RIOS<br /> AGUARDE...";
	document.getElementById('div_busca_resultado').style.display = "none";
	document.getElementById('div_editar').style.display = "none";
	document.getElementById('div_carregando').style.display = "block";
		
	var AjaxBuscaUsuario = getAjax();
	if (AjaxBuscaUsuario != null) {
		AjaxBuscaUsuario.open("POST", "ajax/ajax_funcoes.php", true);
		AjaxBuscaUsuario.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		AjaxBuscaUsuario.setRequestHeader("Content-length", campos.length);
		AjaxBuscaUsuario.setRequestHeader("Connection", "close");		
		AjaxBuscaUsuario.send(campos);
		AjaxBuscaUsuario.onreadystatechange = function(){
			if (AjaxBuscaUsuario.readyState == 4 ){
                //alert(AjaxBuscaUsuario.responseText);
                //return -1;     
                //alert(AjaxBuscaUsuario.responseText);
                //return false;
                if(AjaxBuscaUsuario.responseText.trim().toUpperCase().substr(0,4) == "ERRO"){
					document.getElementById('div_carregando').style.display = "none";
					document.getElementById('div_busca').style.display = "block";
					TrocaStatusBotoesUsuario(false);
					alert('Dados não encontrados.');									
				}else{
					if(AjaxBuscaUsuario.responseXML){
						ProcessaXMLDadosUsuario(AjaxBuscaUsuario.responseXML);
					}else{
						alert('Dados não encontrados.');
					}
				}
			}
		}
	}
	
	return false;
}

function ProcessaXMLDadosUsuario(obj){

	var dataArray  = obj.getElementsByTagName("usuario");
	var quant = dataArray.length;
	var campo;
	var id;
	var registro;
	var nome;
	var grupo;
	var cargo;
	var status_rm;
	var status_usuario;
	var estilo;
	var funcionario_id;
	
	var trs ="<table class=\"box_relatorio\" border=\"1\" align=\"center\" width=\"100\">";
		trs+="<tr class=\"subcabecalho_tr\">";
				trs+="<td colspan=\"4\">A&Ccedil;&Otilde;ES</td>";
		trs+="</tr>";
		trs+="<tr class=\"tr_cor_branco\">";
			trs+="<td nowrap>ADICIONAR GRUPO</td>";
			trs+="<td nowrap>ALTERAR SITUA&Ccedil;&Atilde;O</td>";
			trs+="<td nowrap>RESETAR SENHA</td>";
			trs+="<td nowrap>EXCLUIR</td>";
		trs+="</tr>";
		trs+="<tr class=\"tr_cor_cinza\">";
			trs+="<td><div align=\"center\"><select onchange=\"AlterarSelecionados('grupo', this)\"><?php echo $options_grupo_js?></select></div> </td>";
			trs+="<td><div align=\"center\"><select onchange=\"AlterarSelecionados('situacao', this)\">" +
					"<option value=\"\">Selecione</option>" +
					"<option value=\"LIBERADO\">LIBERADO</option>" +
					"<option value=\"BLOQUEADO\">BLOQUEADO</option>" +
					"</select></div></td>";
 			trs+="<td><img style=\"cursor:pointer;\" onclick=\"ResetarSenha(0, 'Selecionados')\" title=\"Resetar Senha\" height=\"70%\"  src=\"imagens/user_senha.gif\"border=0 /></td>";
 			trs+="<td><img style=\"cursor:pointer;\" onclick=\"AlterarSelecionados('excluir', 'excluir')\" title=\"Excluir\" src=\"imagens/del.gif\"border=0 /></td>";			
		trs+="</tr>";
	trs+="</table><br />"
	
	trs+="<div style=\"display:block;position:absolute;left:10px\"><table class=\"box_relatorio\" border=\"1\"  align=\"left\" width=\"600\">";
		trs+="<tr class=\"subcabecalho_tr\">";
				trs+= "<td colspan=\"11\">REGISTRO(S) ENCONTRADO(S): " + quant + "</td>";
		trs+="</tr>";
		trs+="<tr class=\"subcabecalho_tr\">";
 			trs+="<td><input type=\"checkbox\" onclick=\"AlteraSituacaoChecks(this.checked)\" /></td>";
 			trs+="<td>USU&Aacute;RIO</td>";
 			trs+="<td>NOME</td>";
			trs+="<td>GRUPO</td>";
			trs+="<td>&Aacute;REA</td>";
			trs+="<td>CARGO</td>";
			trs+="<td nowrap>SITUA&Ccedil;&Atilde;O SIGO</td>";
			trs+="<td nowrap>RM</td>";
			trs+="<td>EDITAR</td>";
			trs+="<td>RESETAR SENHA</td>";
			/*trs+="<td>PERMISS&Otilde;ES</td>";*/
			trs+="<td>EXCLUIR</td>";
		trs+="</tr>";

		try{
	if(dataArray.length > 0){

		document.getElementById('div_carregando').style.display = "none";
		
	 	for(var i = 0 ; i < quant ; i++) {
	 		campo = dataArray[i];
	 		id  	= campo.getElementsByTagName('id')[0].firstChild.nodeValue;
	 		registro= campo.getElementsByTagName('registro')[0].firstChild.nodeValue;
	 		nome 	= campo.getElementsByTagName('nome')[0].firstChild.nodeValue;
	 		grupo  	= campo.getElementsByTagName('grupo')[0].firstChild.nodeValue;
	 		cargo  	= campo.getElementsByTagName('cargo')[0].firstChild.nodeValue;
	 		area  	= campo.getElementsByTagName('area')[0].firstChild.nodeValue;
	 		status_rm  = campo.getElementsByTagName('status_rm')[0].firstChild.nodeValue;
	 		status_usuario  = campo.getElementsByTagName('status_usuario')[0].firstChild.nodeValue;
			funcionario_id = campo.getElementsByTagName('funcionario_id')[0].firstChild.nodeValue;
	 		
	 		if(estilo == "alterar_cor_branco"){
	 			estilo = "alterar_cor_cinza";
	 			}else{
	 				estilo = "alterar_cor_branco";
	 		}
	 		
	 		status_usuario = RetornaSelectSituacao(status_usuario, funcionario_id);
	 		
	 		trs+="<tr class=\""+estilo+"\">";
	 			trs+="<td><input type=\"checkbox\" id=\"check_usuario_" + funcionario_id + "\"></td>";
	 			trs+="<td>"+registro+"</td>";
	 			trs+="<td align=\"left\" nowrap>"+nome+"</td>";
				trs+="<td align=\"left\" nowrap>"+grupo+"</td>";
				trs+="<td nowrap>"+area+"</td>";
				trs+="<td align=\"left\" nowrap>"+cargo+"</td>";
				trs+="<td nowrap>"+status_usuario+"</td>";
				trs+="<td nowrap>"+status_rm+"</td>";
				trs+="<td><img style=\"cursor:pointer;\" onclick=\"EditarUsuario('"+funcionario_id+"')\" title=\"Editar\"  src=\"imagens/bt_editar.gif\"border=0 /></td>";
				trs+="<td><img style=\"cursor:pointer;\" onclick=\"ResetarSenha('"+funcionario_id+"', '"+nome+"')\" title=\"Resetar Senha\" height=\"70%\"  src=\"imagens/user_senha.gif\"border=0 /></td>";
				/*trs+="<td><img style=\"cursor:pointer;\" onclick=\"window.location.href='principal.php?mainapp=permissao&app=aplicativo&tipo=usuario&id="+id+"'\" title=\"Permiss&otilde;es\" height=\"70%\"  src=\"imagens/icon_key.gif\"border=0 /></td>";*/
				trs+="<td><img style=\"cursor:pointer;\" onclick=\"ExcluirItem('"+funcionario_id+"', '"+nome+"', 'usuario', 'fun_id', 'Usuário')\" title=\"Excluir\" src=\"imagens/del.gif\"border=0 /></td>";
			trs+="</tr>";

	 	}
		if(estilo == "alterar_cor_branco"){
			estilo = "alterar_cor_cinza";
			}else{
				estilo = "alterar_cor_branco";
		}
		
		trs+="<tr class=\"subcabecalho_tr\">";
 			trs+="<td><input type=\"checkbox\" onclick=\"AlteraSituacaoChecks(this.checked)\" /></td>";
 			trs+="<td colspan=\"10\"></td>";
		trs+="</tr>";
		
	}
		}catch(err){
			alert(err);
			return false;
		}
	trs+="</table></div>"
	
	if (i>35){
		trs+="<br /><table class=\"box_relatorio\" border=\"1\" align=\"center\" width=\"100\">";
			trs+="<tr class=\"subcabecalho_tr\">";
					trs+="<td colspan=\"4\">A&Ccedil;&Otilde;ES</td>";
			trs+="</tr>";
			trs+="<tr class=\"tr_cor_branco\">";
				trs+="<td nowrap>ADICIONAR GRUPO</td>";
				trs+="<td nowrap>ALTERAR SITUA&Ccedil;&Atilde;O</td>";
				trs+="<td nowrap>RESETAR SENHA</td>";
				trs+="<td nowrap>EXCLUIR</td>";
			trs+="</tr>";
			trs+="<tr class=\"tr_cor_cinza\">";
				trs+="<td><div align=\"center\"><select onchange=\"AlterarSelecionados('grupo', this)\"><?php echo $options_grupo_js?></select></div> </td>";
				trs+="<td><div align=\"center\"><select onchange=\"AlterarSelecionados('situacao', this)\">" +
						"<option value=\"\">Selecione</option>" +
						"<option value=\"LIBERADO\">LIBERADO</option>" +
						"<option value=\"BLOQUEADO\">BLOQUEADO</option>" +
						"</select></div></td>";
	 			trs+="<td><img style=\"cursor:pointer;\" onclick=\"ResetarSenha(0, 'Selecionados')\" title=\"Resetar Senha\" height=\"70%\"  src=\"imagens/user_senha.gif\"border=0 /></td>";
	 			trs+="<td><img style=\"cursor:pointer;\" onclick=\"AlterarSelecionados('excluir', 'excluir')\" title=\"Excluir\" src=\"imagens/del.gif\"border=0 /></td>";			
			trs+="</tr>";
		trs+="</table>"
	}
	
	document.getElementById('div_busca_resultado').innerHTML = trs;
	document.getElementById('div_busca_resultado').style.display = "block";
	TrocaStatusBotoesUsuario(false);
	AlterarCorTrs();

}

function EditarUsuario(id){
	
	TrocaStatusBotoesUsuario(true);
	
	var campos = "funcao_ajax=AjaxEditarUsuario&id="+id;
	
	document.getElementById('span_carregando_relatorio').innerHTML = "BUSCANDO USU&Aacute;RIOS<br /> AGUARDE...";
	document.getElementById('div_busca_resultado').style.display = "none";
	document.getElementById('div_carregando').style.display = "block";
		
	var AjaxEditarUsuario = getAjax();
	if (AjaxEditarUsuario != null) {
		AjaxEditarUsuario.open("POST", "ajax/ajax_funcoes.php", true);
		AjaxEditarUsuario.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		AjaxEditarUsuario.setRequestHeader("Content-length", campos.length);
		AjaxEditarUsuario.setRequestHeader("Connection", "close");		
		AjaxEditarUsuario.send(campos);
		AjaxEditarUsuario.onreadystatechange = function(){
			if (AjaxEditarUsuario.readyState == 4 ){
				if(AjaxEditarUsuario.responseXML){
					ProcessaXMLEditarUsuario(AjaxEditarUsuario.responseXML);					
					}else{
						document.getElementById('div_carregando').style.display = "none";
						document.getElementById('div_busca').style.display = "block";
						TrocaStatusBotoesUsuario(false);
						alert(AjaxEditarUsuario.responseText);
				}
			}
		}
	}
	
	return false;
}

function ResetarSenha(id, nome){
	
	var multiplo = "";
	var confirma = false;
	if(id != 0){
		confirma =  confirm("Deseja realmente resetar a senha do usuário " + nome + " ?");
		}else{
			id = RetornaIdsSelecionados();
			if(id != ""){
				confirma =  confirm("Deseja realmente resetar a senha dos usuários selecionados ?");			
				multiplo = true;
				}else{
					alert('Nenhum usuário selecionado!');
			}
	}
	
	if (! confirma){
		return false;
	}
	
	var campos = "funcao_ajax=AjaxEditarCampo&tabela=usuario&prefixo=fun_&campo=usr_senha&id="+id+"&valor=202cb962ac59075b964b07152d234b70&multiplo="+multiplo;
	
	document.getElementById('span_carregando_relatorio').innerHTML = "RESETANDO SENHA<br /> AGUARDE...";
	document.getElementById('div_busca_resultado').style.display = "none";
	document.getElementById('div_carregando').style.display = "block";
		
	var AjaxEditarCampo = getAjax();
	if (AjaxEditarCampo != null) {
		AjaxEditarCampo.open("POST", "ajax/ajax_funcoes.php", true);
		AjaxEditarCampo.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		AjaxEditarCampo.setRequestHeader("Content-length", campos.length);
		AjaxEditarCampo.setRequestHeader("Connection", "close");		
		AjaxEditarCampo.send(campos);
		AjaxEditarCampo.onreadystatechange = function(){
			if (AjaxEditarCampo.readyState == 4 ){
				if(AjaxEditarCampo.responseText == "alterou"){
					alert('Senha resetada para \'123\' !');
					document.getElementById('div_carregando').style.display = "none";
					document.getElementById('div_busca_resultado').style.display = "block";
					document.getElementById('div_busca').style.display = "block";					
					if(multiplo){
						BuscaUsuario("");
					}
					}else{
						document.getElementById('div_carregando').style.display = "none";
						document.getElementById('div_busca').style.display = "block";
						alert(AjaxEditarCampo.responseText);
				}
			}
		}
	}
	
	return false;
}

function ProcessaXMLEditarUsuario(obj){
	
	
	var dataArray2  = obj.getElementsByTagName("grupo_select");
	var quant = dataArray2.length;
	
	var id;
	var descricao;
	var sel_grupo = document.getElementById('grupo');
	LimpaSelect(sel_grupo);
	if(dataArray2.length > 0){
		
	 	for(var i = 0 ; i < quant ; i++) {
	 		campo = dataArray2[i];
	 		id  		= campo.getElementsByTagName('id')[0].firstChild.nodeValue;
	 		descricao	= campo.getElementsByTagName('descricao')[0].firstChild.nodeValue;	 		
			AdicionaOption(sel_grupo,id,descricao);
	 	}		 	
	}	
	
	var dataArray  = obj.getElementsByTagName("usuario");
	quant = dataArray.length;
	var campo;
	
	var id;
	var registro;
	var nome;
	var email;
	var grupo;
	var cargo;
	var atuacao_old;
	var area;
	var status_usuario;
	var estilo;
	var status_rm;
	var obs;
	var funcionario_id;
	
	var gru_id;
    var vet_grupo;
    var vet_area;
    var vet_select = document.getElementById('grupo');
    var vet_select_area = document.getElementById('area');
    var opt_len = vet_select.length;
    var opt_len_area = vet_select_area.length;
	var achou_opt;
	
	if(dataArray.length > 0){
		
		document.getElementById('div_carregando').style.display = "none";
	 	for(var i = 0 ; i < quant ; i++) {
	 		campo = dataArray[i];
	 		id  	= campo.getElementsByTagName('id')[0].firstChild.nodeValue;
	 		registro= campo.getElementsByTagName('registro')[0].firstChild.nodeValue;
	 		nome 	= campo.getElementsByTagName('nome')[0].firstChild.nodeValue;
	 		email 	= campo.getElementsByTagName('email')[0].firstChild.nodeValue;
	 		grupo  	= campo.getElementsByTagName('grupo')[0].firstChild.nodeValue;
	 		cargo  	= campo.getElementsByTagName('cargo')[0].firstChild.nodeValue;
	 		atuacao_old  	= campo.getElementsByTagName('atuacao_old')[0].firstChild.nodeValue;
	 		area  	= campo.getElementsByTagName('area')[0].firstChild.nodeValue;
	 		status_usuario  = campo.getElementsByTagName('status_usuario')[0].firstChild.nodeValue;
	 		status_rm  = campo.getElementsByTagName('status_rm')[0].firstChild.nodeValue;
			obs		= campo.getElementsByTagName('obs')[0].firstChild.nodeValue;
			funcionario_id = campo.getElementsByTagName('funcionario_id')[0].firstChild.nodeValue;
			
	 		grupo		= campo.getElementsByTagName('grupo')[0].firstChild.nodeValue;
            vet_grupo   = grupo.split('|');
            for (var jj=0; jj< opt_len;jj++){
                achou_opt = false;
                for (var opt_id in vet_grupo){
                    gru_id = vet_grupo[opt_id];
                    //alert(sis_id + " - " + vet_select.options[jj].value);
                    if(vet_select.options[jj].value == gru_id){
                        achou_opt = true;
                    }
                }
                vet_select.options[jj].selected = achou_opt;
            }
            
            vet_area   = area.split('|');
            for (var jj=0; jj< opt_len_area;jj++){
                achou_opt = false;
                for (var opt_id in vet_area){
                    area_id = vet_area[opt_id];
                    //alert(sis_id + " - " + vet_select.options[jj].value);
                    if(vet_select_area.options[jj].value == area_id){
                        achou_opt = true;
                    }
                }
                vet_select_area.options[jj].selected = achou_opt;
            }
			
	 		document.getElementById('usr_id').value = id;
	 		document.getElementById('usr_nome').value = nome;
	 		document.getElementById('usr_usuario').value = registro;
	 		document.getElementById('span_usuario').innerHTML = registro;
	 		document.getElementById('span_nome').innerHTML = nome;
	 		document.getElementById('email').value = email;
	 		document.getElementById('cargo').innerHTML = cargo;
	 		document.getElementById('atuacao_old').value = atuacao_old;
	 		document.getElementById('span_status_rm').innerHTML = status_rm;
	 		document.getElementById('status_usuario').value = status_usuario;
			document.getElementById('span_obs').innerHTML = obs;
			document.getElementById('fun_id').value = funcionario_id;
			
	 	}	
	}
	
	document.getElementById('div_busca_resultado').style.display = "block";
	document.getElementById('div_editar').style.display = "block";
	TrocaStatusBotoesUsuario(false);

}


function SalvarUsuario(){

	var area = '';
	for(i = 0; i < document.getElementById('area').options.length; i ++){
		if(document.getElementById('area').options[i].selected){
			area += document.getElementById('area').options[i].value + '|';
		}
	}
	area = area.substr(0,area.length - 1);
	//alert(retorno);
	
	var id = document.getElementById('usr_id');
	var nome = document.getElementById('usr_nome');
	var usuario = document.getElementById('usr_usuario');
	var email = document.getElementById('email');	
	var area = area;
	var atuacao_old = document.getElementById('atuacao_old');
	var status_usuario = document.getElementById('status_usuario');
	var funcionario_id = document.getElementById('fun_id');
	
	var grupo = "";
	var vet_select = document.getElementById('grupo');
	var opt_len = vet_select.length;
	var achou_opt = false;
	var grupo_invalido = false;
	var quant_selecionado = 0;
	
	var email_tmp = email.value;
	var retorno =  true;
	var msg;
	msg = "Erros encontrados:\n\n"
	
	for (var jj=0; jj< opt_len;jj++){
		if(vet_select.options[jj].selected == true){
			if((vet_select.options[jj].value == 2 || vet_select.options[jj].value == 3 || vet_select.options[jj].value == 4)){
				grupo_invalido = true;
			}
			quant_selecionado++;
			grupo = grupo + vet_select.options[jj].value + "|";
			achou_opt = true;
		}
	}

	if(grupo_invalido && quant_selecionado>1){
		retorno = false;
		msg+="Usuários dos Grupos ADMINISTRADOR SIGO, ADMINISTRADOR REGIONAL E DIRETORES não podem ter mais de um Grupo.\n"
	}	
	
	if(achou_opt){
		grupo = grupo.substr(0,grupo.length-1);
		}else{
			retorno = false;
			msg+="Selecione pelo menos um GRUPO antes de salvar.\n"
	}	
	
	if(!email_tmp.match(/\b(^(\S+@).+((\.com)|(\.net)|(\.edu)|(\.mil)|(\.gov)|(\.org)|(\..{2,2}))$)\b/gi)) {
		msg+="- Formato de e-mail inválido.\n"
		retorno = false;
	}
	
	if (grupo.value == ""){
		msg+="Selecione um GRUPO antes de salvar.\n"
		retorno = false;
	}
	
	if (area.value == ""){
		msg+="Selecione uma ÁREA antes de salvar.\n"
		retorno = false;
	}
	
	if (status_usuario.value == ""){
		msg+="Selecione um STATUS USUÁRIO antes de salvar.\n"
		retorno = false;
	}
	
	if(!retorno){
		alert(msg);
		return false;
	}
	
	var campos = "funcao_ajax=AjaxSalvarUsuario&id="+id.value+"&email="+email_tmp+"&grupo="+grupo+"&area="+area+"&atuacao_old="+atuacao_old.value+"&status_usuario="+status_usuario.value+"&nome="+nome.value+"&usuario="+usuario.value+'&fun_id='+funcionario_id.value;
	
	document.getElementById('span_carregando_relatorio').innerHTML = "ALTERANDO USU&Aacute;RIO<br /> AGUARDE...";
	document.getElementById('div_busca_resultado').style.display = "none";
	document.getElementById('div_editar').style.display = "none";
	document.getElementById('div_carregando').style.display = "block";
		
	var AjaxSalvarUsuario = getAjax();
	if (AjaxSalvarUsuario != null) {
		AjaxSalvarUsuario.open("POST", "ajax/ajax_funcoes.php", true);
		AjaxSalvarUsuario.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		AjaxSalvarUsuario.setRequestHeader("Content-length", campos.length);
		AjaxSalvarUsuario.setRequestHeader("Connection", "close");		
		AjaxSalvarUsuario.send(campos);
		AjaxSalvarUsuario.onreadystatechange = function(){
			if (AjaxSalvarUsuario.readyState == 4 ){
				if(AjaxSalvarUsuario.responseText == "salvou"){
					alert('Usuário atualizado com êxito!')
					BuscaUsuario("");					
					}else{
						document.getElementById('div_carregando').style.display = "none";
						document.getElementById('div_busca').style.display = "block";
						document.getElementById('div_editar').style.display = "block";
						document.getElementById('div_busca_resultado').style.display = "block";
						alert(AjaxSalvarUsuario.responseText);
				}
			}
		}
	}
	
	return false;
}

function TrocaStatusBotoesUsuario(status){
	document.getElementById('btn_buscar').disabled = status;
	document.getElementById('btn_salvar').disabled = status;
	document.getElementById('btn_limpar').disabled = status;
}

function RetornaSelectSituacao(situacao, id){
	var select_liberado = "";
	var select_bloqueado = "";
	if (situacao == "LIBERADO"){
		select_liberado = "SELECTED";
		}else{
			select_bloqueado = "SELECTED";
	}
	
	var selects = 	"<div align=\"left\"><select onchange=\"EditarCampo('" + id + "', 'fun_', 'usuario', 'usr_status', this)\">" +
					"<option " + select_liberado + " value=\"LIBERADO\">LIBERADO</option>" +
					"<option " + select_bloqueado + " value=\"BLOQUEADO\">BLOQUEADO</option>" +
					"</select></div>";
					
	return selects;
	
}

function AlteraSituacaoChecks(situacao){
	var c;

	var checks = document.getElementsByTagName("input");
	for(var i = 0; i< checks.length; i++){
		c = checks[i].id;
		//alert(c.substr(0,14));
		if(c.substr(0,14) == "check_usuario_"){
			document.getElementById(c).checked = situacao;
		}
	}
	
}

function AlterarSelecionados(campo, valor){ 
	var campos;
	var ids = RetornaIdsSelecionados();
	if(ids == ""){		
		if(valor != "excluir"){
			valor.value = "";
		}
		alert('Nenhum usuário selecionado!');
		return false;
	}
	valor = valor.value;
	if(valor == ""){
		alert("Selecione um valor antes de enviar.");
		return false;
	}
	
	switch (campo){
		case "grupo" :
			campos = "funcao_ajax=AjaxEditarCampo&tabela=usuario&prefixo=fun_&campo=gru_id&id="+ids+"&valor="+valor+"&multiplo=true";
		break;
		
		case "situacao" :
			campos = "funcao_ajax=AjaxEditarCampo&tabela=usuario&prefixo=fun_&campo=usr_status&id="+ids+"&valor="+valor+"&multiplo=true";
		break;
		
		case "excluir" :
			campos = "funcao_ajax=AjaxEditarCampo&tabela=usuario&prefixo=fun_&campo=excluir&id="+ids+"&valor=excluir&multiplo=true";
		break;
	}
	
	var confirma =  confirm("Deseja realmente atualizar os usuários selecionados?");	
	if (! confirma){
		return false;
	}
	
	document.getElementById('span_carregando_relatorio').innerHTML = "ATUALIZANDO REGISTROS<br /> AGUARDE...";
	document.getElementById('div_busca_resultado').style.display = "none";
	document.getElementById('div_carregando').style.display = "block";
		
	var AjaxEditarCampo = getAjax();
	if (AjaxEditarCampo != null) {
		AjaxEditarCampo.open("POST", "ajax/ajax_funcoes.php", true);
		AjaxEditarCampo.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		AjaxEditarCampo.setRequestHeader("Content-length", campos.length);
		AjaxEditarCampo.setRequestHeader("Connection", "close");		
		AjaxEditarCampo.send(campos);
		AjaxEditarCampo.onreadystatechange = function(){
			if (AjaxEditarCampo.readyState == 4 ){
				if(AjaxEditarCampo.responseText == "alterou"){
					alert('Registros atualizados com êxito !');
					document.getElementById('div_carregando').style.display = "none";
					document.getElementById('div_busca_resultado').style.display = "block";
					document.getElementById('div_busca').style.display = "block";					
					BuscaUsuario("");
					}else{
						document.getElementById('div_carregando').style.display = "none";
						document.getElementById('div_busca').style.display = "block";
						alert(AjaxEditarCampo.responseText);
				}
			}
		}
	}
	
	return false;
	
}

function RetornaIdsSelecionados(){
	var c;
	var v;
	var ids = "";
	var checks = document.getElementsByTagName("input");
	for(var i = 0; i< checks.length; i++){
		c = checks[i].id;
		v = checks[i].checked;
		if(c.substr(0,14) == "check_usuario_"){
			
			if (v == true){
				ids += c.substr(14) + "|";	
			}
		}
	}
	ids = ids.substr(0,ids.length-1);
	return ids;
}
function retornaGruposAcesso(objeto){

	var retorno = '';
	for(i = 0; i < objeto.options.length; i ++){
        if(objeto.options[i].selected){
			retorno += objeto.options[i].value + '|';
		}
	}
	retorno = retorno.substr(0,retorno.length - 1);
	//alert(retorno);
	BuscaGruposAcesso(retorno);
}

function BuscaGruposAcesso(valor){
	var sel_grupo = document.getElementById('grupo');
	
	var grupos = [];
	var cbx_grupo = document.getElementById('grupo').childNodes;
	for(var i in cbx_grupo){
	    if( typeof(cbx_grupo[i]) == "object" ){
		    if(cbx_grupo[i].selected) grupos.push(cbx_grupo[i].value);
	    }
	}
	
        LimpaSelect(sel_grupo);
	AdicionaOption(sel_grupo,"","Carregando Grupos...");
	var campos = "funcao_ajax=AjaxBuscaGruposAcesso&area="+valor;
        
	ReqAjax(Retorno, campos);
	function Retorno(obj){
		if (obj.responseXML) {
			ProcessaXMLBuscaGruposAcesso(obj.responseXML);
		    var cbx_grupo = document.getElementById('grupo').childNodes;
		    
		    for(var i in cbx_grupo){
		    	for(var j in grupos){
			    	if( typeof(cbx_grupo[i]) == "object" ){
			    	    if(cbx_grupo[i].value == grupos[j]){
				    	    cbx_grupo[i].selected = true;
			    	    }
			    	}
		    	}
		    }
		}
		else{
			alert(obj.responseText);
		}		
	}
}

function ProcessaXMLBuscaGruposAcesso(obj){
	var dataArray  = obj.getElementsByTagName("grupo");
	var quant = dataArray.length;
	var campo;
	
	
	var id;
	var descricao;
	var sel_grupo = document.getElementById('grupo');
	LimpaSelect(sel_grupo);
	if(dataArray.length > 0){
		
	 	for(var i = 0 ; i < quant ; i++) {
	 		campo = dataArray[i];
	 		id  		= campo.getElementsByTagName('id')[0].firstChild.nodeValue;
	 		descricao	= campo.getElementsByTagName('descricao')[0].firstChild.nodeValue;	 		
			AdicionaOption(sel_grupo,id,descricao);
	 	}		 	
	}
}



</script>
<table class="box_relatorio" width="1024" border="0">
	<tr class="cabecalho_tr">
		<td nowrap><span class="cabecalho_tr">CADASTRO DE USU&Aacute;RIOS</span></td>
	</tr>
</table>
<br />
<div id="div_busca" style="display:block">
<form onsubmit="return BuscaUsuario('');">
<table class="box_relatorio" width="530" align="center" border="1">
	<tr class="subcabecalho_tr">
		<td>DIGITE O REGISTRO, NOME, &Aacute;REA OU GRUPO DO USU&Aacute;RIO ( * para ver TODOS)</td>
		<td>SITUAÇÃO</td>
	</tr>
	<tr class="tr_cor_cinza">
		<td><input name="valor" type="text" id="valor" size="70" maxlength="1000" /></td>
		<td>
		<select id="situacao" name="situacao">
			<option value="2">TODOS
			<option value="0">LIBERADO
			<option value="1">BLOQUEADO
		</select>
		</td>
	</tr>
	<tr class="tr_cor_cinza">
		<td colspan="2"><input id="btn_buscar" type="submit" value="BUSCAR"></td>
	</tr>
</table>
</form>
</div>

<div id="div_editar" style="display:none">
<input type="hidden" id="usr_id" value="0" />
<input type="hidden" id="usr_nome" value="" />
<input type="hidden" id="usr_usuario" value="" />
<input type="hidden" id="fun_id" value="0" />
<form onsubmit="return SalvarUsuario();">
<table class="box_relatorio" width="600" align="center" border="1">
	<tr class="subcabecalho_tr">
		<td colspan="2">DADOS DO USU&Aacute;RIO</td>
	</tr>
	<tr class="tr_cor_cinza">
		<td nowrap>NOME</td>
		<td><div align="left"><span id="span_nome">NOME</span></div></td>
	</tr>
	<tr class="tr_cor_branco">
		<td nowrap>REGISTRO Oi</td>
		<td><div align="left"><span id="span_usuario">USU&Aacute;RIO</span></div></td>
	</tr>
	<tr class="tr_cor_cinza">
		<td nowrap>EMAIL</td>
		<td><div align="left"><input name="email" type="text" id="email" size="40" maxlength="100" />
			</div></td>
	</tr>
	<tr class="tr_cor_branco">
		<td>&Aacute;REA</td>
		<td><div align="left"><select multiple name="area" id="area" size='3' onclick="retornaGruposAcesso(this);">	
		<?php echo $options_usuario_area ?>		
		</select></div></td>
	</tr>
	<tr class="tr_cor_cinza">
		<td nowrap>GRUPOS</td>
		<td><div align="left"><select multiple id="grupo" size="20" style="width:500px"><?php echo $options_grupo?></select></div></td>
	</tr>
	<tr class="tr_cor_cinza">
		<td>CARGO</td>
		<td><div align="left" id="cargo"></div></td>
	</tr>
	<tr class="tr_cor_branco">
		<td>ATUA&Ccedil;&Atilde;O (LEANDRO)</td>
		<td><div align="left"><select name="atuacao_old" id="atuacao_old">		
		<?php echo $options_atuacao_old ?>		
		</select></div></td>
	</tr>
	<tr class="tr_cor_cinza">
		<td nowrap>STATUS RM</td>
		<td><div align="left"><span id="span_status_rm">ATIVO</span></div></td>
	</tr>
	<tr class="tr_cor_branco">
		<td nowrap>STATUS USU&Aacute;RIO</td>
		<td><div align="left"><select id="status_usuario">
				<option value="">Selecione</option>
				<option value="LIBERADO">LIBERADO</option>
				<option value="BLOQUEADO">BLOQUEADO</option>
			</select></div></td>
	</tr>
    <tr class="tr_cor_cinza">
		<td nowrap>OBSERVA&Ccedil;&Otilde;ES</td>
		<td><div align="left"><span id="span_obs">OBS</span></div></td>
	</tr>
	<tr class="subcabecalho_tr">
		<td colspan="2" class="subcabecalho_tr"><input id="btn_salvar" type="submit" value="SALVAR" />&nbsp;<input id="btn_limpar" type="reset" value="LIMPAR" /></td>
	</tr>
</table>
</form>
</div>

<div id="div_carregando" class="div_carregando_relatorio">
	<div align="center"><img src="imagens/loading.gif" border="0" /></div>
	<span class="span_carregando_relatorio" id="span_carregando_relatorio">????<br /> AGUARDE...</span>
</div>

<div id="div_busca_resultado">
<!-- NAO REMOVER -->
</div>

<?php 
	if (isset($_GET['valor_get'])){
		$_GET['valor_get'] = urldecode($_GET['valor_get']);
				
		print <<< EOF
		<script type="text/javascript">
			BuscaUsuario('{$_GET['valor_get']}');
		</script>
EOF;
	}
?>