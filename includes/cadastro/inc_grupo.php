<?php
$link = RetornaConexaoMysql('local', 'sigo_integrado');

$grupo_id 	= $_SESSION['SIGO']['ACESSO']['ID_GRUPO'];
$filial 	= $_SESSION['SIGO']['ACESSO']['FILIAL'];

$filial = explode("_", $filial);
$filial = substr($filial[0],0,4) . " " . substr($filial[0],4,1) . " " . $filial[1];

$sql_area = "SELECT DISTINCT descricao from tbl_area order by descricao";
$rs_area = mysqli_query($link, $sql_area) or die('erro SQL AREA');
$options_usuario_area = GeraOptionGenerico($rs_area, 'descricao', 'descricao', '', 'Selecione');
//$options_usuario_area = RetornaOptionUsuarioFilial();

if($grupo_id != 2){
	$where_grupo = " WHERE gru_privilegio < 80 AND gru_descricao LIKE '%$filial%'";
	}else{
		$where_grupo = "";
}

$Sql = "SELECT sis_id, sis_titulo FROM tbl_sistema ORDER BY sis_titulo ASC";
$rs_sistema = mysqli_query($link, $Sql);
$options_sistema = GeraOptionGenerico($rs_sistema,'sis_id','sis_titulo','','');

$vet_relacionamento = RetornaIdsRelacionados($link, 'tbl_grupo');

$get = trataPost($link);
if(isset($get['filtro-area'])){
    if(empty ($where_grupo)){
        $where_grupo = " WHERE ";
    }else{
        $where_grupo .= " AND ";
    }

    $where_grupo .= ' gru_area = "'.$get['filtro-area'].'"';
}
$Sql = "SELECT gru_id, gru_descricao,gru_area FROM tbl_grupo $where_grupo ORDER BY gru_area,gru_descricao ASC";
$rs_grupo = mysqli_query($link, $Sql);
$trs = "";
while ($row_grupo=mysqli_fetch_assoc($rs_grupo)) {
	$id = $row_grupo['gru_id'];
	$descricao = $row_grupo['gru_descricao'];
	$area = str_replace("_"," ",$row_grupo['gru_area']);
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
			$onclick = "onclick=\"ExcluirItem('$id', '$descricao', 'grupo', 'gru_id', 'Grupo')\"";
			$src = "src=\"imagens/del.gif\"";
	}

	switch ($id) {
		case 1:
			$img_visualizar = "";
			$img_permissao = "OEM";
			break;

		case 2:
			$img_visualizar = "";
			$img_permissao = "TOTAL";
			break;

		case 3:
			$img_visualizar = "";
			$img_permissao = "TOTAL";
			break;

		case 4:
			$img_visualizar = "";
			$img_permissao = "REGIONAL";
			break;

		case 67:
			$img_visualizar = "";
			$img_permissao = "REGIONAL";
			break;

		//Grupos do seguro inicio em 129 até 134 a principio.
		/*case 129:
		case 130:
		case 131:
		case 132:
		case 133:
		case 134:
			$img_visualizar = "";
			$img_permissao = "REGIONAL";
			break;*/
		//Fim dos grupos do seguro

		default:

			$img_visualizar = "<a href=\"?mainapp=cadastro&app=visualizar_grupo&id=$id&nome_grupo=$descricao\"><img title=\"visualizar\" src=\"imagens/lupa.gif\"border=0 /></a>";
			$img_permissao = "<img style=\"cursor:pointer;\" onclick=\"window.location.href='principal.php?mainapp=permissao&app=aplicativo&tipo=grupo&id=$id'\" title=\"Permiss&otilde;es\" height=\"70%\"  src=\"imagens/icon_key.gif\" border=0 />";

			break;
	}

	$trs.="<tr class=\"$estilo\">";
	$trs.="<td align=\"center\" nowrap>$id</td>";
	$trs.="<td align=\"left\" nowrap>$area</td>";
	$trs.="<td align=\"left\" nowrap>$descricao</td>";
	$trs.="<td><img style=\"cursor:pointer;\" onclick=\"EditarGrupo('$id')\" title=\"Editar\"  src=\"imagens/bt_editar.gif\"border=0 /></td>";
	$trs.="<td>$img_visualizar</td>";
	$trs.="<td>$img_permissao</td>";
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
		document.getElementById('gru_descricao').focus();
		}else{
			document.getElementById('div_busca_resultado').style.display = "block";
			document.getElementById('div_editar').style.display = "none";
	}
}

