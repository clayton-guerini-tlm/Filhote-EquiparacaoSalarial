<?php
ini_set('max_execution_time', '120');

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

$script = "../";

$link = RetornaConexaoMysql('local', 'sigo_integrado');


$p = isset($_GET['p']) ? trim($_GET['p']) : '';

// if ($p == 'trocar_senha_area2') {
//     header("Location: http://189.74.128.245/SIGO_INTEGRADO_3/");
//     exit();
// } elseif ($p == 'trocar_senha_area1rj') {
//     header("Location: http://sigo.telemontrio.com.br/SIGO_INTEGRADO_3/");
//     exit();
// } elseif ($_SERVER['HTTP_HOST'] == "192.168.5.18" or $_SERVER['HTTP_HOST'] == "192.168.5.19" or $_SERVER['HTTP_HOST'] == "192.168.5.51") {
//     header("Location: http://192.168.5.51/SIGO_INTEGRADO_3/");
//     exit();
// } elseif ($_SERVER['HTTP_HOST'] == "172.17.51.102") {
//     header("Location: http://172.17.51.102/SIGO_INTEGRADO_3/");
//     exit();
// }

if (!isset($_SESSION['SIGO']['ACESSO']['fil_codigo'])) {
    $_SESSION['SIGO']['ACESSO']['fil_codigo'] = '';
}

$_COOKIE['registro_brt'] = $_SESSION['SIGO']['ACESSO']['USUARIO'];
$_COOKIE['nome_usuario'] = $_SESSION['SIGO']['ACESSO']['NOME'];
$_COOKIE['area_atuacao'] = $_SESSION['SIGO']['ACESSO']['ATUACAO_OLD'];
$_COOKIE['filial'] = $_SESSION['SIGO']['ACESSO']['fil_codigo'];
$_COOKIE['PHPSESSID'] = $_SESSION['SIGO']['ACESSO']['PHPSESSID'];

$_SESSION['SIGO']['ACESSO']['IMG_SIA'] 	= 'Banner SIA-01.png';
$_SESSION['SIGO']['ACESSO']['IMG_SIGO'] = 'Banner SIGO-01.png';

//$_SESSION['SIGO']['ACESSO']['ID_USUARIO'] = 3234;


//VERIFICAR SE A REMOCAO DO COOKIE FILIAL DARA ALGUM PROBLEMA 
$Sql = "SELECT usr_ip_atual, usr_hash, usr_atualiza_menu FROM tbl_usuario WHERE usr_id = {$_SESSION['SIGO']['ACESSO']['ID_USUARIO']} ";


if ($rs_valida = $link->query($Sql)) {
    
    if ($rs_valida->num_rows) {

        $row_valida = $rs_valida->fetch_assoc();
        $hash = time();

        $Sql = "UPDATE tbl_usuario SET usr_hash='$hash' WHERE usr_id = '{$_SESSION['SIGO']['ACESSO']['ID_USUARIO']}'";
 
        if ($row_valida['usr_atualiza_menu']) {
     
            RetornaMenuSigoUtilizado();
            $Sql_atualiza_menu = "UPDATE tbl_usuario SET usr_atualiza_menu=0 WHERE usr_id={$_SESSION['SIGO']['ACESSO']['ID_USUARIO']}";
            $link->query($Sql_atualiza_menu);
        }
    } else {
        header("location: {$script}index.php?msg=22222");
        exit();
    }
} else {
    header("location: {$script}index.php?msg=2");
    exit();
}
?>