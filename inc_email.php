<?php
/**
      * @author Bruno Macedo Tertuliano
      * @access 09/12/2010
      * @copyright (c 2010)
*/

//set_time_limit (1000);
set_time_limit(0);
error_reporting(E_ALL & ~E_NOTICE);

include('includes/funcoes.php');
include('funcoes.php');


$inicio_web = date("d/m/y G:i:s");

//$conecta = RetornaConexaoMysql('2950','modulo_ponto');
$conecta = RetornaConexaoMysql('local','modulo_ponto');
$Sql		= "SELECT * 
				FROM tbl_email ";
$rs_email 	= @mysqli_query($conecta, $Sql) or die(mysqli_error($conecta));
$row_email 	= @mysqli_fetch_assoc($rs_email);


// 0 = Manhã
// 1 = Noite
if ($row_email['e_flag_turno'] == 1) { // Se ja tiver rodado a noite
	$sHors 		= $row_email['e_seg_turno'];
	$sMins 		= "00";
	$sSecs 		= "00";
	
	$Sql		= "UPDATE tbl_email 
					SET e_flag_turno = 0, e_seg_turno = 10";
	@mysqli_query($conecta, $Sql) or die(mysqli_error($conecta));
	//$row_email 	= @mysqli_fetch_assoc($rs_email);
}else {// Se ja tiver rodado a manha
	$sHors 		= $row_email['e_seg_turno'];
	$sMins 		= "00";
	$sSecs 		= "00";
	
	$Sql		= "UPDATE tbl_email 
					SET e_flag_turno = 1, e_seg_turno = 14";
	@mysqli_query($conecta, $Sql) or die(mysqli_error($conecta));
	//$row_email 	= @mysqli_fetch_assoc($rs_email);
}
mysqli_close($conecta);

?>
<html>
<head>
<style>
.cabecalho{
	font-size: 25pt;
}
</style>

<link  href="<?php echo $caminho_raiz; ?>css/index.css" rel="stylesheet" type="text/css" />

 <script language="JavaScript" type="text/javascript" >
	var sHors = <?php echo $sHors;?>;
	var sMins = <?php echo $sMins;?>;
	var sSecs = <?php echo $sSecs;?>;

	function getSecs(){
		
		sSecs--;
		
		if(sSecs<0){
			sSecs = 59;
			sMins--;
			if(sMins<=9)
				sMins = "0" + sMins;
		}
		
		if(sMins=="0-1"){
			sMins=59;
			sHors--;
			if(sHors<=9)
				sHors = "0" + sHors;
		}
		
		if(sSecs<=9)
			sSecs="0"+sSecs;
			
		if(sHors=="0-1"){
			sHors="00";
			sMins="00";
			sSecs="00";
			clock1.innerHTML = sHors + "<font style=\"color:#FFFFFF;font-weight:bold;font-size: 16px;\">:</font>"+sMins+"<font style=\"color:#FFFFFF;font-weight:bold;font-size: 16px;\">:</font>"+sSecs;
			//alert('Recarregando a página.');
			window.location.reload(true);
		}else{
			clock1.innerHTML = sHors+"<font style=\"color:#FFFFFF;font-weight:bold;font-size: 16px;\">:</font>"+sMins+"<font style=\"color:#FFFFFF;font-weight:bold;font-size: 16px;\">:</font>"+sSecs;
			setTimeout('getSecs()',1000);
		}
	}

 </script>
</head>
<?php
$conecta = RetornaConexaoMysql('2950','modulo_ponto');

$filial 						= "";
// Armazena a contagem por filial dos encarregados
$cont_go_e 						= 0;
$cont_ac_e 						= 0;
$cont_to_e						= 0;
$cont_ro_e 						= 0;
$cont_df_e 						= 0;
$cont_ms_e 						= 0;
$cont_mt_e 						= 0;
// Armazena a contagem por filial dos supervisores
$cont_ac_s 						= 0;
$cont_go_s 						= 0;
$cont_ms_s 						= 0;
$cont_mt_s 						= 0;
$cont_to_s 						= 0;
$cont_ro_s 						= 0;
$cont_df_s 						= 0;
// Armazena a contagem por filial dos não enviador por supervisores
$cont_nao_enviador_s_ac 		= 0;
$cont_nao_enviador_s_go 		= 0;
$cont_nao_enviador_s_ms 		= 0;
$cont_nao_enviador_s_mt 		= 0;
$cont_nao_enviador_s_to 		= 0;
$cont_nao_enviador_s_ro		 	= 0;
$cont_nao_enviador_s_df			= 0;
// Armazena a contagem por filial dos não enviador por encarregados
$cont_nao_enviador_e_ac 		= 0;
$cont_nao_enviador_e_go 		= 0;
$cont_nao_enviador_e_ms 		= 0;
$cont_nao_enviador_e_mt		 	= 0;
$cont_nao_enviador_e_to 		= 0;
$cont_nao_enviador_e_ro 		= 0;
$cont_nao_enviador_e_df 		= 0;
// armazena a contagem com o total por filial de encarregado e supervisor
$total_enviados_ac 				= 0;
$total_enviados_go 				= 0;
$total_enviados_ms 				= 0;
$total_enviados_mt 				= 0;
$total_enviados_to 				= 0;
$total_enviados_ro 				= 0;
$total_enviados_df 				= 0;

$total_geral_nao_enviados		= 0;
$total_encarregado_nao_enviados = 0;
$total_supervisor_nao_enviados	= 0;
$total_encarregado				= 0;
$total_supervisor				= 0;
$total_nao_enviados_ac 			= 0;
$total_geral		 			= 0;

$emailSuperiorEncarregado		= '';
$emailSuperiorSupervisor		= '';

$tipo_busca						= 'dia';
$data 							= date("Y/m/d");

$Sql 								= "select pe.*, rmf.fun_filial from tbl_pessoal_encarregado pe
										inner join sigo_integrado.tbl_rm_funcionario rmf
											on (pe.enc_chapa = rmf.fun_chapa)
										order by
											rmf.fun_filial";
