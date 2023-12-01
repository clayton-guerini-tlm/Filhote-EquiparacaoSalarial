<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

ini_set('max_execution_time','120');
include "../includes/funcoes.php";

if(isset($_REQUEST['funcao_ajax'])){

	$funcao_ajax = $_REQUEST['funcao_ajax'];
	//chama as funcoes de acordo com a necessidade do ajax passando o framework como parametro
	call_user_func($funcao_ajax);

	}else{
		echo "Acesso restrito!";
}



/*

function AjaxBuscaArea(){
	$_REQUEST['banco'] != ''?$bd=$_REQUEST['banco']:$bd='modulo_gestao_operacional_la';
	$link = RetornaConexaoMysql('local', $bd);

	$where_valor = $_REQUEST['where_valor'];
	$campo_busca = $_REQUEST['campo_busca'];
	$campo_where = $_REQUEST['campo_where'];
	$tabela 	 = $_REQUEST['tabela'];

	if (!empty($campo_where) && !empty($where_valor)) {
		$where = "$campo_where= '$where_valor' AND";
		}else{
			$where = "";
	}



	$Sql = "SELECT DISTINCT $campo_busca FROM $tabela WHERE $where $campo_busca <> '' ORDER BY $campo_busca ASC";
	//echo $Sql;exit;
	if ($rs = mysqli_query($link, $Sql)){
		$xml = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n";
		$xml.="<areas>\n";
		while ($row = mysqli_fetch_array($rs)) {
			$xml.="<area>\n";
			$xml.="<nome>{$row["$campo_busca"]}</nome>\n";
			$xml.="</area>\n";
		}
		$xml.="</areas>";
		$ret = $xml;
		header("Content-type: application/xml; charset=iso-8859-1");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		}else{
			header("Content-Type: text/html; charset=ISO-8859-1",true);
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			$ret = "Erro ao buscar macro areas. $Sql";
	}

	mysqli_close($link);

	echo $ret;

}
*/
// LOGIN //

function AjaxBuscaDadosFuncionario(){

	$cpf = $_REQUEST['cpf'];

	$link = RetornaConexaoMysql('serverdge', 'sigo_integrado');

	$Sql = "SELECT fun_id, fun_nome FROM tbl_rm_funcionario WHERE fun_cpf='$cpf' AND sit_id<>'D'";

	//echo $Sql;exit;

	if($rs = mysqli_query($link, $Sql)){
		if(mysqli_num_rows($rs)){
			$row_funcionario = mysqli_fetch_array($rs);
			$Sql = "SELECT usr_id FROM tbl_usuario WHERE fun_id = '{$row_funcionario['fun_id']}'";
			//echo $Sql;exit;
			$rs = mysqli_query($link, $Sql);
			if(mysqli_num_rows($rs) > 0){
				$existe_usuario = "SIM";
				}else{
					$existe_usuario = "NAO";
			}
			$xml = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n";
			$xml.="<funcionarios>\n";
			$xml.="<funcionario>\n";
			$xml.="<existe_usuario>$existe_usuario</existe_usuario>\n";
			$xml.="<id>{$row_funcionario["fun_id"]}</id>\n";
			$xml.="<nome><![CDATA[{$row_funcionario["fun_nome"]}]]></nome>\n";
			$xml.="</funcionario>\n";
			$xml.="</funcionarios>";
			$ret = $xml;
			header("Content-type: application/xml; charset=iso-8859-1");

			}else{
				header("Content-Type: text/html; charset=ISO-8859-1",true);
				$ret = "naoencontrado";

		}
		}else{
			header("Content-Type: text/html; charset=ISO-8859-1",true);
			$ret = "erro";
	}

	mysqli_close($link);

	$gmtDate = gmdate("D, d M Y H:i:s");
	header("Last-Modified: {$gmtDate} GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Expires: -1");

	echo $ret;
	exit;

}

function AjaxInserirUsuario(){

	$fun_id = $_REQUEST['id'];
	$usuario = utf8_decode(strtoupper($_REQUEST['usuario']));
	$email = $_REQUEST['email'];
	$nome = explode('-',utf8_decode($_REQUEST['nome']));
	$nome = ucfirst($nome[0]);
	$observacao = nl2br(utf8_decode($_REQUEST['observacao']));

	//$senha = GeraPalavraAleatoria(10,true);
	$senha = 123;
	$senha_md5 = md5($senha);

	$link = RetornaConexaoMysql('serverdge', 'sigo_integrado');

	$Sql = "SELECT usr_id FROM tbl_usuario WHERE usr_usuario='$usuario'";
	$rs = mysqli_query($link, $Sql);

	if(mysqli_num_rows($rs) == 0){
	//if(true){
		$Sql = "SELECT fun_filial FROM tbl_rm_funcionario WHERE fun_id=$fun_id";
		$rs_filial = mysqli_query($link, $Sql);
		$row_filial = mysqli_fetch_assoc($rs_filial);
		$filial= $row_filial['fun_filial'];
		if($filial == "MG" || $filial == "ES" || $filial == "SP"){
			$filial = "AREA1_MG";
		}elseif($filial == "RJ"){
			$filial = "AREA1_RJ";
		}else{
			$filial = "AREA2";
		}

		$Sql = "INSERT INTO tbl_usuario (fun_id, usr_usuario, usr_senha, usr_email, usr_data_cadastro, usr_observacao, usr_filial, usr_nome_visitante) VALUES ('$fun_id','$usuario', '$senha_md5', '$email', NOW(), '$observacao', '$filial', '{$_REQUEST['nome']}')";
/*		echo $Sql;
		exit(); */
		if($rs = mysqli_query($link, $Sql)){

			GravaLogSentenca($link, $Sql);

			$Sql2 = "INSERT INTO tbl_pendencia (pdt_id, pen_solicitante_fun_id, pen_solicitante, pen_solicitante_email, pen_data_solicitacao, pen_solicitacao) VALUES (3, '$fun_id', '$nome', '$email', NOW(), '$observacao')";
			$rs = mysqli_query($link, $Sql2);

			$assunto = "SIGO - Cadastro de usuários";
			$mensagem = "$nome<br /><br />Seu login do SIGO foi cadastrado.<br /><br />Aguarde a liberação do mesmo pela administração do SIGO.";

			//echo $Sql;exit;

			// EnviarEmail($nome,$email,$assunto,$mensagem)
			// removi  o envio de e-mail na criacao do usuario

			if(EnviarEmail($nome,$email,$assunto,$mensagem)){
				$mensagem = "Novo cadastro no SIGO.<br /><br />Utilize os seguintes dados para acessar o portal:<br /><br /> Portal: <a href=\"http://sigo.telemont.com.br\">http://sigo.telemont.com.br</a> <br /> Nome: <b>$nome</b><br /> Login: <b>$usuario</b><br />Senha: <b>$senha</b><br />Area: <b>$filial</b>";
				$mail_admin = "sigo@telemont.com.br";
				EnviarEmail($nome,$mail_admin,$assunto,$mensagem);

				$ret = "inseriu";
			}else{
				$ret = "Seu usuário SIGO foi criado com êxito. Porém, não foi possível enviar e-mail com os dados do seu usuário.";
			}
 
		}else{
			$ret = "Não foi possível criar seu usuário.";
		}

		}else{
			$ret = "Usuário já utilizado.";
	}


	mysqli_close($link);

	$gmtDate = gmdate("D, d M Y H:i:s");
	header("Last-Modified: {$gmtDate} GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Expires: -1");
	header("Content-Type: text/html; charset=iso-8859-1");

	echo $ret;
	exit;
}

function AjaxEnviarSolicitacao(){
	$tipo = $_REQUEST['tipo'];

	$link = RetornaConexaoMysql('serverdge', 'sigo_integrado');

	switch ($tipo) {
		case 1: // liberacao de usuario

			$fun_id = $_REQUEST['id'];
			$email = $_REQUEST['email'];
			$observacao = nl2br(utf8_decode($_REQUEST['observacao']));

			$Sql = "SELECT  fun_nome FROM tbl_rm_funcionario WHERE fun_id=$fun_id";
			$rs = mysqli_query($link, $Sql);
			$row = mysqli_fetch_assoc($rs);
			$nome = $row['fun_nome'];

			$Sql = "INSERT INTO tbl_pendencia (pdt_id, pen_solicitante_fun_id, pen_solicitante, pen_solicitante_email, pen_data_solicitacao, pen_solicitacao) VALUES ($tipo, '$fun_id', '$nome', '$email', NOW(), '$observacao')";

			break;

		case 2: // cadastro de visitante
			$nome = strtoupper(utf8_decode($_REQUEST['nome']));
			$registro = $_REQUEST['registro'];
			$email = strtolower($_REQUEST['email']);
			$telefone = $_REQUEST['telefone'];
			$observacao = nl2br(utf8_decode($_REQUEST['observacao']));

			$observacao = "REGISTRO: $registro <br />TELEFONE: $telefone <br />E-MAIL: $email <br /><br />" . $observacao;

			$Sql = "INSERT INTO tbl_pendencia (pdt_id, pen_solicitante, pen_solicitante_email, pen_data_solicitacao, pen_solicitacao) VALUES ($tipo, '$nome', '$email', NOW(), '$observacao')";

			break;

		default:
			break;

	}

	//print_r($_REQUEST);exit;

	//echo $Sql;exit;

	if($rs = mysqli_query($link, $Sql)){

		$ret = "inseriu";
		}else{
			$ret = "Erro ao cadastrar a solicitação!";
	}

	mysqli_close($link);

	$gmtDate = gmdate("D, d M Y H:i:s");
	header("Last-Modified: {$gmtDate} GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Expires: -1");
	header("Content-Type: text/html; charset=iso-8859-1");

	echo $ret;
	exit;
}

