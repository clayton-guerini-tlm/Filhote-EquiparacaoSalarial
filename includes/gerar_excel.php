<? 

function mysqli_field_array( $query ) {
	$names = array();
    $fieldinfo = mysqli_fetch_fields($query);
	if(sizeof($fieldinfo)>0) {
		foreach ($fieldinfo as $val) {
			$names[] = $val->name;
		}
	}
	return $names;
}

include('funcoes.php');  
$conecta = null;
$conecta = RetornaConexaoMysql('2950','modulo_co_la');
if ($titulo=="") {$titulo= urldecode($_POST['titulo']);};
if ($sql=="") {$sql= urldecode($_POST['masql']);};
if ($arquivo=="") {$arquivo=$_POST['arquivo']; };
if ($para=="") {$para=$_POST['para']; };
if ($sql=="") {$sql= urldecode($_GET['masql']); $sql=str_replace("''","'", $sql);  $sql=str_replace("\\'","'", $sql); $sql=str_replace("\'","'", $sql);  $sql=str_replace("\'","'", $sql);  };
if ($arquivo=="") {$arquivo=$_GET['arquivo']; };
if ($para=="") {$para=$_GET['parad']; };
if ($arquivo=="") {$arquivo="arquivo.xls";};
 
header('Content-type: application/msexcel');
header('Content-Disposition: attachment; filename="'.$arquivo.'"');
 
?>
<table  >
<? 
	$query = mysqli_query($conecta, $sql); // Executa a query no Banco de 
	$field = mysqli_num_fields( $query );
	$fieldinfo = mysqli_fetch_fields($query);
	if (trim($titulo)<>''){echo '<tr aling="center"  bgcolor="#0099CC"  ><th colspan="'.$field.'">'.$titulo.'</th></tr>';};
	echo '<tr bgcolor="#CCCCCC">';
	
	if(sizeof($fieldinfo)>0) {
		foreach ($fieldinfo as $val) {
			$names[] = $val->name;
			echo "<td>".$val->name."</td>";
		}
	}
	
	echo "</tr>";
$fields = mysqli_field_array( $query );
 
while ($rec=mysqli_fetch_array($query)) {  
echo "<tr>";
        for ( $i = 0; $i < $field; $i++ ) {
       
            echo "<td nowrap> ".str_replace("<br>", "", str_replace("<br/>", "", str_replace("<br />", "",str_replace("<BR>", "", str_replace("<BR/>", "", str_replace("<BR />", "", $rec[$i]))))))."</td>";
       
        }
echo "</tr>";
;}  ?>		
  
 </tr>
<? if (trim($titulo)<>''){echo '<tr aling="left"  bgcolor="#0099CC"  ><th colspan="'.$field.'">'.date('d/m/Y G:i:s').'</th></tr>';};  ?>
</table>

  <?php
  
  exit;
header("location:".$para); 

 
?>