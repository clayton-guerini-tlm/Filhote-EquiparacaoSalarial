<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

include "funcoes.php";
include_once "banco.php";

$login 					= $_POST['login'];
$senha 					= $_POST['senha'];

$conecta 				= RetornaConexaoMysql('local', 'sigo_integrado');

$session_id 			= strtoupper($_POST['login']);//Verificar_id_session_php();

$sql_consulta_usuario	= "SELECT * FROM tbl_usuario WHERE usr_usuario = '$login'";
//$rs_consulta_usuario	= @mysqli_query($conecta, $sql_consulta_usuario) or die('ERROR: ');
//$row_consulta_usuario	= @mysqli_fetch_assoc($rs_consulta_usuario);

$rs_consulta_usuario	= $conecta->query($sql_consulta_usuario) or die('ERROR: ');
$row_consulta_usuario	= $rs_consulta_usuario-> fetch_assoc();

//verifica se é visitante
$sqlVis = "SELECT * FROM tbl_usuario WHERE usr_usuario = '$login' ";
$rs = $conecta->query($sqlVis);
$row2 = $rs->fetch_assoc();

if(strlen($row2['fun_id']) > 6) {
    $Sql = "SELECT  
		        u1.usr_id as ID_USUARIO, 
		        u1.usr_usuario as USUARIO, 
		        u1.usr_email as EMAIL, 
		        u1.usr_senha, 
		        u1.usr_atuacao_old AS ATUACAO_OLD, 
		        u1.usr_senha_padrao as SENHA_PADRAO, 
		        u1.gru_id AS ID_GRUPO, 
		        u1.usr_nome_visitante as NOME, 
		        u1.usr_filial AS FILIAL, 
		        u1.fun_id AS ID_FUNCIONARIO,  
		        u1.usr_status,
		        '' AS SISTEMA_USUARIO  
        	FROM tbl_usuario u1  
        	WHERE u1.usr_usuario = '$login' ";  
}else{
    $Sql = "SELECT  
				u1.usr_id as ID_USUARIO, 
				u1.usr_usuario as USUARIO, 
				u1.usr_email as EMAIL, 
				u1.usr_senha, 
				u1.usr_atuacao_old AS ATUACAO_OLD, 
				u1.usr_senha_padrao as SENHA_PADRAO, 
				u1.gru_id AS ID_GRUPO, 
				u1.usr_nome_visitante as NOME, 
				u1.usr_filial AS FILIAL, 
				u1.fun_id AS ID_FUNCIONARIO,  
				u1.usr_status, 
				u1.usr_codColigada,
				f1.fun_filial as FILIAL_ESTADO, 
				f1.fun_chapa as CHAPA, 
				'' AS SISTEMA_USUARIO, 
				f1.fun_codfilial as CODFILIAL 
			FROM tbl_usuario u1 
				INNER JOIN tbl_rm_funcionario f1 
					ON u1.fun_id=f1.fun_id 
			WHERE u1.usr_usuario = '$login' ";   
}

