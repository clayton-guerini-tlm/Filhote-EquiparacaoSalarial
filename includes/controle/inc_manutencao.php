<?php
//print_r($_SESSION['SIGO']['ACESSO']);

$link = RetornaConexaoMysql('local', 'sigo_integrado');

$Sql = "SELECT gre_id, gre_descricao FROM tbl_email_grupo ORDER BY gre_descricao ASC";
$rs_email_grupo = mysqli_query($link, $Sql) or die(mysqli_error($link));

$options_grupo = GeraOptionGenerico($rs_email_grupo,'gre_id','gre_descricao','','');

$vet_relacionamento = RetornaIdsRelacionados($link, 'tbl_manutencao');

$Sql = "SELECT man_id, man_sistema, man_responsavel, man_dthr_alteracao,man_descricao FROM tbl_manutencao ORDER BY man_descricao ASC";
$rs_manutencao = mysqli_query($link, $Sql);
$trs = "";
while ($row_manutencao=mysqli_fetch_assoc($rs_manutencao)) {
	$id = $row_manutencao['man_id'];
	$descricao 	= $row_manutencao['man_sistema'];
	$responsavel= $row_manutencao['man_responsavel'];
        $descricao_problema = $row_manutencao['man_descricao'];
	$data 		= ConvertDataHoraMysql($row_manutencao['man_dthr_alteracao'],"normal",true);
	
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
			$onclick = "onclick=\"ExcluirItem('$id', '$descricao', 'manutencao', 'man_id', 'Manutenção')\"";
			$src = "src=\"imagens/del.gif\"";
	}
	
	$trs.="<tr class=\"$estilo\">";
	$trs.="<td align=\"left\" nowrap>$descricao</td>";
        $trs.="<td align=\"left\">$descricao_problema</td>";
	$trs.="<td align=\"left\" nowrap>$responsavel</td>";
	$trs.="<td align=\"left\" nowrap>$data</td>";
	$trs.="<td><img style=\"cursor:pointer;\" onclick=\"EditarItem('$id', 'man_', 'manutencao', 'Manutenção', 'man_sistema')\" title=\"Editar\"  src=\"imagens/bt_editar.gif\"border=0 /></td>";
	$trs.="<td><img style=\"cursor:pointer;\" $onclick title=\"Excluir\" $src border=0 /></td>";
	$trs.="</tr>";
	
}

mysqli_close($link);
 		
?>

<script type="text/javascript">
function InserirManutencao(tipo){
	if(tipo=='inserir'){
		document.getElementById('div_busca_resultado').style.display = "none";
		document.getElementById('div_editar').style.display = "block";		
		document.getElementById('man_sistema').focus();		
		}else{
			document.getElementById('div_busca_resultado').style.display = "block";
			document.getElementById('div_editar').style.display = "none";
	}
}
</script>

<table class="box_relatorio" width="100%" border="0">
	<tr class="cabecalho_tr">
		<td width="5%"><img style="cursor:pointer;" align="center" title="Novo" src="imagens/icon_new.gif" border="0" onclick="InserirManutencao('inserir')" /></td>
		<td nowrap><span class="cabecalho_tr">MANUTENÇÃO DE SISTEMAS</span></td>
	</tr>
</table>
<br />

<div id="div_editar" style="display:none">
<input type="hidden" id="man_id" value="0" />
<form onsubmit="return SalvarItem('man_', 'manutencao', 'Manutenção', 'man_descricao')">
<table class="box_relatorio" width="350" align="center" border="1">
	<tr class="subcabecalho_tr">
		<td colspan="2">DADOS DA MANUTENÇÃO</td>
	</tr>
	<tr class="tr_cor_cinza">
		<td nowrap align="right">SISTEMA</td>
		<td><div align="left"><input name=SISTEMA" type="text" id="man_sistema" size="55" maxlength="150" /></div></td>
	</tr>
	<tr class="tr_cor_branco">
		<td nowrap align="right">DESCRIÇÃO</td>
		<td><div align="left"><textarea name="DESCRIÇÃO" id="man_descricao" cols="35" rows="5"></textarea></div></td>
	</tr>
	<tr class="tr_cor_cinza">
		<td nowrap align="right">IMPACTO</td>
		<td><div align="left"><textarea name="IMPACTO" id="man_impacto" cols="35" rows="5"></textarea></div></td>
	</tr>

	<tr class="tr_cor_branco">
		<td nowrap align="right">GRUPO DE E-MAIL</td>
		<td><div align="left"><select multiple name="GRUPO DE E-MAIL" id="man_grupo" size="5"><?php echo $options_grupo ?></select></div></td>
	</tr>
	
	<tr class="tr_cor_cinza">	
		<td nowrap align="right">INÍCIO</td>
		<td nowrap>
		<div align="left"> 
			Às <input name="HORA IN&Iacute;CIO" type="text" id="man_hora_inicio" onKeyPress="return txtBoxFormat(document.myForm,'man_hora_inicio','99:99', event);" value="" size="6" maxlength="5" /> de 
			<input name="DATA IN&Iacute;CIO" type="text" id="man_dt_inicio" onKeyPress="return txtBoxFormat(document.myForm,'man_dt_inicio','99/99/9999', event);" value="" size="8" maxlength="10" /> <input type="button" onClick="displayCalendar(document.getElementById('man_dt_inicio'),'dd/mm/yyyy',this)" class="calendario" style="cursor:pointer;" />
		</div></td>
	</tr>
	
	<tr class="tr_cor_branco">	
		<td nowrap align="right">FIM</td>
		<td nowrap>
		<div align="left">
			Às <input name="HORA FIM" type="text" id="man_hora_fim" onKeyPress="return txtBoxFormat(document.myForm,'man_hora_fim','99:99', event);" value="" size="6" maxlength="5" /> de 
			<input name="DATA FIM" type="text" id="man_dt_fim" onKeyPress="return txtBoxFormat(document.myForm,'man_dt_fim','99/99/9999', event);" value="" size="8" maxlength="10" /> <input type="button" onClick="displayCalendar(document.getElementById('man_dt_fim'),'dd/mm/yyyy',this)" class="calendario" style="cursor:pointer;" />
		</div>
		</td>
	</tr>
	
	<tr class="tr_cor_cinza">
		<td nowrap align="right">RESPONSAVEL</td>
		<td><div align="left"><span id="man_responsavel"></span></div></td>
	</tr>
	
	<tr class="tr_cor_branco">
		<td nowrap align="right">HORA DA MANUTENÇÃO</td>
		<td><div align="left"><span id="man_dthr_alteracao"></span></div></td>
	</tr>
	
	<tr class="subcabecalho_tr">
		<td colspan="2" class="subcabecalho_tr"><input id="btn_salvar" type="submit" value="SALVAR" />&nbsp;<input id="btn_limpar" type="reset" value="LIMPAR" />&nbsp;<input id="btn_voltar" onclick="window.location.href='?mainapp=controle&app=manutencao'" type="button" value="VOLTAR" /></td>
	</tr>
</table>
</form>
</div>

<div id="div_busca_resultado" style="display:block">
<table class="box_relatorio" border="1" align="center" width="100%">
	<tr class="subcabecalho_tr" valign="middle">
		<td colspan="6">MANUTENÇÕES</td>
	</tr>
		<tr class="tr_cor_branco">
			<td>MANUTEN&Ccedil;&Atilde;O</td>
                        <td>DESCRIÇÃO PROBLEMA</td>
			<td>RESPONS&Aacute;VEL</td>
			<td>DATA</td>
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