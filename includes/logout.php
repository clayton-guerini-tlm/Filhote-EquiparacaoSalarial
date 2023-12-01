<?php 
header("Programa: no-cache");
header("Cache: no-cache");
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon 26 jul 1997 05:00:00 GMT");

include "funcoes.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

$conecta = RetornaConexaoMysql('local', 'sigo_integrado');

$session_id = Verificar_id_session_php();

$Sql_delete_session_id = "UPDATE tbl_log_acesso 
                SET lga_session_id = NULL, 
                                lga_session_descricao = NULL, 
                                lga_tempo_ini_sessao = NULL, 
                                lga_tempo_fim_sessao = NULL, 
                                lga_tempo_atual_sessao = NULL 
                WHERE usr_id = {$_SESSION['SIGO']['ACESSO']['ID_USUARIO']} ";
mysqli_query($conecta, $Sql_delete_session_id);

mysqli_close($conecta);

unset($_SESSION["SIGO"]);
unset($_SESSION);
session_destroy();

header ("Location: index.php");

// if($_GET['p'] == 'trocar_senha_area2'){
//     header ("Location: http://189.74.128.245/SIGO_INTEGRADO_3/");
// }elseif($_GET['p'] == 'trocar_senha_area1rj'){
//     header ("Location: http://sigo.telemontrio.com.br/SIGO_INTEGRADO_3/");
// }elseif (substr($_SERVER['HTTP_HOST'],0,12)=="192.168.5.51"){
//     header ("Location: http://192.168.5.51/SIGO_INTEGRADO_3/");
// }elseif (substr($_SERVER['HTTP_HOST'],0,12)=="10.59.99.217"){
//     header ("Location: http://189.74.128.245/SIGO_INTEGRADO_3/");
// }elseif (substr($_SERVER['HTTP_HOST'],0,13)=="172.17.51.102"){
//     header ("Location: http://172.17.51.102/SIGO_INTEGRADO_3/");
// }else{
//     header ("Location: http://" . $_SERVER['HTTP_HOST'] . "/SIGO_INTEGRADO_3/");
// }
exit();
?>