function SalvarGrupo(){
	var id = document.getElementById('gru_id').value;
	var descricao = document.getElementById('gru_descricao').value;
	var area = document.getElementById('gru_area').value;

	var vet_select = document.getElementById('sistema');
	var opt_len = vet_select.length;
	var achou_opt = false;
	var sistema = "";

	if(descricao == ""){
		alert('Campo DESCRIÇÃO não pode ser vazio.');
		return false;
	}

	if(area == ""){
		alert('Campo ÁREA não pode ser vazio.');
		return false;
	}

	for (var jj=0; jj< opt_len;jj++){
		if(vet_select.options[jj].selected == true){
			sistema = sistema + vet_select.options[jj].value + "|";
			achou_opt = true;
		}
	}

	if(achou_opt){
		sistema = sistema.substr(0,sistema.length-1);
		}else{
			retorno = false;
			msg+="Selecione pelo menos um SISTEMA antes de salvar.\n"
	}

	document.getElementById('span_carregando_relatorio').innerHTML = "CADASTRANDO GRUPO<br /> AGUARDE...";
	document.getElementById('div_editar').style.display = "none";
	document.getElementById('div_carregando').style.display = "block";

	var campos = "funcao_ajax=AjaxSalvarGrupo&gru_id="+id+"&gru_descricao="+descricao+"&sistema="+sistema+"&gru_area="+area;

	var AjaxSalvarGrupo = getAjax();
	if (AjaxSalvarGrupo != null) {
		AjaxSalvarGrupo.open("POST", "ajax/ajax_funcoes.php", true);
		AjaxSalvarGrupo.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		AjaxSalvarGrupo.setRequestHeader("Content-length", campos.length);
		AjaxSalvarGrupo.setRequestHeader("Connection", "close");
		AjaxSalvarGrupo.send(campos);
		AjaxSalvarGrupo.onreadystatechange = function(){
			if (AjaxSalvarGrupo.readyState == 4 ){
				if(AjaxSalvarGrupo.responseText == "inseriu"){
					document.getElementById('div_carregando').style.display = "none";
					document.getElementById('div_editar').style.display = "none";
					alert('Grupo cadastrado com êxito!');
					window.location.href='?mainapp=cadastro&app=grupo';
					}else if(AjaxSalvarGrupo.responseText == "alterou"){
						document.getElementById('div_carregando').style.display = "none";
						document.getElementById('div_editar').style.display = "none";
						alert('Grupo alterado com êxito!');
						window.location.href='?mainapp=cadastro&app=grupo';
						}else{
							document.getElementById('div_carregando').style.display = "none";
							document.getElementById('div_editar').style.display = "block";
							alert(AjaxSalvarGrupo.responseText);
				}
			}
		}
	}

	return false;
}

function EditarGrupo(id){

	document.getElementById('span_carregando_relatorio').innerHTML = "BUSCANDO GRUPO<br /> AGUARDE...";
	document.getElementById('div_editar').style.display = "none";
	document.getElementById('div_busca_resultado').style.display = "none";
	document.getElementById('div_carregando').style.display = "block";
	var campos = "funcao_ajax=AjaxBuscaGrupo&gru_id="+id;
	var AjaxBuscaGrupo = getAjax();
	if (AjaxBuscaGrupo != null) {
		AjaxBuscaGrupo.open("POST", "ajax/ajax_funcoes.php", true);
		AjaxBuscaGrupo.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		AjaxBuscaGrupo.setRequestHeader("Content-length", campos.length);
		AjaxBuscaGrupo.setRequestHeader("Connection", "close");
		AjaxBuscaGrupo.send(campos);
		AjaxBuscaGrupo.onreadystatechange = function(){
			if (AjaxBuscaGrupo.readyState == 4 ){
				if(AjaxBuscaGrupo.responseXML){
					ProcessaXMLEditarGrupo(AjaxBuscaGrupo.responseXML);
					}else{
						document.getElementById('div_carregando').style.display = "none";
						document.getElementById('div_busca_resultado').style.display = "block";
						alert(AjaxBuscaGrupo.responseText);
				}
			}
		}
	}
	return false;
}

