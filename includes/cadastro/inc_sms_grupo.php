<?php
$link = RetornaConexaoMysql('local', 'sigo_integrado');

$vet_relacionamento = RetornaIdsRelacionados($link, 'tbl_sms_grupo');

$Sql = "SELECT sms_id, sms_descricao FROM tbl_sms_grupo ORDER BY sms_descricao ASC";
$rs_sms_grupo = mysqli_query($link, $Sql) or die(mysqli_error($link));
$trs = "";
while ($row_sms_grupo=mysqli_fetch_assoc($rs_sms_grupo)) {
	$id = $row_sms_grupo['sms_id'];
	$descricao = $row_sms_grupo['sms_descricao'];
	if($estilo == "tr_cor_cinza"){
		$estilo = "tr_cor_branco";
		}else{
			$estilo = "tr_cor_cinza";
	}
	
	$tabela_encontrado = BuscaItemRelacionamento($vet_relacionamento,$id);
	
	if($tabela_encontrado != "nenhum"){
		$onclick = "onclick=\"alert('Exclusão não permitida.\\nItem utilizado na(s) seguinte(s) tabela(s): \\n\\n $tabela_encontrado')\"";
		$src = "src=\"imagens/del_disabled.gif\"";
		}else{
			$onclick = "onclick=\"ExcluirItem('$id', '$descricao', 'sms_grupo', 'sms_id', 'Grupo de SMS')\"";
			$src = "src=\"imagens/del.gif\"";
	}
	
	$trs.="<tr class=\"$estilo\">";
	$trs.="<td align=\"left\" nowrap>$descricao</td>";
	$trs.="<td><img style=\"cursor:pointer;\" onclick=\"EditarItem('$id', 'sms_', 'sms_grupo', 'Grupo de SMS', 'sms_descricao')\" title=\"Editar\"  src=\"imagens/bt_editar.gif\"border=0 /></td>";
	$trs.="<td><img style=\"cursor:pointer;\" onclick=\"window.location.href='principal.php?mainapp=cadastro&app=contato_sms&id=$id&nome_grupo=$descricao'\" title=\"SMS\" height=\"70%\"  src=\"imagens/icone_email.gif\"border=0 /></td>";
	$trs.="<td><img style=\"cursor:pointer;\" $onclick title=\"Excluir\" $src border=0 /></td>";
	$trs.="</tr>";
	
}

mysqli_close($link);
 		
?>

<script type="text/javascript">
function InserirGrupo(tipo){
	if(tipo=='inserir'){
		document.getElementById('div_busca_resultado').style.display = "none";
		document.getElementById('div_editar').style.display = "block";		
		document.getElementById('sms_descricao').focus();		
		}else{
			document.getElementById('div_busca_resultado').style.display = "block";
			document.getElementById('div_editar').style.display = "none";
	}
}
</script>

<table class="box_relatorio" width="1024" border="0">
	<tr class="cabecalho_tr">
		<td width="5%"><img style="cursor:pointer;" align="center" title="Novo" src="imagens/icon_new.gif" border="0" onclick="InserirGrupo('inserir')" /></td>
		<td nowrap><span class="cabecalho_tr">CADASTRO DE GRUPOS DE SMS</span></td>
	</tr>
</table>
<br />

<div id="div_editar" style="display:none">
<input type="hidden" id="sms_id" value="0" />
<form onsubmit="return SalvarItem('sms_', 'sms_grupo', 'Grupo de SMS', 'sms_descricao')">
<table class="box_relatorio" width="350" align="center" border="1">
	<tr class="subcabecalho_tr">
		<td colspan="2">DADOS DO GRUPO DE SMS</td>
	</tr>
	<tr class="tr_cor_cinza">
		<td nowrap>NOME</td>
		<td><div align="left"><input name="sms_descricao" type="text" id="sms_descricao" size="60" maxlength="150" /></div></td>
	</tr>
	<tr class="subcabecalho_tr">
		<td colspan="2" class="subcabecalho_tr"><input id="btn_salvar" type="submit" value="SALVAR" />&nbsp;<input id="btn_limpar" type="reset" value="LIMPAR" />&nbsp;<input id="btn_voltar" onclick="window.location.href='?mainapp=cadastro&app=sms_grupo'" type="button" value="VOLTAR" /></td>
	</tr>
</table>
</form>
</div>

<div id="div_busca_resultado" style="display:block">
<table class="box_relatorio" border="1" align="center" width="300">
	<tr class="subcabecalho_tr" valign="middle">
		<td colspan="4">GRUPOS DE SMS</td>
	</tr>
		<tr class="tr_cor_branco">
			<td>NOME</td>
			<td>EDITAR</td>
			<td>SMS</td>
			<td>EXCLUIR</td>
		</tr>
		<?php echo("$trs"); ?>
</table>
</div>

<div id="div_carregando" class="div_carregando_relatorio">
	<div align="center"><img src="imagens/loading.gif" border="0" /></div>
	<span class="span_carregando_relatorio" id="span_carregando_relatorio">????<br /> AGUARDE...</span>
</div>