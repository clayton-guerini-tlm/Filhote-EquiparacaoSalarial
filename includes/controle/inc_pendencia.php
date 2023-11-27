<?php
$link = RetornaConexaoMysql('local', 'sigo_integrado');

$where_pendencia = isset($_GET['pendencia_tipo']) ? "AND t1.pdt_id={$_GET['pendencia_tipo']}" : "";
$vet_relacionamento = RetornaIdsRelacionados($link, 'tbl_pendencia');

$Sql = " SELECT p1.pen_id, p1.pen_data_solicitacao, p1.pen_solicitacao, p1.pen_solicitante_email, p1.pen_data_execucao, p1.pen_status, t1.pdt_descricao, t1.pdt_id, p1.pen_solicitante, u1.usr_nome_visitante as executor FROM tbl_pendencia p1 INNER JOIN tbl_pendencia_tipo t1 ON p1.pdt_id = t1.pdt_id LEFT JOIN tbl_usuario u1 ON p1.pen_executor=u1.usr_id WHERE 1=1 $where_pendencia AND p1.pen_mostrar = 1 ORDER BY t1.pdt_descricao, p1.pen_status desc, p1.pen_data_solicitacao  DESC";

//echo $Sql;exit;

$rs_pendencia = mysqli_query($link, $Sql);
$trs = "";
while ($row_pendencia=mysqli_fetch_assoc($rs_pendencia)) {
	$pen_id = $row_pendencia['pen_id'];
	$pendencia_tipo_id = $row_pendencia['pdt_id'];
	$data_solicitacao = ConvertDataHoraMysql($row_pendencia['pen_data_solicitacao'],'normal',true);
	$data_execucao = ConvertDataHoraMysql($row_pendencia['pen_data_execucao'],'normal',true);
	$pendencia_tipo = $row_pendencia['pdt_descricao'];
	$email_solicitante = $row_pendencia['pen_solicitante_email'];
	$solicitacao = $row_pendencia['pen_solicitacao'];
	$nome_funcionario = $row_pendencia['pen_solicitante'];
	$nome_executor = $row_pendencia['executor'];
	$status_pendencia = $row_pendencia['pen_status'];
	
	//AdicionaSubstringDeLinha($solicitacao,"<br />",35);
	
	if($estilo == "tr_cor_cinza"){
		$estilo = "tr_cor_branco";
		}else{
			$estilo = "tr_cor_cinza";
	}
	
	if($status_pendencia == "PENDENTE"){
		$img_status_pendencia = "<img style=\"cursor:pointer;\" onclick=\"MostraPendencia($pen_id, $pendencia_tipo_id, '$nome_funcionario', '$email_solicitante');\" title=\"A resolver...\"  src=\"imagens/ball_red.png\" border=0 />";
		}else{
			$img_status_pendencia = "<img title=\"Resolvido!\" onclick=\"//MostraPendencia($pen_id, $pendencia_tipo_id, '$nome_funcionario', '$email_solicitante');\"  src=\"imagens/ball_green.png\" border=0 />";
	}
	
	if($pendencia_tipo == "CADASTRO DE VISITANTES"){
		$endereco_get = "mainapp=cadastro&app=visitante";
		}else{
			$endereco_get = "mainapp=cadastro&app=usuario";
	}
	
	$trs.="<tr class=\"$estilo\">";
	$trs.="<td align=\"center\" nowrap><a href=\"principal.php?{$endereco_get}&valor_get=". urlencode($nome_funcionario) ."\" target=\"_blank\"><img src=\"imagens/icon_trabalhar.png\" height=\"70%\" border=0 /></a></td>";
	$trs.="<td align=\"center\" nowrap>$pendencia_tipo</td>";
	$trs.="<td align=\"center\" nowrap>$data_solicitacao</td>";
	$trs.="<td align=\"left\" nowrap>$nome_funcionario</td>";
	$trs.="<td align=\"center\" nowrap>$solicitacao</td>";
	$trs.="<td align=\"center\" nowrap>$data_execucao</td>";
	$trs.="<td align=\"left\" nowrap>$nome_executor</td>";
	$trs.="<td><img style=\"cursor:pointer;\" title=\"Esconder\" onclick=\"EditarCampo($pen_id, 'pen_', 'pendencia', 'pen_mostrar', this);\" src=\"imagens/icon_mostrar.jpg\" border=0 /></td>";
	$trs.="<td>$img_status_pendencia</td>";	
	$trs.="</tr>";
	
}

$Sql = "SELECT pdt_id, pdt_descricao FROM tbl_pendencia_tipo ORDER BY pdt_descricao ASC";
$rs_pendencia_tipo = mysqli_query($link, $Sql);
$options_pendencia_tipo = GeraOptionGenerico($rs_pendencia_tipo,'pdt_id','pdt_descricao',$_GET['pendencia_tipo'], 'Selecione');

?>
<script type="text/javascript">
function MudaTipoPendencia(pendencia_tipo){
	if(pendencia_tipo != ""){
		document.location.href='principal.php?mainapp=controle&app=pendencia&pendencia_tipo='+pendencia_tipo;
	}
}

