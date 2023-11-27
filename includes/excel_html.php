<?  
/********************* 
autor:Jean
data: 27/02/2011 
----------------como usar ---------------
coloque os inputs baixo dentro do form
<input type="hidden" name="excel_conteudo" id="excel_conteudo" value=""/>
<input type="hidden" name="excel_mainvoltar" id="excel_mainvoltar" value=""/>
<input type="hidden" name="excel_titulo" id="excel_titulo" value=""/>
coloque o seguinte script no  onclick em uma imagem qualquer

<img width="20" height="20" lign="absmiddle" src="<?php echo $caminho_raiz;?>imagens/icexcel.jpg"  
onclick="javascript:document.getElementById('excel_conteudo').value = document.getElementById('mandar_pro_exel').innerHTML; 
document.getElementById('excel_mainvoltar').value = '<?=$GLOBALS[REQUEST_URI]?>'; 
document.getElementById('excel_titulo').value = 'matriculas_sigo_dados'; 
document.forms[0].action='<?php echo $caminho_raiz;?>includes/excel_html.php'; 
document.forms[0].submit();"   />
--------------------------------------------------
-------------------- importante ------------------
--------------------------------------------------
nao exporta css, ser quiser cor tem que por na mÃ£o
--------------------------------------------------
o script exporta o que estiver dentro da div 
<div id="mandar_pro_exel" style="vertical-align:inherit" >
	coisas para mandar pro excel aqui dentro
</div>
*/

echo '
<html>
<title>:: SIGO - TELEMONT ENGENHARIA ::</title>

<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; CHARSET=iso-8859-1">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="-1">
<link href="http://'.$_POST['excel_caminho'].'/../css/menu.css" rel="stylesheet" type="text/css"></link>
<link href="http://'.$_POST['excel_caminho'].'/../css/index.css" rel="stylesheet" type="text/css"></link>
</head>
<body    >

';

 	header("Pragma: no-cache"); 
    header("Expires: 0"); 
    header('Content-Transfer-Encoding: none');  
    header('Content-Type: application/vnd.ms-excel;');                 // This should work for IE & Opera 
    header("Content-type: application/x-excel");     
	header('Content-Disposition: attachment; filename="/temp/'.$_POST['excel_titulo'].'_'.date('Y-m-d_G:i:s').'.xls"');
 
	$ex= $_POST['excel_conteudo'] ;
	?><table><tr ><td ><img   src="http://<?=$_POST['excel_caminho']?>/../imagens/banner_topo.jpg" border="0"  start="fileopen"  align="middle" onClick="<?=$_POST['excel_mainvoltar']?>" /></td></tr><td >&nbsp;</td></tr><tr ><td >&nbsp;</td></tr><tr ><td >&nbsp;</td></tr><td >&nbsp;</td></tr></table><?
	 
	echo strip_tags( $ex, '<table><tr><td><th><select><option><input><css><class>');
 
	   	?>
<body>
<html>
  <script>window.open('<?=$_POST['excel_mainvoltar']?>', '_self');</script>