if($rs = $conecta->query($Sql)){
	if(($rs->num_rows)){
		
		$row_login = $rs->fetch_assoc();
		
		if($row_login['usr_status'] == "LIBERADO"){
			$senha_banco = $row_login['usr_senha'];
			$fun_id = $row_login['ID_FUNCIONARIO'];
			$usr_id = $row_login['ID_USUARIO'];
			$gru_id = $row_login['ID_GRUPO'];
			$usr_codColigada = $row_login['usr_codColigada'];
		
			if(md5($senha) == $senha_banco){

				if($row_login['FILIAL'] == 'AREA1'){$row_login['FILIAL']='AREA1_MG';}
				$areas = explode('|', $row_login['FILIAL']);
				
				$where_areas = "";
				foreach($areas as $a){
					$where_areas .= "gru_area = '$a' OR ";
				}
				$where_areas = substr($where_areas, 0, -3);
				
				$grupos = explode('|',$row_login['ID_GRUPO']);
				
				$where_sistema = "";
				foreach($grupos as $g){
					$where_sistema.="gru_id = $g OR ";
				}					
							
				$where_sistema = substr($where_sistema,0,-3);
				//$Sql = "SELECT sis_id FROM tbl_grupo WHERE ($where_sistema) AND (gru_area='TODAS' OR $where_areas)";
                                //Só mostra os sistemas cujo o usuário possui acesso naquela área acessada.
				$Sql = "SELECT sis_id FROM tbl_grupo WHERE ($where_sistema) AND (gru_area='TODAS' OR gru_area = '$AREA_SIGO')";
                                
				$rs_sistema = $conecta->query($Sql) or die (mysqli_error($conecta).$Sql);
				while($row_sistema = $rs_sistema->fetch_assoc()){
					if (empty($row_login['SISTEMA_USUARIO'])){
						$row_login['SISTEMA_USUARIO'] = $row_sistema['sis_id'];
					}else{
						$row_login['SISTEMA_USUARIO'].= "|" . $row_sistema['sis_id'];
					}
				}

				$sis_tmp = explode("|",$row_login['SISTEMA_USUARIO']);
				$sis_tmp = array_unique($sis_tmp);
				$row_login['SISTEMA_USUARIO'] = implode("|",$sis_tmp);
				
				$hash = time();
				
				// Atualiza os dados do funcionário logado.
				$Sql = "UPDATE tbl_usuario 
							SET usr_ip_atual		= '{$_SERVER['REMOTE_ADDR']}', 
								usr_ultimo_acesso	= Now(), 
								usr_hash			= '$hash' 
						WHERE usr_id = '{$row_login['ID_USUARIO']}'";
				$conecta->query($Sql);
				
				
				// Insere um registro na tabela de log de acesso com os dados do proprio acesso, o id da sessao, tempo inicio, tempo final e o tempo atual da sessao.
				$Sql = "INSERT INTO tbl_log_acesso (usr_id, lga_ip, lga_dthr_acesso, lga_browser, 
													lga_session_id, lga_tempo_ini_sessao, 
													lga_tempo_fim_sessao, lga_tempo_atual_sessao) 
						VALUES ('{$row_login['ID_USUARIO']}', '{$_SERVER['REMOTE_ADDR']}', NOW(), '{$_SERVER['HTTP_USER_AGENT']}', 
								'{$session_id}', TIME_TO_SEC(TIME(NOW())), TIME_TO_SEC(TIME(NOW())) + 4320, TIME_TO_SEC(TIME(NOW())))";
				$conecta->query($Sql);
				
				$referer 					= $_SESSION['SIGO']['LOGIN']['REFERER'];
				$msg_login 					= $_SESSION['SIGO']['LOGIN']['MSG'];
				$_SESSION['SIGO'] 			= null;
				$_SESSION['SIGO']['AJAX'] 	= 1;					
				
				foreach ($row_login as $key => $r) {
					if($key != 'usr_senha' && $key != 'usr_status'){
						$_SESSION['SIGO']['ACESSO'][$key] = $r;
					}
				}

				$_SESSION['SIGO']['ACESSO']['IP_ATUAL'] 	  = $_SERVER['REMOTE_ADDR'];
				$_SESSION['SIGO']['ACESSO']['HASH'] 		  = $hash;
				$_SESSION['SIGO']['ACESSO']['AREA_SIGO'] 	  = $AREA_SIGO;
				$_SESSION['SIGO']['ACESSO']['PHPSESSID'] 	  = $session_id;
				$_COOKIE['registro_brt'] 					  = $session_id;
				$_SESSION['SIGO']['ACESSO']['ID_FUNCIONARIO'] = $fun_id;
				$_SESSION['SIGO']['ACESSO']['ID_USUARIO']	  = $usr_id;
				$_SESSION['SIGO']['ACESSO']['LOGIN_EMPRESA']  = $gru_id;
				$_SESSION['SIGO']['ACESSO']['CODCOLIGADA']    = $usr_codColigada;
	
				RetornaMenuSigoUtilizado();
				
				$ret = "logou";
			}else{
				$ret = "1";
			}
		}else{
			$ret = "8";
		}
	}else{
		$ret = "9";
	}
}else{
	$ret = "10";
}

if($ret == "logou"){
	/*if( !verifica_ultimo_update_senha($_SESSION['SIGO']['ACESSO']['CHAPA']) ){
		header("location: ./trocar_senha.php?msg=senha_expirada");
		exit();
	}*/

	header("location: ../sistema/index.php?mainapp=home&app=home");
        //header("location: ../principal.php");
	exit();
}else{
	header("location: ../?login={$login}&msg=$ret");
	exit();
}
mysqli_close($conecta);	

exit();
?>