function AjaxValidaLogin(){

	$login = utf8_decode($_REQUEST['login']);
	$senha = utf8_decode($_REQUEST['senha']);

	//print_r($_REQUEST);exit;

	$link = RetornaConexaoMysql('serverdge', 'sigo_integrado');

	$Sql = "SELECT u1.usr_id as ID_USUARIO, u1.usr_usuario as USUARIO, u1.usr_email as EMAIL, u1.usr_senha, u1.usr_atuacao_old AS ATUACAO_OLD, u1.usr_senha_padrao as SENHA_PADRAO, g1.gru_id AS ID_GRUPO, g1.gru_descricao AS GRUPO, u1.usr_nome_visitante as NOME, u1.usr_filial AS FILIAL, u1.fun_id AS ID_FUNCIONARIO,  u1.usr_status, f1.fun_filial as FILIAL_ESTADO FROM tbl_usuario u1 INNER JOIN tbl_grupo g1 ON u1.gru_id=g1.gru_id INNER JOIN tbl_rm_funcionario f1 ON u1.fun_id=f1.fun_id WHERE u1.usr_usuario = '$login'";

	//echo $Sql;exit;

	if($rs = mysqli_query($link, $Sql)){
		if(mysqli_num_rows($rs)){
			$row_login = mysqli_fetch_assoc($rs);
			if($row_login['usr_status'] == "LIBERADO"){
				$senha_banco = $row_login['usr_senha'];
				$fun_id = $row_login['ID_FUNCIONARIO'];
				if(md5($senha) == $senha_banco){

					//$Sql = "UPDATE tbl_usuario SET usr_ip_atual='{$_SERVER['REMOTE_ADDR']}', usr_ultimo_acesso=Now() WHERE usr_id = '{$row_login['ID_USUARIO']}'";
					$Sql = "UPDATE tbl_usuario SET usr_ip_atual='{$_SERVER['REMOTE_ADDR']}', usr_ultimo_acesso=Now() WHERE fun_id = '{$row_login['ID_FUNCIONARIO']}' and fun_id != 1";
					if(mysqli_query($link, $Sql)){
						//GravaLogSentenca($link, $Sql, $row_login['ID_USUARIO']);
					}

					$Sql = "INSERT INTO tbl_log_acesso (usr_id, lga_ip, lga_dthr_acesso, lga_browser) VALUES ('{$row_login['ID_USUARIO']}', '{$_SERVER['REMOTE_ADDR']}', NOW(), '{$_SERVER['HTTP_USER_AGENT']}')";
					if(mysqli_query($link, $Sql)){
						//GravaLogSentenca($link, $Sql, $row_login['ID_USUARIO']);
					}

					$_SESSION['SIGO'] = null;
					$_SESSION['SIGO']['AJAX'] = 1;
					$_SESSION['SIGO']['ACESSO']['IP_ATUAL'] = $_SERVER['REMOTE_ADDR'];

					foreach ($row_login as $key => $r) {
						if($key != 'usr_senha' && $key != 'usr_status'){
							$_SESSION['SIGO']['ACESSO'][$key] = $r;
						}
					}

					$ret = "logou";

					//echo $row_login['usr_filial'];exit;

					}else{
						$ret = "Erro ao retornar os dados do usuário. Senha incorreta.";
				}
				}else{
					$ret = "Erro ao retornar os dados do usuário. usuário bloqueado.";
			}
			}else{
				$ret = "Erro ao retornar os dados do usuário. usuário não encontrado. ";
		}
		}else{
			$ret = "Erro ao retornar os dados do usuário. Tente novamente e caso o erro persista contate a DGE.";
	}

	mysqli_close($link);

	header("Last-Modified: {$gmtDate} GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Expires: -1");
	header("Content-Type: text/html; charset=iso-8859-1");

	echo $ret;
	exit;
}

function AjaxRecadastraSenha(){

	$usuario = strtoupper($_REQUEST['usuario']);
	$email = $_REQUEST['email'];


	$link = RetornaConexaoMysql('serverdge', 'sigo_integrado');

	$Sql = "SELECT u1.usr_id, u1.fun_id, f1.fun_nome, u1.usr_email FROM tbl_usuario u1 INNER JOIN tbl_rm_funcionario f1 ON u1.fun_id=f1.fun_id WHERE u1.usr_usuario = '$usuario'";

	//echo $Sql;exit;

	$rs_usuario = mysqli_query($link, $Sql);

	if($rs_usuario = mysqli_query($link, $Sql)){
		if(mysqli_num_rows($rs_usuario)){
			$row_usuario = mysqli_fetch_assoc($rs_usuario);
			$email_banco = $row_usuario['usr_email'];
			$nome = explode(' ',$row_usuario['fun_nome']);
			$nome = ucfirst($nome[0]);
			if($email_banco == $email){

				//$senha = GeraPalavraAleatoria(10,true);
				$senha = rand(1234,50000);
				$senha_md5 = md5($senha);

				//$Sql = "UPDATE tbl_usuario SET usr_senha='$senha_md5' WHERE usr_id='{$row_usuario['usr_id']}'";
				$Sql = "UPDATE tbl_usuario SET usr_senha='$senha_md5' WHERE fun_id='{$row_usuario['fun_id']}' and fun_id != 1";

				if($rs = mysqli_query($link, $Sql)){
					GravaLogSentenca($link, $Sql);

					$assunto = "SIGO - Recadastro de senha";
					$mensagem = "$nome<br /><br />Sua senha do SIGO foi recadastrada.<br /><br />Utilize os seguintes dados para acessar o portal:<br /><br /> Portal: <a href=\"http://sigo.telemont.com.br\">http://sigo.telemont.com.br</a> <br /> Login: <b>$usuario</b><br>Senha: <b>$senha</b>";

					if(EnviarEmail($nome,$email,$assunto,$mensagem)){

						$ret = "alterou";
						}else{
							$ret = "Não foi possível enviar e-mail de alteração de senha.";
					}

					}else{
						$ret = "Erro ao recadastrar senha. Não foi possível alterar a senha do usuário. Tente novamente e caso o erro persista contate a DGE";
				}
				}else{
					$ret = "Erro ao recadastrar senha. O e-mail fornecido não corresponde ao cadastro nesta conta de usuário.";
			}
			}else{
				$ret = "Erro ao recadastrar senha. usuário inexistente.";
		}
		}else{
			$ret = "Erro ao retornar os dados do usuário. Tente novamente e caso o erro persista contate a DGE.";
	}

	mysqli_close($link);

	$gmtDate = gmdate("D, d M Y H:i:s");
	header("Last-Modified: {$gmtDate} GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Expires: -1");
	header("Content-Type: text/html; charset=iso-8859-1");

	echo $ret;
	exit;
}

