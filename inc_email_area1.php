<?php
/**
      * @author Bruno Macedo Tertuliano
      * @access 06/02/2012
      * @copyright (c 2012)
*/
include('includes/funcoes.php');
include('funcoes.php');

set_time_limit(0);
@ini_set("memory_limit", "1G");
error_reporting(E_ALL & ~E_NOTICE);

$inicio_web = date("d/m/y G:i:s");

$conecta = RetornaConexaoMysql('serverdge','modulo_ponto');
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
}else {// Se ja tiver rodado a manha
	$sHors 		= $row_email['e_seg_turno'];
	$sMins 		= "00";
	$sSecs 		= "00";
	
	$Sql		= "UPDATE tbl_email 
					SET e_flag_turno = 1, e_seg_turno = 14";
	@mysqli_query($conecta, $Sql) or die(mysqli_error($conecta));
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt" lang="pt">
<head>
	<title>:: SIGO - ADMINISTRATIVO - PONTO ::</title>
	<meta http-equiv="Content-Type" content="text/html; CHARSET=iso-8859-1" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<!--Menu-->
	<link  href="css/menu.css" rel="stylesheet" type="text/css" />
	<link  href="css/index.css" rel="stylesheet" type="text/css" />
	<link  href="<?php echo $caminho_raiz; ?>css/index.css" rel="stylesheet" type="text/css" />
	<style>
	.cabecalho{
		font-size: 25pt;
	}
	</style>

	<link href="<?php echo $caminho_raiz; ?>css/index.css" rel="stylesheet" type="text/css" />

	<script language="JavaScript" type="text/javascript">
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
				
				document.getElementById("clock1").innerHTML	=	sHors + "<font style=\"color:#FFFFFF;font-weight:bold;font-size: 16px;\">:</font>"+sMins+"<font style=\"color:#FFFFFF;font-weight:bold;font-size: 16px;\">:</font>"+sSecs;
				
				window.location.reload(true);
			}else{
				document.getElementById("clock1").innerHTML	=	sHors + "<font style=\"color:#FFFFFF;font-weight:bold;font-size: 16px;\">:</font>"+sMins+"<font style=\"color:#FFFFFF;font-weight:bold;font-size: 16px;\">:</font>"+sSecs;
				
				setTimeout('getSecs()',1000);
			}
		}
	
	 </script>
	
</head>
<!--Fim Menu-->
<?php
$filial 						= "";
$estilo							= "";
// Armazena a contagem por filial dos encarregados
$cont_mg_e 						= 0;
$cont_es_e						= 0;
// Armazena a contagem por filial dos supervisores
$cont_mg_s 						= 0;
$cont_es_s 						= 0;
// Armazena a contagem por filial dos coordenadores
$cont_mg_c 						= 0;
$cont_es_c 						= 0;
// Armazena a contagem por filial dos não enviador por encarregados
$cont_nao_enviador_e_mg 		= 0;
$cont_nao_enviador_e_es 		= 0;
// Armazena a contagem por filial dos não enviador por supervisores
$cont_nao_enviador_s_mg 		= 0;
$cont_nao_enviador_s_es 		= 0;
// Armazena a contagem por filial dos não enviador por coordenador
$cont_nao_enviador_c_mg 		= 0;
$cont_nao_enviador_c_es 		= 0;
// Armazena a contagem com o total por filial de encarregado e supervisor
$total_enviados_mg 				= 0;
$total_enviados_es 				= 0;

// Armazena os encarregados que não foram enviados
$detalha_nao_enviados_e_mg		= "";
$detalha_nao_enviados_e_es		= "";

// Armazena os supervisores que não foram enviados
$detalha_nao_enviados_s_mg		= "";
$detalha_nao_enviados_s_es		= "";

// Armazena os coordneadores que não foram enviados
$detalha_nao_enviados_c_mg		= "";
$detalha_nao_enviados_c_es		= "";

// Armazena a tabela com a lista dos não enviados por filial
$tabela_nao_enviados_mg			= "";
$tabela_nao_enviados_es			= "";

$total_geral_nao_enviados		= 0;
$total_encarregado_nao_enviados = 0;
$total_supervisor_nao_enviados	= 0;
$total_encarregado				= 0;
$total_supervisor				= 0;
$total_coordenador				= 0;
$total_nao_enviados_ac 			= 0;
$total_geral		 			= 0;

$emailSuperiorEncarregado		= '';
$emailSuperiorSupervisor		= '';
$emailSuperiorCoordenador		= '';

$tableResultado					= '';
$tableResultadoEmailMG			= '';
$tableResultadoEmailES			= '';
$cabecalhoResultado				= '';
$rodapeResultado				= '';
$assuntoResultado				= '';
$textoResultado					= '';
$filialPorExtenso				= '';
$textoEnvia						= '';

//variaveis de controle de e-mail enviados e não enviados para os responsáveis pelas filiais.
$enviadasResponsaveisMG			= 0;
$enviadasResponsaveisES			= 0;
$naoEnviadasResponsaveisMG		= 0;
$naoEnviadasResponsaveisES		= 0;
$totalEnviadas					= 0;
$totalNaoEnviadas				= 0;

$tipo_busca						= 'dia';
$data 							= date("Y/m/d");

