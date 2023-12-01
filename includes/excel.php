<?php
ob_clean();
ob_start();
ini_set('memory_limit', '-1');

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}
/**
 * ver o arquivivo excel_exemplo para ver o funcionamento 
 */


$servidor	= trim($_SESSION['EXCEL']['SERVIDOR']);
$banco 		= trim($_SESSION['EXCEL']['BANCO']);
$sistema	= strtoupper(trim($_SESSION['EXCEL']['SISTEMA']));
$consulta 	= trim($_SESSION['EXCEL']['CONSULTA']);
$campos 	= trim($_SESSION['EXCEL']['CAMPOS']);
$label 		= trim($_SESSION['EXCEL']['CAMPOS_LABEL']);
$titulo 	= trim($_SESSION['EXCEL']['TITULO']);
$nome_arquivo   = trim($_SESSION['EXCEL']['NOME_ARQUIVO']);
$tipo_arquivo	= strtoupper(trim($_SESSION['EXCEL']['TIPO_ARQUIVO']));//CSV/EXCEL - Qualquer coisa diferente de CSV será excel



if(substr(strtoupper(trim($consulta)),0,6) != "SELECT"){
    die("FORMATO INCORRETO DA CONSULTA.");
}

$rand = rand(1,999999999);
$rand = substr(md5($rand),1,8);

if($tipo_arquivo == 'CSV'){
	$file_type = "text/csv";
	$extensao = "csv";
}else{
	$file_type = "application/vnd.ms-excel";
	$extensao = "xls";
}

// Estabelece o nome do arquivo que será exportado para o excel
if($nome_arquivo){
    $file_name= "{$nome_arquivo}.{$rand}.{$extensao}";
}else{
    $file_name= "relatorio_{$rand}.{$extensao}";
}

$conecta = RetornaConexaoMysql($servidor, $banco, true);

$vet_label 	= explode("|", $label);
$vet_campos = explode("|", $campos);

$Sql = $consulta;

$rs_excel = mysqli_query($conecta, $Sql) or die(mysqli_error($conecta));
$total = mysqli_num_rows($rs_excel);

$tds = "";
$grad = 0;
if (mysqli_num_rows($rs_excel)) {
	
	if($campos != "*"){
		while ($row_excel = mysqli_fetch_assoc($rs_excel)){
			$colspan = sizeof($vet_campos);
			$tds .= "<tr".(($grad % 2 == 0)? " bgcolor=\"#CCCCCC\" ": "").">";
			$grad++;
			foreach ($vet_campos as $key => $field_name) {
				$valor = $row_excel[$field_name];
				$nowrap = "nowrap";
				$align = "align=\"left\"";
				/*
				 * PARTE RETIRADA POR ATRAPALHAR FILTROS NO EXCEL, CAUSA FORMATAÇÃO ERRADA DE DADOS
				if(strlen($valor) > 10 && is_numeric($valor)){
					$valor = "&nbsp;" . $valor;
				}
				*/
				$tds.= "<td $nowrap $align>$valor</td>";
			}
			$tds .= "</tr>";
		}
	}else{
		while ($row_excel = mysqli_fetch_assoc($rs_excel)){
			$colspan = sizeof($row_excel);
			$tds .= "<tr".(($grad % 2 == 0)? " bgcolor=\"#CCCCCC\" ": "").">";
			$grad++;
			$tds_label = "<tr>";
			$nowrap = "nowrap";
			$align = "align=\"left\"";
			foreach ($row_excel as $key => $valor) {
				$tds_label.="<td $nowrap align=\"left\">$key</td>";
				if(strlen($valor) > 10  && is_numeric($valor)){
					$valor = "&nbsp;" . $valor;
				}
				$tds.= "<td $nowrap $align>$valor</td>";
			}
			$tds .= "</tr>";
			$tds_label.= "</tr>";
		}
	}
}

$tabela_excel.= "<table align=\"left\">";
switch($sistema){
	case 'SEGURO':
		$tabela_excel.= "<tr><td align=\"left\"><img src=\"../imagens/banner_seguro.jpg\" /></td></tr>";
		break;
	default:
		$tabela_excel.= "<tr><td align=\"left\"><img src=\"../imagens/banner_topo.jpg\" /></td></tr>";
		break;
}

$tabela_excel.= "<tr><td></td></tr>";
$tabela_excel.= "<tr><td></td></tr>";
$tabela_excel.= "<tr><td></td></tr>";
$tabela_excel.= "<tr><td></td></tr>";
$tabela_excel.= "</table>";
$tabela_excel.= "<table border=\"1\" align=\"center\">";
$tabela_excel.= "<tr><td colspan=$colspan align=\"left\"  bgcolor=\"#333333\" >     <b style=\"color:#FFFFFF\">$titulo</b></td></tr>";
if($campos != "*"){
	$tabela_excel.= "<tr>";
	foreach ($vet_campos as $key => $valor) {
		$width = "";
		$tabela_excel.= "<td align=\"center\" $width nowrap>{$vet_label[$key]}</td>";
	}
	$tabela_excel.= "</tr>";
}else{
	$tabela_excel.=$tds_label;
}

$tabela_excel.= $tds;
$tabela_excel.= "</table>";
//echo $Sql;exit;

header("Content-Type: $file_type");
header("Content-Disposition: attachment; filename=$file_name");
header("Pragma: no-cache");
header("Expires: 0");
	


echo "$tabela_excel</table>";



mysqli_close($conecta);

ob_end_flush();
ob_flush();
flush();

?>