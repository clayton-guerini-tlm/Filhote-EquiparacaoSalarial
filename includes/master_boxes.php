<script >
 
function getXmlHttp() {
var xmlhttp;
try{
 xmlhttp = new XMLHttpRequest();
}catch(ee){
 try{
  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
 }catch(e){
  try{
   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
  }catch(E){
    xmlhttp = false;
  }
 }
}
return xmlhttp;
} 

function master_box_post(variavel, valor) {
	if (valor>=0){
		if (variavel=='indice'){
			tmp_java=document.getElementById(variavel).value;
			if (tmp_java.indexOf("-" + valor + ";")>=0){
				tmp_java=tmp_java.replace("-" + valor + ";", "");
			} else {
				if (tmp_java.indexOf( valor + ";")>=0){
					tmp_java=tmp_java.replace( valor + ";", "-" + valor + ";");
				} else {
					tmp_java=tmp_java + valor + ";" ;
				};
			};
			valor=tmp_java;
		};
		document.getElementById(variavel).value=valor;
//		window.alert(valor);
		document.f_master_box.submit();
 
	};
};


function cadastrar(r, c, pk, campo){

var nm = document.getElementById('ed_r_' + r + '_c_' + c).value;
 
  var url= "/../includes/master_boxes_edita.php?nome="+nm + "&pk=" + pk + "&campo=" + campo;
  request.open('GET', url, true);
  request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  request.onreadystatechange = confirma;
  request.send(null);
}

function confirma(){
	if(request.readyState == 4){
		var responde = request.responseText;
		if (responde!=''){alert(responde);};
	}
}
 var request = getXmlHttp();
</script>
<? 
$dir_raiz = $_SERVER['HTTP_HOST'].'/../';


if ($excel=='10'){master_box_excel(  ); };
function mas_box_rodape($linhas, $ponto, $total_de_linhas, $colunas) {
	if ($linhas<>0){
		$move_pagina[1]=$ponto-$linhas; if ($move_pagina[1] <= $linhas ) {$move_pagina[1]=0; };
		$move_pagina[2]=$ponto+$linhas; if ($move_pagina[2] > $total_de_linhas ) {$move_pagina[2]=$total_de_linhas-$linhas; };
		$move_pagina[3]=$total_de_linhas-$linhas; if ($move_pagina[3] <= 0 ) {$move_pagina[3]=0; };
		
		?><tr><td colspan="<? echo $colunas; ?>">
		<table class="box_adsl" width="100%" >
		<tr class="subcabecalho_tr" >
 
		<td width="25%"><? if (trim($b['voltar_para'])<>''){ ?><a href="<? echo $b['voltar_para']; ?>"><img src="<?=$dir_raiz?>/Imagens/botao_voltar.gif" border="0" height="18" width="18" /></a><? }; ?></td>
		<td width="10%" title="Volta a primeira linha." onclick="javascript:master_box_post('ponto', 0);"><strong><a href="#" ><img src="<?=$dir_raiz?>/Imagens/icc_esquerda_0.png" border="0" height="18" width="18" /></a></strong></td>
		<td width="10%" title="Voltar <? echo $move_pagina[1]; ?> linhas." onclick="javascript:master_box_post('ponto', <? echo $move_pagina[1]; ?>);" ><strong><a href="#" ><img src="<?=$dir_raiz?>/Imagens/icc_esquerda_1.png" alt="" width="18" height="18" border="0" /></a></strong></td>
		<td width="10%" title="A consulta tem <? echo $total_de_linhas; ?> linhas e esta mostrando da linha <? echo  $ponto ;?> at&eacute; a linha <? if ($total_de_linhas<=($ponto+$linhas)){echo $total_de_linhas ;} else {echo ($ponto+$linhas);}; ?>." ><strong><? echo $ponto; ?> - <? if ($total_de_linhas<=($ponto+$linhas)){echo $total_de_linhas ;} else {echo ($ponto+$linhas);}; ?> / <? echo $total_de_linhas;?></strong></td>
		<td width="10%" title="Avan&ccedil;ar <? echo $move_pagina[2]; ?> linhas." onclick="javascript:master_box_post('ponto', <? echo $move_pagina[2]; ?>);"><strong><a href="#" ><img src="<?=$dir_raiz?>/Imagens/icc_direita_1.png" alt="" width="18" height="18" border="0" /></a></strong></td>
		<td width="10%" title="Vai a ultima linha." onclick="javascript:master_box_post('ponto', <? echo $move_pagina[3]; ?>);"><strong><a href="#" ><img src="<?=$dir_raiz?>/Imagens/icc_direita_0.png" alt="" width="18" height="18" border="0" /></a></strong></td>
		<td width="25%">Horário do Servidor: <? echo date('d/m/Y h:m')?></td>
		</tr> 
		</table> </td></tr>
		<? 
	};
}; 



