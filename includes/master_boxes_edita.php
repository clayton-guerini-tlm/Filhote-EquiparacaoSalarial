<?php
//include "../../../includes/banco.php";
include "./banco.php";
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}
$conecta = RetornaConexaoMysql('2950', 'modulo_co_la');

$nm = addslashes(trim($_POST['nome']));
$se = addslashes(trim($_POST['senha']));

$sql = "Update  ".str_replace('from', ' ', strtolower( $_SESSION['box'][2]));
$sql.=" set $campo='".$_GET['nome']."' ".$_SESSION['box']['filtro'];
if (trim($_SESSION['box']['filtro'])==''){$a=' where '; } else {$a=' and ';};
 
$valores=  explode(', ' , urldecode($_GET['pk']));

$i=0;
foreach ($_SESSION['box']['chave_primaria'] as $campos){
	if ($campos<>'') {
	$sql_tmp.=$a.$campos."='".$valores[$i]."'"; $a=' and '; };$i+=1; };
$sql=$sql.$sql_tmp.' limit 1';
// echo $sql;
$rs = mysqli_query($conecta, $sql);//';// or die(mysqli_error($conecta)) ;
 
if (!$rs) {echo "Nao foi possivel alterar! ".$campo.' '.$_GET['nome'];};
@mysqli_close($conecta);
?>