// CADASTRO DE USUARIO //
function AjaxBuscaUsuario(){

	$grupo_id = $_SESSION['SIGO']['ACESSO']['ID_GRUPO'];
	$filial = $_SESSION['SIGO']['ACESSO']['FILIAL'];

	if($grupo_id != 2){
		$where_filial = " AND u1.usr_filial='$filial'";
		//$where_privilegio = " AND g1.gru_privilegio <= 80";
		}else{
			$where_filial = "";
			$where_privilegio = "";

	}

	$valor 		= $_REQUEST['valor'];
	$situacao 	= $_REQUEST['situacao'];

	if ($situacao == '0')
		$situacao = " u1.usr_status like '%LIBERADO%' AND ";
	elseif ($situacao == '1')
		$situacao = " u1.usr_status like '%BLOQUEADO%' AND ";
	else
		$situacao = " u1.usr_status like '%%' AND ";

	$link = RetornaConexaoMysql('serverdge', 'sigo_integrado');

	if($valor != "*"){

		$Sql = "SELECT * FROM tbl_grupo WHERE gru_descricao LIKE '%$valor%' ORDER BY gru_id";
		$rs_grupo = mysqli_query($link, $Sql) or die(mysqli_error($link));
		$where_like_grupo = "";
		while ($row_grupo = mysqli_fetch_assoc($rs_grupo)){
			$where_like_grupo.= " OR u1.gru_id = '{$row_grupo['gru_id']}' OR u1.gru_id LIKE '{$row_grupo['gru_id']}|%' OR u1.gru_id LIKE '%|{$row_grupo['gru_id']}|%' OR u1.gru_id LIKE '%|{$row_grupo['gru_id']}' ";
		}

		//echo $Sql;exit;
		 $valorV = $valor;
		$where_like = "";
		$valor = explode(" ", $valor);
		foreach($valor as $v){

			$where_like.= " ('{$_REQUEST['valor']}' REGEXP u1.usr_usuario OR f1.fun_nome LIKE '%$v%' OR u1.usr_nome_visitante LIKE '%$v%' OR s1.sit_descricao LIKE '%$v%' OR u1.usr_status LIKE '%$v%' OR u1.usr_filial LIKE '%$v%' OR u1.usr_email LIKE '%$v%' OR u1.usr_atuacao_old LIKE '%$v%' OR s1.sit_descricao LIKE '%$v%' $where_like_grupo) AND";

		}

		$where_like = substr($where_like,0,-3);


		}else{
			$where_like = "1=1";
	}

	$vetGrupo = array();
	$Sql = "SELECT * FROM tbl_grupo ORDER BY gru_id";
	$rs_grupo = mysqli_query($link, $Sql);
	while ($row_grupo = mysqli_fetch_assoc($rs_grupo)){
		$vetGrupo[$row_grupo['gru_id']] =  $row_grupo['gru_descricao'];
	}



     $sqlVis = " select * from tbl_usuario u1 where ('{$_REQUEST['valor']}' REGEXP u1.usr_usuario  OR u1.usr_nome_visitante LIKE '%$valorV%' OR u1.usr_status LIKE '%$valorV%' OR u1.usr_filial LIKE '%$valorV%' OR u1.usr_email LIKE '%$valorV%' OR u1.usr_atuacao_old LIKE '%$valorV%'  OR u1.fun_id LIKE '%$valorV%')   ";
    $xml = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n";

    $rs = mysqli_query($link, $sqlVis);
    while ($rowVis = mysqli_fetch_assoc($rs)){
    //{
    //$row2 = mysqli_fetch_assoc($rs);
       // echo (strlen($row2['fun_id']) > 8); exit;
        if(strlen($rowVis['fun_id']) > 6) {
            $fun_id = 1;

            $grupos_tmp = explode('|',$rowVis['gru_id']);
            $grupos_tmp = array_unique($grupos_tmp);
            asort($grupos_tmp);
            $grupos = $grupos_tmp;
            $grupo_descricao = "";

            $cargo = "";
            $rowVis['usr_filial'] = str_replace("|", " E ",$rowVis['usr_filial']);


                foreach($grupos as $g){
                    $grupo_descricao.= "{$vetGrupo[$g]}<br />";
                }

//            $nome = $rowVis['usr_nome_visitante'] ;
//            $usuario = $rowVis['usr_usuario'];
//            $status_rm = "VISITANTE";
//            $status_usuario = $rowVis['usr_status'];
//
//
//            $usuario = strtoupper(trim($usuario));
//            if(!$usuario){die('Erro: Login do usuário não foi encontrado.');}
//
//            $xml.="<usuarios>\n";
//
//                $xml.="<usuario>\n";
//                $xml.="<id>{$rowVis['usr_id']}</id>\n";
//                $xml.="<registro><![CDATA[$usuario]]></registro>\n";
//                $xml.="<nome><![CDATA[$nome]]></nome>\n";
//                $xml.="<grupo><![CDATA[{$grupo_descricao}]]></grupo>\n";
//                $xml.="<area><![CDATA[{$rowVis['usr_filial']} - N/A]]></area>\n";
//                $xml.="<cargo><![CDATA[$cargo]]></cargo>\n";
//                $xml.="<status_rm><![CDATA[$status_rm]]></status_rm>\n";
//                $xml.="<status_usuario><![CDATA[$status_usuario]]></status_usuario>\n";
//                $xml.="<funcionario_id>{$rowVis['fun_id']}</funcionario_id>\n";
//                $xml.="</usuario>\n";
//
//                $xml.="</usuarios>";
//
//                header("Content-type: application/xml; charset=iso-8859-1");
//                $ret = $xml;
		

        }else{
                $Sql = "SELECT u1.usr_id, u1.usr_usuario, u1.usr_nome_visitante, u1.usr_filial, u1.gru_id, f1.fun_nome, f1.sit_id, u1.usr_status, s1.sit_descricao, c1.car_descricao, f1.fun_filial, u1.fun_id FROM tbl_usuario u1 LEFT JOIN tbl_rm_funcionario f1 ON u1.fun_id = f1.fun_id LEFT JOIN tbl_rm_situacao s1 ON f1.sit_id = s1.sit_id LEFT JOIN tbl_rm_cargo c1 ON f1.car_id = c1.car_id  WHERE $situacao $where_like $where_filial $where_privilegio ORDER BY f1.sit_id DESC, u1.usr_status ASC, u1.usr_nome_visitante ASC";


    //echo $Sql;exit;

    if($rs2 = mysqli_query($link, $Sql)){
        $quant_rows =mysqli_num_rows($rs2);
        if($quant_rows){
            $xml = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n";
            $xml.="<usuarios>\n";
            while ($row = mysqli_fetch_assoc($rs2)) {
                $fun_id = $row['fun_id'];

                $status_rm = $row['sit_descricao'];
                $status_usuario = $row['usr_status'];
				$nome = $row['fun_nome'];
                if($fun_id != 1){
                    $nome = $row['fun_nome'];
                    }else{
                        $nome = $row['usr_nome_visitante'];
                        $status_rm = "VISITANTE";
                }
				$nome = $row['usr_nome_visitante'];
                $nome = strtoupper($nome);

                $usuario = strtoupper($row['usr_usuario']);

                $grupos_tmp = explode('|',$row['gru_id']);
                $grupos_tmp = array_unique($grupos_tmp);
                asort($grupos_tmp);
                $grupos = $grupos_tmp;
                $grupo_descricao = "";

                $cargo = substr($row['car_descricao'],0, 30);
                if (strlen($row['car_descricao']) > 30){ $cargo.= "...";}
				$cargo = $row['car_descricao'];
				
                $row['usr_filial'] = str_replace("|", " E ",$row['usr_filial']);


                foreach($grupos as $g){
                    $grupo_descricao.= "{$vetGrupo[$g]}<br />";
                }

                $xml.="<usuario>\n";
                $xml.="<id>{$row['usr_id']}</id>\n";
                $xml.="<registro><![CDATA[$usuario]]></registro>\n";
                $xml.="<nome><![CDATA[$nome]]></nome>\n";
                $xml.="<grupo><![CDATA[{$grupo_descricao}]]></grupo>\n";
                $xml.="<area><![CDATA[{$row['usr_filial']} - {$row['fun_filial']}]]></area>\n";
                $xml.="<cargo><![CDATA[$cargo]]></cargo>\n";
                $xml.="<status_rm><![CDATA[$status_rm]]></status_rm>\n";
                $xml.="<status_usuario><![CDATA[$status_usuario]]></status_usuario>\n";
                $xml.="<funcionario_id>{$row['fun_id']}</funcionario_id>\n";
                $xml.="</usuario>\n";
            }
            $xml.="</usuarios>";

            header("Content-type: application/xml; charset=iso-8859-1");
            $ret = $xml;


        }else{
            header("Content-Type: text/html; charset=ISO-8859-1",true);
            $ret = "erro";

        } 
    }else{
        header("Content-Type: text/html; charset=ISO-8859-1",true);
        $ret = "erro";
    }
	}





        }


	mysqli_close($link);

	$gmtDate = gmdate("D, d M Y H:i:s");
	header("Last-Modified: {$gmtDate} GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Expires: -1");

	echo $ret;
	exit;
}



function AjaxExcluirItem(){
	$id 	= $_REQUEST['id'];
	$tabela = $_REQUEST['tabela'];
	$campo 	= $_REQUEST['campo'];
	$label	= $_REQUEST['label'];
	$label 	= strtolower($label);

	$link = RetornaConexaoMysql('serverdge', 'sigo_integrado');

	switch ($tabela) {
		case 'menu':
			$atualizar_menu = true;
			break;
		case 'menu_submenu':
			$atualizar_menu = true;
			break;
		case 'menu_aplicacao':
			$atualizar_menu = true;
			break;

		default:
			$where_outros = "";
			$atualizar_menu = false;
			break;
	}

	$vet_relacionamento = RetornaIdsRelacionados($link, 'tbl_'.$tabela);
	$tabela_encontrado = BuscaItemRelacionamento($vet_relacionamento,$id);

	if($tabela_encontrado == "nenhum"){
		$Sql = "DELETE FROM tbl_$tabela WHERE $campo=$id";
        if($campo == "fun_id") $Sql.= " and fun_id != 1 ";

		//echo $Sql;exit;

		if($rs = mysqli_query($link, $Sql)){
			GravaLogSentenca($link, $Sql);
			if($atualizar_menu){
				$Sql_atualiza_menu = "UPDATE tbl_usuario SET usr_atualiza_menu=1";
				GravaLogSentenca($Sql_atualiza_menu,0,0);
			}
			$ret = "excluiu";
			}else{
				$ret = "Não foi possível excluir o(a) $label. Tente novamente e caso o erro persista contate a DGE.";
		}
		}else{
			$ret = "Exclusão não permitida.\nItem utilizado na(s) seguinte(s) tabela(s): \n\n $tabela_encontrado";
	}

	mysqli_close($link);

	$gmtDate = gmdate("D, d M Y H:i:s");
	header("Last-Modified: {$gmtDate} GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Expires: -1");
	header("Content-Type: text/html; charset=iso-8859-1");

	echo $ret;
	exit;
}

