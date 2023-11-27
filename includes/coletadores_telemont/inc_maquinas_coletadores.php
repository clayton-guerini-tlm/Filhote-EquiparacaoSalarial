<?php
	include_once "includes/funcoes.php";
	$conecta =  RetornaConexaoMysql('local', 'modulo_coletadores');
	$sql_sistemas = "Select * from tbl_cadastro_sistemas order by area_sistema asc, area_operacional asc";
	
	$rs_sistemas = mysqli_query($conecta, $sql_sistemas);
	
	$trs = "";
	while ($row_sistemas = mysqli_fetch_array($rs_sistemas)){
		if($class == 'alterar_cor_cinza'){
			$class = 'alterar_cor_branco';
		}else{
			$class = 'alterar_cor_cinza';
		}
	    $icone = "<img src=\"imagens/almoxarifado_dados/recibo.png\" width=\"20px\" height=\"20px\" style=\"cursor: pointer;\" onclick=\"javascript: mostra_observacao('{$row_sistemas['id']}');\" >";
	    $editar = "<a href='./principal.php?mainapp=coletadores_telemont&app=editar&coletador={$row_sistemas['id']}'><img src=\"imagens/editar.gif\" style=\"cursor: pointer; border: 0;\" >";
		$excluir = "<img src=\"imagens/del2.gif\" style=\"cursor: pointer;\" onclick=\"javascript: exclusaoColetador('{$row_sistemas['id']}');\">";
	        
		$trs .= "<tr class=\"$class\" >";
		$trs .= "<td>{$row_sistemas['area_sistema']}</td>";
		$trs .= "<td>{$row_sistemas['area_operacional']}</td>";
		$trs .= "<td>{$row_sistemas['ip']}</td>";
		$trs .= "<td>{$row_sistemas['login']}</td>";
		$trs .= "<td>{$row_sistemas['senha']}</td>";
		$trs .= "<td>$icone</td>";
        $trs .= "<td>$editar</td>";
        $trs .= "<td>$excluir</td>";
		$trs .= "</tr>";
		
	}
?>
<link href="../css/index.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
	function mostra_observacao(id)
	{
		var width = 700;
		var height = 500;
		var url = 'includes/coletadores_telemont/inc_observacao_coletador.php?id=' + id;
		var title = 'OBSERVAÇÃO DO COLETADOR';
		var root = '';
		var view_button_close = 'sim'; 
		var action_on_close = '';
		var function_action_on_close = '';
		
		var janela = new Janela(width, height, url, title, root, view_button_close, action_on_close, function_action_on_close);
	}
    function exclusaoColetador(id){
	  	var campos = "funcao_ajax=excluir_coletador&excluir=" + id;
	  	ReqAjax(Pronto, campos);
	  	
	  	function Pronto(obj){
	  	    alert(obj.responseText); 
	  	    window.location.reload();
		}
   	
	}
</script>

<table class="box_relatorio" width="100%" >
	<tr class="cabecalho_tr" >
		<td><span class="cabecalho_tr" >MAQUINAS COLETADORES</span></td>
	</tr>
</table>
<br />
<table class="box_relatorio" width="100%" >
	<tr class="subcabecalho_tr" >
		<td>&Aacute;REA</td>
		<td>OPERA&Ccedil;&Atilde;O</td>
		<td>IP</td>
		<td>LOGIN</td>
		<td>SENHA</td>
        <td>OBSERVAÇÕES</td>
        <td>EDITAR</td>
        <td>EXCLUIR</td>
	</tr>
	<?php echo $trs; ?>
</table>