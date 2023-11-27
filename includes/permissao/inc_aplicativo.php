<?php

function RetornaOptionNivel($selecionado){
	$options = "";
	for ($i=0;$i<=30;$i++){
		$selecionado == $i ? $selected="selected" : $selected = "";
		$options.="<option $selected value=\"$i\">$i</option>";
	}
	return $options;
}

$link = RetornaConexaoMysql('local', 'sigo_integrado');

$grupo_id = $_SESSION['SIGO']['ACESSO']['ID_GRUPO'];
if($grupo_id != 2){
	$where_area = " AND men_area = '{$_SESSION['SIGO']['ACESSO']['FILIAL']}'";
	}else{
		$where_area = "";
}

//men_resumo <>'SIGO_ADM_HOME' AND 
$Sql = "SELECT men_id, men_nome FROM tbl_menu WHERE men_resumo <>'SIGO_ADM_LOGOUT' $where_area ORDER BY men_nome ASC";
$rs_menu = mysqli_query($link, $Sql);
$options_menu = GeraOptionGenerico($rs_menu,'men_id','men_nome',$_GET['menu'], 'Selecione');

$checked_todos = "checked";
$tipo_selecionado 	= $_GET['tipo'];

if($tipo_selecionado == "usuario"){
	$retorno = "?mainapp=cadastro&app=usuario";
	}else{
		$retorno = "?mainapp=cadastro&app=grupo";
}

if(isset($_GET['menu'])){
	$menu = $_GET['menu'];
	if(isset($_GET['tipo']) && isset($_GET['id'])){
		$tipo_selecionado 	= $_GET['tipo'];
		$id_selecionado 	= $_GET['id'];
		
		if($tipo_selecionado == "usuario"){
			$tabela = 'tbl_usuario';
			$campo_retorno = 'usr_nome_visitante';
			$campo_id = 'usr_id';
			$prefixo_nome = 'USU&Aacute;RIO: ';
			}else{
				$tabela = 'tbl_grupo';
				$campo_retorno = 'gru_descricao';
				$campo_id = 'gru_id';
				$prefixo_nome = 'GRUPO: ';
		}
		
		$nome_usuario = RetornaCampoGenerico($tabela, $campo_retorno, $campo_id, $id_selecionado);
		
		if($tipo_selecionado == "usuario"){
			$retorno = "?mainapp=cadastro&app=usuario&valor_get=$nome_usuario";
		}
		
		$vet_permissao = RetornaPermissao($link, $tipo_selecionado, $id_selecionado);
		//echo "<pre>";print_r($vet_permissao);exit;
		
		$Sql = "SELECT * FROM tbl_menu WHERE men_id='$menu' ";
		if($rs = mysqli_query($link, $Sql)){
			if($row = mysqli_fetch_assoc($rs)){
				$menu_id = $row['men_id'];	
				$menu_nome = $row['men_nome'];	
				$Sql = "SELECT * FROM tbl_menu_submenu WHERE men_id=$menu_id AND smu_mostrar=1 ORDER BY smu_ordem ASC";
				//echo $Sql;exit;
				$menu = "";
				if($rs_submenu = mysqli_query($link, $Sql)){
					$i=0;
					while ($row_submenu = mysqli_fetch_assoc($rs_submenu)) {
						$submenu_id = $row_submenu['smu_id'];
						$submenu_nome = $row_submenu['smu_nome'];
						$submenu_link = $row_submenu['smu_link'];
						
						$marcado = $vet_permissao['per_habilitar'][0][$submenu_id] == 1 ? "checked" : "";
						if($vet_permissao['per_habilitar'][0][$submenu_id] == 0){
							$checked_todos = ""	;
						}
						$nivel = $vet_permissao['per_nivel'][0][$submenu_id];						
						$options_nivel =RetornaOptionNivel($nivel);
						
						
						$menu.= <<< EOF
						<tr class="tr_cor_branco">
							<td nowrap align="left">$submenu_id</td>
							<td nowrap align="left">$submenu_nome</td>
							<td nowrap></td>
							<td nowrap align="left">$submenu_link</td>
							<td nowrap><input id="per_habilitar_$submenu_id" type="checkbox" $marcado onclick="SalvarPermissao('$tipo_selecionado', $id_selecionado, 'submenu', $submenu_id, 'per_habilitar_$submenu_id', 'per_nivel_$submenu_id','check')" /></td>
							<td nowrap><select id="per_nivel_$submenu_id" onchange="SalvarPermissao('$tipo_selecionado', $id_selecionado, 'submenu', $submenu_id, 'per_habilitar_$submenu_id', 'per_nivel_$submenu_id','select')">$options_nivel</select>
						</tr>
EOF;
						
						$Sql = "SELECT * FROM tbl_menu_aplicacao WHERE smu_id=$submenu_id AND apl_mostrar=1 ORDER BY apl_ordem ASC";
						if($rs_aplicacao = mysqli_query($link, $Sql)){
							$j=0;
							while ($row_aplicacao = mysqli_fetch_assoc($rs_aplicacao)) {
								$aplicacao_id 	= $row_aplicacao['apl_id'];
								$aplicacao_nome = $row_aplicacao['apl_nome'];
								$aplicacao_link = $row_aplicacao['apl_link'];
								
								$marcado = $vet_permissao['per_habilitar'][$aplicacao_id][0] == 1 ? "checked" : "";
								if($vet_permissao['per_habilitar'][$aplicacao_id][0] == 0){
									$checked_todos = ""	;
								}
								$nivel = $vet_permissao['per_nivel'][$aplicacao_id][0];
								$options_nivel = RetornaOptionNivel($nivel);
								
								$menu.= <<< EOF
								<tr class="tr_cor_cinza">
									<td nowrap>$aplicacao_id</td>
									<td nowrap>--></td>
									<td nowrap align="left">$aplicacao_nome</td>
									<td nowrap align="left">$aplicacao_link</td>
									<td nowrap><input id="per_habilitar_0_{$aplicacao_id}" type="checkbox" $marcado onclick="SalvarPermissao('$tipo_selecionado', $id_selecionado, 'aplicacao', $aplicacao_id, 'per_habilitar_0_{$aplicacao_id}', 'per_nivel_0_{$aplicacao_id}','check')" /></td>
									<td nowrap><select id="per_nivel_0_{$aplicacao_id}" onchange="SalvarPermissao('$tipo_selecionado', $id_selecionado, 'aplicacao', $aplicacao_id, 'per_habilitar_0_{$aplicacao_id}', 'per_nivel_0_{$aplicacao_id}','select')">$options_nivel</select>
								</tr>
EOF;
								$j++;
							}
						}
						$i++;
					}
					}else{
						$menu = "Erro ao retornar o MENU.";
				}
				}else{
					$menu = "Erro ao retornar o MENU.";
			}
			}else{
				$menu = "Erro ao retornar o MENU.";
			}
		}else{
			$menu = "Erro ao retornar o MENU.";

	}
$arvore_menu = <<< EOF
	<tr class="subcabecalho_tr">
		<td colspan="6">
			$nome_usuario
		</td>
	</tr>
	<tr class="subcabecalho_tr">
		<td align="right" valign="middle" colspan="6">
			MARCAR/DESMARCAR TODOS<input type="checkbox" $checked_todos id="marcar_todos" onclick="MarcaDesmarcaTodasPermissoes(this)" />
		</td>
	</tr>
	<tr class="subcabecalho_tr">
		<td nowrap align="center">ID</td>
		<td nowrap align="left">SUBMENU</td>
		<td nowrap align="left">APLICA&Ccedil;&Atilde;O</td>
		<td nowrap align="left">LINK</td>
		<td nowrap>HABILITAR?</td>
		<td nowrap>N&Iacute;VEL</td>
	</tr>
	$menu
EOF;
	//$arvore_menu = $menu;
}else{
	$arvore_menu = "";
}

