<?php
$link = RetornaConexaoMysql('local', 'sigo_integrado');

$vet_relacionamento = RetornaIdsRelacionados($link, 'tbl_pendencia_tipo');
//echo "<pre>";print_r($vet_relacionamento);
//exit;

$Sql = "SELECT pdt_id, pdt_descricao FROM tbl_pendencia_tipo ORDER BY pdt_descricao ASC";
$rs_pendencia_tipo = mysqli_query($link, $Sql);
$trs = "";
while ($row_pendencia_tipo=mysqli_fetch_assoc($rs_pendencia_tipo)) {
	$id = $row_pendencia_tipo['pdt_id'];
	$descricao = $row_pendencia_tipo['pdt_descricao'];
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
			$onclick = "onclick=\"ExcluirItem('$id', '$descricao', 'pendencia_tipo', 'pdt_id', 'Tipo de Pendência')\"";
			$src = "src=\"imagens/del.gif\"";
	}
	
	$trs.="<tr class=\"$estilo\">";
	$trs.="<td align=\"left\" nowrap>$descricao</td>";
	$trs.="<td><img style=\"cursor:pointer;\" onclick=\"EditarItem('$id', 'pdt_', 'pendencia_tipo', 'pendencia_tipo', 'pdt_descricao')\" title=\"Editar\"  src=\"imagens/bt_editar.gif\" border=0 /></td>";
	$trs.="<td><img style=\"cursor:pointer;\" $onclick title=\"Excluir\" $src border=0 /></td>";
	$trs.="</tr>";
	
}

?>
<table class="box_relatorio" width="1024" border="0">
	<tr class="cabecalho_tr">
		<td width="5%"><img style="cursor:pointer;" align="center" title="Novo" src="imagens/icon_new.gif" border="0" onclick="InserirItem('pdt_','pdt_descricao')" /></td>
		<td nowrap><span class="cabecalho_tr">CADASTRO DE TIPOS DE PENDÊNCIAS</span></td>
	</tr>
</table>
<br />
<div id="div_sel_menu">
</div>
<div id="div_editar" style="display:none">
<input type="hidden" name="ID" id="pdt_id" value="0" />
<form onsubmit="return SalvarItem('pdt_', 'pendencia_tipo', 'Tipo de Pendência', 'pdt_descricao');">
<table class="box_relatorio" width="350" align="center" border="1">
	<tr class="subcabecalho_tr">
		<td colspan="2">DADOS DO TIPO DE PENDÊNCIA</td>
	</tr>
	<tr class="tr_cor_branco">
		<td nowrap>DESCRIÇÃO</td>
		<td><div align="left"><input name="descricao" type="text" id="pdt_descricao" size="60" maxlength="150" /></div></td>
	</tr>
	<tr class="subcabecalho_tr">
		<td colspan="2" class="subcabecalho_tr"><input id="btn_salvar" type="submit" value="SALVAR" />&nbsp;<input id="btn_limpar" type="reset" value="LIMPAR" />&nbsp;<input id="btn_voltar" onclick="window.location.href='?mainapp=controle&app=pendencia_tipo'" type="button" value="VOLTAR" /></td>
	</tr>
</table>
</form>
</div>

<div id="div_busca_resultado" style="display:block">
<table class="box_relatorio" border="1" align="center" width="300">
	<tr class="subcabecalho_tr" valign="middle">
		<td colspan="3">MENUS</td>
	</tr>
		<tr class="tr_cor_branco">
			<td>DESCRI&Ccedil;&Atilde;O</td>
			<td>EDITAR</td>
			<td>EXCLUIR</td>
		</tr>
		<?php echo("$trs"); ?>
</table>
</div>

<div id="div_carregando" class="div_carregando_relatorio">
	<div align="center"><img src="imagens/loading.gif" border="0" /></div>
	<span class="span_carregando_relatorio" id="span_carregando_relatorio">????<br /> AGUARDE...</span>
</div>