//echo $Sql."<br>";
$rs_pessoal_encarregado 			= @mysqli_query($conecta, $Sql) or die(mysqli_error($conecta));
//$tot = mysqli_num_rows($rs_pessoal_encarregado);
//die('Entrou: ' . $tot);
while ($row_pessoal_encarregado 	= @mysqli_fetch_assoc($rs_pessoal_encarregado)) {
	
	$chapa_superior 				= $row_pessoal_encarregado['enc_chapa'];
	$nome_encarregado 				= $row_pessoal_encarregado['enc_nome'];
	$emailSuperiorEncarregado 		= $row_pessoal_encarregado['enc_email'];
	$uf								= $row_pessoal_encarregado['fun_filial'];
	//echo $uf."<br>";
	if ($row_pessoal_encarregado['enc_email']){
		
		$Sql = "SELECT a1.*, t1.* FROM modulo_ponto.tbl_abono a1 
		INNER JOIN modulo_ponto.tbl_abono_tipo t1 ON a1.abt_id = t1.abt_id 
		INNER JOIN modulo_ponto.tbl_funcionario_ponto p1 ON a1.fun_chapa = p1.fun_chapa 
		WHERE p1.fpo_supervisor = '$chapa_superior' ";
		$rs_abono = @mysqli_query($conecta, $Sql) or die(mysqli_error($conecta));
		//$tot2 = mysqli_num_rows($rs_abono);
		//die('Entrou: ' . $tot2);
		while ($row_abono = @mysqli_fetch_assoc($rs_abono)) {
			$vetAbono[$row_abono['abo_id']]['chapa'] = $row_abono['fun_chapa'];
			$vetAbono[$row_abono['abo_id']]['inicio'] = str_replace("-","",$row_abono['abo_inicio']);
			$vetAbono[$row_abono['abo_id']]['fim'] = str_replace("-","",$row_abono['abo_fim']);
			$vetAbono[$row_abono['abo_id']]['resumo'] = $row_abono['abt_resumo'];
			$vetAbono[$row_abono['abo_id']]['id'] = $row_abono['abt_id'];
			$vetAbono[$row_abono['abo_id']]['turno'] = $row_abono['abo_turno'];
		}
		
		$vetFuncionarios = array();
		
		$trs = "";
	
		$Sql = "SELECT p1.fun_chapa, f1.fun_nome as nome_funcionario, f2.fun_nome as nome_supervisor, c1.car_descricao, sf.sit_descricao, f1.fun_filial, 
					p1.fpo_email_superior, p1.fpo_registro, f1.fun_filial,
					ep.exp_descricao AS horario_rm, p1.fpo_horario_trabalho AS horario_realizado, p1.fpo_senha_inicio, p1.fpo_senha_final
				FROM modulo_ponto.tbl_funcionario_ponto p1
					INNER JOIN sigo_integrado.tbl_rm_funcionario f1 
						ON f1.fun_chapa = p1.fun_chapa
					INNER JOIN sigo_integrado.tbl_rm_funcionario f2 
						ON f2.fun_chapa = p1.fpo_supervisor
					INNER JOIN sigo_integrado.tbl_rm_cargo c1 
						ON f1.car_id = c1.car_id 
					INNER JOIN sigo_integrado.tbl_rm_situacao sf 
						ON sf.sit_id = f1.sit_id
					INNER JOIN sigo_integrado.tbl_rm_expediente_padrao ep
						ON f1.fun_codhorario = ep.exp_codigo
				WHERE 
					(p1.fpo_supervisor = '$chapa_superior' OR p1.fun_chapa = '$chapa_superior')
		 			AND f1.fun_filial = '$uf'
		 			AND f1.sit_id = 'A'
				ORDER BY 
					nome_funcionario,nome_supervisor";
		//echo "<pre>"; echo $Sql."<br>"; echo "</pre>"; exit;
		$rs_relatorio = @mysqli_query($conecta, $Sql) or die(mysqli_error($conecta));
		//$tot3 = mysqli_num_rows($rs_relatorio);
		//die('Entrou: ' . $tot3);
		while ($row_relatorio = @mysqli_fetch_assoc($rs_relatorio)) {
			
			$vetFuncionarios[$row_relatorio['fun_chapa']]['nome_funcionario']	= $row_relatorio['nome_funcionario'];
			$vetFuncionarios[$row_relatorio['fun_chapa']]['status_rm']			= $row_relatorio['sit_descricao'];
			$vetFuncionarios[$row_relatorio['fun_chapa']]['nome_supervisor'] 	= $row_relatorio['nome_supervisor'];	
			$vetFuncionarios[$row_relatorio['fun_chapa']]['cargo'] 				= $row_relatorio['car_descricao'];
			$vetFuncionarios[$row_relatorio['fun_chapa']]['sit_descricao'] 		= $row_relatorio['sit_descricao'];
			$vetFuncionarios[$row_relatorio['fun_chapa']]['entrada']			= "-";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['saida'] 				= "-";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['atz'] 				= "00:00";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['atc'] 				= "00:00";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['hea'] 				= "00:00";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['hed'] 				= "00:00";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['fun_filial']			= $row_relatorio['fun_filial'];
			$vetFuncionarios[$row_relatorio['fun_chapa']]['fpo_registro'] 		= $row_relatorio['fpo_registro'];
			$vetFuncionarios[$row_relatorio['fun_chapa']]['horario_rm'] 		= $row_relatorio['horario_rm'];
			$vetFuncionarios[$row_relatorio['fun_chapa']]['horario_realizado'] 	= $row_relatorio['horario_realizado'];
			$vetFuncionarios[$row_relatorio['fun_chapa']]['fpo_senha_inicio'] 	= $row_relatorio['fpo_senha_inicio'];
			$vetFuncionarios[$row_relatorio['fun_chapa']]['fpo_senha_final'] 	= $row_relatorio['fpo_senha_final'];
		}
		$Sql = "SELECT b1.pba_data, b1.pba_chapa, b1.pba_entrada, b1.pba_saida, b1.pba_entrada, b1.pba_je_entrada, 
					b1.pba_je_saida, WEEKDAY(b1.pba_data) AS diaSemana, b1.pba_atz, b1.pba_atc, b1.pba_hea, b1.pba_hed, 
					f1.fun_nome, f1.fun_filial, p1.fpo_email_superior, p1.fpo_registro
				FROM modulo_ponto.tbl_ponto_batido b1
					INNER JOIN modulo_ponto.tbl_funcionario_ponto p1 
						ON p1.fun_chapa = b1.pba_chapa 
					INNER JOIN sigo_integrado.tbl_rm_funcionario f1 
						ON f1.fun_chapa = b1.pba_chapa
					INNER JOIN sigo_integrado.tbl_rm_funcionario f2 
						ON f2.fun_chapa = p1.fpo_supervisor
					INNER JOIN sigo_integrado.tbl_rm_situacao sf 
						ON sf.sit_id = f1.sit_id
					WHERE pba_data='$data'
						AND (p1.fpo_supervisor = '$chapa_superior' OR p1.fun_chapa = '$chapa_superior')
			 			AND f1.fun_filial = '$uf'
			 			AND f1.sit_id = 'A'
					ORDER BY pba_chapa, pba_data ";
		//echo $Sql."<br>";exit;
		$rs_relatorio = @mysqli_query($conecta, $Sql) or die(mysqli_error($conecta));
		//$tot4 = mysqli_num_rows($rs_relatorio);
		//die('Entrou: ' . $tot4);
		while ($row_relatorio = @mysqli_fetch_assoc($rs_relatorio)) {
			
			$abono_id = LocalizaAbono($vetAbono,ConvertDataHoraMysql($row_relatorio['pba_data'],'normal'),$row_relatorio['pba_chapa']);
			if ($abono_id != "naoachou"){
				if($vetAbono[$abono_id]['turno'] == "ENTRADA"){
					$row_relatorio['pba_atz'] = "00:00";
					$row_relatorio['pba_hea'] = "00:00";
				}else if ($vetAbono[$abono_id]['turno'] == "SAIDA"){
					$row_relatorio['pba_atc'] = "00:00";
					$row_relatorio['pba_hed'] = "00:00";
				}else{
					$row_relatorio['pba_atz'] = "00:00";
					$row_relatorio['pba_hea'] = "00:00";
					$row_relatorio['pba_atc'] = "00:00";
					$row_relatorio['pba_hed'] = "00:00";
				}
			}
			if ($row_relatorio['diaSemana'] == 5 or $row_relatorio['diaSemana'] == 6) {
				$vetFuncionarios[$row_relatorio['pba_chapa']]['data'] 			= $row_relatorio['pba_data'];
				$vetFuncionarios[$row_relatorio['pba_chapa']]['entrada'] 		= $row_relatorio['pba_je_entrada'];
				$vetFuncionarios[$row_relatorio['pba_chapa']]['saida']			= $row_relatorio['pba_je_saida'];
			}else {
				$vetFuncionarios[$row_relatorio['pba_chapa']]['data'] 			= $row_relatorio['pba_data'];
				$vetFuncionarios[$row_relatorio['pba_chapa']]['entrada'] 		= $row_relatorio['pba_entrada'];
				$vetFuncionarios[$row_relatorio['pba_chapa']]['saida']			= $row_relatorio['pba_saida'];
			}
			$vetFuncionarios[$row_relatorio['pba_chapa']]['atz'] 				= RetornaMinutoHora(RetornaMinutoHora($vetFuncionarios[$row_relatorio['pba_chapa']]['atz']) + RetornaMinutoHora($row_relatorio['pba_atz']), "hora");
			$vetFuncionarios[$row_relatorio['pba_chapa']]['atc'] 				= RetornaMinutoHora(RetornaMinutoHora($vetFuncionarios[$row_relatorio['pba_chapa']]['atc']) + RetornaMinutoHora($row_relatorio['pba_atc']), "hora");
		
			$vetFuncionarios[$row_relatorio['pba_chapa']]['hea'] 				= RetornaMinutoHora(RetornaMinutoHora($vetFuncionarios[$row_relatorio['pba_chapa']]['hea']) + RetornaMinutoHora($row_relatorio['pba_hea']), "hora");
			$vetFuncionarios[$row_relatorio['pba_chapa']]['hed'] 				= RetornaMinutoHora(RetornaMinutoHora($vetFuncionarios[$row_relatorio['pba_chapa']]['hed']) + RetornaMinutoHora($row_relatorio['pba_hed']), "hora");
	
		}
			
		$trs 			= "";
		$total_atz 		= 0;
		$total_atc 		= 0;
		$total_he_geral = 0;
		$total_email 	= 0;
		$estilo 		= "";
		
		if ($vetFuncionarios) {
			foreach ($vetFuncionarios as $key => $registro) {
				$estilo == "tr_cor_cinza" ? $estilo = "tr_cor_branco" : $estilo = "tr_cor_cinza";	
				
				//if ($registro['entrada'] <> '00:00:00' && $registro['saida'] <> '00:00:00'){
					$total_he = RetornaMinutoHora(RetornaMinutoHora($registro['hea']) + RetornaMinutoHora($registro['hed']),"hora");
					$total_debito = RetornaMinutoHora($registro['hea']) + RetornaMinutoHora($registro['hed']) - RetornaMinutoHora($registro['atz']) - RetornaMinutoHora($registro['atc']);
				//}
				
				$registro['entrada'] 	= ConverteHoraMysql($registro['entrada']);
				$registro['saida'] 		= ConverteHoraMysql($registro['saida']);
				
				$total_he_geral+= RetornaMinutoHora($registro['hea']) + RetornaMinutoHora($registro['hed']);
				$total_atc+= RetornaMinutoHora($registro['atc']);
				$total_atz+= RetornaMinutoHora($registro['atz']);
				
				if($total_debito < 0) { 
			   		$total_debito = "(" . RetornaMinutoHora($total_debito*(-1),"hora") . ")";    		
			  	}else{ 
			  		$total_debito = RetornaMinutoHora($total_debito,"hora");   		
			  	}
				
			  	empty($registro['entrada']) ? $registro['entrada'] = "-" : false;
				empty($registro['saida']) ? $registro['saida'] = "-" : false;
				empty($registro['atz']) || $registro['atz'] == "00:00" ? $registro['atz'] = "-" : false;
				empty($registro['atc']) || $registro['atc'] == "00:00" ? $registro['atc'] = "-" : false;
				empty($total_he) 		|| $total_he 		== "00:00" ? $total_he 		  = "-" : false;
				empty($total_debito) 	|| $total_debito 	== "00:00" ? $total_debito 	  = "-" : false;
				
				if ($registro['fun_filial'] == 'DF' or $registro['fun_filial'] == 'GO' or $registro['fun_filial'] == 'TO') {
					$horario = $registro['horario_rm'];
				}else {
					$horario = $registro['horario_realizado'];
					// Quebra o vetor de hora para separar e validar
					$vetorHoraTrab = explode(";", $horario);
					
					// Conta o tamanho do valor da hora
					$hora1 	= strlen(trim($vetorHoraTrab[0]));
					$hora2 	= strlen(trim($vetorHoraTrab[1]));
					$hora3 	= strlen(trim($vetorHoraTrab[2]));
					$hora4 	= strlen(trim($vetorHoraTrab[3]));
					$hora5 	= strlen(trim($vetorHoraTrab[4]));
					$hora6 	= strlen(trim($vetorHoraTrab[5]));
					$hora7 	= strlen(trim($vetorHoraTrab[6]));
					$hora8 	= strlen(trim($vetorHoraTrab[7]));

					// Recebe as horas
					$hora_1	= trim($vetorHoraTrab[0]);
					$hora_2	= trim($vetorHoraTrab[1]);
					$hora_3	= trim($vetorHoraTrab[2]);
					$hora_4	= trim($vetorHoraTrab[3]);
					$hora_5	= trim($vetorHoraTrab[4]);
					$hora_6	= trim($vetorHoraTrab[5]);
					$hora_7	= trim($vetorHoraTrab[6]);
					$hora_8	= trim($vetorHoraTrab[7]);
			
					if (strlen($hora_1) == 4){
						$hora_1 = "0".$hora_1;
						$hora1 = $hora1+1;
					}
					if (strlen($hora_2) == 4){
						$hora_2 = "0".$hora_2;
						$hora2 = $hora2+1;
					}
					if (strlen($hora_3) == 4){
						$hora_3 = "0".$hora_3;
						$hora3 = $hora3+1;
					}
					if (strlen($hora_4) == 4){
						$hora_4 = "0".$hora_4;
						$hora4 = $hora4+1;
					}
					if (strlen($hora_5) == 4){
						$hora_5 = "0".$hora_5;
						$hora5 = $hora5+1;
					}
					if (strlen($hora_6) == 4){
						$hora_6 = "0".$hora_6;
						$hora6 = $hora6+1;
					}
					if (strlen($hora_7) == 4){
						$hora_7 = "0".$hora_7;
						$hora7 = $hora7+1;
					}
					if (strlen($hora_8) == 4){
						$hora_8 = "0".$hora_8;
						$hora8 = $hora8+1;
					}
					
					if ($hora_8) {
						// Recebe as horas e coloca no padrao para aparecer como mensagem
						$horaTab = $hora_1."/".$hora_2."-".$hora_3."/".$hora_4." (SAB ".$hora_5."/".$hora_6."-".$hora_7."/".$hora_8.")";
					}else if (!$hora_5 && !$hora_6 && !$hora_7 && !$hora_8) {
						$horaTab = $hora_1."/".$hora_2."-".$hora_3."/".$hora_4;
					}elseif (!$hora_8) {
						$horaTab = $hora_1.";".$hora_2.";".$hora_3.";".$hora_4." (SAB ".$hora_5."/".$hora_6.")";
					}else {
						$horaTab = '-';
					}
					
					$horario = $horaTab;
					
				}
				
				if (!$horario or $horario == '/-/') {
					$horario = '-';
				}
								
				if ($tipo_busca == "dia"){
					$valor_entradasaida = <<< EOF
						<td>{$registro['entrada']}</td>		
						<td>{$registro['saida']}</td>
EOF;
				}
				
				
				if ($key == $chapa_superior){
					$tr_tmp= <<< EOF
						<tr class="tr_cor_branco">
							<td>$key</td>
							<td>{$registro['fpo_registro']}</td>
							<td align="left">{$registro['nome_funcionario']}</td>
							<td>{$registro['cargo']}</td>
							<td align="left">{$registro['nome_supervisor']}</td>
							<td align="center">{$registro['status_rm']}</td>
							<td align="center">{$registro['fpo_senha_inicio']}</td>
							<td align="center">{$registro['fpo_senha_final']}</td>
							$valor_entradasaida
							<td>{$registro['atz']}</td>		
							<td>{$registro['atc']}</td>
							<td>$total_he</td>
							<td>$total_debito</td>
							<td>$horario</td>
						</tr>
EOF;
					$trs = $tr_tmp . $trs;
				}else{
					$trs.= <<< EOF
						<tr class="$estilo">
							<td>$key</td>
							<td>{$registro['fpo_registro']}</td>
							<td align="left">{$registro['nome_funcionario']}</td>
							<td align="left">{$registro['cargo']}</td>
							<td align="left">{$registro['nome_supervisor']}</td>
							<td align="center">{$registro['status_rm']}</td>
							<td align="center">{$registro['fpo_senha_inicio']}</td>
							<td align="center">{$registro['fpo_senha_final']}</td>
							$valor_entradasaida
							<td>{$registro['atz']}</td>		
							<td>{$registro['atc']}</td>
							<td>$total_he</td>
							<td>$total_debito</td>
							<td>$horario</td>
						</tr>
EOF;
				}
				$total_email ++;
			}
		}
		
		$saldo_geral = $total_he_geral - $total_atc - $total_atz;
		if($saldo_geral <0) { 
			$saldo_geral = "(" . RetornaMinutoHora($saldo_geral*(-1),"hora") . ")";    		
		}else{ 
			$saldo_geral = RetornaMinutoHora($saldo_geral,"hora");   		
		}
		
		$total_he_geral = RetornaMinutoHora($total_he_geral,"hora");
		$total_atc = RetornaMinutoHora($total_atc,"hora");
		$total_atz = RetornaMinutoHora($total_atz,"hora");
		
		if ($tipo_busca == "dia"){
			$campo_entradasaida = "
			<td>ENTRADA</td>
			<td>SAIDA</td>";
			$colspan_somatorio = 10;
		}else{
			$colspan_somatorio = 8;
		}
		
		$table = <<< EOF
		<style>
		.subcabecalho_tr {
			/* sub cabecalho da tabela */	
			background-color:#efefef;
			text-align:center;
			font-size:11px;
		}
		
		.box_relatorio{
			border:solid;
			border-width:1px;
			border-collapse: collapse;
			background-color:#FFFFFF;
		}
		
		.tr_cor_cinza {
			/* qualquer TR q tenha esta class ira alterar de cor com o passar do mouse */	
			background-color:#fefefe;
			font-size:11px;
			text-align:center;
		}
	
		.tr_cor_branco {
			/* qualquer TR q tenha esta class ira alterar de cor com o passar do mouse */	
			background-color:#EEEFFF;
			font-size:11px;
			text-align:center;
		}
		</style>
		
		<table class="box_relatorio" align="center" width="100%">
			<tr class="subcabecalho_tr">
				<td>CHAPA</td>
				<td>REGISTRO OI</td>
				<td>NOME</td>
				<td>CARGO</td>
				<td>SUPERIOR</td>
				<td>STATUS RM</td>
				<td colspan="2">SENHA INICIO - FINAL</td>
				$campo_entradasaida
				<td>ATRASO</td>
				<td>ANTEC.</td>
				<td>H.EXTRA</td>
				<td>SALDO</td>
				<td>HORÁRIO RM</td>
			</tr>
			$trs
			<tr class="subcabecalho_tr">
				<td colspan="$colspan_somatorio" align="right">SOMAT&Oacute;RIO</td>
				<td>$total_atz</td>
				<td>$total_atc</td>
				<td>$total_he_geral</td>
				<td>$saldo_geral</td>
				<td></td>
			</tr>
		</table>
EOF;
	
		$cabecalho = 'Prezado, '.$nome_encarregado.'<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Segue acompanhamento da jornada de trabalho dos empregados que estão sob sua supervisão';
		
		$rodape = '<br><br><b>Att<br>E-mail automatico do SIGO. Favor nao responder este e-mail!<b>';
		
		$texto = "$cabecalho<br><br>".$table.$rodape;
		
		$assunto = 'STATUS DIÁRIO - '.$row_pessoal_encarregado['fun_filial'];
		
		if ($emailSuperiorEncarregado){
			echo '<span style="display:none;">';
				$emailEnviado = EnviarEmail($row_pessoal_encarregado['enc_nome'],$emailSuperiorEncarregado, $assunto, $texto,"","sigo@telemont.com.br","SIGO TELEMONT","bruno.tertuliano@telemont.com.br");
			echo '</span>';
			
			if ($emailEnviado) {
				if ($row_pessoal_encarregado['fun_filial'] == 'AC')
					$cont_ac_e ++;
				
				if ($row_pessoal_encarregado['fun_filial'] == 'GO')
					$cont_go_e ++;
				
				if ($row_pessoal_encarregado['fun_filial'] == 'TO')
					$cont_to_e ++;
				
				if ($row_pessoal_encarregado['fun_filial'] == 'RO')
					$cont_ro_e ++;
				
				if ($row_pessoal_encarregado['fun_filial'] == 'DF')
					$cont_df_e ++;
				
				if ($row_pessoal_encarregado['fun_filial'] == 'MS')
					$cont_ms_e ++;
				
				if ($row_pessoal_encarregado['fun_filial'] == 'MT')
					$cont_mt_e ++;
			}else {
				if ($row_pessoal_encarregado['fun_filial'] == 'AC')
					$cont_nao_enviador_e_ac ++;
				
				if ($row_pessoal_encarregado['fun_filial'] == 'GO')
					$cont_nao_enviador_e_go ++;
				
				if ($row_pessoal_encarregado['fun_filial'] == 'TO')
					$cont_nao_enviador_e_to ++;
				
				if ($row_pessoal_encarregado['fun_filial'] == 'RO')
					$cont_nao_enviador_e_ro ++;
				
				if ($row_pessoal_encarregado['fun_filial'] == 'DF')
					$cont_nao_enviador_e_df ++;
				
				if ($row_pessoal_encarregado['fun_filial'] == 'MS')
					$cont_nao_enviador_e_ms ++;
				
				if ($row_pessoal_encarregado['fun_filial'] == 'MT')
					$cont_nao_enviador_e_mt ++;
			}
		}
	} else {
		if ($row_pessoal_encarregado['fun_filial'] == 'AC')
			$cont_nao_enviador_e_ac ++;
		
		if ($row_pessoal_encarregado['fun_filial'] == 'GO')
			$cont_nao_enviador_e_go ++;
		
		if ($row_pessoal_encarregado['fun_filial'] == 'TO')
			$cont_nao_enviador_e_to ++;
		
		if ($row_pessoal_encarregado['fun_filial'] == 'RO')
			$cont_nao_enviador_e_ro ++;
		
		if ($row_pessoal_encarregado['fun_filial'] == 'DF')
			$cont_nao_enviador_e_df ++;
		
		if ($row_pessoal_encarregado['fun_filial'] == 'MS')
			$cont_nao_enviador_e_ms ++;
		
		if ($row_pessoal_encarregado['fun_filial'] == 'MT')
			$cont_nao_enviador_e_mt ++;
	}
}