$estiloEmail 					= "		
	<style>
	.subcabecalho_tr {
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
	
	.cabecalho_tr {
		background-color:#888FFF;
		text-align:center;
		font-weight:bold;
		font-size:14px;
		color:#FFFFFF;
	}
	.tr_cor_cinza {
		background-color:#fefefe;
		font-size:11px;
		text-align:center;
	}

	.tr_cor_branco {
		background-color:#EEEFFF;
		font-size:11px;
		text-align:center;
	}
	</style>
";

$Sql 								= "select pe.*, rmf.fun_filial from tbl_pessoal_encarregado pe
										inner join sigo_integrado.tbl_rm_funcionario rmf
											on (pe.enc_chapa = rmf.fun_chapa)
										inner join tbl_funcionario_ponto fp
											on (pe.enc_chapa = fp.fun_chapa)
										where rmf.fun_filial in('MG','ES')
											AND rmf.sit_id <> 'D' AND rmf.sit_id <> 'I' AND rmf.fun_codtipo <> 'A' AND rmf.fun_codtipo <> 'D'
											AND fp.fpo_tipo_ponto <> 'dimep'
											AND fp.fpo_tipo_empregado = 'E'
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
					INNER JOIN modulo_ponto.tbl_abono_tipo t1 
						ON a1.abt_id = t1.abt_id 
					INNER JOIN modulo_ponto.tbl_funcionario_ponto p1 
						ON a1.fun_chapa = p1.fun_chapa 
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
					ep.exp_descricao AS horario_rm, p1.fpo_horario_trabalho AS horario_realizado,
					IF('$data' BETWEEN f1.fun_ferias_inicio AND f1.fun_ferias_fim,'FERIAS',NULL) AS ferias
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
		 			AND (p1.fpo_bate_ponto = 'sim' OR p1.fpo_bate_ponto IS NULL OR p1.fpo_bate_ponto = '')
		 			AND f1.sit_id <> 'D' AND f1.sit_id <> 'I' AND f1.fun_codtipo <> 'A' AND f1.fun_codtipo <> 'D'
		 			AND p1.fpo_tipo_ponto <> 'dimep'
		 			AND p1.fpo_tipo_empregado = 'E'
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
			$vetFuncionarios[$row_relatorio['fun_chapa']]['ferias'] 			= $row_relatorio['ferias'];
			$vetFuncionarios[$row_relatorio['fun_chapa']]['entrada']			= "-";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['saida'] 				= "-";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['atz'] 				= "00:00";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['atc'] 				= "00:00";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['hea'] 				= "00:00";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['hed'] 				= "00:00";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['je'] 				= "00:00";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['fun_filial']			= $row_relatorio['fun_filial'];
			$vetFuncionarios[$row_relatorio['fun_chapa']]['fpo_registro'] 		= $row_relatorio['fpo_registro'];
			$vetFuncionarios[$row_relatorio['fun_chapa']]['horario_rm'] 		= $row_relatorio['horario_rm'];
			$vetFuncionarios[$row_relatorio['fun_chapa']]['horario_realizado'] 	= $row_relatorio['horario_realizado'];
		}
		$Sql = "SELECT
					b1.pba_data, b1.pba_chapa,
					IF(IF(SUBSTR(f.fer_data,6),1,0) = 1 OR WEEKDAY('$data') = 6 OR a.abt_id = 13,b1.pba_je_entrada,IF(b1.pba_entrada = '00:00:00',b1.pba_je_entrada,b1.pba_entrada)) pba_entrada,
					IF(IF(SUBSTR(f.fer_data,6),1,0) = 1 OR WEEKDAY('$data') = 6 OR a.abt_id = 13,b1.pba_je_saida,IF(b1.pba_saida = '00:00:00',b1.pba_je_saida,b1.pba_saida)) pba_saida,
					IF(IF(SUBSTR(f.fer_data,6),1,0) = 1 OR WEEKDAY(b1.pba_data) = 6 OR a.abt_id = 13,IF(b1.pba_je_entrada,1,0),IF(b1.pba_entrada,1,0)) qtdEntrada,
					IF(IF(SUBSTR(f.fer_data,6),1,0) = 1 OR WEEKDAY(b1.pba_data) = 6 OR a.abt_id = 13,IF(b1.pba_je_saida,1,0),IF(b1.pba_saida,1,0)) qtdSaida,
					IF(IF(SUBSTR(f.fer_data,6),1,0) = 1 OR WEEKDAY(b1.pba_data) = 6 OR a.abt_id = 13,IF(b1.pba_je_entrada IS NULL AND b1.pba_je_saida IS NULL,1,0),IF(b1.pba_entrada IS NULL AND b1.pba_saida IS NULL,1,0)) semRegistros,
					b1.pba_atz, b1.pba_atc, b1.pba_hea, b1.pba_hed,
					b1.pba_je,
					f1.fun_nome, p1.fpo_registro
				FROM modulo_ponto.tbl_ponto_batido b1
					INNER JOIN modulo_ponto.tbl_funcionario_ponto p1 
						ON p1.fun_chapa = b1.pba_chapa 
					INNER JOIN sigo_integrado.tbl_rm_funcionario f1 
						ON f1.fun_chapa = b1.pba_chapa
					INNER JOIN sigo_integrado.tbl_rm_funcionario f2 
						ON f2.fun_chapa = p1.fpo_supervisor
					INNER JOIN sigo_integrado.tbl_rm_situacao sf 
						ON sf.sit_id = f1.sit_id
					LEFT JOIN tbl_feriado f
						ON SUBSTR(f.fer_data,6) = SUBSTR(b1.pba_data,6)
					LEFT JOIN tbl_abono a
						ON a.abo_data_ocorrido = b1.pba_data
				WHERE pba_data='$data'
					AND (p1.fpo_supervisor = '$chapa_superior' OR p1.fun_chapa = '$chapa_superior')
					AND (p1.fpo_bate_ponto = 'sim' OR p1.fpo_bate_ponto IS NULL OR p1.fpo_bate_ponto = '')
		 			AND f1.fun_filial = '$uf'
		 			AND f1.sit_id <> 'D' AND f1.sit_id <> 'I' AND f1.fun_codtipo <> 'A' AND f1.fun_codtipo <> 'D'
		 			AND p1.fpo_tipo_ponto <> 'dimep'
		 			AND p1.fpo_tipo_empregado = 'E'
		 		GROUP BY b1.pba_chapa
				ORDER BY pba_chapa, pba_data ";
		//echo $Sql."<br>";exit;
		$rs_relatorio = @mysqli_query($conecta, $Sql) or die(mysqli_error($conecta));
		//$tot4 = mysqli_num_rows($rs_relatorio);
		//die('Entrou: ' . $tot4);
		while ($row_relatorio = @mysqli_fetch_assoc($rs_relatorio)) {
			$abono_id = LocalizaAbono($vetAbono,ConvertDataHoraMysql($row_relatorio['pba_data'],'normal'),trim($row_relatorio['pba_chapa']));
			if ($abono_id != "naoachou"){
				if($vetAbono[$abono_id]['turno'] == "ENTRADA"){
					$row_relatorio['pba_atz'] 	= "00:00";
					$row_relatorio['pba_hea'] 	= "00:00";
					$row_relatorio['pba_je'] 	= "00:00";
				}else if ($vetAbono[$abono_id]['turno'] == "SAIDA"){
					$row_relatorio['pba_atc'] 	= "00:00";
					$row_relatorio['pba_hed'] 	= "00:00";
					$row_relatorio['pba_je'] 	= "00:00";
				}else{
					$row_relatorio['pba_atz'] 	= "00:00";
					$row_relatorio['pba_hea'] 	= "00:00";
					$row_relatorio['pba_atc'] 	= "00:00";
					$row_relatorio['pba_hed'] 	= "00:00";
					$row_relatorio['pba_je'] 	= "00:00";
				}
			}
			$vetFuncionarios[trim($row_relatorio['pba_chapa'])]['data'] 	= $row_relatorio['pba_data'];
			$vetFuncionarios[trim($row_relatorio['pba_chapa'])]['entrada'] 	= $row_relatorio['pba_entrada'];
			$vetFuncionarios[trim($row_relatorio['pba_chapa'])]['saida']	= $row_relatorio['pba_saida'];
			$vetFuncionarios[trim($row_relatorio['pba_chapa'])]['atz'] 		= RetornaMinutoHora(RetornaMinutoHora($vetFuncionarios[trim($row_relatorio['pba_chapa'])]['atz']) + RetornaMinutoHora($row_relatorio['pba_atz']), "hora");
			$vetFuncionarios[trim($row_relatorio['pba_chapa'])]['atc'] 		= RetornaMinutoHora(RetornaMinutoHora($vetFuncionarios[trim($row_relatorio['pba_chapa'])]['atc']) + RetornaMinutoHora($row_relatorio['pba_atc']), "hora");
			$vetFuncionarios[trim($row_relatorio['pba_chapa'])]['hea'] 		= RetornaMinutoHora(RetornaMinutoHora($vetFuncionarios[trim($row_relatorio['pba_chapa'])]['hea']) + RetornaMinutoHora($row_relatorio['pba_hea']), "hora");
			$vetFuncionarios[trim($row_relatorio['pba_chapa'])]['hed'] 		= RetornaMinutoHora(RetornaMinutoHora($vetFuncionarios[trim($row_relatorio['pba_chapa'])]['hed']) + RetornaMinutoHora($row_relatorio['pba_hed']), "hora");
			$vetFuncionarios[trim($row_relatorio['pba_chapa'])]['je'] 		= RetornaMinutoHora(RetornaMinutoHora($row_relatorio['pba_je']),"hora");
		}
			
		$trs 			= "";
		$total_atz 		= 0;
		$total_atc 		= 0;
		$total_he_geral = 0;
		$total_email 	= 0;
		$estilo 		= "";
		$texto 			= "";
		$assunto		= "";
		$rodape			= "";
		$cabecalho		= "";
		
		if ($vetFuncionarios) {
			foreach ($vetFuncionarios as $key => $registro) {
				
				$estilo == "tr_cor_cinza" ? $estilo = "tr_cor_branco" : $estilo = "tr_cor_cinza";	
			
				$total_he 				= RetornaMinutoHora(RetornaMinutoHora($registro['hea']) + RetornaMinutoHora($registro['hed']),"hora");
				$total_je 				= RetornaMinutoHora(RetornaMinutoHora($registro['je']),"hora");
				$total_debito 			= RetornaMinutoHora($registro['hea']) + RetornaMinutoHora($registro['hed']) + RetornaMinutoHora($registro['je']) - RetornaMinutoHora($registro['atz']) - RetornaMinutoHora($registro['atc']);
				
				$registro['entrada'] 	= ConverteHoraMysql($registro['entrada']);
				$registro['saida'] 		= ConverteHoraMysql($registro['saida']);
				
				$total_he_geral			+= RetornaMinutoHora($registro['hea']) + RetornaMinutoHora($registro['hed']);
				$total_je_geral			+= RetornaMinutoHora($registro['je']);
				$total_atc				+= RetornaMinutoHora($registro['atc']);
				$total_atz				+= RetornaMinutoHora($registro['atz']);
				
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
				empty($total_je) 		|| $total_je 		== "00:00" ? $total_je 		  = "-" : false;
				empty($total_debito) 	|| $total_debito 	== "00:00" ? $total_debito 	  = "-" : false;
				
				$horario = $registro['horario_rm'];
						
				if (!$horario or $horario == '/-/') {
					$horario = '-';
				}
				
				if ($registro['ferias']) {
					$status_rm = "FÉRIAS";
				}else {
					$status_rm = $registro['status_rm'];
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
							$valor_entradasaida
							<td>{$registro['atz']}</td>		
							<td>{$registro['atc']}</td>
							<td>$total_he</td>
							<td>$total_je</td>
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
							$valor_entradasaida
							<td>{$registro['atz']}</td>		
							<td>{$registro['atc']}</td>
							<td>$total_he</td>
							<td>$total_je</td>
							<td>$total_debito</td>
							<td>$horario</td>
						</tr>
EOF;
				}
				$total_email ++;
			}
		}
		
		$saldo_geral = ($total_je_geral + $total_he_geral) - ($total_atc - $total_atz);
		if($saldo_geral <0) { 
			$saldo_geral = "(" . RetornaMinutoHora($saldo_geral*(-1),"hora") . ")";    		
		}else{ 
			$saldo_geral = RetornaMinutoHora($saldo_geral,"hora");   		
		}
		
		$total_he_geral = RetornaMinutoHora($total_he_geral,"hora");
		$total_je_geral = RetornaMinutoHora($total_je_geral,"hora");
		$total_atc 		= RetornaMinutoHora($total_atc,"hora");
		$total_atz 		= RetornaMinutoHora($total_atz,"hora");
		
		if ($tipo_busca == "dia"){
			$campo_entradasaida = "
			<td>ENTRADA</td>
			<td>SAIDA</td>";
			$colspan_somatorio = 8;
		}else{
			$colspan_somatorio = 7;
		}
		
		$table = <<< EOF
		$estiloEmail		
		<table class="box_relatorio" align="center" width="100%">
			<tr class="subcabecalho_tr">
				<td>CHAPA</td>
				<td>REGISTRO OI</td>
				<td>NOME</td>
				<td>CARGO</td>
				<td>SUPERIOR</td>
				<td>STATUS RM</td>
				$campo_entradasaida
				<td>ATRASO</td>
				<td>ANTEC.</td>
				<td>H.EXTRA</td>
				<td>J.EXTRA</td>
				<td>SALDO</td>
				<td>HORARIO RM</td>
			</tr>
			$trs
			<tr class="subcabecalho_tr">
				<td colspan="$colspan_somatorio" align="right">SOMAT&Oacute;RIO</td>
				<td>$total_atz</td>
				<td>$total_atc</td>
				<td>$total_he_geral</td>
				<td>$total_je_geral</td>
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
				$emailEnviado = EnviarEmail($row_pessoal_encarregado['enc_nome'],$emailSuperiorEncarregado, $assunto, $texto,"","sigo@telemont.com.br","SIGO TELEMONT","");
			echo '</span>';
			
			if ($emailEnviado) {
				if ($row_pessoal_encarregado['fun_filial'] == 'MG')
					$cont_mg_e ++;
				
				if ($row_pessoal_encarregado['fun_filial'] == 'ES')
					$cont_es_e ++;
			}else {
				if ($row_pessoal_encarregado['fun_filial'] == 'MG'){
					$cont_nao_enviador_e_mg ++;
					$detalha_nao_enviados_e_mg .= $row_pessoal_encarregado['enc_nome']."<br>";
				}
				if ($row_pessoal_encarregado['fun_filial'] == 'ES'){
					$cont_nao_enviador_e_es ++;
					$detalha_nao_enviados_e_es .= $row_pessoal_encarregado['enc_nome']."<br>";
				}
			}
		}
	}else {
		if ($row_pessoal_encarregado['fun_filial'] == 'MG'){
			$cont_nao_enviador_e_mg ++;
		}
		if ($row_pessoal_encarregado['fun_filial'] == 'ES'){
			$cont_nao_enviador_e_es ++;
		}
	}
}