mysqli_close($link);


?>
<script type="text/javascript">
function MudaMenu(menu){
	if(menu != ""){
		document.location.href='principal.php?mainapp=permissao&app=aplicativo&tipo=<?php echo $_GET['tipo'] ?>&id=<?php echo $_GET['id'] ?>&menu='+menu;
	}
}

function MarcaDesmarcaTodasPermissoes(obj){
	
	var id_tipo_permissao = document.getElementById('id_tipo_permissao').value;
	var tipo_permissao = document.getElementById('tipo_permissao').value;
	var submenus = "";
	var apps = "";
	
	var checks = document.getElementsByTagName('input');
	var c;
	var tmp;
	var tmp2;
	
	var vetTmp;
	//alert('ok');
	for (var i=0;i<checks.length;i++) {
		c = checks[i];
		if (c.id.substr(0,14) == 'per_habilitar_'){
			tmp = c.id.substr(14);
			vetTmp = tmp.split("_");
			//alert(obj.checked + " " + document.getElementById(c.id).checked);
			document.getElementById(c.id).checked = obj.checked
			if (vetTmp.length == 2){
				apps = apps + vetTmp[1] + "|";
				}else if (vetTmp.length == 1){
					submenus = submenus + vetTmp[0] + "|";
			}
			
		}
	}
	
	
	var campos = "funcao_ajax=AjaxSalvarPermissaoMultipla&id_tipo_permissao="+id_tipo_permissao+"&tipo_permissao="+tipo_permissao+"&submenus="+submenus+"&apps="+apps+"&valor="+obj.checked;
	ReqAjax(Retorno, campos);

	function Retorno(obj){
			
			if (obj.responseText != "ok") {
				alert(obj.responseText);
			}
			return false;
		
	}
	
}

</script>
<table class="box_relatorio" width="1024" border="0">
	<tr class="cabecalho_tr">
		<td width="5%"><img style="cursor:pointer;" onclick="window.location.href='<?php echo $retorno ?>'" src="imagens/botao_voltar.gif" title="Voltar" height="65%" /></td>
		<td nowrap><span class="cabecalho_tr">PERMISS&Otilde;ES PARA APLICATIVOS</span></td>
	</tr>
</table>
<br />
<div id="div_sel_menu">
<input type="hidden" id="id_tipo_permissao" value="<?php echo $id_selecionado ?>" />
<input type="hidden" id="tipo_permissao" value="<?php echo $tipo_selecionado ?>" />
<table class="box_relatorio" width="350" align="center" border="1">
	<tr class="subcabecalho_tr">
		<td>SELECIONE O MENU</td>
	</tr>
	<tr class="tr_cor_cinza">
		<td><select id="sel_menu" onchange="MudaMenu(this.value)"><?php echo $options_menu ?></select></td>
	</tr>
</table>
<br />
</div>

<div id="div_busca_resultado" style="display:block">
<table class="box_relatorio" border="1" align="center" width="300">
	<?php echo $arvore_menu ?>
</table>
</div>

<div id="div_carregando" class="div_carregando_relatorio">
	<div align="center"><img src="imagens/loading.gif" border="0" /></div>
	<span class="span_carregando_relatorio" id="span_carregando_relatorio">????<br /> AGUARDE...</span>
</div>