die('Não entrou.');

$Sql 							= "select ps.*, rmf.fun_filial from tbl_pessoal_supervisor ps
										inner join sigo_integrado.tbl_rm_funcionario rmf
											on (ps.sup_chapa = rmf.fun_chapa)
										order by
											rmf.fun_filial";
$rs_pessoal_supervisor 			= @mysqli_query($conecta, $Sql) or die(mysqli_error($conecta));
/*$tot5 = mysqli_num_rows($rs_pessoal_supervisor);
die('Entrou: ' . $tot5);*/

//echo "<br>";

while ($row_pessoal_supervisor 	= @mysqli_fetch_assoc($rs_pessoal_supervisor)) {
	
	//echo " - ".$row_configuracao['enc_nome']."<br>";
	$chapa_superior 			= $row_pessoal_supervisor['sup_chapa'];
	$nome_supervisor 			= $row_pessoal_supervisor['sup_nome'];
	$emailSuperiorSupervisor	= $row_pessoal_supervisor['sup_email'];
	$uf 						= $row_pessoal_supervisor['fun_filial'];
	//echo $uf."<br>";
	//$chapa_superior = '013396';

	if ($row_pessoal_supervisor['sup_email']) {
	
		$Sql = "SELECT a1.*, t1.* FROM modulo_ponto.tbl_abono a1 
		INNER JOIN modulo_ponto.tbl_abono_tipo t1 ON a1.abt_id = t1.abt_id 
		INNER JOIN modulo_ponto.tbl_funcionario_ponto p1 ON a1.fun_chapa = p1.fun_chapa 
		WHERE p1.fpo_supervisor = '$chapa_superior'";
		// abo_inicio >= '$ano_anterior-$mes_anterior-21' AND abo_fim<= '$ano-$mes-20'
		$rs_abono = @mysqli_query($conecta, $Sql) or die(mysqli_error($conecta));
		while ($row_abono = @mysqli_fetch_assoc($rs_abono)) {
			$vetAbono[$row_abono['abo_id']]['chapa'] = $row_abono['fun_chapa'];
			$vetAbono[$row_abono['abo_id']]['inicio'] = str_replace("-","",$row_abono['abo_inicio']);
			$vetAbono[$row_abono['abo_id']]['fim'] = str_replace("-","",$row_abono['abo_fim']);
			$vetAbono[$row_abono['abo_id']]['resumo'] = $row_abono['abt_resumo'];
			$vetAbono[$row_abono['abo_id']]['id'] = $row_abono['abt_id'];
			$vetAbono[$row_abono['abo_id']]['turno'] = $row_abono['abo_turno'];
		}
		
		$vetFuncionarios = array();
		
		$trs = "";
	
		$Sql = "SELECT p1.fun_chapa, f1.fun_nome as nome_funcionario, f2.fun_nome as nome_supervisor, c1.car_descricao, sf.sit_descricao, 
					p1.fpo_email_superior, f1.fun_filial, ep.exp_descricao AS horario_rm, p1.fpo_horario_trabalho AS horario_realizado, p1.fpo_senha_inicio, p1.fpo_senha_final
				FROM modulo_ponto.tbl_funcionario_ponto p1
					INNER JOIN sigo_integrado.tbl_rm_funcionario f1 
						ON f1.fun_chapa = p1.fun_chapa
					INNER JOIN sigo_integrado.tbl_rm_funcionario f2 
						ON f2.fun_chapa = p1.fpo_supervisor
					INNER JOIN sigo_integrado.tbl_rm_cargo c1 
						ON f1.car_id = c1.car_id 
					INNER JOIN sigo_integrado.tbl_rm_situacao sf 
						ON sf.sit_id = f1.sit_id
					INNER JOIN sigo_integrado.tbl_rm_expediente_padrao ep
						ON f1.fun_codhorario = ep.exp_codigo
				WHERE 
					(p1.fpo_supervisor = '$chapa_superior' OR p1.fun_chapa = '$chapa_superior')
		 			AND f1.fun_filial LIKE '%$uf%'
		 			AND f1.sit_id = 'A'
				ORDER BY 
					nome_funcionario,nome_supervisor ";
		$rs_relatorio = @mysqli_query($conecta, $Sql) or die(mysqli_error($conecta));
/*		$tot6 = mysqli_num_rows($rs_relatorio);
		die('Entrou: ' . $tot6);*/
		while ($row_relatorio = @mysqli_fetch_assoc($rs_relatorio)) {
			
			$vetFuncionarios[$row_relatorio['fun_chapa']]['nome_funcionario']	= $row_relatorio['nome_funcionario'];
			$vetFuncionarios[$row_relatorio['fun_chapa']]['status_rm']			= $row_relatorio['sit_descricao'];
			$vetFuncionarios[$row_relatorio['fun_chapa']]['nome_supervisor'] 	= $row_relatorio['nome_supervisor'];	
			$vetFuncionarios[$row_relatorio['fun_chapa']]['cargo'] 				= $row_relatorio['car_descricao'];
			$vetFuncionarios[$row_relatorio['fun_chapa']]['sit_descricao'] 		= $row_relatorio['sit_descricao'];
			$vetFuncionarios[$row_relatorio['fun_chapa']]['entrada']			= "-";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['saida'] 				= "-";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['atz'] 				= "00:00";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['atc'] 				= "00:00";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['hea'] 				= "00:00";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['hed'] 				= "00:00";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['fun_filial']			= $row_relatorio['fun_filial'];
			$vetFuncionarios[$row_relatorio['fun_chapa']]['fpo_registro'] 		= $row_relatorio['fpo_registro'];
			$vetFuncionarios[$row_relatorio['fun_chapa']]['horario_rm'] 		= $row_relatorio['horario_rm'];
			$vetFuncionarios[$row_relatorio['fun_chapa']]['horario_realizado'] 	= $row_relatorio['horario_realizado'];
			$vetFuncionarios[$row_relatorio['fun_chapa']]['fpo_senha_inicio'] 	= $row_relatorio['fpo_senha_inicio'];
			$vetFuncionarios[$row_relatorio['fun_chapa']]['fpo_senha_final'] 	= $row_relatorio['fpo_senha_final'];
		}
		
		$Sql = "SELECT b1.pba_data, b1.pba_chapa, b1.pba_entrada, b1.pba_saida, b1.pba_entrada, b1.pba_je_entrada, 
					b1.pba_je_saida, WEEKDAY(b1.pba_data) AS diaSemana, b1.pba_atz, b1.pba_atc, b1.pba_hea, b1.pba_hed, f1.fun_nome, f1.fun_filial, p1.fpo_registro
				FROM modulo_ponto.tbl_ponto_batido b1
					INNER JOIN modulo_ponto.tbl_funcionario_ponto p1 
						ON p1.fun_chapa = b1.pba_chapa 
					INNER JOIN sigo_integrado.tbl_rm_funcionario f1 
						ON f1.fun_chapa = b1.pba_chapa
					INNER JOIN sigo_integrado.tbl_rm_funcionario f2 
						ON f2.fun_chapa = p1.fpo_supervisor
					INNER JOIN sigo_integrado.tbl_rm_situacao sf 
						ON sf.sit_id = f1.sit_id
					WHERE pba_data='$data'
						AND (p1.fpo_supervisor = '$chapa_superior' OR p1.fun_chapa = '$chapa_superior')
			 			AND f1.fun_filial LIKE '%$uf%'
			 			AND f1.sit_id = 'A'
					ORDER BY pba_chapa, pba_data";
		//echo $Sql;exit;
		$rs_relatorio = @mysqli_query($conecta, $Sql) or die(mysqli_error($conecta));
/*		$tot7 = mysqli_num_rows($rs_relatorio);
		die('Entrou: ' . $tot7);*/
		while ($row_relatorio = @mysqli_fetch_assoc($rs_relatorio)) {
			
			$abono_id = LocalizaAbono($vetAbono,ConvertDataHoraMysql($row_relatorio['pba_data'],'normal'),$row_relatorio['pba_chapa']);
			if ($abono_id != "naoachou"){
				if($vetAbono[$abono_id]['turno'] == "ENTRADA"){
					$row_relatorio['pba_atz'] = "00:00";
					$row_relatorio['pba_hea'] = "00:00";
				}else if ($vetAbono[$abono_id]['turno'] == "SAIDA"){
					$row_relatorio['pba_atc'] = "00:00";
					$row_relatorio['pba_hed'] = "00:00";
				}else{
					$row_relatorio['pba_atz'] = "00:00";
					$row_relatorio['pba_hea'] = "00:00";
					$row_relatorio['pba_atc'] = "00:00";
					$row_relatorio['pba_hed'] = "00:00";
				}
			}
			if ($row_relatorio['diaSemana'] == 5 or $row_relatorio['diaSemana'] == 6) {
				$vetFuncionarios[$row_relatorio['pba_chapa']]['data'] 			= $row_relatorio['pba_data'];
				$vetFuncionarios[$row_relatorio['pba_chapa']]['entrada'] 		= $row_relatorio['pba_je_entrada'];
				$vetFuncionarios[$row_relatorio['pba_chapa']]['saida']			= $row_relatorio['pba_je_saida'];
			}else {
				$vetFuncionarios[$row_relatorio['pba_chapa']]['data'] 			= $row_relatorio['pba_data'];
				$vetFuncionarios[$row_relatorio['pba_chapa']]['entrada'] 		= $row_relatorio['pba_entrada'];
				$vetFuncionarios[$row_relatorio['pba_chapa']]['saida']			= $row_relatorio['pba_saida'];
			}
			$vetFuncionarios[$row_relatorio['pba_chapa']]['atz'] 			= RetornaMinutoHora(RetornaMinutoHora($vetFuncionarios[$row_relatorio['pba_chapa']]['atz']) + RetornaMinutoHora($row_relatorio['pba_atz']), "hora");
			$vetFuncionarios[$row_relatorio['pba_chapa']]['atc'] 			= RetornaMinutoHora(RetornaMinutoHora($vetFuncionarios[$row_relatorio['pba_chapa']]['atc']) + RetornaMinutoHora($row_relatorio['pba_atc']), "hora");
		
			$vetFuncionarios[$row_relatorio['pba_chapa']]['hea'] 			= RetornaMinutoHora(RetornaMinutoHora($vetFuncionarios[$row_relatorio['pba_chapa']]['hea']) + RetornaMinutoHora($row_relatorio['pba_hea']), "hora");
			$vetFuncionarios[$row_relatorio['pba_chapa']]['hed'] 			= RetornaMinutoHora(RetornaMinutoHora($vetFuncionarios[$row_relatorio['pba_chapa']]['hed']) + RetornaMinutoHora($row_relatorio['pba_hed']), "hora");
			
		}
			
		//echo "<pre>";print_r($vetFuncionarios);
		$trs 			= "";
		$total_atz 		= 0;
		$total_atc 		= 0;
		$total_he_geral = 0;
		$total_email 	= 0;
		$estilo 		= "";
		if ($vetFuncionarios) {
			foreach ($vetFuncionarios as $key => $registro) {
				
				//echo "<pre>";print_r($registro);
				$estilo == "tr_cor_cinza" ? $estilo = "tr_cor_branco" : $estilo = "tr_cor_cinza";	
				
				//if ($registro['entrada'] <> '00:00:00' && $registro['saida'] <> '00:00:00'){
					$total_he = RetornaMinutoHora(RetornaMinutoHora($registro['hea']) + RetornaMinutoHora($registro['hed']),"hora");
					$total_debito = RetornaMinutoHora($registro['hea']) + RetornaMinutoHora($registro['hed']) - RetornaMinutoHora($registro['atz']) - RetornaMinutoHora($registro['atc']);
				//}
				
				$registro['entrada'] 	= ConverteHoraMysql($registro['entrada']);
				$registro['saida'] 		= ConverteHoraMysql($registro['saida']);
				
				//echo "@@@".$registro['fun_filial']."<br>";
				
				$total_he_geral+= RetornaMinutoHora($registro['hea']) + RetornaMinutoHora($registro['hed']);
				$total_atc+= RetornaMinutoHora($registro['atc']);
				$total_atz+= RetornaMinutoHora($registro['atz']);
				
				if($total_debito <0) { 
			   		$total_debito = "(" . RetornaMinutoHora($total_debito*(-1),"hora") . ")";    		
			  	}else{ 
			  		$total_debito = RetornaMinutoHora($total_debito,"hora");   		
			  	}
				
			  	empty($registro['entrada']) ? $registro['entrada'] = "-" : false;
				empty($registro['saida']) ? $registro['saida'] = "-" : false;
				empty($registro['atz']) || $registro['atz'] == "00:00" ? $registro['atz'] = "-" : false;
				empty($registro['atc']) || $registro['atc'] == "00:00" ? $registro['atc'] = "-" : false;
				empty($total_he) 		|| $total_he 		== "00:00" ? $total_he 		  = "-" : false;
				empty($total_debito) 	|| $total_debito 	== "00:00" ? $total_debito 	  = "-" : false;
				
				if ($registro['fun_filial'] == 'DF' or $registro['fun_filial'] == 'GO' or $registro['fun_filial'] == 'TO') {
					$horario = $registro['horario_rm'];
				}else {
					$horario = $registro['horario_realizado'];
					// Quebra o vetor de hora para separar e validar
					$vetorHoraTrab = explode(";", $horario);
					
					// Conta o tamanho do valor da hora
					$hora1 	= strlen(trim($vetorHoraTrab[0]));
					$hora2 	= strlen(trim($vetorHoraTrab[1]));
					$hora3 	= strlen(trim($vetorHoraTrab[2]));
					$hora4 	= strlen(trim($vetorHoraTrab[3]));
					$hora5 	= strlen(trim($vetorHoraTrab[4]));
					$hora6 	= strlen(trim($vetorHoraTrab[5]));
					$hora7 	= strlen(trim($vetorHoraTrab[6]));
					$hora8 	= strlen(trim($vetorHoraTrab[7]));
					
					// Recebe as horas
					$hora_1	= trim($vetorHoraTrab[0]);
					$hora_2	= trim($vetorHoraTrab[1]);
					$hora_3	= trim($vetorHoraTrab[2]);
					$hora_4	= trim($vetorHoraTrab[3]);
					$hora_5	= trim($vetorHoraTrab[4]);
					$hora_6	= trim($vetorHoraTrab[5]);
					$hora_7	= trim($vetorHoraTrab[6]);
					$hora_8	= trim($vetorHoraTrab[7]);
			
					if (strlen($hora_1) == 4){
						$hora_1 = "0".$hora_1;
						$hora1 = $hora1+1;
					}
					if (strlen($hora_2) == 4){
						$hora_2 = "0".$hora_2;
						$hora2 = $hora2+1;
					}
					if (strlen($hora_3) == 4){
						$hora_3 = "0".$hora_3;
						$hora3 = $hora3+1;
					}
					if (strlen($hora_4) == 4){
						$hora_4 = "0".$hora_4;
						$hora4 = $hora4+1;
					}
					if (strlen($hora_5) == 4){
						$hora_5 = "0".$hora_5;
						$hora5 = $hora5+1;
					}
					if (strlen($hora_6) == 4){
						$hora_6 = "0".$hora_6;
						$hora6 = $hora6+1;
					}
					if (strlen($hora_7) == 4){
						$hora_7 = "0".$hora_7;
						$hora7 = $hora7+1;
					}
					if (strlen($hora_8) == 4){
						$hora_8 = "0".$hora_8;
						$hora8 = $hora8+1;
					}
					
					if ($hora_8) {
						// Recebe as horas e coloca no padrão para aparecer como mensagem
						$horaTab = $hora_1."/".$hora_2."-".$hora_3."/".$hora_4." (SAB ".$hora_5."/".$hora_6."-".$hora_7."/".$hora_8.")";
					}else if (!$hora_5 && !$hora_6 && !$hora_7 && !$hora_8) {
						$horaTab = $hora_1."/".$hora_2."-".$hora_3."/".$hora_4;
					}elseif (!$hora_8) {
						$horaTab = $hora_1.";".$hora_2.";".$hora_3.";".$hora_4." (SAB ".$hora_5."/".$hora_6.")";
					}else {
						$horaTab = '-';
					}
					
					$horario = $horaTab;
				}
				
				if (!$horario or $horario == '/-/') {
					$horario = '-';
				}
								
				if ($tipo_busca == "dia"){
					$valor_entradasaida = <<< EOF
						<td>{$registro['entrada']}</td>		
						<td>{$registro['saida']}</td>
EOF;
				}
				
				
				if ($key == $chapa_superior){
					$tr_tmp= <<< EOF
					<tr class="tr_cor_branco">
						<td>$key</td>
						<td>{$registro['fpo_registro']}</td>
						<td align="left">{$registro['nome_funcionario']}</td>
						<td>{$registro['cargo']}</td>
						<td align="left">{$registro['nome_supervisor']}</td>
						<td align="center">{$registro['status_rm']}</td>
						<td align="center">{$registro['fpo_senha_inicio']}</td>
						<td align="center">{$registro['fpo_senha_final']}</td>
						$valor_entradasaida
						<td>{$registro['atz']}</td>		
						<td>{$registro['atc']}</td>
						<td>$total_he</td>
						<td>$total_debito</td>
						<td>$horario</td>
					</tr>
EOF;
					$trs = $tr_tmp . $trs;
				}else{
					$trs.= <<< EOF
						<tr class="$estilo">
							<td>$key</td>
							<td>{$registro['fpo_registro']}</td>
							<td align="left">{$registro['nome_funcionario']}</td>
							<td align="left">{$registro['cargo']}</td>
							<td align="left">{$registro['nome_supervisor']}</td>
							<td align="center">{$registro['status_rm']}</td>
							<td align="center">{$registro['fpo_senha_inicio']}</td>
							<td align="center">{$registro['fpo_senha_final']}</td>
							$valor_entradasaida
							<td>{$registro['atz']}</td>		
							<td>{$registro['atc']}</td>
							<td>$total_he</td>
							<td>$total_debito</td>
							<td>$horario</td>
						</tr>
EOF;
				}
				$total_email ++;
			}
		}
		
		$saldo_geral = $total_he_geral - $total_atc - $total_atz;
		if($saldo_geral <0) { 
			$saldo_geral = "(" . RetornaMinutoHora($saldo_geral*(-1),"hora") . ")";    		
		}else{ 
			$saldo_geral = RetornaMinutoHora($saldo_geral,"hora");   		
		}
		
		$total_he_geral = RetornaMinutoHora($total_he_geral,"hora");
		$total_atc = RetornaMinutoHora($total_atc,"hora");
		$total_atz = RetornaMinutoHora($total_atz,"hora");
		
		if ($tipo_busca == "dia"){
			$campo_entradasaida = "
			<td>ENTRADA</td>
			<td>SAIDA</td>";
			$colspan_somatorio = 10;
		}else{
			$colspan_somatorio = 8;
		}
		
		$table = <<< EOF
		<style>
		.subcabecalho_tr {
			/* sub cabecalho da tabela */	
			background-color:#efefef;
			text-align:center;
			font-size:11px;
		}
		
		.box_relatorio{
			border:solid;
			border-width:1px;
			border-collapse: collapse;
			background-color:#FFFFFF;
		}
		
		.tr_cor_cinza {
			/* qualquer TR q tenha esta class ira alterar de cor com o passar do mouse */	
			background-color:#fefefe;
			font-size:11px;
			text-align:center;
		}
	
		.tr_cor_branco {
			/* qualquer TR q tenha esta class ira alterar de cor com o passar do mouse */	
			background-color:#EEEFFF;
			font-size:11px;
			text-align:center;
		}
		</style>
		
		<table class="box_relatorio" align="center" width="100%">
			<tr class="subcabecalho_tr">
				<td>CHAPA</td>
				<td>REGISTRO OI</td>
				<td>NOME</td>
				<td>CARGO</td>
				<td>SUPERIOR</td>
				<td>STATUS RM</td>
				<td colspan="2">SENHA INICIO - FINAL</td>
				$campo_entradasaida
				<td>ATRASO</td>
				<td>ANTEC.</td>
				<td>H.EXTRA</td>
				<td>SALDO</td>
				<td>HORÁRIO RM</td>
			</tr>
			$trs
			<tr class="subcabecalho_tr">
				<td colspan="$colspan_somatorio" align="right">SOMAT&Oacute;RIO</td>
				<td>$total_atz</td>
				<td>$total_atc</td>
				<td>$total_he_geral</td>
				<td>$saldo_geral</td>
				<td></td>
			</tr>
		</table>
EOF;
	
		$cabecalho = 'Prezado, '.$nome_supervisor.'<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Segue acompanhamento da jornada de trabalho dos empregados que estão sob sua supervisão';
		
		$rodape = '<b>Att<br>E-mail automatico do SIGO. Favor nao responder este e-mail!<b>';
	
		$texto = "$cabecalho<br><br>".$table.$rodape;
		
		$assunto = 'STATUS DIÁRIO - '.$row_pessoal_supervisor['fun_filial'];
		
		if ($emailSuperiorSupervisor){
			echo '<span style="display:none;">';
				$emailEnviado = EnviarEmail($row_pessoal_supervisor['sup_nome'],$emailSuperiorSupervisor, $assunto, $texto,"","sigo@telemont.com.br","SIGO TELEMONT","bruno.tertuliano@telemont.com.br");
			echo '</span>';
			if ($emailEnviado) {
				if ($row_pessoal_supervisor['fun_filial'] == 'AC')
					$cont_ac_s ++;
				
				if ($row_pessoal_supervisor['fun_filial'] == 'GO')
					$cont_go_s ++;
				
				if ($row_pessoal_supervisor['fun_filial'] == 'TO')
					$cont_to_s ++;
				
				if ($row_pessoal_supervisor['fun_filial'] == 'RO')
					$cont_ro_s ++;
				
				if ($row_pessoal_supervisor['fun_filial'] == 'DF')
					$cont_df_s ++;
				
				if ($row_pessoal_supervisor['fun_filial'] == 'MS')
					$cont_ms_s ++;
				
				if ($row_pessoal_supervisor['fun_filial'] == 'MT')
					$cont_mt_s ++;
			}else {
				if ($row_pessoal_supervisor['fun_filial'] == 'AC')
					$cont_nao_enviador_s_ac ++;
				
				if ($row_pessoal_supervisor['fun_filial'] == 'GO')
					$cont_nao_enviador_s_go ++;
				
				if ($row_pessoal_supervisor['fun_filial'] == 'TO')
					$cont_nao_enviador_s_to ++;
				
				if ($row_pessoal_supervisor['fun_filial'] == 'RO')
					$cont_nao_enviador_s_ro ++;
				
				if ($row_pessoal_supervisor['fun_filial'] == 'DF')
					$cont_nao_enviador_s_df ++;
				
				if ($row_pessoal_supervisor['fun_filial'] == 'MS')
					$cont_nao_enviador_s_ms ++;
				
				if ($row_pessoal_supervisor['fun_filial'] == 'MT')
					$cont_nao_enviador_s_mt ++;
			}
		}
	}else {
		if ($row_pessoal_supervisor['fun_filial'] == 'AC')
			$cont_nao_enviador_s_ac ++;
		
		if ($row_pessoal_supervisor['fun_filial'] == 'GO')
			$cont_nao_enviador_s_go ++;
		
		if ($row_pessoal_supervisor['fun_filial'] == 'TO')
			$cont_nao_enviador_s_to ++;
		
		if ($row_pessoal_supervisor['fun_filial'] == 'RO')
			$cont_nao_enviador_s_ro ++;
		
		if ($row_pessoal_supervisor['fun_filial'] == 'DF')
			$cont_nao_enviador_s_df ++;
		
		if ($row_pessoal_supervisor['fun_filial'] == 'MS')
			$cont_nao_enviador_s_ms ++;
		
		if ($row_pessoal_supervisor['fun_filial'] == 'MT')
			$cont_nao_enviador_s_mt ++;
	}
}