$Sql 							= "select ps.*, rmf.fun_filial from tbl_pessoal_supervisor ps
										inner join sigo_integrado.tbl_rm_funcionario rmf
											on (ps.sup_chapa = rmf.fun_chapa)
										inner join tbl_funcionario_ponto fp
											on (ps.sup_chapa = fp.fun_chapa)
										where rmf.fun_filial in('MG','ES')
											AND rmf.sit_id <> 'D' AND rmf.sit_id <> 'I' AND rmf.fun_codtipo <> 'A' AND rmf.fun_codtipo <> 'D'
											AND fp.fpo_tipo_ponto <> 'dimep'
											AND fp.fpo_tipo_empregado = 'E'
										order by
											rmf.fun_filial";
$rs_pessoal_supervisor 			= @mysqli_query($conecta, $Sql) or die(mysqli_error($conecta));

while ($row_pessoal_supervisor 	= @mysqli_fetch_assoc($rs_pessoal_supervisor)) {
	
	$chapa_superior 			= $row_pessoal_supervisor['sup_chapa'];
	$nome_supervisor 			= $row_pessoal_supervisor['sup_nome'];
	$emailSuperiorSupervisor	= $row_pessoal_supervisor['sup_email'];
	$uf 						= $row_pessoal_supervisor['fun_filial'];

	if ($row_pessoal_supervisor['sup_email']) {
	
		$Sql = "SELECT a1.*, t1.* FROM modulo_ponto.tbl_abono a1 
					INNER JOIN modulo_ponto.tbl_abono_tipo t1 
						ON a1.abt_id = t1.abt_id 
					INNER JOIN modulo_ponto.tbl_funcionario_ponto p1 
						ON a1.fun_chapa = p1.fun_chapa 
				WHERE p1.fpo_supervisor = '$chapa_superior'";
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
					p1.fpo_email_superior, f1.fun_filial, ep.exp_descricao AS horario_rm, p1.fpo_horario_trabalho AS horario_realizado,
					IF('$data' BETWEEN f1.fun_ferias_inicio AND f1.fun_ferias_fim,'FERIAS',NULL) AS ferias
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
					AND (p1.fpo_bate_ponto = 'sim' OR p1.fpo_bate_ponto IS NULL OR p1.fpo_bate_ponto = '')
		 			AND f1.fun_filial LIKE '%$uf%'
		 			AND f1.sit_id <> 'D' AND f1.sit_id <> 'I' AND f1.fun_codtipo <> 'A' AND f1.fun_codtipo <> 'D'
		 			AND p1.fpo_tipo_ponto <> 'dimep'
		 			AND p1.fpo_tipo_empregado = 'E'
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
			$vetFuncionarios[$row_relatorio['fun_chapa']]['ferias'] 			= $row_relatorio['ferias'];
			$vetFuncionarios[$row_relatorio['fun_chapa']]['entrada']			= "-";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['saida'] 				= "-";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['atz'] 				= "00:00";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['atc'] 				= "00:00";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['hea'] 				= "00:00";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['hed'] 				= "00:00";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['je'] 				= "00:00";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['fun_filial']			= $row_relatorio['fun_filial'];
			$vetFuncionarios[$row_relatorio['fun_chapa']]['fpo_registro'] 		= $row_relatorio['fpo_registro'];
			$vetFuncionarios[$row_relatorio['fun_chapa']]['horario_rm'] 		= $row_relatorio['horario_rm'];
			$vetFuncionarios[$row_relatorio['fun_chapa']]['horario_realizado'] 	= $row_relatorio['horario_realizado'];
		}
		
		$Sql = "SELECT
					b1.pba_data, b1.pba_chapa,
					IF(IF(SUBSTR(f.fer_data,6),1,0) = 1 OR WEEKDAY('$data') = 6 OR a.abt_id = 13,b1.pba_je_entrada,IF(b1.pba_entrada = '00:00:00',b1.pba_je_entrada,b1.pba_entrada)) pba_entrada,
					IF(IF(SUBSTR(f.fer_data,6),1,0) = 1 OR WEEKDAY('$data') = 6 OR a.abt_id = 13,b1.pba_je_saida,IF(b1.pba_saida = '00:00:00',b1.pba_je_saida,b1.pba_saida)) pba_saida,
					IF(IF(SUBSTR(f.fer_data,6),1,0) = 1 OR WEEKDAY(b1.pba_data) = 6 OR a.abt_id = 13,IF(b1.pba_je_entrada,1,0),IF(b1.pba_entrada,1,0)) qtdEntrada,
					IF(IF(SUBSTR(f.fer_data,6),1,0) = 1 OR WEEKDAY(b1.pba_data) = 6 OR a.abt_id = 13,IF(b1.pba_je_saida,1,0),IF(b1.pba_saida,1,0)) qtdSaida,
					IF(IF(SUBSTR(f.fer_data,6),1,0) = 1 OR WEEKDAY(b1.pba_data) = 6 OR a.abt_id = 13,IF(b1.pba_je_entrada IS NULL AND b1.pba_je_saida IS NULL,1,0),IF(b1.pba_entrada IS NULL AND b1.pba_saida IS NULL,1,0)) semRegistros,
					b1.pba_atz, b1.pba_atc, b1.pba_hea, b1.pba_hed,
					b1.pba_je,
					f1.fun_nome, p1.fpo_registro
				FROM modulo_ponto.tbl_ponto_batido b1
					INNER JOIN modulo_ponto.tbl_funcionario_ponto p1 
						ON p1.fun_chapa = b1.pba_chapa 
					INNER JOIN sigo_integrado.tbl_rm_funcionario f1 
						ON f1.fun_chapa = b1.pba_chapa
					INNER JOIN sigo_integrado.tbl_rm_funcionario f2 
						ON f2.fun_chapa = p1.fpo_supervisor
					INNER JOIN sigo_integrado.tbl_rm_situacao sf 
						ON sf.sit_id = f1.sit_id
					LEFT JOIN tbl_feriado f
						ON SUBSTR(f.fer_data,6) = SUBSTR(b1.pba_data,6)
					LEFT JOIN tbl_abono a
						ON a.abo_data_ocorrido = b1.pba_data
				WHERE pba_data='$data'
					AND (p1.fpo_supervisor = '$chapa_superior' OR p1.fun_chapa = '$chapa_superior')
					AND (p1.fpo_bate_ponto = 'sim' OR p1.fpo_bate_ponto IS NULL OR p1.fpo_bate_ponto = '')
		 			AND f1.fun_filial = '$uf'
		 			AND f1.sit_id <> 'D' AND f1.sit_id <> 'I' AND f1.fun_codtipo <> 'A' AND f1.fun_codtipo <> 'D'
		 			AND p1.fpo_tipo_ponto <> 'dimep'
		 			AND p1.fpo_tipo_empregado = 'E'
		 		GROUP BY b1.pba_chapa
				ORDER BY pba_chapa, pba_data";
		//echo $Sql;exit;
		$rs_relatorio = @mysqli_query($conecta, $Sql) or die(mysqli_error($conecta));
/*		$tot7 = mysqli_num_rows($rs_relatorio);
		die('Entrou: ' . $tot7);*/
		while ($row_relatorio = @mysqli_fetch_assoc($rs_relatorio)) {
			$abono_id = LocalizaAbono($vetAbono,ConvertDataHoraMysql($row_relatorio['pba_data'],'normal'),trim($row_relatorio['pba_chapa']));
			if ($abono_id != "naoachou"){
				if($vetAbono[$abono_id]['turno'] == "ENTRADA"){
					$row_relatorio['pba_atz'] 	= "00:00";
					$row_relatorio['pba_hea'] 	= "00:00";
					$row_relatorio['pba_je'] 	= "00:00";
				}else if ($vetAbono[$abono_id]['turno'] == "SAIDA"){
					$row_relatorio['pba_atc'] 	= "00:00";
					$row_relatorio['pba_hed'] 	= "00:00";
					$row_relatorio['pba_je'] 	= "00:00";
				}else{
					$row_relatorio['pba_atz'] 	= "00:00";
					$row_relatorio['pba_hea'] 	= "00:00";
					$row_relatorio['pba_atc'] 	= "00:00";
					$row_relatorio['pba_hed'] 	= "00:00";
					$row_relatorio['pba_je'] 	= "00:00";
				}
			}
			$vetFuncionarios[trim($row_relatorio['pba_chapa'])]['data'] 	= $row_relatorio['pba_data'];
			$vetFuncionarios[trim($row_relatorio['pba_chapa'])]['entrada'] 	= $row_relatorio['pba_entrada'];
			$vetFuncionarios[trim($row_relatorio['pba_chapa'])]['saida']	= $row_relatorio['pba_saida'];
			$vetFuncionarios[trim($row_relatorio['pba_chapa'])]['atz'] 		= RetornaMinutoHora(RetornaMinutoHora($vetFuncionarios[trim($row_relatorio['pba_chapa'])]['atz']) + RetornaMinutoHora($row_relatorio['pba_atz']), "hora");
			$vetFuncionarios[trim($row_relatorio['pba_chapa'])]['atc'] 		= RetornaMinutoHora(RetornaMinutoHora($vetFuncionarios[trim($row_relatorio['pba_chapa'])]['atc']) + RetornaMinutoHora($row_relatorio['pba_atc']), "hora");
			$vetFuncionarios[trim($row_relatorio['pba_chapa'])]['hea'] 		= RetornaMinutoHora(RetornaMinutoHora($vetFuncionarios[trim($row_relatorio['pba_chapa'])]['hea']) + RetornaMinutoHora($row_relatorio['pba_hea']), "hora");
			$vetFuncionarios[trim($row_relatorio['pba_chapa'])]['hed'] 		= RetornaMinutoHora(RetornaMinutoHora($vetFuncionarios[trim($row_relatorio['pba_chapa'])]['hed']) + RetornaMinutoHora($row_relatorio['pba_hed']), "hora");
			$vetFuncionarios[trim($row_relatorio['pba_chapa'])]['je'] 		= RetornaMinutoHora(RetornaMinutoHora($row_relatorio['pba_je']),"hora");
		}
			
		//echo "<pre>";print_r($vetFuncionarios);
		$trs 			= "";
		$total_atz 		= 0;
		$total_atc 		= 0;
		$total_he_geral = 0;
		$total_email 	= 0;
		$estilo 		= "";
		$estilo 		= "";
		$texto 			= "";
		$assunto		= "";
		$rodape			= "";
		$cabecalho		= "";

		if ($vetFuncionarios) {
			foreach ($vetFuncionarios as $key => $registro) {
				$estilo == "tr_cor_cinza" ? $estilo = "tr_cor_branco" : $estilo = "tr_cor_cinza";	
			
				$total_he 				= RetornaMinutoHora(RetornaMinutoHora($registro['hea']) + RetornaMinutoHora($registro['hed']),"hora");
				$total_je 				= RetornaMinutoHora(RetornaMinutoHora($registro['je']),"hora");
				$total_debito 			= RetornaMinutoHora($registro['hea']) + RetornaMinutoHora($registro['hed']) + RetornaMinutoHora($registro['je']) - RetornaMinutoHora($registro['atz']) - RetornaMinutoHora($registro['atc']);
				
				$registro['entrada'] 	= ConverteHoraMysql($registro['entrada']);
				$registro['saida'] 		= ConverteHoraMysql($registro['saida']);
				
				$total_he_geral			+= RetornaMinutoHora($registro['hea']) + RetornaMinutoHora($registro['hed']);
				$total_je_geral			+= RetornaMinutoHora($registro['je']);
				$total_atc				+= RetornaMinutoHora($registro['atc']);
				$total_atz				+= RetornaMinutoHora($registro['atz']);
				
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
				empty($total_je) 		|| $total_je 		== "00:00" ? $total_je 		  = "-" : false;
				empty($total_debito) 	|| $total_debito 	== "00:00" ? $total_debito 	  = "-" : false;
				
				$horario = $registro['horario_rm'];
						
				if (!$horario or $horario == '/-/') {
					$horario = '-';
				}
				
				if ($registro['ferias']) {
					$status_rm = "FÉRIAS";
				}else {
					$status_rm = $registro['status_rm'];
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
						$valor_entradasaida
						<td>{$registro['atz']}</td>		
						<td>{$registro['atc']}</td>
						<td>$total_he</td>
						<td>$total_je</td>
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
							$valor_entradasaida
							<td>{$registro['atz']}</td>		
							<td>{$registro['atc']}</td>
							<td>$total_he</td>
							<td>$total_je</td>
							<td>$total_debito</td>
							<td>$horario</td>
						</tr>
EOF;
				}
				$total_email ++;
			}
		}
		
		$saldo_geral = ($total_je_geral + $total_he_geral) - ($total_atc - $total_atz);
		if($saldo_geral <0) { 
			$saldo_geral = "(" . RetornaMinutoHora($saldo_geral*(-1),"hora") . ")";    		
		}else{ 
			$saldo_geral = RetornaMinutoHora($saldo_geral,"hora");   		
		}
		
		$total_he_geral = RetornaMinutoHora($total_he_geral,"hora");
		$total_je_geral = RetornaMinutoHora($total_je_geral,"hora");
		$total_atc 		= RetornaMinutoHora($total_atc,"hora");
		$total_atz 		= RetornaMinutoHora($total_atz,"hora");
		
		if ($tipo_busca == "dia"){
			$campo_entradasaida = "
			<td>ENTRADA</td>
			<td>SAIDA</td>";
			$colspan_somatorio = 8;
		}else{
			$colspan_somatorio = 7;
		}
		
		$table = <<< EOF
		$estiloEmail
		<table class="box_relatorio" align="center" width="100%">
			<tr class="subcabecalho_tr">
				<td>CHAPA</td>
				<td>REGISTRO OI</td>
				<td>NOME</td>
				<td>CARGO</td>
				<td>SUPERIOR</td>
				<td>STATUS RM</td>
				$campo_entradasaida
				<td>ATRASO</td>
				<td>ANTEC.</td>
				<td>H.EXTRA</td>
				<td>J.EXTRA</td>
				<td>SALDO</td>
				<td>HORÁRIO RM</td>
			</tr>
			$trs
			<tr class="subcabecalho_tr">
				<td colspan="$colspan_somatorio" align="right">SOMAT&Oacute;RIO</td>
				<td>$total_atz</td>
				<td>$total_atc</td>
				<td>$total_he_geral</td>
				<td>$total_je_geral</td>
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
				$emailEnviado = EnviarEmail($row_pessoal_supervisor['sup_nome'],$emailSuperiorSupervisor, $assunto, $texto,"","sigo@telemont.com.br","SIGO TELEMONT","");
			echo '</span>';
			if ($emailEnviado) {
				if ($row_pessoal_supervisor['fun_filial'] == 'MG')
					$cont_mg_s ++;
				
				if ($row_pessoal_supervisor['fun_filial'] == 'ES')
					$cont_es_s ++;
			}else {
				if ($row_pessoal_supervisor['fun_filial'] == 'MG'){
					$cont_nao_enviador_s_mg ++;
					$detalha_nao_enviados_s_mg .= $row_pessoal_supervisor['sup_nome']."<br>";
				}
				if ($row_pessoal_supervisor['fun_filial'] == 'ES'){
					$cont_nao_enviador_s_es ++;
					$detalha_nao_enviados_s_es .= $row_pessoal_supervisor['sup_nome']."<br>";
				}
			}
		}
	}else {
		if ($row_pessoal_supervisor['fun_filial'] == 'MG')
			$cont_nao_enviador_s_mg ++;
		
		if ($row_pessoal_supervisor['fun_filial'] == 'ES')
			$cont_nao_enviador_s_es ++;
	}
}

