<?php
ini_set('session.cookie_lifetime', "0");
ini_set('session.cache_limiter', 'nocache');
ini_set('max_execution_time', '120');

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

$script = "../";

$link = RetornaConexaoMysql('local', 'sigo_integrado');

// A funcao Verificar_id_session_php busca o id da sessao.
$session_id = Verificar_id_session_php();

if(!$session_id){
    header("location: {$script}index.php?msg=666");
    exit();
}

if($_SESSION['SIGO']['ACESSO']['USUARIO'] == ''){
    
        
    $qry = "select * from sigo_integrado.tbl_log_acesso where lga_session_id = '$session_id' limit 1 ";
    $rs = mysqli_query($link, $qry) or die('Erro ao recuperar a sess&atilde;o do usuario no banco de dados. Descri&ccedil;&atilde;o: ' . mysqli_error($link));
    
    if(mysqli_num_rows($rs)){
        
        $sessao = mysqli_fetch_assoc($rs);
        
        $_SESSION = unserialize($sessao['lga_session_descricao']);
        
	    $Sql = "SELECT usr_ip_atual, usr_hash, usr_atualiza_menu FROM tbl_usuario WHERE usr_id={$_SESSION['SIGO']['ACESSO']['ID_USUARIO']}";
		if ($rs_valida = mysqli_query($link, $Sql)) {
		    if (mysqli_num_rows($rs_valida)) {
		
		        $row_valida = mysqli_fetch_assoc($rs_valida);
		        $Sql = "UPDATE tbl_usuario SET usr_hash='$hash' WHERE usr_id = '{$_SESSION['SIGO']['ACESSO']['ID_USUARIO']}'";
		
		        if ($row_valida['usr_atualiza_menu']) {
		            
		        	RetornaMenuSigoUtilizado();
		        	
		            $Sql_atualiza_menu = "UPDATE tbl_usuario SET usr_atualiza_menu=0 WHERE usr_id={$_SESSION['SIGO']['ACESSO']['ID_USUARIO']}";
		            mysqli_query($link, $Sql_atualiza_menu);
		        }
		        
		    } else {
		    	
		        header("location: {$script}index.php?msg=22222");
		        exit;
		        
		    }
		} else {
			
		    header("location: {$script}index.php?msg=2");
		    exit;
		    
		}
        
    }else{
        
        header("Location: {$script}index.php?msg=2");
        exit();
    }
    
}

if($_SESSION['SIGO']['ACESSO']['USUARIO'] == ''){
    
    header("Location: {$script}index.php?msg=3&sessid=$session_id");
    exit();
}
?>