function master_box_titulo($b, $total_de_linhas) { ?>
<table align="center" width="100%" border="0">
	<tr  class="cabecalho_tr" >
    <? if (trim($b['voltar_para'])<>''){ ?><td width="2%" title="Voltar" ><span class="cabecalho_tr" ><a href="<? echo $b['voltar_para'];?>"><img src="<?=$dir_raiz?>/Imagens/botao_voltar.gif" border="0" height="18" width="18" /></a></span></td><? }; ?>
    <? if ($b['gerar_excel']){ ?><td width="2%" title="Exportar para o Excel." ><span class="cabecalho_tr" ><a href="#" onclick="javascript:master_box_post('excel', 10);"><img src="<?=$dir_raiz?>/Imagens/icexcel.jpg" border="0" height="18" width="18" /></a></span></td><? }; ?>    
    <td width="2%" title="Total de Linhas" ><? echo $total_de_linhas;?></td>
    <td width="92%" ><span class="cabecalho_tr"><? echo $b[0]; ?></span></td>
     <? if (trim($b['voltar_para'])<>''){ ?><td width="2%" title="Voltar" ><span class="cabecalho_tr" ><a href="<? echo $b['voltar_para'];?>"><img src="<?=$dir_raiz?>/Imagens/botao_voltar.gif" border="0" height="18" width="18" /></a></span></td><? }; ?>
 
    <td width="2%" title="Total de Linhas" ><? echo $total_de_linhas;?></td>
    </tr>
</table><? 
};
function master_box_excel( ) {

  	echo '<meta http-equiv="refresh" content="0;URL=/master_boxes_excel.php" />';

//header('location:?mainapp=home&app=home');
  //header('location:./master_boxes_excel.php');
 /*   ?><script>
//	var pop_win;
//pop_win = window.open('./master_boxes_excel.php', 'Excel');
 
 
  var url= "master_boxes_excel.php";
  request.open('GET', url, true);
  request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  request.onreadystatechange = confirma;
  request.send(null);
 </script>

	<?     */
// exit; return;
};
function master_box_cabecalho_sql($b ){
	if ($b['label']) {echo '<td>N</td>';};
	$tmp='';
	$farta=explode(";", $b['indice']); ///indices
	for($i=0; $i<=3; ++$i){
		if( $farta[$i]<>''){
			if($farta[$i]<0){
				$mat_indice[abs($farta[$i])][0]=($i+1)."- ";
				$mat_indice[abs($farta[$i])][1]=' desc ';
			} else {
				$mat_indice[abs($farta[$i])][0]=($i+1)."+ ";
				$mat_indice[abs($farta[$i])][1]=' asc ';				
			};
		};
	};
	$i=0;
	foreach ($farta as $m){$aux_indice .= $b[1][abs($m)-1][2].$mat_indice[abs($m)][1].', '; }; // indices
	$i=0;
	foreach ($b[1] as $c) {
		$i+=1;
		echo "<td $c[1] ";
		if ($c[3]) {echo  ' onclick="javascript:master_box_post(\'indice\', '.$i.');" ><a href="#" '; }; //indices
		echo ">".$c[0].' '.$mat_indice[$i][0];
		if ($c[3]) {echo  '</a>';}; //indices
		echo '</td>';
		$tmp.=trim($c[2]).' as cp'.$i.', ';
	};
	$tmp=substr($tmp, 0, -2);
	if (strlen(trim($aux_indice))>5 ){$aux_indice=' order by '.substr(trim($aux_indice),0, -3) ;} else {$aux_indice='';};
	if (trim($b['chave_primaria'])<>''){$tmp_chaveprimaria=$b['chave_primaria'];};
	$sql="SeleCt $tmp_chaveprimaria $tmp ".$b[2].' '.$b['filtro'].$aux_indice;
	return $sql;
};
function master_box($b ) {
//	$conecta = RetornaConexaoMysql('2950', 'exin');
	$excel=$b['excel'];
	if ($b['gerar_excel']){$b['excel'] = str_replace(' ', '_', trim($b[0]));} ;

	if ($b['linhas']==''){$linhas=17;}  else {$linhas=$b['linhas'];};
	$sql="Select count(*) as total_de_linhas ".$b[2].' '.$b['filtro']; 
	
	//echo $sql;
	
	$rs=mysqli_query($sql );
	$l=mysqli_fetch_array($rs);
	$total_de_linhas=$l['total_de_linhas'];

	if ($b['ponto']<=0){$ponto=0;} else{$ponto=$b['ponto'];};
 
	?>
    <form name="f_master_box" id="f_master_box" action="" method="post">
    <input type="hidden" id="ponto" name="ponto" value="<? echo $ponto; ?>" />
    <input type="hidden" id="indice" name="indice" value="<? echo $b['indice']; ?>" />    
    <input type="hidden" id="excel" name="excel" value="" />
    <input type="hidden" name="sqlCompleto" value="<?=$sql?>" />

    
    <table class="box_adsl" align="center" width="100%" height="85%" >
	<tr class="cabecalho_tr" ><td  colspan="<? echo (count($b[1])+$b['label']); ?>" > 
    <? master_box_titulo($b, $total_de_linhas); ?>
	</td></tr>  
	<tr class="subcabecalho_tr">
<?  $sql=master_box_cabecalho_sql($b); ?></tr><? 
	foreach ($b[1] as $editavel) { if ($editavel[4]<>'') {$edita='sim';}; }; $editavel=''; // verifica se vai ativar edição
	$sql_excel=str_replace($b['chave_primaria'], '', $sql);
	$_SESSION['master_box_excel_sql']=$sql;
	$_SESSION['master_box_excel_titulo']=$b['excel'];
	$_SESSION['master_box_excel_colunas']=$b[1];	
 
	if ($b['mostra_sql']){echo $sql;};
 
//	print_r($b);
 
	if ($linhas<>0){$sql.=" limit $ponto, $linhas" ;};
	$rs=mysqli_query($sql )  or die(mysqli_error()); 
	$i=0;
	if (trim($b['chave_primaria'])<>''){$pk=explode( ', ' , $b['chave_primaria']); $_SESSION['box']['chave_primaria']=$pk;  $_SESSION['box']['filtro']=$b['filtro'];};

	while ($l=mysqli_fetch_array($rs)) { 
		$i+=1;
		if (trim($b['chave_primaria'])<>""){if (strpos($b['chave_primaria'], $c[2])>=0)	{$pk_linha=''; foreach ($pk as $primari){$pk_linha.=$l[$primari].', ';}};	};
		$tmp='<tr bgcolor="#'; if ($i%3==0) {$tmp.='efefef';} else {$tmp.='FFFFFF'; }; $tmp.='"  >';
		if ($b['label']) {$tmp.='<td>'.($i+$ponto).'</td>';};
		$xx=0;
		foreach ($b[1] as $c) {
			$xx+=1;
			if (($edita) and ($c[4]<>'')){
				$tmp.= box_relatorio_monta_linha($c, $i, $xx, $l, $b, $pk, urlencode( $pk_linha ));
			} else {
				$tmp.="<td $c[1] >".$l['cp'.$xx].'</td>';
			};
			
		};
		echo $tmp.'</tr>'.chr(13);
	};
	while ($i< $linhas) {$i+=1;	echo '<tr  ><td colspan="'.($xx+$b['label']).'" >&nbsp;</td></tr>';	};// coloca linhas em branco
	mas_box_rodape($linhas, $ponto, $total_de_linhas, ( count($b[1]) + $b['label'] )) ; 
	  ?>
</table>
 </form><?   };?>
 
 <? function box_relatorio_monta_linha($c, $i, $xx, $l, $b, $pk, $pk_linha) {
	 $c[6]="r_$i"."_c_$xx";
	 $monta="<td $c[1] id=\"$c[6]\"  >   ";
 	 if (strpos($b['chave_primaria'], $c[2])>=0){$c[7]=" onChange=\"javascript:cadastrar('$i', '$xx', '$pk_linha', '$c[2]');\" ";}; 
	 if ( is_array($c[4]) ){
		 $monta.="<select id=\"ed_$c[6]\"  $c[7]   >";
		 foreach($c[4] as $a) {
			$monta.="<option  value=\"$a\""; 
			if ($a==$l['cp'.$xx]) {$monta.= " selected ";}; 
			$monta.=" >$a</option> ";
 
		 };
         $monta.="</select>".chr(13);	 
	 } elseif ($c[4]=='data'){
		 $monta.="<input id=\"ed_$c[6]\" type=\"text\" onKeyPress=\"return txtBoxFormat(document.myForm,'ed_$c[6]','99/99/9999', event);\" value=\"".$l['cp'.$xx]."\" size=\"11\" maxlength=\"11\" $c[7]  /> <input type=\"button\" onClick=\"displayCalendar(document.getElementById('ed_$c[6]'),'dd/mm/yyyy',this)\" class=\"calendario\" style=\"cursor:pointer;\" />";
	 } elseif (substr($c[4],0,6)=='editar'){
		  $monta.="<input id=\"ed_$c[6]\" type=\"text\" value=\"".$l['cp'.$xx]."\" $c[7] ";
		  if (substr($c[4],6)<>'') {$monta.='size=\"'.(substr($c[4],6)+1).'\" maxlength=\"'.(substr($c[4],6)+1).'\"'; }
		  $monta.="/>";
	 };
	 $monta.='</td>'.chr(13).chr(13).chr(13);
 return $monta;
 };
 
/////////////asdf/asdf ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///df asdf f asdf 
/// asasf asdf afasasdd
///asdfdf asdsdf f 
/// asdf aasdssdfasd ads df asdf 
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ?>