function AjaxSalvarItem(){

	$atualizar_menu = false;

	$prefixo 	= $_REQUEST['prefixo'];
	$tabela 	= $_REQUEST['tabela'];
	$titulo 	= utf8_decode($_REQUEST['titulo']);
	$focus		= $_REQUEST['focus'];

	$campo_id 	= $prefixo.'id';
	$campo_nome = $focus;

	$valor_id 	= $_REQUEST[$campo_id];
	$valor_nome = $_REQUEST[$campo_nome];

	switch ($tabela) {
		case 'menu':
			$atualizar_menu = true;
			break;
		case 'menu_submenu':
			$where_outros = "men_id='{$_REQUEST['smu_men_id']}' AND";
			$atualizar_menu = true;
			break;
		case 'menu_aplicacao':
			$where_outros = "smu_id='{$_REQUEST['apl_smu_id']}' AND";
			$atualizar_menu = true;
			break;
		case 'manutencao':
			$_REQUEST['man_responsavel'] = $_SESSION['SIGO']['ACESSO']['NOME'];
			$_REQUEST['man_dthr_alteracao'] = date("Y-m-d H:i:s");
			$atualizar_menu = false;
			break;
		default:
			$where_outros = "";
			$atualizar_menu = false;
			break;
	}

	foreach ($_REQUEST as $key => $valor) {

		if(substr($key,0,strlen($prefixo)) == $prefixo && $key != $campo_id){

			$valor = utf8_decode($valor);
			if(substr($key,-5,strlen($key)) != "_link" && substr($key,-6,strlen($key)) != "_email"){
				$valor = strtoupper($valor);
			}

			$valor = str_replace('|ecom|','&',$valor);
			$_REQUEST[$key] = $valor;
		}

	}

	//print_r($_REQUEST);exit;

	$link = RetornaConexaoMysql('serverdge', 'sigo_integrado');

	$Sql = "SELECT * FROM tbl_$tabela WHERE $where_outros $campo_nome='$valor_nome' AND $campo_id<>$valor_id";
	//echo $Sql;exit;
	if ($rs = mysqli_query($link, $Sql)){
		if(! mysqli_num_rows($rs) || $tabela == "manutencao" || $tabela == "contato"){

			if($valor_id){

				$valores = "";
				$valores_sinc = "";
				foreach ($_REQUEST as $key => $valor) {
					if(substr($key,0,strlen($prefixo)) == $prefixo && $key != $campo_id){
						if(substr($key,-3,strlen($key)) == "_id"){
							$key = substr($key,4);
						}

						if ($key == "man_dt_inicio" || $key == "man_dt_fim") {

							$valor = ConvertDataHoraMysql($valor,"mysql");

						}

						$valor_sinc = str_replace('\"','|AD|',$valor);
						$valor_sinc = str_replace("\'",'|AS|',$valor_sinc);
						$valor_sinc = str_replace('"','|AD|',$valor_sinc);
						$valor_sinc = str_replace("'",'|AS|',$valor_sinc);

						$valores.= "$key='$valor',";
						$valores_sinc.= "$key='$valor_sinc',";
					}
				}


				if ($tabela == "contato"){

					$valores.= "gre_id='{$_REQUEST['gre_id']}',";
					$valores_sinc.= "gre_id='{$_REQUEST['gre_id']}',";

				}

				$valores = substr($valores,0,-1);
				$valores_sinc = substr($valores_sinc,0,-1);

				$Sql = "UPDATE tbl_$tabela SET $valores WHERE $campo_id=$valor_id";
				$Sql_sinc = "UPDATE tbl_$tabela SET $valores_sinc WHERE $campo_id=$valor_id";

				if($rs = mysqli_query($link, $Sql)){
					GravaLogSentenca($link, $Sql_sinc);
					if($atualizar_menu){
						$Sql_atualiza_menu = "UPDATE tbl_usuario SET usr_atualiza_menu=1";
						GravaLogSentenca($Sql_atualiza_menu,0,0);
					}
					$ret = "alterou";
				}else{
					$ret = "Não foi possível alterar os dados do(a) $titulo. Tente novamente e caso o erro persista contate a DGE.";
				}
				}else{
					$valores_sinc = "$campo_id";
					$valores = "$campo_id";
					$campos  = "$campo_id";
					foreach ($_REQUEST as $key => $valor) {

						if(substr($key,0,strlen($prefixo)) == $prefixo && $key != $campo_id){
							if(substr($key,-3,strlen($key)) == "_id"){
								$key = substr($key,4);
							}

							if ($key == "man_dt_inicio" || $key == "man_dt_fim") {
								$valor = ConvertDataHoraMysql($valor,"mysql");
							}

							$valor_sinc = str_replace('\"','|AD|',$valor);
							$valor_sinc = str_replace("\'",'|AS|',$valor_sinc);
							$valor_sinc = str_replace('"','|AD|',$valor_sinc);
							$valor_sinc = str_replace("'",'|AS|',$valor_sinc);
							$valores.= ",'$valor'";
							$valores_sinc.= ",'$valor_sinc'";
							$campos.= ",$key";
						}

					}

					if ($tabela == "contato"){

						$valores.= ",'{$_REQUEST['gre_id']}'";
						$valores_sinc.= ",'{$_REQUEST['gre_id']}'";
						$campos.= ",gre_id";

					}

					$Sql = "INSERT INTO tbl_$tabela ($campos) VALUES ($valores)";
					$Sql_sinc = "INSERT INTO tbl_$tabela ($campos) VALUES ($valores_sinc)";
					if($rs = mysqli_query($link, $Sql)){
						GravaLogSentenca($link, $Sql_sinc);
						if($atualizar_menu){
							$Sql_atualiza_menu = "UPDATE tbl_usuario SET usr_atualiza_menu=1";
							GravaLogSentenca($Sql_atualiza_menu,0,0);
						}
						$ret = "inseriu";
					}else{
						$ret = "Não foi possível cadastrar o $titulo. Tente novamente e caso o erro persista contate a DGE.";
					}
			}

			if ($tabela == "manutencao"){
				$grupos = str_replace("|"," ",$_REQUEST['man_grupo']);
				$Sql = "SELECT * FROM tbl_contato WHERE '$grupos' REGEXP gre_id";
				//echo $Sql;exit;
				$rs_grupo_email  = mysqli_query($link, $Sql);

				while ($row_grupo_email = mysqli_fetch_assoc($rs_grupo_email)) {
					$vetNome.= $row_grupo_email['con_nome'].",";
					$vetEmail.= $row_grupo_email['con_email'].",";
				}

				$vetNome = substr($vetNome,0,-1);
				$vetEmail = substr($vetEmail,0,-1);



				$arquivo_email=file("../includes/manutencao.html");
				$texto="";
				foreach($arquivo_email as $arq){
					$texto.=$arq."\r\n";
				}

				$texto=@preg_replace("/SISTEMA/",$_REQUEST['man_sistema'],$texto);
				$texto=@preg_replace("/DESCRICAO/",nl2br($_REQUEST['man_descricao']),$texto);
				$texto=@preg_replace("/IMPACTO/",nl2br($_REQUEST['man_impacto']),$texto);
				$texto=@preg_replace("/DATA_INICIO/",$_REQUEST['man_dt_inicio'],$texto);
				$texto=@preg_replace("/HORA_INICIO/",$_REQUEST['man_hora_inicio'],$texto);
				$texto=@preg_replace("/DATA_FIM/",$_REQUEST['man_dt_fim'],$texto);
				$texto=@preg_replace("/HORA_FIM/",$_REQUEST['man_hora_fim'],$texto);


				EnviarEmail($vetNome, $vetEmail,"COMUNICADO: Desenvolvimento de Sistemas em TI - DGE",$texto);

			}

			}else{
				$ret = "Este $titulo já está cadastrado no SIGO.";
		}
		}else{
			$ret = "Não foi possível verificar a existência do(a) $titulo. Tente novamente e caso o erro persista contate a DGE.";
	}

	mysqli_close($link);

	$gmtDate = gmdate("D, d M Y H:i:s");
	header("Last-Modified: {$gmtDate} GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Expires: -1");
	header("Content-Type: text/html; charset=iso-8859-1");

	//$ret = $Sql;
	echo $ret;
	exit;
}

function AjaxEditarItem(){

	$prefixo 	= $_REQUEST['prefixo'];
	$tabela 	= $_REQUEST['tabela'];
	$titulo 	= $_REQUEST['titulo'];
	$focus		= $_REQUEST['focus'];

	$campo_id 	= $prefixo.'id';
	$valor_id 	= $_REQUEST['id'];

	$link = RetornaConexaoMysql('serverdge', 'sigo_integrado');
	$Sql = "SELECT * FROM tbl_$tabela WHERE $campo_id=$valor_id";

	if($rs = mysqli_query($link, $Sql)){
		if(mysqli_num_rows($rs)){
			$row = mysqli_fetch_assoc($rs);

			$xml = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n";
			$xml.="<items>\n";
			$xml.="<item>\n";

			foreach ($row as $key => $valor){
				if(substr($key,0,strlen($prefixo)) != $prefixo){
					$key = $prefixo.$key;
				}

				if ($key == "man_dt_inicio" || $key == "man_dt_fim") {
					$valor = ConvertDataHoraMysql($valor,"normal");
					}else if ($key == "man_dthr_alteracao"){
						$valor = ConvertDataHoraMysql($valor,"normal",true);
				}

				$xml.="<".trim($key)."><![CDATA[".trim($valor)."]]></".trim($key).">\n";
			}

			$xml.="</item>\n";
			$xml.="</items>";

			header("Content-type: application/xml; charset=iso-8859-1");
			$ret = $xml;

			}else{
				header("Content-Type: text/html; charset=ISO-8859-1",true);
				$ret = "$titulo não encontrado.";

		}
		}else{
			header("Content-Type: text/html; charset=ISO-8859-1",true);
			$ret = "Erro ao retornar dados do(a) $titulo.";
	}

	mysqli_close($link);

	$gmtDate = gmdate("D, d M Y H:i:s");
	header("Last-Modified: {$gmtDate} GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Expires: -1");

	echo $ret;
	exit;
}