$Sql 							= "select pc.* from tbl_pessoal_coordenador pc
										inner join sigo_integrado.tbl_rm_funcionario rmf
											on (pc.cor_chapa = rmf.fun_chapa)
										inner join tbl_funcionario_ponto fp
											on (pc.cor_chapa = fp.fun_chapa)
										where rmf.fun_filial in('MG','ES')
											AND rmf.sit_id <> 'D' AND rmf.sit_id <> 'I' AND rmf.fun_codtipo <> 'A' AND rmf.fun_codtipo <> 'D'
											AND fp.fpo_tipo_ponto <> 'dimep'
											AND fp.fpo_tipo_empregado = 'E'
										order by
											rmf.fun_filial";
$rs_pessoal_coordenador 			= @mysqli_query($conecta, $Sql) or die(mysqli_error($conecta));

while ($row_pessoal_coordenador 	= @mysqli_fetch_assoc($rs_pessoal_coordenador)) {
	
	$chapa_superior 			= $row_pessoal_coordenador['cor_chapa'];
	$nome_coordenador 			= $row_pessoal_coordenador['cor_nome'];
	$emailSuperiorCoordenador	= $row_pessoal_coordenador['cor_email'];
	$uf 						= $row_pessoal_coordenador['fun_filial'];

	if ($row_pessoal_coordenador['cor_email']) {
	
		$Sql = "SELECT a1.*, t1.* FROM modulo_ponto.tbl_abono a1 
					INNER JOIN modulo_ponto.tbl_abono_tipo t1 
						ON a1.abt_id = t1.abt_id 
					INNER JOIN modulo_ponto.tbl_funcionario_ponto p1 
						ON a1.fun_chapa = p1.fun_chapa 
				WHERE p1.fpo_supervisor = '$chapa_superior'";
		$rs_abono = @mysqli_query($conecta, $Sql) or die(mysqli_error($conecta));
		while ($row_abono = @mysqli_fetch_assoc($rs_abono)) {
			$vetAbono[$row_abono['abo_id']]['chapa'] 	= $row_abono['fun_chapa'];
			$vetAbono[$row_abono['abo_id']]['inicio'] 	= str_replace("-","",$row_abono['abo_inicio']);
			$vetAbono[$row_abono['abo_id']]['fim'] 		= str_replace("-","",$row_abono['abo_fim']);
			$vetAbono[$row_abono['abo_id']]['resumo'] 	= $row_abono['abt_resumo'];
			$vetAbono[$row_abono['abo_id']]['id'] 		= $row_abono['abt_id'];
			$vetAbono[$row_abono['abo_id']]['turno'] 	= $row_abono['abo_turno'];
		}
		
		$vetFuncionarios = array();
		
		$trs = "";
	
		$Sql = "SELECT p1.fun_chapa, f1.fun_nome as nome_funcionario, f2.fun_nome as nome_supervisor, c1.car_descricao, sf.sit_descricao, 
					p1.fpo_email_superior, f1.fun_filial, ep.exp_descricao AS horario_rm, p1.fpo_horario_trabalho AS horario_realizado,
					IF('$data' BETWEEN f1.fun_ferias_inicio AND f1.fun_ferias_fim,'FERIAS',NULL) AS ferias
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
					AND (p1.fpo_bate_ponto = 'sim' OR p1.fpo_bate_ponto IS NULL OR p1.fpo_bate_ponto = '')
		 			AND f1.fun_filial LIKE '%$uf%'
		 			AND f1.sit_id <> 'D' AND f1.sit_id <> 'I' AND f1.fun_codtipo <> 'A' AND f1.fun_codtipo <> 'D'
		 			AND p1.fpo_tipo_ponto <> 'dimep'
		 			AND p1.fpo_tipo_empregado = 'E'
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
			$vetFuncionarios[$row_relatorio['fun_chapa']]['ferias'] 			= $row_relatorio['ferias'];
			$vetFuncionarios[$row_relatorio['fun_chapa']]['entrada']			= "-";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['saida'] 				= "-";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['atz'] 				= "00:00";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['atc'] 				= "00:00";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['hea'] 				= "00:00";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['hed'] 				= "00:00";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['je'] 				= "00:00";
			$vetFuncionarios[$row_relatorio['fun_chapa']]['fun_filial']			= $row_relatorio['fun_filial'];
			$vetFuncionarios[$row_relatorio['fun_chapa']]['fpo_registro'] 		= $row_relatorio['fpo_registro'];
			$vetFuncionarios[$row_relatorio['fun_chapa']]['horario_rm'] 		= $row_relatorio['horario_rm'];
			$vetFuncionarios[$row_relatorio['fun_chapa']]['horario_realizado'] 	= $row_relatorio['horario_realizado'];
		}
		
		$Sql = "SELECT
					b1.pba_data, b1.pba_chapa,
					IF(IF(SUBSTR(f.fer_data,6),1,0) = 1 OR WEEKDAY('$data') = 6 OR a.abt_id = 13,b1.pba_je_entrada,IF(b1.pba_entrada = '00:00:00',b1.pba_je_entrada,b1.pba_entrada)) pba_entrada,
					IF(IF(SUBSTR(f.fer_data,6),1,0) = 1 OR WEEKDAY('$data') = 6 OR a.abt_id = 13,b1.pba_je_saida,IF(b1.pba_saida = '00:00:00',b1.pba_je_saida,b1.pba_saida)) pba_saida,
					IF(IF(SUBSTR(f.fer_data,6),1,0) = 1 OR WEEKDAY(b1.pba_data) = 6 OR a.abt_id = 13,IF(b1.pba_je_entrada,1,0),IF(b1.pba_entrada,1,0)) qtdEntrada,
					IF(IF(SUBSTR(f.fer_data,6),1,0) = 1 OR WEEKDAY(b1.pba_data) = 6 OR a.abt_id = 13,IF(b1.pba_je_saida,1,0),IF(b1.pba_saida,1,0)) qtdSaida,
					IF(IF(SUBSTR(f.fer_data,6),1,0) = 1 OR WEEKDAY(b1.pba_data) = 6 OR a.abt_id = 13,IF(b1.pba_je_entrada IS NULL AND b1.pba_je_saida IS NULL,1,0),IF(b1.pba_entrada IS NULL AND b1.pba_saida IS NULL,1,0)) semRegistros,
					b1.pba_atz, b1.pba_atc, b1.pba_hea, b1.pba_hed,
					b1.pba_je,
					f1.fun_nome, p1.fpo_registro
				FROM modulo_ponto.tbl_ponto_batido b1
					INNER JOIN modulo_ponto.tbl_funcionario_ponto p1 
						ON p1.fun_chapa = b1.pba_chapa 
					INNER JOIN sigo_integrado.tbl_rm_funcionario f1 
						ON f1.fun_chapa = b1.pba_chapa
					INNER JOIN sigo_integrado.tbl_rm_funcionario f2 
						ON f2.fun_chapa = p1.fpo_supervisor
					INNER JOIN sigo_integrado.tbl_rm_situacao sf 
						ON sf.sit_id = f1.sit_id
					LEFT JOIN tbl_feriado f
						ON SUBSTR(f.fer_data,6) = SUBSTR(b1.pba_data,6)
					LEFT JOIN tbl_abono a
						ON a.abo_data_ocorrido = b1.pba_data
				WHERE pba_data='$data'
					AND (p1.fpo_supervisor = '$chapa_superior' OR p1.fun_chapa = '$chapa_superior')
					AND (p1.fpo_bate_ponto = 'sim' OR p1.fpo_bate_ponto IS NULL OR p1.fpo_bate_ponto = '')
		 			AND f1.fun_filial = '$uf'
		 			AND f1.sit_id <> 'D' AND f1.sit_id <> 'I' AND f1.fun_codtipo <> 'A' AND f1.fun_codtipo <> 'D'
		 			AND p1.fpo_tipo_ponto <> 'dimep'
		 			AND p1.fpo_tipo_empregado = 'E'
		 		GROUP BY b1.pba_chapa
				ORDER BY pba_chapa, pba_data";
		//echo $Sql;exit;
		$rs_relatorio = @mysqli_query($conecta, $Sql) or die(mysqli_error($conecta));
/*		$tot7 = mysqli_num_rows($rs_relatorio);
		die('Entrou: ' . $tot7);*/
		while ($row_relatorio = @mysqli_fetch_assoc($rs_relatorio)) {
			$abono_id = LocalizaAbono($vetAbono,ConvertDataHoraMysql($row_relatorio['pba_data'],'normal'),trim($row_relatorio['pba_chapa']));
			if ($abono_id != "naoachou"){
				if($vetAbono[$abono_id]['turno'] == "ENTRADA"){
					$row_relatorio['pba_atz'] 	= "00:00";
					$row_relatorio['pba_hea'] 	= "00:00";
					$row_relatorio['pba_je'] 	= "00:00";
				}else if ($vetAbono[$abono_id]['turno'] == "SAIDA"){
					$row_relatorio['pba_atc'] 	= "00:00";
					$row_relatorio['pba_hed'] 	= "00:00";
					$row_relatorio['pba_je'] 	= "00:00";
				}else{
					$row_relatorio['pba_atz'] 	= "00:00";
					$row_relatorio['pba_hea'] 	= "00:00";
					$row_relatorio['pba_atc'] 	= "00:00";
					$row_relatorio['pba_hed'] 	= "00:00";
					$row_relatorio['pba_je'] 	= "00:00";
				}
			}
			$vetFuncionarios[trim($row_relatorio['pba_chapa'])]['data'] 	= $row_relatorio['pba_data'];
			$vetFuncionarios[trim($row_relatorio['pba_chapa'])]['entrada'] 	= $row_relatorio['pba_entrada'];
			$vetFuncionarios[trim($row_relatorio['pba_chapa'])]['saida']	= $row_relatorio['pba_saida'];
			$vetFuncionarios[trim($row_relatorio['pba_chapa'])]['atz'] 		= RetornaMinutoHora(RetornaMinutoHora($vetFuncionarios[trim($row_relatorio['pba_chapa'])]['atz']) + RetornaMinutoHora($row_relatorio['pba_atz']), "hora");
			$vetFuncionarios[trim($row_relatorio['pba_chapa'])]['atc'] 		= RetornaMinutoHora(RetornaMinutoHora($vetFuncionarios[trim($row_relatorio['pba_chapa'])]['atc']) + RetornaMinutoHora($row_relatorio['pba_atc']), "hora");
			$vetFuncionarios[trim($row_relatorio['pba_chapa'])]['hea'] 		= RetornaMinutoHora(RetornaMinutoHora($vetFuncionarios[trim($row_relatorio['pba_chapa'])]['hea']) + RetornaMinutoHora($row_relatorio['pba_hea']), "hora");
			$vetFuncionarios[trim($row_relatorio['pba_chapa'])]['hed'] 		= RetornaMinutoHora(RetornaMinutoHora($vetFuncionarios[trim($row_relatorio['pba_chapa'])]['hed']) + RetornaMinutoHora($row_relatorio['pba_hed']), "hora");
			$vetFuncionarios[trim($row_relatorio['pba_chapa'])]['je'] 		= RetornaMinutoHora(RetornaMinutoHora($row_relatorio['pba_je']),"hora");
		}
			
		//echo "<pre>";print_r($vetFuncionarios);
		$trs 			= "";
		$total_atz 		= 0;
		$total_atc 		= 0;
		$total_he_geral = 0;
		$total_email 	= 0;
		$estilo 		= "";
		$estilo 		= "";
		$texto 			= "";
		$assunto		= "";
		$rodape			= "";
		$cabecalho		= "";
		if ($vetFuncionarios) {
			foreach ($vetFuncionarios as $key => $registro) {
				$estilo == "tr_cor_cinza" ? $estilo = "tr_cor_branco" : $estilo = "tr_cor_cinza";	
			
				$total_he 				= RetornaMinutoHora(RetornaMinutoHora($registro['hea']) + RetornaMinutoHora($registro['hed']),"hora");
				$total_je 				= RetornaMinutoHora(RetornaMinutoHora($registro['je']),"hora");
				$total_debito 			= RetornaMinutoHora($registro['hea']) + RetornaMinutoHora($registro['hed']) + RetornaMinutoHora($registro['je']) - RetornaMinutoHora($registro['atz']) - RetornaMinutoHora($registro['atc']);
				
				$registro['entrada'] 	= ConverteHoraMysql($registro['entrada']);
				$registro['saida'] 		= ConverteHoraMysql($registro['saida']);
				
				$total_he_geral			+= RetornaMinutoHora($registro['hea']) + RetornaMinutoHora($registro['hed']);
				$total_je_geral			+= RetornaMinutoHora($registro['je']);
				$total_atc				+= RetornaMinutoHora($registro['atc']);
				$total_atz				+= RetornaMinutoHora($registro['atz']);
				
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
				empty($total_je) 		|| $total_je 		== "00:00" ? $total_je 		  = "-" : false;
				empty($total_debito) 	|| $total_debito 	== "00:00" ? $total_debito 	  = "-" : false;
				
				$horario = $registro['horario_rm'];
						
				if (!$horario or $horario == '/-/') {
					$horario = '-';
				}
				
				if ($registro['ferias']) {
					$status_rm = "FÉRIAS";
				}else {
					$status_rm = $registro['status_rm'];
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
						$valor_entradasaida
						<td>{$registro['atz']}</td>		
						<td>{$registro['atc']}</td>
						<td>$total_he</td>
						<td>$total_je</td>
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
							$valor_entradasaida
							<td>{$registro['atz']}</td>		
							<td>{$registro['atc']}</td>
							<td>$total_he</td>
							<td>$total_je</td>
							<td>$total_debito</td>
							<td>$horario</td>
						</tr>
EOF;
				}
				$total_email ++;
			}
		}
		
		$saldo_geral = ($total_je_geral + $total_he_geral) - ($total_atc - $total_atz);
		if($saldo_geral <0) { 
			$saldo_geral = "(" . RetornaMinutoHora($saldo_geral*(-1),"hora") . ")";    		
		}else{ 
			$saldo_geral = RetornaMinutoHora($saldo_geral,"hora");   		
		}
		
		$total_he_geral = RetornaMinutoHora($total_he_geral,"hora");
		$total_je_geral = RetornaMinutoHora($total_je_geral,"hora");
		$total_atc 		= RetornaMinutoHora($total_atc,"hora");
		$total_atz 		= RetornaMinutoHora($total_atz,"hora");
		
		if ($tipo_busca == "dia"){
			$campo_entradasaida = "
			<td>ENTRADA</td>
			<td>SAIDA</td>";
			$colspan_somatorio = 8;
		}else{
			$colspan_somatorio = 7;
		}
		
		$table = <<< EOF
		$estiloEmail
		<table class="box_relatorio" align="center" width="100%">
			<tr class="subcabecalho_tr">
				<td>CHAPA</td>
				<td>REGISTRO OI</td>
				<td>NOME</td>
				<td>CARGO</td>
				<td>SUPERIOR</td>
				<td>STATUS RM</td>
				$campo_entradasaida
				<td>ATRASO</td>
				<td>ANTEC.</td>
				<td>H.EXTRA</td>
				<td>J.EXTRA</td>
				<td>SALDO</td>
				<td>HORÁRIO RM</td>
			</tr>
			$trs
			<tr class="subcabecalho_tr">
				<td colspan="$colspan_somatorio" align="right">SOMAT&Oacute;RIO</td>
				<td>$total_atz</td>
				<td>$total_atc</td>
				<td>$total_he_geral</td>
				<td>$total_je_geral</td>
				<td>$saldo_geral</td>
				<td></td>
			</tr>
		</table>
EOF;

		$cabecalho = 'Prezado, '.$nome_coordenador.'<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Segue acompanhamento da jornada de trabalho dos empregados que estão sob sua supervisão';
		
		$rodape = '<b>Att<br>E-mail automatico do SIGO. Favor nao responder este e-mail!<b>';
	
		$texto = "$cabecalho<br><br>".$table.$rodape;
		
		$assunto = 'STATUS DIÁRIO - '.$row_pessoal_coordenador['fun_filial'];
		
		if ($emailSuperiorCoordenador){
			echo '<span style="display:none;">';
				$emailEnviado = EnviarEmail($row_pessoal_coordenador['cor_nome'],$emailSuperiorCoordenador, $assunto, $texto,"","sigo@telemont.com.br","SIGO TELEMONT","");
			echo '</span>';
			if ($emailEnviado) {
				if ($row_pessoal_coordenador['fun_filial'] == 'MG')
					$cont_mg_c ++;
				
				if ($row_pessoal_coordenador['fun_filial'] == 'ES')
					$cont_es_c ++;
			}else {
				if ($row_pessoal_coordenador['fun_filial'] == 'MG'){
					$cont_nao_enviador_c_mg ++;
					$detalha_nao_enviados_c_mg .= $row_pessoal_coordenador['cor_nome']."<br>";
				}
				if ($row_pessoal_coordenador['fun_filial'] == 'ES'){
					$cont_nao_enviador_c_es ++;
					$detalha_nao_enviados_c_es .= $row_pessoal_coordenador['cor_nome']."<br>";
				}
			}
		}
	}else {
		if ($row_pessoal_coordenador['fun_filial'] == 'MG')
			$cont_nao_enviador_c_mg ++;
		
		if ($row_pessoal_coordenador['fun_filial'] == 'ES')
			$cont_nao_enviador_c_es ++;
	}
}