$total_enviados_ac 					= $cont_ac_e + $cont_ac_s;
$total_enviados_go 					= $cont_go_e + $cont_go_s;
$total_enviados_ms 					= $cont_ms_e + $cont_ms_s;
$total_enviados_mt 					= $cont_mt_e + $cont_mt_s;
$total_enviados_to 					= $cont_to_e + $cont_to_s;
$total_enviados_ro 					= $cont_ro_e + $cont_ro_s;
$total_enviados_df 					= $cont_df_e + $cont_df_s;

$total_nao_enviados_ac 				= $cont_nao_enviador_e_ac + $cont_nao_enviador_s_ac;
$total_nao_enviados_go 				= $cont_nao_enviador_e_go + $cont_nao_enviador_s_go;
$total_nao_enviados_ms 				= $cont_nao_enviador_e_ms + $cont_nao_enviador_s_ms;
$total_nao_enviados_mt 				= $cont_nao_enviador_e_mt + $cont_nao_enviador_s_mt;
$total_nao_enviados_to 				= $cont_nao_enviador_e_to + $cont_nao_enviador_s_to;
$total_nao_enviados_ro 				= $cont_nao_enviador_e_ro + $cont_nao_enviador_s_ro;
$total_nao_enviados_df 				= $cont_nao_enviador_e_df + $cont_nao_enviador_s_df;