function AjaxEditarUsuario(){
	$id = $_REQUEST['id'];

    $link = RetornaConexaoMysql('serverdge', 'sigo_integrado');

    //editar Visitante
    $sqlVis = "SELECT * FROM tbl_usuario WHERE fun_id = $id";
    $rs = mysqli_query($link, $sqlVis)  ;
    $row2 = mysqli_fetch_assoc($rs);
    
    if(strlen($row2['fun_id']) > 6)
    {
        $fun_id = 1;

        $nome = strtoupper($row2['usr_nome_visitante']);

        $observacao = strtoupper($row2['usr_observacao']);
        $email = $row2['usr_email'];

        $grupos_tmp = explode('|',$row2['gru_id']);
        $grupos_tmp = array_unique($grupos_tmp);
        asort($grupos_tmp);
        $grupos = implode("|",$grupos_tmp);


        $grupo_descricao = "";

        $cargo = "";
       // $row2['usr_filial'] = str_replace("|", " E ",$row2['usr_filial']);


        $usuario = $row2['usr_usuario'];
        $status_rm = "VISITANTE";
        $status_usuario = $row2['usr_status'];
        $filial = $row2['usr_filial'];

        $xml.="<usuarios>\n";

        $xml.="<usuario>\n";
        $xml.="<id>{$row2['usr_id']}</id>\n";
        $xml.="<registro>$usuario</registro>\n";
        $xml.="<nome><![CDATA[$nome]]></nome>\n";
        $xml.="<email><![CDATA[{$email}]]></email>\n";
        $xml.="<grupo><![CDATA[{$grupos}]]></grupo>\n";
        $xml.="<cargo><![CDATA[$cargo]]></cargo>\n";
        $xml.="<area><![CDATA[$filial]]></area>\n";
        $xml.="<atuacao_old><![CDATA[". $row2['usr_atuacao_old'] ."]]></atuacao_old>\n";
        $xml.="<status_rm><![CDATA[$status_rm]]></status_rm>\n";
        $xml.="<status_usuario><![CDATA[$status_usuario]]></status_usuario>\n";
        $xml.="<funcionario_id>{$row2['fun_id']}</funcionario_id>\n";
        $xml.="<obs><![CDATA[$observacao]]></obs>\n";
        $xml.="</usuario>\n";


        if(!$conf){
            header("Content-Type: text/html; charset=ISO-8859-1",true);
            $ret = "Usuário não encontrado.";

        }
        if(!$rs){
            header("Content-Type: text/html; charset=ISO-8859-1",true);
            $ret = "Erro ao retornar dados do usuário.";
        }

    }
    else
    {


	    //$Sql = "SELECT u1.*, f1.*, s1.*, c1.* FROM tbl_usuario u1 INNER JOIN tbl_rm_funcionario f1 ON u1.fun_id = f1.fun_id INNER JOIN tbl_rm_situacao s1 ON f1.sit_id = s1.sit_id LEFT JOIN tbl_rm_cargo c1 ON f1.car_id = c1.car_id WHERE u1.usr_id =$id";
	    $Sql = "SELECT u1.*, f1.*, s1.*, c1.* FROM tbl_usuario u1 INNER JOIN tbl_rm_funcionario f1 ON u1.fun_id = f1.fun_id INNER JOIN tbl_rm_situacao s1 ON f1.sit_id = s1.sit_id LEFT JOIN tbl_rm_cargo c1 ON f1.car_id = c1.car_id WHERE u1.fun_id = $id";

	    $rs = mysqli_query($link, $Sql); //{
		$conf = mysqli_num_rows($rs);//{
		$row = mysqli_fetch_assoc($rs);

		$fun_id = $row['fun_id'];
		if($fun_id!= 1){
			$nome = $row['fun_nome'];
			}else{
				$nome = $row['usr_nome_visitante'];
				$row['sit_descricao'] = "usuário VISITANTE";
		}

		$nome = strtoupper($nome);

		$observacao = strtoupper($row['usr_observacao']);
		$email = $row['usr_email'];

		$grupos_tmp = explode('|',$row['gru_id']);
		$grupos_tmp = array_unique($grupos_tmp);
		asort($grupos_tmp);
		$grupos = implode("|",$grupos_tmp);

		$grupo_descricao = "";

		$xml = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n";
		$xml.="<usuarios>\n";
		$xml.="<usuario>\n";
		$xml.="<id>{$row['usr_id']}</id>\n";
		$xml.="<registro>{$row['usr_usuario']}</registro>\n";
		$xml.="<nome><![CDATA[$nome]]></nome>\n";
		$xml.="<email><![CDATA[{$email}]]></email>\n";
		$xml.="<grupo><![CDATA[{$grupos}]]></grupo>\n";
		$xml.="<cargo><![CDATA[{$row['car_descricao']}]]></cargo>\n";
		$xml.="<area><![CDATA[{$row['usr_filial']}]]></area>\n";
		$xml.="<atuacao_old><![CDATA[{$row['usr_atuacao_old']}]]></atuacao_old>\n";
		$xml.="<status_usuario>{$row['usr_status']}</status_usuario>\n";
		$xml.="<status_rm>{$row['sit_descricao']}</status_rm>\n";
		$xml.="<funcionario_id>{$row['fun_id']}</funcionario_id>\n";
		$xml.="<obs><![CDATA[$observacao]]></obs>\n";
		$xml.="</usuario>\n";
    }

	$area = $row['usr_filial'];

	switch ($area){
		case 'AREA2':
			$area_label = "gru_descricao LIKE '%AREA 2%'";
			break;

		case 'AREA1_MG':
			$area_label = "gru_descricao LIKE '%AREA 1 MG%' or gru_descricao LIKE '%SEGURO%' ";
			break;

		case 'AREA1_RJ':
			$area_label = "gru_descricao LIKE '%AREA 1 RJ%'";
			break;

		case 'AREA1_ES':
			$area_label = "gru_descricao LIKE '%AREA 1 ES%'";
			break;

		case 'AREA1_MG|AREA1_ES':
			$area_label = "(gru_descricao LIKE '%AREA 1 ES%' OR gru_descricao LIKE '%AREA 1 MG%')";
			break;
		default:
			$area_label = "1=1";
			break;
	}
	$grupo_id = $_SESSION['SIGO']['ACESSO']['ID_GRUPO'];
	if($grupo_id == 2){
		$where_grupo = " OR gru_id < 5 OR gru_id=67";
		}else{
			$where_grupo = " AND gru_privilegio < 80";
	}

	$Sql = "SELECT gru_id, gru_descricao FROM tbl_grupo WHERE  $area_label $where_grupo ORDER BY gru_descricao ASC";
	if ($rs_grupo = mysqli_query($link, $Sql)){

		while ($row_grupo = mysqli_fetch_assoc($rs_grupo)) {
			$xml.="<grupo_select>\n";
			$xml.="<id>{$row_grupo["gru_id"]}</id>\n";
			$xml.="<descricao><![CDATA[{$row_grupo["gru_descricao"]}]]></descricao>\n";
			$xml.="</grupo_select>\n";
		}
		}else{
			die(mysqli_error($link));
			header("Content-Type: text/html; charset=iso-8859-1");
	}

	$xml.="</usuarios>";

	header("Content-type: application/xml; charset=iso-8859-1");
	$ret = $xml;


	mysqli_close($link);

	$gmtDate = gmdate("D, d M Y H:i:s");
	header("Last-Modified: {$gmtDate} GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Expires: -1");

	echo $ret;
	exit;
}

function AjaxSalvarUsuario(){

	$id 	= $_REQUEST['id'];
	$nome 	= $_REQUEST['nome'];
	$usuario= $_REQUEST['usuario'];
	$email 	= $_REQUEST['email'];
	$grupo 	= $_REQUEST['grupo'];
	$area 	= $_REQUEST['area'];
	$atuacao_old = utf8_decode($_REQUEST['atuacao_old']);
	$status_usuario = $_REQUEST['status_usuario'];
	$fun_id = $_REQUEST['fun_id'];

	$link = RetornaConexaoMysql('serverdge', 'sigo_integrado');

	//$Sql = "SELECT usr_status FROM tbl_usuario WHERE usr_id=$id";
	$Sql = "SELECT usr_status FROM tbl_usuario WHERE fun_id = $fun_id";
	$rs = mysqli_query($link, $Sql);
	$row = mysqli_fetch_assoc($rs);
	$status_atual = $row['usr_status'];

	$senha_123 = "";

	if($status_atual == "BLOQUEADO" && $status_usuario == "LIBERADO"){
		$senha_123 = ",usr_senha = '202cb962ac59075b964b07152d234b70', usr_senha_padrao=1";
	}

	//$Sql = "UPDATE tbl_usuario set usr_email='$email', gru_id='$grupo', usr_filial='$area', usr_atuacao_old='$atuacao_old', usr_status='$status_usuario' $senha_123 WHERE usr_id=$id";
	$Sql = "UPDATE tbl_usuario set usr_email='$email', gru_id='$grupo', usr_filial='$area', usr_atuacao_old='$atuacao_old', usr_status='$status_usuario' $senha_123 WHERE fun_id = $fun_id and fun_id != 1";

	//echo $Sql;exit;

	if($rs = mysqli_query($link, $Sql)){
		GravaLogSentenca($link, $Sql);
		if($status_atual == "BLOQUEADO" && $status_usuario == "LIBERADO"){
			$assunto = "SIGO - Liberação de usuário";
			$mensagem = "$nome<br /><br />Seu login do SIGO foi liberado.<br /><br />Utilize os seguintes dados para acessar o portal:<br /><br />  Portal: <a href=\"http://sigo.telemont.com.br\">http://sigo.telemont.com.br</a> <br /> Login: <b>$usuario</b><br />Senha: <b>123</b>";

			EnviarEmail($nome,$email,$assunto,$mensagem);
		}
		$ret = "salvou";
	}else{
		$ret = "Não foi possível alterar os dados do usuário. Tente novamente e caso o erro persista contate a DGE.";
	}

	mysqli_close($link);

	$gmtDate = gmdate("D, d M Y H:i:s");
	header("Last-Modified: {$gmtDate} GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Expires: -1");
	header("Content-Type: text/html; charset=iso-8859-1");

	echo $ret;
	exit;
}

function AjaxInserirVisitante(){
	$_REQUEST['usuario'] = utf8_decode($_REQUEST['usuario']);
	$_REQUEST['email'] = utf8_decode($_REQUEST['email']);
	$_REQUEST['observacao'] = utf8_decode($_REQUEST['observacao']);
	$_REQUEST['nome'] = utf8_decode($_REQUEST['nome']);

	$usuario = strtoupper($_REQUEST['usuario']);
	$filial = $_REQUEST['area'];
	$email = $_REQUEST['email'];
	$nome = explode('-',$_REQUEST['nome']);
	$nome = strtoupper($nome[0]);
	$observacao = nl2br($_REQUEST['observacao']);
    $cpf = $_REQUEST['cpf'];

	$nome_mail = explode(' ',$nome);
	$nome_mail = htmlentities(utf8_decode($nome_mail[0]));
	//print_r($_REQUEST);exit;

	$senha = GeraPalavraAleatoria(10,true);
	$senha = '123';
	$senha_md5 = md5($senha);

	$link = RetornaConexaoMysql('serverdge', 'sigo_integrado');

	$Sql = "SELECT usr_id FROM tbl_usuario WHERE usr_usuario='$usuario' ";
	if(!$rs = mysqli_query($link, $Sql)){
		$ret = "Ocorreu um erro no processamento de sua solicitação: ".mysqli_error($link);
	}elseif(mysqli_num_rows($rs) == 0){

		$Sql = "SELECT usr_id FROM tbl_usuario WHERE fun_id = '$cpf' ";
		if(!$rs = mysqli_query($link, $Sql)){
			$ret = "Ocorreu um erro no processamento de sua solicitação: ".mysqli_error($link);
		}elseif(mysqli_num_rows($rs) == 0){

			$Sql = "INSERT INTO tbl_usuario (usr_usuario, usr_senha, usr_email, usr_data_cadastro, usr_observacao, usr_nome_visitante, usr_filial, usr_status, fun_id, gru_id) VALUES ('$usuario', '$senha_md5', '$email', NOW(), '$observacao', '$nome', '$filial', 'BLOQUEADO', $cpf, 1)";

			if($rs = mysqli_query($link, $Sql)){
				GravaLogSentenca($link, $Sql);
				$ret = "inseriu";
			}else{
				$ret = "Não foi possível criar o usuário.";
			}

		}else{
			$ret = "CPF já esta cadastrado.";
		}

	}else{
		$ret = "usuário já utilizado.";
	}

	mysqli_close($link);

	$gmtDate = gmdate("D, d M Y H:i:s");
	header("Last-Modified: {$gmtDate} GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Expires: -1");
	header("Content-Type: text/html; charset=iso-8859-1");

	echo $ret;
	exit;
}

