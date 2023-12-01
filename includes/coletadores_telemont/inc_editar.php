<?php
	include_once "includes/funcoes.php";
	$conecta =  RetornaConexaoMysql('local', 'modulo_coletadores');
	
	//print_r($_POST);
	
	if( !isset($_GET["coletador"]) || empty($_GET["coletador"]) ){
		header('location: principal.php');
		exit();
	}
	
	if (isset($_POST['inserir'])){
	    $area = strtoupper($_POST['area']);
	    $area_operacional = strtoupper($_POST['area_operacional']);
	    $ip = $_POST['ip'];
	    $login = $_POST['login'];
	    $senha = $_POST['senha'];
	    $observacao = $_POST['observacao'];
	    
	    $sql_inc = "UPDATE tbl_cadastro_sistemas SET area_sistema = '$area', area_operacional = '$area_operacional', ip = '$ip', login = '$login', senha = '$senha', observacao = '$observacao' WHERE id = ".$_GET["coletador"];
	    //echo $sql_inc; exit;
	    mysqli_query($conecta, $sql_inc) or die(mysqli_error($conecta));
	    
	    if (mysqli_affected_rows($conecta) == 1) {
	        echo '<script>alert("Registro Alterado com Sucesso");</script>';
	    }else{
	        echo '<script>alert("Problemas ao Alterar o Registro");</script>';
	    }
	}
	$sql = "SELECT * FROM tbl_cadastro_sistemas WHERE id = ". $_GET["coletador"];
	$exe = mysqli_query($conecta, $sql);
	$row = array();
?>
<?php if( !$exe ): ?>
    <script type="text/javascript">
        alert('Erro! Entre em contato com a DGE!');
        window.location.href = './principal.php';
    </script>
<?php elseif( mysqli_num_rows( $exe ) <= 0 ): ?>
    <script type="text/javascript">
        alert('Nenhum coletador encontrado!');
        window.location.href = './principal.php';
    </script>
<?php else: ?>
    <?php $row = mysqli_fetch_assoc($exe); ?>
<?php endif; ?>

<script type="text/javascript" src="ajax/ajax.js"></script>
<script type="text/javascript" src="ajax/ReqAjax.js"></script>
<link href="../css/index.css" rel="stylesheet" type="text/css" />
<form name="myForm" id="myForm" method="post" onsubmit='javascript: return valida_form();'>
	<table class="box_relatorio" border="1" width="70%" align="center" >
	    <tr class="cabecalho_tr">
	        <td colspan="6"><span class="cabecalho_tr">Editar dados do Sistemas</span></td>
	    </tr>
	    <tr>
	        <td>&Aacute;rea:</td>
	        <td>
	            <input type="text" name="area" id="area" maxlength='2' size='2' value="<?php echo $row["area_sistema"]; ?>" />
	        </td>
	        <td>Segmento:</td>
	        <td>
	            <input type="text" id="area_operacional" name="area_operacional" value="<?php echo $row["area_operacional"]; ?>" />
	        </td>
	        <td>IP:</td>
	        <td>
	            <input type="text" id="ip" name="ip" size='15' maxlength='15' value="<?php echo $row["ip"]; ?>" />
	        </td>
	    </tr>
	    <tr>
	        <td>Login:</td>
	        <td> 
	            <input type="text" id="login" name="login" value="<?php echo $row["login"]; ?>" />
	        </td>
	        <td>Senha:</td>
	        <td>
	            <input type="text" name="senha" id="senha" value="<?php echo $row["senha"]; ?>" /> 
	        </td>
	        <td colspan="2"></td>
	    </tr>
	    <tr>
	        <td>Observações:</td>
	        <td colspan="5"> <textarea id="observacao" name="observacao" rows="3" cols="120"><?php echo $row["observacao"]; ?></textarea> </td>
	    </tr>
	    <tr class='subcabecalho_tr'>
	        <td align='center' colspan='6'>
	            <input type='submit' name='inserir' id='inserir' value='Salvar' />
	        </td>
	    </tr>
	</table>
</form>

<script type="text/javascript">
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