$total_enviados_mg 					= $cont_mg_e + $cont_mg_s + $cont_mg_c;
$total_enviados_es 					= $cont_es_e + $cont_es_s + $cont_es_c;

$total_nao_enviados_mg 				= $cont_nao_enviador_e_mg + $cont_nao_enviador_s_mg + $cont_nao_enviador_c_mg;
$total_nao_enviados_es 				= $cont_nao_enviador_e_es + $cont_nao_enviador_s_es + $cont_nao_enviador_c_es;

$total_geral 						= $total_enviados_mg + $total_enviados_es;

$total_encarregado 					= $cont_mg_e + $cont_es_e;
$total_supervisor					= $cont_mg_s + $cont_es_s;
$total_coordenador					= $cont_mg_c + $cont_es_c;

$total_encarregado_nao_enviados		= $cont_nao_enviador_e_mg + $cont_nao_enviador_e_es;

$total_supervisor_nao_enviados		= $cont_nao_enviador_s_mg + $cont_nao_enviador_s_es;

$total_coordenador_nao_enviados		= $cont_nao_enviador_c_mg + $cont_nao_enviador_c_es;

$total_geral_nao_enviados 			= $total_encarregado_nao_enviados + $total_supervisor_nao_enviados + $total_coordenador_nao_enviados;