function AjaxSalvarGrupo(){

	$id = $_REQUEST['gru_id'];
	$descricao = utf8_decode(strtoupper($_REQUEST['gru_descricao']));
	$area = utf8_decode(strtoupper($_REQUEST['gru_area']));
	$sistema = utf8_decode(strtoupper($_REQUEST['sistema']));

	$link = RetornaConexaoMysql('serverdge', 'sigo_integrado');

	$Sql = "SELECT * FROM tbl_grupo WHERE gru_descricao='$descricao' AND gru_id<>$id";
	if ($rs = mysqli_query($link, $Sql)){
		if(! mysqli_num_rows($rs)){
			if($id){
				$Sql = "UPDATE tbl_grupo SET gru_descricao='$descricao',sis_id='$sistema',gru_area='$area' WHERE gru_id=$id";
				if($rs = mysqli_query($link, $Sql)){
					GravaLogSentenca($link, $Sql);
					$ret = "alterou";
					}else{
						$ret = "Não foi possível alterar os dados do grupo. Tente novamente e caso o erro persista contate a DGE.";
				}
				}else{
				$Sql = "INSERT INTO tbl_grupo VALUES (gru_id, '$descricao', gru_privilegio, '$sistema', '$area')";

				if($rs = mysqli_query($link, $Sql)){
					GravaLogSentenca($link, $Sql);
					$ret = "inseriu";
					}else{
						$ret = "Não foi possível cadastrar o grupo. Tente novamente e caso o erro persista contate a DGE.";
				}
			}
			}else{
				$ret = "Este grupo já está cadastrado no SIGO.";
		}
		}else{
			$ret = "Não foi possível verificar a existência do grupo. Tente novamente e caso o erro persista contate a DGE.";
	}

	mysqli_close($link);

	$gmtDate = gmdate("D, d M Y H:i:s");
	header("Last-Modified: {$gmtDate} GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Expires: -1");
	header("Content-Type: text/html; charset=iso-8859-1");

	echo $ret;
	exit;

}

function AjaxBuscaGrupo(){
	$id = $_REQUEST['gru_id'];

	$link = RetornaConexaoMysql('serverdge', 'sigo_integrado');
	$Sql = "SELECT * FROM tbl_grupo WHERE gru_id=$id";

	//echo $Sql;exit;

	if($rs = mysqli_query($link, $Sql)){
		if(mysqli_num_rows($rs)){
			$row = mysqli_fetch_assoc($rs);

			$xml = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n";
			$xml.="<grupos>\n";
			$xml.="<grupo>\n";
			$xml.="<id>{$row["gru_id"]}</id>\n";
			$xml.="<descricao><![CDATA[{$row["gru_descricao"]}]]></descricao>\n";
			$xml.="<sistema><![CDATA[{$row['sis_id']}]]></sistema>\n";
			$xml.="<area><![CDATA[{$row['gru_area']}]]></area>\n";
			$xml.="</grupo>\n";
			$xml.="</grupos>";

			header("Content-type: application/xml; charset=iso-8859-1");
			$ret = $xml;


			}else{
				header("Content-Type: text/html; charset=ISO-8859-1",true);
				$ret = "Grupo não encontrado.";

		}
		}else{
			header("Content-Type: text/html; charset=ISO-8859-1",true);
			$ret = "Erro ao retornar dados do grupo.";
	}

	mysqli_close($link);

	$gmtDate = gmdate("D, d M Y H:i:s");
	header("Last-Modified: {$gmtDate} GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Expires: -1");

	echo $ret;
	exit;
}

function AjaxEditarCampo(){

	$prefixo 	= $_REQUEST['prefixo'];
	$id 		= $_REQUEST['id'];
	$tabela 	= $_REQUEST['tabela'];
	$campo		= $_REQUEST['campo'];
	$valor		= $_REQUEST['valor'];
	$multiplo	= $_REQUEST['multiplo'];

	$campo_id 	= $prefixo.'id';

	switch ($tabela) {
		case 'menu':
			$atualizar_menu = true;
			break;
		case 'menu_submenu':
			$atualizar_menu = true;
			break;
		case 'menu_aplicacao':
			$atualizar_menu = true;
			break;

		default:
			$atualizar_menu = false;
			break;
	}

	$link = RetornaConexaoMysql('serverdge', 'sigo_integrado');

	if(empty($multiplo)){

		if($campo == "gru_id" && $tabela == "usuario")
		{
			//$Sql = "UPDATE tbl_usuario SET gru_id=CONCAT(gru_id, '|{$valor}') WHERE usr_id=$id AND (gru_id <> '2' AND gru_id <> '3' AND gru_id <> '4')";
			$Sql = "UPDATE tbl_usuario SET gru_id=CONCAT(gru_id, '|{$valor}') WHERE fun_id= $id and fun_id != 1 AND (gru_id <> '2' AND gru_id <> '3' AND gru_id <> '4')";
		}else{
				$Sql = "UPDATE tbl_$tabela SET $campo='$valor' WHERE $campo_id=$id ";
				if($campo_id == 'fun_id') $Sql.= " and fun_id != 1";
		}

		if($rs = mysqli_query($link, $Sql)){
			//echo $Sql;exit;
			if ($tabela != "pendencia"){
				GravaLogSentenca($link, $Sql);
			}

			if($atualizar_menu){
				$Sql_atualiza_menu = "UPDATE tbl_usuario SET usr_atualiza_menu=1";
				GravaLogSentenca($link, $Sql_atualiza_menu,0,0);
			}

			if ($campo == "usr_status" && $valor=="LIBERADO" || $campo == "usr_senha"){

				$Sql = "SELECT f1.fun_id, f1.fun_nome, u1.usr_usuario, u1.usr_nome_visitante, u1.usr_email FROM tbl_usuario u1 INNER JOIN tbl_rm_funcionario f1 ON f1.fun_id=u1.fun_id WHERE u1.usr_id=$id";
				$rs = mysqli_query($link, $Sql);
				$row = mysqli_fetch_assoc($rs);

				if($row['fun_id'] == 1){
					$nome = $row['usr_nome_visitante'];
					}else{
						$nome = $row['fun_nome'];
				}

				$usuario = $row['usr_usuario'];
				$email = $row['usr_email'];

				$assunto = "SIGO - Liberação de usuário";
				$mensagem = "$nome<br /><br />Seu login do SIGO foi liberado.<br /><br />Utilize os seguintes dados para acessar o portal:<br /><br />  Portal: <a href=\"http://sigo.telemont.com.br\">http://sigo.telemont.com.br</a> <br /> Login: <b>$usuario</b><br />Senha: <b>123</b>";

				if(EnviarEmail($nome,$email,$assunto,$mensagem)){
					$ret = "alterou";
					}else{
						$ret = "Usuário liberado Porem, não foi possível enviar um e-mail para o usuário.";
				}
				}else{
					$ret = "alterou";
			}
			}else{
				$ret = "Não foi possível alterar o dado. Tente novamente e caso o erro persista contate a DGE.";
				//$ret = mysqli_error($link);
		}
		}else{

			$ids  = explode("|", $id);
			foreach ($ids as $id) {
				$where_multiplo.= "$campo_id='$id' OR ";
			}
			$where_multiplo = substr($where_multiplo,0,-3);

			if ($valor != "excluir"){
				if($campo == "gru_id" && $tabela == "usuario"){
					$Sql = "UPDATE tbl_usuario SET gru_id= if (gru_id='1','{$valor}', CONCAT(gru_id, '|{$valor}')) WHERE ($where_multiplo) AND (gru_id <> '2' AND gru_id <> '3' AND gru_id <> '4')";
					}else{
						$Sql = "UPDATE tbl_$tabela SET $campo='$valor' WHERE $where_multiplo";
				}
				}else{
					$Sql = "DELETE FROM tbl_$tabela WHERE $where_multiplo";
			}

			//echo $Sql;exit;

			if($rs = mysqli_query($link, $Sql)){
				GravaLogSentenca($link, $Sql);

				if($atualizar_menu){
					$Sql_atualiza_menu = "UPDATE tbl_usuario SET usr_atualiza_menu=1";
					GravaLogSentenca($link, $Sql_atualiza_menu,0,0);
				}

				// envia email de liberacao para todos os usuarios selecionados

				if ($campo == "usr_status" && $valor=="LIBERADO" || $campo == "usr_senha"){
					$nome = array();
					$email = array();
					$usuario = array();

					$Sql = "SELECT f1.fun_id, f1.fun_nome, u1.usr_usuario, u1.usr_nome_visitante, u1.usr_email FROM tbl_usuario u1 INNER JOIN tbl_rm_funcionario f1 ON f1.fun_id=u1.fun_id WHERE $where_multiplo";
					$rs = mysqli_query($link, $Sql);
					while ($row = mysqli_fetch_assoc($rs)){
						if($row['fun_id'] == 1){
							array_push($nome,$row['usr_nome_visitante']);
							}else{
								array_push($nome,$row['fun_nome']);
						}

						array_push($usuario,$row['usr_usuario']);
						array_push($email,$row['usr_email']);
					}

					$assunto = "SIGO - Liberação de usuário";

					foreach ($nome as $k => $n) {
						$e = $email[$k];
						$u = $usuario[$k];

						$mensagem = "$n<br /><br />Seu login do SIGO foi liberado.<br /><br />Utilize os seguintes dados para acessar o portal:<br /><br />  Portal: <a href=\"http://sigo.telemont.com.br\">http://sigo.telemont.com.br</a> <br /> Login: <b>$u</b><br />Senha: <b>123</b>";

						EnviarEmail($n,$e,$assunto,$mensagem);
					}
				}
				$ret = "alterou";
				}else{
					$ret = "Não foi possível alterar o dado. Tente novamente e caso o erro persista contate a DGE.";
					//$ret = mysqli_error($link);
			}
	}

	mysqli_close($link);

	$gmtDate = gmdate("D, d M Y H:i:s");
	header("Last-Modified: {$gmtDate} GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Expires: -1");
	header("Content-Type: text/html; charset=iso-8859-1");

	echo $ret;
	exit;
}