$total_geral 						= $total_enviados_ac + $total_enviados_go + $total_enviados_ms + $total_enviados_mt + $total_enviados_to + $total_enviados_ro + $total_enviados_df;

$total_encarregado 					= $cont_df_e + $cont_ro_e + $cont_to_e + $cont_mt_e + $cont_ms_e + $cont_go_e + $cont_ac_e;
$total_supervisor					= $cont_df_s + $cont_ro_s + $cont_to_s + $cont_mt_s + $cont_ms_s + $cont_go_s + $cont_ac_s;

$total_encarregado_nao_enviados		= $cont_nao_enviador_e_df + $cont_nao_enviador_e_ro + $cont_nao_enviador_e_to + $cont_nao_enviador_e_mt + $cont_nao_enviador_e_ms + $cont_nao_enviador_e_go + $cont_nao_enviador_e_ac;
$total_supervisor_nao_enviados		= $cont_nao_enviador_s_df + $cont_nao_enviador_s_ro + $cont_nao_enviador_s_to + $cont_nao_enviador_s_mt + $cont_nao_enviador_s_ms + $cont_nao_enviador_s_go + $cont_nao_enviador_s_ac;

$total_geral_nao_enviados 			= $total_encarregado_nao_enviados + $total_supervisor_nao_enviados;

