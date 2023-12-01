<?php

$AREA_SIGO = "AREA1_MG";
$DIRETORIO_SIGO = "SIGO_MG";

function RetornaConexaoMysql($pc, $db, $forcar_conexao=false){
	
	//$forcar_conexao = false;
	
	// if(($_SERVER['HTTP_HOST'] == "localhost" || $_SERVER['HTTP_HOST'] == "127.0.0.1") && ! $forcar_conexao){
	// 	$pc = "local";
	// }
	//$pc = "serverdge";
	switch ($pc) {
		case 1900:
			$host = '172.30.200.6';
			$user = 'root';
			$pass = 'S1g027#,ZZ3FRR';
			break;
		
		case 2950:
			$host = '172.30.200.5';
			$user = 'root';
			$pass = 'S1g027#ZZ3FRR';
			break;
	
		case 2900:
			$host = '172.30.200.6';
			$user = 'root';
			$pass = 'S1g027#,ZZ3FRR';
			break;	
			
		case 'sigoaline':
			$host = '10.67.149.15';
			$user = 'walter';
			$pass = '448300';
			break;	
			
		case 'serveradsl':
			$host = '192.168.1.22';
			$user = 'root';
			$pass = 'qwerty';
			break;
			
		case 'serverdge':
			$host = '192.168.5.220';
			$user = 'dge';
			$pass = '@dge2011%_';
			break;

		case 'serverdge2':
			$host = '192.168.5.221';
			$user = 'dge';
			$pass = '@dge2011%_';
			break;

		case 'serverdge3':
			$host = '192.168.5.225';
			$user = 'root';
			$pass = '@dge2011%_';
			break;

		case 'serveres':
			$host = '192.168.5.222';
			$user = 'dge';
			$pass = '@dge2011%_';
			break;
		
		case 'serversp':
			$host = '172.30.200.36';
			$user = 'dge';
			$pass = 'dge@2011%_';
			break;

		case 'serversp2':
			$host = '172.18.5.144';
			$user = 'dge';
			$pass = 'dge@2011%_';
			break;

		case 'vm_adsl_dados':
			$host = '192.168.5.220';
			$user = 'apache_web';
			$pass = '09KJL09lkjIPOU90';
			break;		
	
		case 'serverrj':
			$host = '172.17.51.100';
			$user = 'dge';
			$pass = 'dge@2011%_';//Old
			//$pass_new = '@dge2011%_';
			break;
			
		case 'serversp':
			$host = '172.30.200.36';
			$user = 'dge';
			$pass = 'dge@2011%_';//Old
			//$pass_new = '@dge2011%_';
			break;			

		case 'seguro':
			$host = '192.168.5.223';
			$user = 'dge';
			$pass = '@dge2011%_';
			break;	

		case 'sigo_ponto':
			$host = '192.168.5.224';
			$user = 'root';
			$pass = '@dge2011%_';
			break;
			
		case 'sigo_ponto_conorte':
			$host = '172.30.200.8';
			$user = 'root';
			$pass = 'tmt@dmin@1975@2016';
			break;
		
		case 'sigo_homologacao':
			$host = '192.168.5.226';
			$user = 'dge';
			$pass = '@dge2011%_';
			break;

		case 'servermg':
			$host = '192.168.5.40';
			$user = 'sigo';
			$pass = 't3l3m0nt@31';
			break;

		case 'SRVDBDAF':
			$host = '192.168.5.228';
			$user = 'dti_dev';
			$pass = 'D3s3nv0lv1m3nt0@2022';
			break;
			
		case 'local':
			$host = '192.168.5.220';
			$user = 'dge';
			$pass = '@dge2011%_';
			break;	
			
		default:
			break;
	}
	
	if (!empty($usuario) && !empty($senha)) {
		$user = $usuario;
		$pass = $senha;
	}

	if(isset($_POST['login'])){
		$login = $_POST['login'];
		}else{
			$login = $_SESSION['SIGO']['ACESSO']['USUARIO'];
	}
	
    $link =  new mysqli("$host", "$user", "$pass", $db);
    if ($link -> connect_errno) {
        echo "IMPOSSIVEL CONECTAR AO HOST $host: " . $link -> connect_error;
        exit();
      }
	
	return $link;
	
}
?>