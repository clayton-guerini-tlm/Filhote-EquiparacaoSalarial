<?php
$link = RetornaConexaoMysql('local', 'sigo_integrado');

$gre_id = $_GET['id'];
$nome_grupo = $_GET['nome_grupo'];

$vet_relacionamento = RetornaIdsRelacionados($link, 'tbl_contato');

$Sql = "SELECT con_id, con_email, con_nome FROM tbl_contato WHERE gre_id=$gre_id ORDER BY con_nome ASC";
$rs_contato = mysqli_query($link, $Sql);
$trs = "";
while ($row_contato=mysqli_fetch_assoc($rs_contato)) {
	$id = $row_contato['con_id'];
	$nome = $row_contato['con_nome'];
	$email = $row_contato['con_email'];
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
			$onclick = "onclick=\"ExcluirItem('$id', '$nome', 'contato', 'con_id', 'Contatos')\"";
			$src = "src=\"imagens/del.gif\"";
	}
	
	$trs.="<tr class=\"$estilo\">";
	$trs.="<td align=\"left\" nowrap>$nome</td>";
	$trs.="<td align=\"left\" nowrap>$email</td>";
	$trs.="<td><img style=\"cursor:pointer;\" onclick=\"EditarItem('$id', 'con_', 'contato', 'Contatos', 'con_nome')\" title=\"Editar\"  src=\"imagens/bt_editar.gif\"border=0 /></td>";
	$trs.="<td><img style=\"cursor:pointer;\" $onclick title=\"Excluir\" $src border=0 /></td>";
	$trs.="</tr>";
	
}

mysqli_close($link);
 		
?>
<script type="text/javascript">
function InserirContato(tipo){
	if(tipo=='inserir'){
		document.getElementById('div_busca_resultado').style.display = "none";
		document.getElementById('div_editar').style.display = "block";		
		document.getElementById('con_nome').focus();		
		}else{
			document.getElementById('div_busca_resultado').style.display = "block";
			document.getElementById('div_editar').style.display = "none";
	}
}
</script>
<table class="box_relatorio" width="1024" border="0">
	<tr class="cabecalho_tr">
		<td width="5%"><img style="cursor:pointer;" onclick="window.location.href='?mainapp=cadastro&app=email_grupo'" src="imagens/botao_voltar.gif" title="Voltar" height="65%" /></td>
		<td width="5%"><img style="cursor:pointer;" align="center" title="Novo" src="imagens/icon_new.gif" border="0" onclick="InserirContato('inserir')" /></td>
		<td nowrap><span class="cabecalho_tr">CADASTRO DE CONTATOS</span></td>
	</tr>
</table>
<br />

<div id="div_editar" style="display:none">
<input type="hidden" id="con_id" value="0" />
<input type="hidden" id="gre_id" value="<?php echo $gre_id ?>" />
<form onsubmit="return SalvarItem('con_', 'contato', 'Grupo de E-mail', 'con_nome')">
<table class="box_relatorio" width="350" align="center" border="1">
	<tr class="subcabecalho_tr">
		<td colspan="2">DADOS DO CONTATO</td>
	</tr>
	<tr class="tr_cor_cinza">
		<td nowrap>NOME</td>
		<td><div align="left"><input name="NOME" type="text" id="con_nome" size="60" maxlength="150" /></div></td>
	</tr>
	<tr class="tr_cor_branco">
		<td nowrap>E-MAIL</td>
		<td><div align="left"><input name="EMAIL" type="text" id="con_email" size="60" maxlength="150" /></div></td>
	</tr>
	<tr class="subcabecalho_tr">
		<td colspan="2" class="subcabecalho_tr"><input id="btn_salvar" type="submit" value="SALVAR" />&nbsp;<input id="btn_limpar" type="reset" value="LIMPAR" />&nbsp;<input id="btn_voltar" onclick="window.location.href='?mainapp=cadastro&app=contato&id=<?php echo $gre_id?>&nome_grupo=<?php echo $nome_grupo?>'" type="button" value="VOLTAR" /></td>
	</tr>
</table>
</form>
</div>

<div id="div_busca_resultado" style="display:block">
<table class="box_relatorio" border="1" align="center" width="300">
	<tr class="subcabecalho_tr" valign="middle">
		<td colspan="4">CONTATOS DO GRUPO DE EMAILS - <?php echo strtoupper($nome_grupo) ?></td>
	</tr>
		<tr class="tr_cor_branco">
			<td>NOME</td>
			<td>EMAIL</td>
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