<?php

$funcao = $_POST['funcao_ajax'];



if (!empty($funcao)) {
	require "../../includes/banco.php";
	$conecta = RetornaConexaoMysql('local','gestao_sistemas');
}else {
	$conecta = RetornaConexaoMysql('local','gestao_sistemas');
}

//$conecta = RetornaConexaoMysql('local','modulo_co_adsl');

if(!empty($funcao)){
	call_user_func($funcao);	
}

	
function mostrar_areas_operacionais(){
	$area = $_POST['area'];
	$sql_area_op= "Select distinct(area_operacional) FROM tbl_cadastro_sistemas WHERE area_sistema='$area' ORDER BY area_operacional asc";
	//echo $sql_area_op; exit();
	$rs_area_op = mysqli_query($conecta, $sql_area_op);
	$cont = 0;
	$tab_area_op = "<table class='box_relatorio' align='center'>";
	while ($row_area_op = mysqli_fetch_array($rs_area_op)) {
		$area_op = $row_area_op['area_operacional'];
		if ($cont < 1){
			$tab_area_op .= "<tr>
								<td>
									$area_op<br />
									<div id='$area$area_op'></div>
								</td>
								<td><a onclick='javascript: gerar_ip(\"$area\", \"$area_op\", \"$area$area_op\");'><img src='../imagens/ico_pesq.png' height='18' width='21'/></a></td>";
			$cont++;
		}else{
			$tab_area_op .= 	"<td>
									$area_op<br />
									<div id='$area$area_op'></div>
								</td>
								<td><a onclick='javascript: gerar_ip(\"$area\", \"$area_op\", \"$area$area_op\");'><img src='../imagens/ico_pesq.png' height='18' width='21'/></a></td>
							</tr>";
			$cont = 0;
		}
	}
	$tab_area_op .= "</table>";
	echo $tab_area_op;
	echo "<div id='xip'></div>";
}

function mostrar_ip(){
	$area = $_POST['area'];
	$area_op = $_POST['area_operacional'];
	$sql_ip= "Select distinct(ip) FROM tbl_cadastro_sistemas WHERE area_sistema= '$area' AND area_operacional='$area_op' ORDER BY ip asc";
	$rs_ip = mysqli_query($conecta, $sql_ip);
	$cont = 0;
	$tab_ip = "<table class='box_relatorio' align='center'>";
	while ($row_ip = mysqli_fetch_array($rs_ip)) {
		$ip = $row_ip['ip'];
		if ($cont == 0) {
			$tab_ip .= "<tr>
								<td>$ip</td>";
			$cont++;
		}elseif ($cont < 3){
			$tab_ip .= 	"		<td>$ip</td>";
			$cont++;
		}elseif ($cont == 3){
			$tab_ip .= 	"		<td>$ip</td>
						</tr>";
			$cont++;
		}
	}
	$tab_ip .= "</table>";
	echo $tab_ip;
}



	?>