?>

<body onload="javascript: setTimeout('getSecs()',1000);"  >  

	<h1 class="cabecalho" align="center" > E-MAILS DE ACOMPANHAMENTO DA JORNADA DE TRABALHO DOS EMPREGADOS - AREA 1 - MG e ES</h1>

	<br />
<?php
$tableResultado = <<< EOF
	<table class="box_relatorio" align="center" width="800px">
		<tr class="cabecalho_tr">
			<td colspan="11" nowrap><div style="color:#FFF;font-weight:bold;font-size: 16px;" id="clock1"></div></td>
		</tr>

		<tr class="cabecalho_tr">
			<td colspan="5" nowrap><span class="cabecalho_tr">E-MAILS ENVIADOS POR FILIAL</span></td>
			<td colspan="6" nowrap><span class="cabecalho_tr">E-MAILS NÃO ENVIADOS POR FILIAL</span></td>
		</tr>
		<tr class="subcabecalho_tr">
			<td>FILIAL</td>
			<td>COORDENADOR</td>
			<td>SUPERVISOR</td>
			<td>ENCARREGADO</td>
			<td>TOTAL PARCIAL</td>
			<td>COORDENADOR</td>
			<td>SUPERVISOR</td>
			<td>ENCARREGADO</td>
			<td colspan="3">TOTAL PARCIAL</td>
		</tr>
		<tr class="tr_cor_branca" align="center">
			<td>MG</td>
			<td>$cont_mg_c</td>
			<td>$cont_mg_s</td>
			<td>$cont_mg_e</td>
			<td>$total_enviados_mg</td>
			<td>$cont_nao_enviador_c_mg</td>
			<td>$cont_nao_enviador_s_mg</td>
			<td>$cont_nao_enviador_e_mg</td>
			<td colspan="3">$total_nao_enviados_mg</td>
		</tr>
		<tr class="tr_cor_cinsa" align="center">
			<td>ES</td>
			<td>$cont_es_c</td>
			<td>$cont_es_s</td>
			<td>$cont_es_e</td>
			<td>$total_enviados_es</td>
			<td>$cont_nao_enviador_c_es</td>
			<td>$cont_nao_enviador_s_es</td>
			<td>$cont_nao_enviador_e_es</td>
			<td colspan="3">$total_nao_enviados_es</td>
		</tr>
		<tr class="subcabecalho_tr" align="center">
			<td>TOTAL GERAL</td>
			<td>$total_coordenador</td>
			<td>$total_supervisor</td>
			<td>$total_encarregado</td>
			<td>$total_geral</td>
			<td>$total_coordenador_nao_enviados</td>
			<td>$total_supervisor_nao_enviados</td>
			<td>$total_encarregado_nao_enviados</td>
			<td colspan="3">$total_geral_nao_enviados</td>
		</tr>
	</table>
