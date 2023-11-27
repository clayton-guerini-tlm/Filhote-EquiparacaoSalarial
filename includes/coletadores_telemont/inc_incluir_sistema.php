<?php
include_once "includes/funcoes.php";
$conecta =  RetornaConexaoMysql('local', 'modulo_coletadores');

//print_r($post);
$post = trataPost($conecta);
if (isset($post['inserir'])){
	//print_r($post);die();
	$area = strtoupper($post['area']);
	$area_operacional = strtoupper($post['area_operacional']);
	$ip = $post['ip'];
	$login = $post['login'];
	$senha = $post['senha'];
	$observacao = $post['observacao'];
	
	$sql_inc = "insert into tbl_cadastro_sistemas (area_sistema, area_operacional, ip, login, senha, observacao) 
											values ('$area', '$area_operacional', '$ip', '$login', '$senha', '$observacao')";
	//echo $sql_inc; exit;
	mysqli_query($conecta, $sql_inc);
	
	if (mysqli_affected_rows($conecta) == 1) {
		echo '<script>alert("Registro Inserido com Sucesso")
			 </script>';
	}else{
		echo '<script>alert("Problemas ao Inserir o Registro")
			 </script>';
                echo mysqli_error($conecta);
	}
}

?>


<script type="text/javascript" src="ajax/ajax.js"></script>
<script type="text/javascript" src="ajax/ReqAjax.js"></script>
<link href="../css/index.css" rel="stylesheet" type="text/css" />



<form name="myForm" id="myForm" method="post" onsubmit='javascript: return valida_form();'>
<table class="box_relatorio" border="1" width="70%" align="center" >
	<tr class="cabecalho_tr">
		<td colspan="6"><span class="cabecalho_tr">Cadastro de Sistemas</span></td>
	</tr>
	<tr>
		<td>&Aacute;rea:</td>
		<td>
			<input type="text" name="area" id="area" maxlength='2' size='2'/>
		</td>
		<td>Segmento:</td>
		<td>
			<input type="text" id="area_operacional" name="area_operacional">
		</td>
		<td>IP:</td>
		<td>
			<input type="text" id="ip" name="ip" size='15' maxlength='15' />
		</td>
	</tr>
	<tr>
		<td>Login:</td>
		<td> 
			<input type="text" id="login" name="login" />
		</td>
		<td>Senha:</td>
		<td>
			<input type="text" name="senha" id="senha" /> 
		</td>
		<td colspan="2"></td>
	</tr>
	<tr>
		<td>Observações:</td>
		<td colspan="5"> <textarea id="observacao" name="observacao" rows="3" cols="120"></textarea> </td>
	</tr>
	<tr class='subcabecalho_tr'>
		<td align='center' colspan='6'>
			<input type='submit' name='inserir' id='inserir' value='Cadastrar' />
		</td>
	</tr>

</table>
</form>



<script>

function valida_form(){
	if(document.getElementById('area').value.trim() == ''){
		alert('O campo area precisa ser preenchido');
		return false;
	}
	if(document.getElementById('area_operacional').value.trim() == ''){
		alert('O campo Segmento precisa ser preenchido');
		return false;
	}
	if(document.getElementById('ip').value.trim() == ''){
		alert('O campo IP precisa ser preenchido');
		return false;
	}
	if(document.getElementById('login').value.trim() == ''){
		alert('O campo Login precisa ser preenchido');
		return false;
	}
	if(document.getElementById('senha').value.trim() == ''){
		alert('O campo Senha precisa ser preenchido');
		return false;
	}
	if(document.getElementById('observacao').value.trim() == ''){
		alert('O campo Observação precisa ser preenchido');
		return false;
	}
}

</script>