function ExecutarTarefa(){
	
	var confirmar = confirm("Confirma execução da pendência?");
	
	if(!confirmar){
		return false;
	}
	
	var pen_id = document.getElementById('pen_id').value;
	var pdt_id = document.getElementById('pdt_id').value;
	var fun_id = document.getElementById('fun_id').value;
	var pen_solicitante_email = document.getElementById('pen_solicitante_email').value;
	var pen_execucao = document.getElementById('pen_execucao').value;
	
	if(pdt_id){
		pendencia_tipo = "&pendencia_tipo="+pdt_id;
		}else{
			pendencia_tipo = "";
	}
	
	document.getElementById('span_carregando_relatorio').innerHTML = "ATUALIZANDO REGISTRO<br /> AGUARDE...";
	document.getElementById('div_busca_resultado').style.display = "none";
	document.getElementById('div_carregando').style.display = "block";
	
	var campos = "funcao_ajax=AjaxExecutarTarefa&pen_id="+pen_id+"&pdt_id="+pdt_id+"&fun_id="+fun_id+"&pen_execucao="+pen_execucao+"&pen_solicitante_email="+pen_solicitante_email;
	var AjaxExecutarTarefa = getAjax();
	if (AjaxExecutarTarefa != null) {
		AjaxExecutarTarefa.open("POST", "ajax/ajax_funcoes.php", true);
		AjaxExecutarTarefa.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		AjaxExecutarTarefa.setRequestHeader("Content-length", campos.length);
		AjaxExecutarTarefa.setRequestHeader("Connection", "close");		
		AjaxExecutarTarefa.send(campos);
		AjaxExecutarTarefa.onreadystatechange = function(){
			if (AjaxExecutarTarefa.readyState == 4 ){
				if(AjaxExecutarTarefa.responseText == "ok"){
					document.getElementById('div_carregando').style.display = "none";					
					alert('Pendência atualizada com êxito!');
					document.location.href='principal.php?mainapp=controle&app=pendencia'+pendencia_tipo;
					}else{
						document.getElementById('div_carregando').style.display = "none";
						document.getElementById('div_busca_resultado').style.display = "block";
						alert(AjaxExecutarTarefa.responseText);
				}
			}
		}
	}
	
	return false;
}

function MostraPendencia(pen_id, pdt_id, solicitante, pen_solicitante_email){
	var form_pendencia = document.getElementById('form_pendencia');
	document.getElementById('pen_id').value = pen_id;
	document.getElementById('pdt_id').value = pdt_id;
	document.getElementById('span_solicitante').innerHTML = solicitante;
	document.getElementById('fun_id').value = solicitante;
	document.getElementById('pen_solicitante_email').value = pen_solicitante_email;
	
	if(form_pendencia.style.display == "none"){
		form_pendencia.style.display = "block";
	}
}

function EscondePendencia(){
	document.getElementById('form_pendencia').style.display = "none";
}
</script>
<table class="box_relatorio" width="1024" border="0">
	<tr class="cabecalho_tr">
		<td nowrap><span class="cabecalho_tr">CONTROLE DE PENDÊNCIAS</span></td>
	</tr>
</table>
<br />
<div id="div_sel_pendencia_tipo">
<table class="box_relatorio" width="250" align="center" border="1">
	<tr class="subcabecalho_tr">
		<td>SELECIONE O TIPO DE PENDÊNCIA</td>
	</tr>
	<tr class="tr_cor_cinza">
		<td><select id="sel_pendencia_tipo" onchange="MudaTipoPendencia(this.value)"><?php echo $options_pendencia_tipo ?></select></td>
	</tr>
</table>
</div>

<br />
<div id="form_pendencia" style="display:none;">
<form onsubmit="return ExecutarTarefa();">
	<input type="hidden" name="ID PENDENCIA" id="pen_id" value="0" />
	<input type="hidden" name="ID PENDENCIA TIPO" id="pdt_id" value="0" />
	<input type="hidden" name="FUNCIONARIO" id="fun_id" value="" />
	<input type="hidden" name="EMAIL DO SOLICITANTE" id="pen_solicitante_email" value="" />
	<table border="1" align="center" class="box_relatorio">
		<tr class="subcabecalho_tr">
			<td>DADOS DA PENDÊNCIA</td>
		</tr>
		<tr class="tr_cor_cinza">
			<td>SOLICITANTE: <span id="span_solicitante"></span></td>
		</tr>
		<tr class="tr_cor_branco">
			<td>OBSERVAÇÕES</td>
		</tr>
		<tr class="tr_cor_cinza">
			<td><textarea style="width:300px; height:200px" cols="10" id="pen_execucao"></textarea></td>
		</tr>
		<tr class="tr_cor_branco">
			<td><input type="submit" value="SALVAR" /><input type="button" value="VOLTAR" onclick="EscondePendencia()" /></td>
		</tr>
		
	</table>
</form>
</div>

<br />
<div id="div_busca_resultado" style="display:block">
<table class="box_relatorio" border="1" align="center" width="300">
	<tr class="subcabecalho_tr" valign="middle">
		<td colspan="9">PEND&Ecirc;NCIAS</td>
	</tr>
		<tr class="tr_cor_branco">
			<td>RESOLVER</td>
			<td>TIPO</td>
			<td>DATA SOLICITA&Ccedil;&Atilde;O</td>
			<td>SOLICITANTE</td>
			<td>SOLICITA&Ccedil;&Atilde;O</td>
			<td>DATA EXECU&Ccedil;&Atilde;O</td>
			<td>EXECUTOR</td>
			<td>OCULTAR</td>
			<td>STATUS</td>
		</tr>
		<?php echo("$trs"); ?>
</table>
</div>

<div id="div_carregando" class="div_carregando_relatorio">
	<div align="center"><img src="imagens/loading.gif" border="0" /></div>
	<span class="span_carregando_relatorio" id="span_carregando_relatorio">????<br /> AGUARDE...</span>
</div>
