<?php

/**
 * ver o arquivivo excel_exemplo para ver o funcionamento 
 */


$servidor	= $_SESSION['EXCEL']['SERVIDOR'];
$banco 		= $_SESSION['EXCEL']['BANCO'];
$consulta 	= $_SESSION['EXCEL']['CONSULTA'];
$campos 	= $_SESSION['EXCEL']['CAMPOS'];
$label 		= $_SESSION['EXCEL']['CAMPOS_LABEL'];
$titulo 	= $_SESSION['EXCEL']['TITULO'];


if(strtoupper(substr(trim($consulta),0,6)) != "SELECT" || strpos($consulta,";") !== FALSE){
	echo "FORMATO INCORRETO DA CONSULTA";EXIT;	
}

$rand = rand(1,999999999);
$rand = substr(md5($rand),1,8);


$file_type = "vnd.ms-excel";
$file_name= "relatorio_$rand.xls";

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
		while ($row_excel = mysqli_fetch_row($rs_excel)){
			$colspan = sizeof($vet_campos);
			$tds .= "<tr".(($grad % 2 == 0)? " bgcolor=\"#CCCCCC\" ": "").">";
			$grad++;
			foreach ($row_excel as $key => $valor) {
				$nowrap = "nowrap";
				$align = "align=\"left\"";
				if(strlen($valor) > 10 && is_numeric($valor)){
					$valor = "&nbsp;" . $valor;
				}
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

$tabela_excel.= "</table>";
$tabela_excel.= "<table border=\"1\" align=\"center\">";
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

header("Content-Type: application/$file_type");
header("Content-Disposition: attachment; filename=$file_name");
header("Pragma: no-cache");
header("Expires: 0");

echo "$tabela_excel";

$_SESSION['EXCEL'] = null;
mysqli_close($conecta);

?>