?>

<body onload="javascript: setTimeout('getSecs()',1000);"  >  

	<h1 class="cabecalho" align="center" > E-MAILS DE ACOMPANHAMENTO DA JORNADA DE TRABALHO DOS EMPREGADOS </h1>

	<br />
	
	<table class="box_relatorio" align="center" width="700px">
		<tr class="cabecalho_tr">
			<td colspan="8" nowrap><span style="color:#FFFFFF;font-weight:bold;font-size: 16px;" id="clock1"></span></td>
		</tr>

		<tr class="cabecalho_tr">
			<td colspan="4" nowrap><span class="cabecalho_tr">E-MAILS ENVIADOS POR FILIAL</span></td>
			<td colspan="4" nowrap><span class="cabecalho_tr">E-MAILS NÃO ENVIADOS POR FILIAL</span></td>
		</tr>
		<tr class="subcabecalho_tr">
			<td>FILIAL</td>
			<td>SUPERVISOR</td>
			<td>ENCARREGADO</td>
			<td>TOTAL PARCIAL</td>
			<td>SUPERVISOR</td>
			<td>ENCARREGADO</td>
			<td colspan="2">TOTAL PARCIAL</td>
		</tr>
		<tr class="tr_cor_branca" align="center">
			<td>AC</td>
			<td><?php echo $cont_ac_s;?></td>
			<td><?php echo $cont_ac_e;?></td>
			<td><?php echo $total_enviados_ac;?></td>
			<td><?php echo $cont_nao_enviador_s_ac; ?></td>
			<td><?php echo $cont_nao_enviador_e_ac; ?></td>
			<td colspan="2"><?php echo $total_nao_enviados_ac; ?></td>
		</tr>
		<tr class="tr_cor_cinsa" align="center">
			<td>GO</td>
			<td><?php echo $cont_go_s;?></td>
			<td><?php echo $cont_go_e;?></td>
			<td><?php echo $total_enviados_go;?></td>
			<td><?php echo $cont_nao_enviador_s_go; ?></td>
			<td><?php echo $cont_nao_enviador_e_go; ?></td>
			<td colspan="2"><?php echo $total_nao_enviados_go; ?></td>
		</tr>
		<tr class="tr_cor_cinsa" align="center">
			<td>MS</td>
			<td><?php echo $cont_ms_s;?></td>
			<td><?php echo $cont_ms_e;?></td>
			<td><?php echo $total_enviados_ms;?></td>
			<td><?php echo $cont_nao_enviador_s_ms; ?></td>
			<td><?php echo $cont_nao_enviador_e_ms; ?></td>
			<td colspan="2"><?php echo $total_nao_enviados_ms; ?></td>
		</tr>
		<tr class="tr_cor_cinsa" align="center">
			<td>MT</td>
			<td><?php echo $cont_mt_s;?></td>
			<td><?php echo $cont_mt_e;?></td>
			<td><?php echo $total_enviados_mt;?></td>
			<td><?php echo $cont_nao_enviador_s_mt; ?></td>
			<td><?php echo $cont_nao_enviador_e_mt; ?></td>
			<td colspan="2"><?php echo $total_nao_enviados_mt; ?></td>
		</tr>
		<tr class="tr_cor_cinsa" align="center">
			<td>TO</td>
			<td><?php echo $cont_to_s;?></td>
			<td><?php echo $cont_to_e;?></td>
			<td><?php echo $total_enviados_to;?></td>
			<td><?php echo $cont_nao_enviador_s_to; ?></td>
			<td><?php echo $cont_nao_enviador_e_to; ?></td>
			<td colspan="2"><?php echo $total_nao_enviados_to; ?></td>
		</tr>
		<tr class="tr_cor_cinsa" align="center">
			<td>RO</td>
			<td><?php echo $cont_ro_s;?></td>
			<td><?php echo $cont_ro_e;?></td>
			<td><?php echo $total_enviados_ro;?></td>
			<td><?php echo $cont_nao_enviador_s_ro; ?></td>
			<td><?php echo $cont_nao_enviador_e_ro; ?></td>
			<td colspan="2"><?php echo $total_nao_enviados_ro; ?></td>
		</tr>
		<tr class="tr_cor_cinsa" align="center">
			<td>DF</td>
			<td><?php echo $cont_df_s;?></td>
			<td><?php echo $cont_df_e;?></td>
			<td><?php echo $total_enviados_df;?></td>
			<td><?php echo $cont_nao_enviador_s_df; ?></td>
			<td><?php echo $cont_nao_enviador_e_df; ?></td>
			<td colspan="2"><?php echo $total_nao_enviados_df; ?></td>
		</tr>
		<tr class="subcabecalho_tr" align="center">
			<td>TOTAL GERAL</td>
			<td><?php echo $total_supervisor;?></td>
			<td><?php echo $total_encarregado;?></td>
			<td><?php echo $total_geral;?></td>
			<td><?php echo $total_supervisor_nao_enviados; ?></td>
			<td><?php echo $total_encarregado_nao_enviados; ?></td>
			<td colspan="2"><?php echo $total_geral_nao_enviados; ?></td>
		</tr>
	</table>

</body>
</html>