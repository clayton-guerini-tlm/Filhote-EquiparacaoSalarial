<?php

$link = RetornaConexaoMysql('local', 'sigo_integrado');

$grupo_id = $_SESSION['SIGO']['ACESSO']['ID_GRUPO'];
if($grupo_id != 2){
	$where_area = " AND men_area = '{$_SESSION['SIGO']['ACESSO']['FILIAL']}'";
	}else{
		$where_area = "";
}

$grupo_id = $_GET['id'];
$grupo_nome = $_GET['nome_grupo'];
	
$Sql = "

 SELECT DISTINCT(m1.men_id) as menu, m1.men_nome as descricao FROM tbl_permissao p1 

	LEFT JOIN tbl_menu_aplicacao a1 ON p1.apl_id = a1.apl_id 
	LEFT JOIN  tbl_menu_submenu s1 ON a1.smu_id = s1.smu_id 
	LEFT JOIN  tbl_menu m1 ON s1.men_id = m1.men_id  

WHERE p1.gru_id=$grupo_id AND  m1.men_id <> ''

UNION
 SELECT DISTINCT(m2.men_id) as menu, m2.men_nome as descricao FROM tbl_permissao p1 
	LEFT JOIN  tbl_menu_submenu s2 ON p1.smu_id = s2.smu_id 
	LEFT JOIN  tbl_menu m2 ON s2.men_id = m2.men_id  
WHERE p1.gru_id=$grupo_id AND m2.men_id <> ''

";

$rs_grupo = mysqli_query($link, $Sql) or die(mysqli_error($link));
while ($row_grupo = mysqli_fetch_assoc($rs_grupo)) {

	$menu_id = $row_grupo['menu'];	
	$menu_descricao = $row_grupo['descricao'];	
	$Sql = "SELECT s1.* FROM tbl_menu_submenu s1 
			INNER JOIN tbl_permissao p1 ON p1.smu_id=s1.smu_id
			WHERE s1.men_id=$menu_id AND p1.gru_id=$grupo_id
			ORDER BY s1.smu_ordem ASC";
	
	if($rs_submenu = mysqli_query($link, $Sql)){
		$menu.= <<< EOF
		<tr class="tr_cor_branco">
			<td colspan=4 nowrap align="center"><i>$menu_descricao</i>&nbsp;&nbsp;&nbsp;<a href="principal.php?mainapp=permissao&app=aplicativo&tipo=grupo&id=$grupo_id&menu=$menu_id"><img src="{$caminho_raiz}imagens/icon_key.gif" title="Permiss&otilde;es" height="70%" border=0 /></a></td>
		</tr>
		<tr class="subcabecalho_tr">
			<td nowrap align="center">ID</td>
			<td nowrap align="left">SUBMENU</td>
			<td nowrap align="left">APLICA&Ccedil;&Atilde;O</td>
			<td nowrap align="left">LINK</td>
		</tr>
EOF;
		while ($row_submenu = mysqli_fetch_assoc($rs_submenu)) {
			$submenu_id = $row_submenu['smu_id'];
			$submenu_nome = $row_submenu['smu_nome'];
			$submenu_link = $row_submenu['smu_link'];
			
			$menu.= <<< EOF
			<tr class="tr_cor_branco">
				<td nowrap align="center">$submenu_id</td>
				<td nowrap align="left">$submenu_nome</td>
				<td nowrap></td>
				<td nowrap align="left">$submenu_link</td>				
			</tr>
EOF;
			
			$Sql = "SELECT a1.* FROM tbl_menu_aplicacao a1
			INNER JOIN tbl_permissao p1 ON p1.apl_id=a1.apl_id
			WHERE a1.smu_id=$submenu_id AND p1.gru_id=$grupo_id
			ORDER BY a1.apl_ordem ASC";
			if($rs_aplicacao = mysqli_query($link, $Sql)){
				while ($row_aplicacao = mysqli_fetch_assoc($rs_aplicacao)) {
					$aplicacao_id 	= $row_aplicacao['apl_id'];
					$aplicacao_nome = $row_aplicacao['apl_nome'];
					$aplicacao_link = $row_aplicacao['apl_link'];
					
					$menu.= <<< EOF
					<tr class="tr_cor_cinza">
						<td nowrap>$aplicacao_id</td>
						<td nowrap>--></td>
						<td nowrap align="left">$aplicacao_nome</td>
						<td nowrap align="left">$aplicacao_link</td>						
					</tr>
EOF;
				}
			}
		}
		
		$menu.= <<< EOF
		<tr class="tr_cor_cinza">
			<td colspan=4 nowrap align="center"><i>---------- FIM -----------</i></td>
		</tr>
EOF;
	
		$arvore_menu = <<< EOF
			<tr class="subcabecalho_tr">
				<td colspan="4">
					MENUS
				</td>
			</tr>
			$menu
EOF;
		//$arvore_menu = $menu;
		}else{
			$arvore_menu = "";
	}
}


mysqli_close($link);


?>

<table class="box_relatorio" width="1024" border="0">
	<tr class="cabecalho_tr">
		<td width="5%"><img style="cursor:pointer;" onclick="window.location.href='?mainapp=cadastro&app=grupo'" src="imagens/botao_voltar.gif" title="Voltar" height="65%" /></td>
		<td nowrap><span class="cabecalho_tr">ITENS DO MENU - <?php echo $grupo_nome ?></span></td>
	</tr>
</table>
<br />

<div id="div_busca_resultado" style="display:block">
<table class="box_relatorio" border="1" align="center" width="300">
	<?php  echo $arvore_menu ?>
</table>
</div>

<div id="div_carregando" class="div_carregando_relatorio">
	<div align="center"><img src="imagens/loading.gif" border="0" /></div>
	<span class="span_carregando_relatorio" id="span_carregando_relatorio">????<br /> AGUARDE...</span>
</div>