function AjaxSalvarPermissao(){

	$tipo_permissao 	= $_REQUEST['tipo_permissao'];
	$id_tipo_permissao 	= $_REQUEST['id_tipo_permissao'];
	$tipo_menu 			= $_REQUEST['tipo_menu'];
	$id_tipo_menu		= $_REQUEST['id_tipo_menu'];
	$valor				= $_REQUEST['valor'];
	$nivel				= $_REQUEST['nivel'];

	if(!$valor){
		$nivel = 0;
	}

	$valor = $valor=="true" ? 1 : 0;
	$campo_tipo_permissao = $tipo_permissao == "usuario" ? 'usr_id' : 'gru_id';
	$campo_tipo_menu	  = $tipo_menu == "submenu" ? 'smu_id' : 'apl_id';

	$link = RetornaConexaoMysql('serverdge', 'sigo_integrado');

	$Sql = "SELECT * FROM tbl_permissao WHERE $campo_tipo_permissao=$id_tipo_permissao AND $campo_tipo_menu=$id_tipo_menu";
	//echo $Sql;exit;
	if($rs = mysqli_query($link, $Sql)){
		if(mysqli_num_rows($rs)){
			if($valor){
				$Sql = "UPDATE tbl_permissao SET per_habilitar=$valor, per_nivel=$nivel WHERE $campo_tipo_permissao=$id_tipo_permissao AND $campo_tipo_menu=$id_tipo_menu ";
				}else{
					$Sql = "DELETE FROM tbl_permissao WHERE $campo_tipo_permissao=$id_tipo_permissao AND $campo_tipo_menu=$id_tipo_menu ";
			}
			}else{
				$Sql = "INSERT INTO  tbl_permissao ($campo_tipo_permissao, $campo_tipo_menu, per_habilitar, per_nivel) VALUES ($id_tipo_permissao, $id_tipo_menu, $valor, $nivel)";
		}
		//echo $Sql;exit;

		if($rs = mysqli_query($link, $Sql)){
			GravaLogSentenca($link, $Sql);
			$ret = "ok";
			}else{
				$ret = "Não foi possível salvar a permissão. Tente novamente e caso o erro persista contate a DGE.";
		}
		}else{
			$ret = "Não foi possível salvar a permissão. Tente novamente e caso o erro persista contate a DGE.";
	}

	mysqli_close($link);

	$gmtDate = gmdate("D, d M Y H:i:s");
	header("Last-Modified: {$gmtDate} GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Expires: -1");
	header("Content-Type: text/html; charset=iso-8859-1");

	echo $ret;
	exit;
}

function AjaxSalvarPermissaoMultipla(){

	//print_r($_REQUEST);exit();

	$tipo_permissao 	= $_REQUEST['tipo_permissao'];
	$id_tipo_permissao 	= $_REQUEST['id_tipo_permissao'];
	$submenus 			= substr($_REQUEST['submenus'],0,-1);
	$apps	 			= substr($_REQUEST['apps'],0,-1);
	$valor 				= $_REQUEST['valor'];

	$vetSubmenus = explode("|",$submenus);
	$vetApps = explode("|",$apps);

	if(!$valor){
		$nivel = 0;
	}

	$valor = $valor=="true" ? 1 : 0;

	$campo_tipo_permissao = $tipo_permissao == "usuario" ? 'usr_id' : 'gru_id';
	$campo_tipo_menu	  = $tipo_menu == "submenu" ? 'smu_id' : 'apl_id';

	$link = RetornaConexaoMysql('serverdge', 'sigo_integrado');



	foreach ($vetApps as $v) {

		$Sql = "SELECT * FROM tbl_permissao WHERE $campo_tipo_permissao=$id_tipo_permissao AND apl_id=$v";
		if($rs = mysqli_query($link, $Sql)){
			if(mysqli_num_rows($rs)){
				if ($valor == 1) {
					$Sql = "UPDATE tbl_permissao SET per_habilitar=1 WHERE $campo_tipo_permissao=$id_tipo_permissao AND apl_id=$v ";
					}else{
						$Sql = "DELETE FROM tbl_permissao WHERE $campo_tipo_permissao=$id_tipo_permissao AND apl_id=$v ";
				}
				}else{
					$Sql = "INSERT INTO tbl_permissao ($campo_tipo_permissao, apl_id, per_habilitar, per_nivel) VALUES ($id_tipo_permissao, $v, 1, 0)";
			}
		}

		if($rs = mysqli_query($link, $Sql)){
			GravaLogSentenca($link, $Sql);
			$ret = "ok";
			}else{
				$ret = mysqli_error($link);
		}
	}

	foreach ($vetSubmenus as $v) {

		$Sql = "SELECT * FROM tbl_permissao WHERE $campo_tipo_permissao=$id_tipo_permissao AND smu_id=$v";
		if($rs = mysqli_query($link, $Sql)){
			if(mysqli_num_rows($rs)){
				if ($valor == 1) {
					$Sql = "UPDATE tbl_permissao SET per_habilitar=1 WHERE $campo_tipo_permissao=$id_tipo_permissao AND smu_id=$v ";
					}else{
						$Sql = "DELETE FROM tbl_permissao WHERE $campo_tipo_permissao=$id_tipo_permissao AND smu_id=$v ";
				}
				}else{
					$Sql = "INSERT INTO tbl_permissao ($campo_tipo_permissao, smu_id, per_habilitar, per_nivel) VALUES ($id_tipo_permissao, $v, 1, 0)";
			}
		}

		if($rs = mysqli_query($link, $Sql)){
			GravaLogSentenca($link, $Sql);
			$ret = "ok";
			}else{
				$ret = mysqli_error($link);
		}
	}

	mysqli_close($link);

	$gmtDate = gmdate("D, d M Y H:i:s");
	header("Last-Modified: {$gmtDate} GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Expires: -1");
	header("Content-Type: text/html; charset=iso-8859-1");

	echo $ret;
	exit;
}

function AjaxTrocarSenha(){
	$senha 		= md5($_REQUEST['senha']);
	$nova_senha = md5($_REQUEST['nova_senha']);
	$id = $_SESSION['SIGO']['ACESSO']['ID_USUARIO'];
	
	if($senha == $nova_senha){
		$ret = "Não é possível cadastrar a mesma senha novamente";
	}else if($nova_senha == "123"){
		$ret = "Não é possível cadastrar a senha '123'";
	}else{
	
		$link = RetornaConexaoMysql('serverdge', 'sigo_integrado');
	
		$Sql = "SELECT usr_senha, fun_id FROM tbl_usuario WHERE usr_id='$id'";
		
		if ($rs = mysqli_query($link, $Sql)){
			if(mysqli_num_rows($rs) > 0){
				$row = mysqli_fetch_assoc($rs);
				$senha_banco = $row['usr_senha'];
				$fun_id 	 = $row['fun_id'];
				if($senha_banco == $senha){
					mysqli_close($link);
					$link2 = RetornaConexaoMysql('serverdge', 'sigo_integrado');
					//$Sql = "UPDATE tbl_usuario SET usr_senha='$nova_senha', usr_senha_padrao=0 WHERE usr_id='$id'";
					$Sql = "UPDATE tbl_usuario SET usr_senha='$nova_senha', usr_senha_padrao=0, usr_data_troca_senha=NOW() WHERE fun_id = $fun_id and fun_id != 1 ";
					mysqli_query($link2, $Sql);
					$_SESSION['SIGO']['ACESSO']['SENHA_PADRAO'] = 0;
					GravaLogSentenca($link2, $Sql,$id,0);
					$ret = "alterou";
					mysqli_close($link2);
				}else{
					$ret = "Senha incorreta.";
				}
			}else{
				$ret = "Erro ao retornar os dados do usuário. Tente novamente e caso o erro persista contate a DGE.";
			}
		}else{
			$ret = "Erro ao retornar os dados do usuário. Tente novamente e caso o erro persista contate a DGE.";
		}
	}

	$gmtDate = gmdate("D, d M Y H:i:s");
	header("Last-Modified: {$gmtDate} GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Expires: -1");
	header("Content-Type: text/html; charset=iso-8859-1");

	echo $ret;
	exit;

}

function AjaxTrocarSenha2(){
	
    $senha 		= md5(rawurldecode(trim($_REQUEST['senha'])));
    $nova_senha = md5(rawurldecode(trim($_REQUEST['nova_senha'])));
    $id 		= $_SESSION['SIGO']['ACESSO']['USUARIO'];

    $link = RetornaConexaoMysql('serverdge', 'sigo_integrado');
    
	if($senha == $nova_senha){
		$ret = "Não é possível cadastrar a mesma senha novamente";
	}else if($nova_senha == md5("123")){
		$ret = "Não é possível cadastrar a senha '123'";
	}else{

	    $Sql = "SELECT usr_senha FROM tbl_usuario WHERE usr_usuario = '$id'";
	
	    if ($rs = mysqli_query($link, $Sql)) {
	        if (mysqli_num_rows($rs) > 0) {
	
	            $row = mysqli_fetch_assoc($rs);
	            $senha_banco = $row['usr_senha'];
	
	            if ($senha_banco == $senha) {
	                $Sql = "UPDATE tbl_usuario SET usr_senha='$nova_senha', usr_senha_padrao=0 WHERE usr_usuario = '$id' ";
	                if (!$rs = mysqli_query($link, $Sql)) {
	                    $ret = mysqli_error($link);
	                } else {
	                    $ret = "alterou";
	                    GravaLogSentenca($link, $Sql);
	                }
	            } else {
	                $ret = "Senha incorreta. " ;
	                /*echo "$senha_banco || $senha";
	                print_r($_REQUEST);
	                exit;*/
	            }
	        } else {
	            $ret = "Erro ao retornar os dados do usuário. Tente novamente e caso o erro persista contate a DGE.";
	        }
	    } else {
	        $ret = "Erro ao retornar os dados do usuário. Tente novamente e caso o erro persista contate a DGE.";
	    }
	}

    mysqli_close($link);

    $gmtDate = gmdate("D, d M Y H:i:s");
    header("Last-Modified: {$gmtDate} GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: -1");
    header("Content-Type: text/html; charset=iso-8859-1");

    echo $ret;
    exit;

}