EOF;

if ($detalha_nao_enviados_c_mg or $detalha_nao_enviados_s_mg or $detalha_nao_enviados_e_mg) {
	$tabela_nao_enviados_mg = <<< EOF
		<br>
		<table class="box_relatorio" align="center" width="800px">
			<tr class="cabecalho_tr">
				<td colspan="3" nowrap><span class="cabecalho_tr">LISTAGEM DOS E-MAILS NÃO ENVIADOS</span></td>
			</tr>
			<tr class="subcabecalho_tr" align="center">
				<td>COORDENADOR</td>
				<td>SUPERVISOR</td>
				<td>ENCARREGADO</td>
			</tr>
			<tr class="tr_cor_branca">
				<td valign="top">$detalha_nao_enviados_c_mg</td>
				<td valign="top">$detalha_nao_enviados_s_mg</td>
				<td valign="top">$detalha_nao_enviados_e_mg</td>
			</tr>
		</table>
EOF;
}

if ($detalha_nao_enviados_c_es or $detalha_nao_enviados_s_es or $detalha_nao_enviados_e_es) {
	$tabela_nao_enviados_es = <<< EOF
		<br>
		<table class="box_relatorio" align="center" width="800px">
			<tr class="cabecalho_tr">
				<td colspan="3" nowrap><span class="cabecalho_tr">LISTAGEM DOS E-MAILS NÃO ENVIADOS</span></td>
			</tr>
			<tr class="subcabecalho_tr" align="center">
				<td>COORDENADOR</td>
				<td>SUPERVISOR</td>
				<td>ENCARREGADO</td>
			</tr>
			<tr class="tr_cor_branca">
				<td valign="top">$detalha_nao_enviados_c_es</td>
				<td valign="top">$detalha_nao_enviados_s_es</td>
				<td valign="top">$detalha_nao_enviados_e_es</td>
			</tr>
		</table>
EOF;
}

