<?   
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

$caminho_raiz='../../../includes';	$caminho_raiz='./';
include $caminho_raiz."funcoes.php";
include $caminho_raiz."valida_sessao.php";

//    include("./banco.php");
//	RetornaConexaoMysql(2950, 'modulo_co_la');

$sql=$_SESSION['master_box_excel_sql'];
$titulo=$_SESSION['master_box_excel_titulo'];
$colunas=$_SESSION['master_box_excel_colunas'];

RetornaConexaoMysql('2950','modulo_co_la');	



header("Pragma: no-cache"); 
header("Expires: 0"); 
header('Content-Transfer-Encoding: none'); 
header('Content-Type: application/vnd.ms-excel;');                 // This should work for IE & Opera 
header("Content-type: application/x-excel");     


header('Content-Disposition: attachment; filename="'.$titulo.'_'.date('Y-m-d_G:i:s').'.xls"');

$rsm=mysqli_query($sql );
//echo $sql;
$field = mysqli_num_fields( $rsm );
//	echo $titulo;
foreach ($colunas as $c) {
	//print_r($c);
	echo $c[0].chr(9);
}	
//$fields = mysqli_field_array( $query );
echo chr(13);
while ($rec=mysqli_fetch_array($rsm)) {  
	for ( $i = 0; $i < $field; $i++ ) {
		echo strip_tags( str_replace(";", "", str_replace(chr(9), "", str_replace(chr(13), "",  str_replace(chr(10), "",  $rec[$i]))))).chr(9);
	}
	echo chr(13);
}
$_SESSION['master_box_excel_sql']=NULL;
$_SESSION['master_box_excel_titulo']=NULL;
$_SESSION['master_box_excel_colunas']=NULL;