function AjaxExecutarTarefa(){

	$pen_id = $_REQUEST['pen_id'];
	$pdt_id = $_REQUEST['pdt_id'];
	$email = $_REQUEST['pen_solicitante_email'];
	$nome = $_REQUEST['fun_id'];
	$pen_execucao = utf8_decode(nl2br($_REQUEST['pen_execucao']));
	$executor = $_SESSION['SIGO']['ACESSO']['ID_USUARIO'];
	//print_r($_REQUEST);exit;

	$link = RetornaConexaoMysql('serverdge', 'sigo_integrado');

	$Sql = "UPDATE tbl_pendencia SET pen_executor=$executor, pen_data_execucao=NOW(), pen_status='EXECUTADA', pen_execucao='$pen_execucao' WHERE pen_id=$pen_id";

	//echo $Sql;exit;

	if($rs = mysqli_query($link, $Sql)){

		$assunto = "SIGO - Execução de Pendências";
		$mensagem = "$nome<br /><br />Sua solicitação foi executada pelos administradores do SIGO.<br /><br /> $pen_execucao";

		if(EnviarEmail($nome,$email,$assunto,$mensagem)){
			$ret = "ok";
			}else{
				$ret = "Pendência executada, Porem não foi possível enviar um e-mail de confirmação para o solicitante.";
		}
		}else{
			$ret = "Erro ao cadastrar a solicitação!";
	}

	mysqli_close($link);

	$gmtDate = gmdate("D, d M Y H:i:s");
	header("Last-Modified: {$gmtDate} GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Expires: -1");
	header("Content-Type: text/html; charset=iso-8859-1");

	echo $ret;
	exit;

}

function AjaxBuscaGruposAcesso(){
	$area = $_REQUEST['area'];
	$area = explode("|", $area);
	for($i=0;$i<count($area);$i++){

		switch ($area[$i]) {
	 		case 'AREA2':
	 			$area_label .= " gru_descricao LIKE '%AREA 2%' OR";
	 			break;

		 	case 'AREA1_MG':
		 		$area_label .= " gru_descricao LIKE '%AREA 1 MG%' OR";
	 			break;

		 	case 'AREA1_RJ':
		 		$area_label .= " gru_descricao LIKE '%AREA 1 RJ%' OR";
	 			break;

		 	case 'AREA1_ES':
		 		$area_label .= " gru_descricao LIKE '%AREA 1 ES%' OR";
	 			break;
                            
		 	case 'AREA3_SP':
		 		$area_label .= " gru_descricao LIKE '%AREA 3 SP%' OR";
	 			break;                            

		 	/*case 'AREA1_MG|AREA1_ES':
		 		$area_label = "(gru_descricao LIKE '%AREA 1 ES%' OR gru_descricao LIKE '%AREA 1 MG%')";
	 			break;
			*/
	 		default:
		 		$area_label .= " 1=1 OR ";
	 			break;
		}

	}
	$area_label = "(".substr($area_label,0,strlen($area_label)-3).")";
	$grupo_id = $_SESSION['SIGO']['ACESSO']['ID_GRUPO'];
	if($grupo_id == 2){
		$where_grupo = " OR gru_id < 5 OR gru_id=67";
		}else{
			$where_grupo = " AND gru_privilegio < 80";
	}

	$conecta = RetornaConexaoMysql('serverdge', 'sigo_integrado');

	$Sql = "SELECT gru_id, gru_descricao FROM tbl_grupo WHERE  $area_label $where_grupo ORDER BY gru_descricao ASC";
	/*echo "<script type='text/javascript'>
			alert($Sql); </script>";exit;*/
	if ($rs_grupo = mysqli_query($conecta, $Sql)){

		$xml = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n";
		$xml.="<grupos>\n";

		while ($row_grupo = mysqli_fetch_assoc($rs_grupo)) {
			$xml.="<grupo>\n";
			$xml.="<id>{$row_grupo["gru_id"]}</id>\n";
			$xml.="<descricao><![CDATA[{$row_grupo["gru_descricao"]}]]></descricao>\n";
			$xml.="</grupo>\n";
		}
		$xml.="</grupos>";
		$ret = $xml;
		header("Content-type: application/xml; charset=iso-8859-1");
		}else{
			$ret = mysqli_error($conecta);
			header("Content-Type: text/html; charset=iso-8859-1");
	}

	mysqli_close($conecta);

	$gmtDate = gmdate("D, d M Y H:i:s");
	header("Last-Modified: {$gmtDate} GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Expires: -1");


	echo $ret;
	exit;
}


function AjaxBuscaArea(){


	$pc 		= $_REQUEST['pc'];
	$banco 		= $_REQUEST['banco'];
	$tabela 	= $_REQUEST['tabela'];
	$campo_busca= $_REQUEST['campo_busca'];
	$campo_where= $_REQUEST['campo_where'];
	$where_valor= $_REQUEST['where_valor'];

	empty($_REQUEST['campo_valor']) ? $campo_valor = $campo_busca :$campo_valor = $_REQUEST['campo_valor'];

	switch ($campo_where) {
		case 'gerenciaLIKE':
			$campo_where = "
			sup_atuacao like '$where_valor' OR
			sup_atuacao like '$where_valor' OR
			sup_atuacao like '$where_valor' OR
			sup_atuacao = '$where_valor'";
			break;

		default:
			$campo_where = "{$campo_where}='{$where_valor}'";
			break;
	}

	$conecta = RetornaConexaoMysql($pc, $banco);

	$Sql = "SELECT DISTINCT $campo_valor,$campo_busca FROM $tabela WHERE $campo_where ORDER BY $campo_busca ASC";
	
	if (($rs_area = mysqli_query($conecta, $Sql)) !== false){

		$xml = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n";
		$xml.= "<areas>\n";

		while ($row_area = mysqli_fetch_assoc($rs_area)) {
			$xml.="<area>\n";
			$xml.="<valor><![CDATA[{$row_area[$campo_valor]}]]></valor>\n";
			$xml.="<nome><![CDATA[{$row_area[$campo_busca]}]]></nome>\n";
			$xml.="</area>\n";
		}
		$xml.="</areas>";
		$ret = $xml;
		header("Content-type: application/xml; charset=iso-8859-1");
	}else{
		$ret = mysqli_error($conecta);
		header("Content-Type: text/html; charset=iso-8859-1");
	}

	$gmtDate = gmdate("D, d M Y H:i:s");
	header("Last-Modified: {$gmtDate} GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Expires: -1");


	echo $ret;
	exit;

}

function AjaxBuscaSupervisor(){

	$pc 		= $_REQUEST['pc'];
	$banco 		= $_REQUEST['banco'];
	$tabela 	= $_REQUEST['tabela'];
	$campo_busca= $_REQUEST['campo_busca'];
	$campo_where= $_REQUEST['campo_where'];
	$where_valor= $_REQUEST['where_valor'];

	empty($_REQUEST['campo_valor']) ? $campo_valor = $campo_busca :$campo_valor = $_REQUEST['campo_valor'];

	if (!empty($_REQUEST['campo_where'])){
            $campo_where = " ot.gra = '".$where_valor."'";    
        }

	$conecta = RetornaConexaoMysql($pc, $banco);

	$Sql = "SELECT ts.chapa_supervisor,ts.nome from modulo_pessoal_com_tap.tbl_operador_tecnico ot 
                     INNER JOIN modulo_pessoal_com_tap.tbl_supervisor ts ON 
                     (ot.chapa_supervisor = ts.chapa_supervisor) WHERE $campo_where  GROUP BY chapa_supervisor ORDER BY ts.nome";

	if (($rs_area = mysqli_query($conecta, $Sql)) !== false){

		$xml = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n";
		$xml.= "<areas>\n";

		while ($row_area = mysqli_fetch_assoc($rs_area)) {
			$xml.="<area>\n";
			$xml.="<valor><![CDATA[{$row_area[$campo_valor]}]]></valor>\n";
			$xml.="<nome><![CDATA[{$row_area[$campo_busca]}]]></nome>\n";
			$xml.="</area>\n";
		}
		$xml.="</areas>";
		$ret = $xml;
		header("Content-type: application/xml; charset=iso-8859-1");
	}else{
		$ret = mysqli_error($conecta);
		header("Content-Type: text/html; charset=iso-8859-1");
	}

	$gmtDate = gmdate("D, d M Y H:i:s");
	header("Last-Modified: {$gmtDate} GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Expires: -1");


	echo $ret;
	exit;

}

function preencher_menu_pai(){
	$conecta = RetornaConexaoMysql('serverdge', 'sigo_integrado');
	$area = $_REQUEST['area'];
	$sql_menu = "Select * from tbl_menu WHERE (men_id NOT IN (14,103,25,23)) AND men_area='$area' ORDER BY men_nome ASC";
	$rs_menu = mysqli_query($conecta, $sql_menu);
	$option_menu_pai = GeraOptionGenerico($rs_menu,'men_id','men_nome','', '');
	echo $option_menu_pai;
}

//função para excluir coletadores
function excluir_coletador( ){
    $id =  $_REQUEST['excluir'];
    $conecta =  RetornaConexaoMysql('serverdge', 'modulo_coletadores');
    $sql_sistemas = "DELETE FROM tbl_cadastro_sistemas WHERE id = $id";
    $rs_sistemas = mysqli_query($conecta, $sql_sistemas);
    
    if( $rs_sistemas ){
        echo 'Coletador excluirdo!';
    }
    else{
         echo 'Coletador não excluirdo!';
         echo mysqli_error($conecta);
    }
      
}