$tableResultadoEmailMG = <<< EOF
	$estiloEmail
	<table class="box_relatorio" align="center" width="800px">
		<tr class="cabecalho_tr">
			<td colspan="5" nowrap><span class="cabecalho_tr">E-MAILS ENVIADOS POR FILIAL</span></td>
			<td colspan="6" nowrap><span class="cabecalho_tr">E-MAILS NÃO ENVIADOS POR FILIAL</span></td>
		</tr>
		<tr class="subcabecalho_tr">
			<td>FILIAL</td>
			<td>COORDENADOR</td>
			<td>SUPERVISOR</td>
			<td>ENCARREGADO</td>
			<td>TOTAL PARCIAL</td>
			<td>COORDENADOR</td>
			<td>SUPERVISOR</td>
			<td>ENCARREGADO</td>
			<td colspan="3">TOTAL PARCIAL</td>
		</tr>
		<tr class="tr_cor_branca" align="center">
			<td>MG</td>
			<td>$cont_mg_c</td>
			<td>$cont_mg_s</td>
			<td>$cont_mg_e</td>
			<td>$total_enviados_mg</td>
			<td>$cont_nao_enviador_c_mg</td>
			<td>$cont_nao_enviador_s_mg</td>
			<td>$cont_nao_enviador_e_mg</td>
			<td colspan="3">$total_nao_enviados_mg</td>
		</tr>
		<tr class="subcabecalho_tr" align="center">
			<td>TOTAL GERAL</td>
			<td>$cont_mg_c</td>
			<td>$cont_mg_s</td>
			<td>$cont_mg_e</td>
			<td>$total_enviados_mg</td>
			<td>$cont_nao_enviador_c_mg</td>
			<td>$cont_nao_enviador_s_mg</td>
			<td>$cont_nao_enviador_e_mg</td>
			<td colspan="3">$total_nao_enviados_mg</td>
		</tr>
	</table>
	$tabela_nao_enviados_mg
EOF;

$tableResultadoEmailES = <<< EOF
	$estiloEmail
	<table class="box_relatorio" align="center" width="800px">
		<tr class="cabecalho_tr">
			<td colspan="5" nowrap><span class="cabecalho_tr">E-MAILS ENVIADOS POR FILIAL</span></td>
			<td colspan="6" nowrap><span class="cabecalho_tr">E-MAILS NÃO ENVIADOS POR FILIAL</span></td>
		</tr>
		<tr class="subcabecalho_tr">
			<td>FILIAL</td>
			<td>COORDENADOR</td>
			<td>SUPERVISOR</td>
			<td>ENCARREGADO</td>
			<td>TOTAL PARCIAL</td>
			<td>COORDENADOR</td>
			<td>SUPERVISOR</td>
			<td>ENCARREGADO</td>
			<td colspan="3">TOTAL PARCIAL</td>
		</tr>
		<tr class="tr_cor_cinsa" align="center">
			<td>ES</td>
			<td>$cont_es_c</td>
			<td>$cont_es_s</td>
			<td>$cont_es_e</td>
			<td>$total_enviados_es</td>
			<td>$cont_nao_enviador_c_es</td>
			<td>$cont_nao_enviador_s_es</td>
			<td>$cont_nao_enviador_e_es</td>
			<td colspan="3">$total_nao_enviados_es</td>
		</tr>
		<tr class="subcabecalho_tr" align="center">
			<td>TOTAL GERAL</td>
			<td>$cont_es_c</td>
			<td>$cont_es_s</td>
			<td>$cont_es_e</td>
			<td>$total_enviados_es</td>
			<td>$cont_nao_enviador_c_es</td>
			<td>$cont_nao_enviador_s_es</td>
			<td>$cont_nao_enviador_e_es</td>
			<td colspan="3">$total_nao_enviados_es</td>
		</tr>
	</table>
	$tabela_nao_enviados_es
EOF;
echo $tableResultado;

$sql_responsaveis = "select * 
					 from tbl_responsavel_ponto";
$rs_responsaveis = mysqli_query($conecta, $sql_responsaveis);
while ($row_responsaveis = mysqli_fetch_assoc($rs_responsaveis)) {
	if ($row_responsaveis['rp_filial'] == 'MG') {
		$textoEnvia 		= $tableResultadoEmailMG;
		$filialPorExtenso 	= " de MINAS GERAIS";
	}
	if ($row_responsaveis['rp_filial'] == 'ES') {
		$textoEnvia 		= $tableResultadoEmailES;
		$filialPorExtenso 	= " do ESPIRITO SANTO";
	}
	
	$cabecalhoResultado 	= 'Prezado, '.$row_responsaveis['rp_nome'].'<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Segue acompanhamento da jornada de trabalho dos funcionários'.$filialPorExtenso.'.';
	$rodapeResultado 		= '<b>Att<br>E-mail automatico do SIGO. Favor nao responder este e-mail!<b>';
	$assuntoResultado 		= 'STATUS DIÁRIO - '.$row_responsaveis['rp_filial'];
	
	echo '<span style="display:none;">';
		$textoResultado 	= "$cabecalhoResultado<br><br>".$textoEnvia."<br>".$rodapeResultado;
		$emailEnviado 		= EnviarEmail($row_responsaveis['rp_nome'],$row_responsaveis['rp_email'], $assuntoResultado, $textoResultado,"","sigo@telemont.com.br","SIGO TELEMONT","");
	echo '</span>';
	
	if ($emailEnviado) {
		if ($row_responsaveis['rp_filial'] == 'MG') {
			$enviadasResponsaveisMG++;
		}
		if ($row_responsaveis['rp_filial'] == 'ES') {
			$enviadasResponsaveisES++;
		}
	}else {
		if ($row_responsaveis['rp_filial'] == 'MG') {
			$naoEnviadasResponsaveisMG++;
		}
		if ($row_responsaveis['rp_filial'] == 'ES') {
			$naoEnviadasResponsaveisES++;
		}
	}
	
	$cabecalhoResultado		= '';
	$rodapeResultado		= '';
	$assuntoResultado		= '';
	$textoResultado			= '';
	$filialPorExtenso		= '';
	$textoEnvia				= '';
}

$totalEnviadas				= $enviadasResponsaveisMG + $enviadasResponsaveisES;
$totalNaoEnviadas			= $naoEnviadasResponsaveisMG + $naoEnviadasResponsaveisES;

$tableResultadoResponsaveis = <<< EOF
	<table class="box_relatorio" align="center" width="500px">
		<tr class="cabecalho_tr">
			<td colspan="3" nowrap><span class="cabecalho_tr">E-MAILS PARA OS RESPONSÁVEIS</span></td>
		</tr>
		<tr class="subcabecalho_tr">
			<td>NOME</td>
			<td>ENVIADOS</td>
			<td>NÃO ENVIADOS</td>
		</tr>
		<tr class="tr_cor_branca" align="center">
			<td>MG</td>
			<td>$enviadasResponsaveisMG</td>
			<td>$naoEnviadasResponsaveisMG</td>
		</tr>
		<tr class="tr_cor_cinsa" align="center">
			<td>ES</td>
			<td>$enviadasResponsaveisES</td>
			<td>$naoEnviadasResponsaveisES</td>
		</tr>
		<tr class="subcabecalho_tr" align="center">
			<td>TOTAL GERAL</td>
			<td>$totalEnviadas</td>
			<td>$totalNaoEnviadas</td>
		</tr>
	</table>
EOF;

echo "<br>".$tableResultadoResponsaveis;
?>
</body>
</html>
<?php mysqli_close($conecta); ?>