function ProcessaXMLEditarGrupo(obj){

	var dataArray  = obj.getElementsByTagName("grupo");
	var quant = dataArray.length;
	var campo;

	var id;
	var descricao;
	var sistema;
	var area;
	var vet_sistema;
	var vet_select = document.getElementById('sistema');
	var opt_len = vet_select.length;
	var achou_opt;

	if(dataArray.length > 0){
		document.getElementById('div_carregando').style.display = "none";
	 	for(var i = 0 ; i < quant ; i++) {
	 		campo = dataArray[i];
	 		id  		= campo.getElementsByTagName('id')[0].firstChild.nodeValue;
	 		descricao	= campo.getElementsByTagName('descricao')[0].firstChild.nodeValue;
	 		sistema		= campo.getElementsByTagName('sistema')[0].firstChild.nodeValue;
	 		area		= campo.getElementsByTagName('area')[0].firstChild.nodeValue;
			vet_sistema = sistema.split('|');

			for (var jj=0; jj< opt_len;jj++){
				achou_opt = false;
				for (var opt_id in vet_sistema){
					sis_id = vet_sistema[opt_id];
					//alert(sis_id + " - " + vet_select.options[jj].value);
					if(vet_select.options[jj].value == sis_id){
						achou_opt = true;
					}
				}
				vet_select.options[jj].selected = achou_opt;
			}

	 		document.getElementById('gru_id').value = id;
	 		document.getElementById('gru_descricao').value = descricao;
	 		document.getElementById('gru_area').value = area;
	 	}
	}
	document.getElementById('div_carregando').style.display = "none";
	document.getElementById('div_busca_resultado').style.display = "none";
	document.getElementById('div_editar').style.display = "block";
}

</script>


<table class="box_relatorio" width="1024" border="0">
	<tr class="cabecalho_tr">
		<td width="5%"><img style="cursor:pointer;" align="center" title="Novo" src="imagens/icon_new.gif" border="0" onclick="InserirGrupo('inserir')" /></td>
		<td nowrap><span class="cabecalho_tr">CADASTRO DE GRUPOS DE USU&Aacute;RIO</span></td>
	</tr>
</table>
<br />

<table class="box_relatorio" align="center" border="0">
    <form method="POST" url="principal.php?mainapp=cadastro&app=grupo">
	<tr class="subcabecalho_tr">
            <td width=42 >ÁREA</td>
            <?php
                $conn = RetornaConexaoMysql('local', 'sigo_integrado');
                $executou = mysqli_query($link, "SELECT * FROM tbl_area;");
            ?>
            <td>
                <select name="filtro-area" style="float:left">
                    <option value="">SELECIONE</option>
                    <?php if($executou): ?>
                        <?php while(($resultado = mysqli_fetch_array($executou))):?>
                            <option value="<?php echo $resultado['descricao']?>">
                                <?php echo str_replace('_', ' ', $resultado['descricao']);?>
                            </option>
                        <?php endwhile;?>
                    <?php endif;?>
                </select>
            </td>
	</tr><tr class="subcabecalho_tr">
            <td colspan="2">
                <input type="submit" value="FILTRAR"/>
            </td>

	</tr>
    </form>
</table>
<br />

<div id="div_editar" style="display:none">
<input type="hidden" id="gru_id" value="0" />
<form onsubmit="return SalvarGrupo();">
<table class="box_relatorio" width="350" align="center" border="1">
	<tr class="subcabecalho_tr">
		<td colspan="2">DADOS DO GRUPO</td>
	</tr>
	<tr class="tr_cor_cinza">
		<td nowrap>NOME</td>
		<td><div align="left"><input name="gru_descricao" type="text" id="gru_descricao" size="60" maxlength="150" /></div></td>
	</tr>
	<tr class="tr_cor_branco">
		<td>ÁREA</td>
		<td><div align="left"><select name="ÁREA" id="gru_area">
		<?php echo $options_usuario_area ?>
		</select></div></td>
	</tr>
	<tr class="tr_cor_cinza">
		<td nowrap>SISTEMAS</td>
		<td><div align="left"><select id="sistema" multiple >
				<?php echo $options_sistema ?>
			</select></div></td>
	</tr>
	<tr class="subcabecalho_tr">
		<td colspan="2" class="subcabecalho_tr"><input id="btn_salvar" type="submit" value="SALVAR" />&nbsp;<input id="btn_limpar" type="reset" value="LIMPAR" />&nbsp;<input id="btn_voltar" onclick="window.location.href='?mainapp=cadastro&app=grupo'" type="button" value="VOLTAR" /></td>
	</tr>
</table>
</form>
</div>

<div id="div_busca_resultado" style="display:block">
<table class="box_relatorio" border="1" align="center" width="300">
	<tr class="subcabecalho_tr" valign="middle">
		<td colspan="7">GRUPOS</td>
	</tr>
		<tr class="tr_cor_branco">
			<td>ID</td>
			<td>&Aacute;REA</td>
			<td>NOME</td>
			<td>EDITAR</td>
			<td>VISUALIZAR</td>
			<td>PERMISS&Otilde;ES</td>
			<td>EXCLUIR</td>
		</tr>
		<?php echo("$trs"); ?>
</table>
</div>

<div id="div_carregando" class="div_carregando_relatorio">
	<div align="center"><img src="imagens/loading.gif" border="0" /></div>
	<span class="span_carregando_relatorio" id="span_carregando_relatorio">????<br /> AGUARDE...</span>
</div>