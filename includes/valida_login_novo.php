<?php

ini_set('session.cookie_lifetime', "0");
ini_set('session.cache_limiter', 'nocache');
ini_set('max_execution_time', '120');

header("Programa: no-cache");
header("Cache: no-cache");
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon 26 jul 1997 05:00:00 GMT");

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

include "funcoes.php";

$conecta = RetornaConexaoMysql('local', 'sigo_integrado');

//Recupera login e senha enviados pelo form HTML
$usr_usuario = mysqli_real_escape_string($conecta, $_POST['login']);
$usr_senha = mysqli_real_escape_string($conecta, $_POST['senha']);

//Trazendo os dados do usuario
$qry = "SELECT * FROM tbl_usuario WHERE usr_usuario = '$usr_usuario' LIMIT 1 ";
$rs = mysqli_query($conecta, $qry);

//Retorno do processo de login. 
//Mantido padrao do arquivo antigo
$ret = 'NOK';
if ($rs !== false) {//Se não ocorreu erro na consulta
    //Verificando se o usuario esta correto (Usuario cadastrado na base)
    if (mysqli_num_rows($rs)) {//Usuario esta ok
        //Converte a consulta em um array associativo
        $dados_usuario = mysqli_fetch_assoc($rs);

        //Libera o resultado da consulta
        mysqli_free_result($rs);


        //Verificando se o usuario do portal esta liberado ou bloqueado
        if ($dados_usuario['usr_status'] == "LIBERADO") {

            //Confere se a senha do usuario esta correta
            if (md5($usr_senha) == $dados_usuario['usr_senha']) {

                //Verifica se é visitante ou colaborador telemont
                //Essa regra foi mantida pois foi alterada em 2010 e devido a urgencia nao pode ser reformulada neste momento
                //Se possui o fun_id maior que 6 o fun_id é cpf do visitante
                if (strlen($dados_usuario['fun_id']) <= 6) {//Cruza com a tbl_rm_funcionario para obter os dados do colaborador
                    $qry = "SELECT f.* FROM tbl_usuario u 
                            INNER JOIN tbl_rm_funcionario f USING (fun_id)
                            WHERE u.usr_usuario = '$usr_usuario' AND sit_id <> 'D' LIMIT 1 ";

                    $rs = mysqli_query($conecta, $qry) or die('Erro ao consultar a base de dados. Descri&ccedil;&atilde;o: ' . mysqli_error($conecta));

                    $dados_usuario['rm_funcionario'] = mysqli_fetch_assoc($rs);

                    //Libera o resultado da consulta
                    mysqli_free_result($rs);
                }

                //Não sei o motivo desta condição, achei melhor mante-la a principio
                if ($dados_usuario['usr_filial'] == 'AREA1') {
                    $dados_usuario['usr_filial'] = 'AREA1_MG';
                }

                //Obtem as areas permitidas ao usuario
                $areas = explode('|', $dados_usuario['usr_filial']);

                //Obtem os grupos do usuario
                $grupos = explode('|', $dados_usuario['gru_id']);

                //Monta a condição de areas permitidas ao usuario
                $where_areas = array();
                $where_areas[] = " (gru_area = 'TODAS') ";
                foreach ($areas as $a) {
                    $where_areas[] = " (gru_area = '$a') ";
                }
                $where_areas = implode(" OR ", $where_areas);

                //Monta a condição dos grupos do usuario
                $where_grupos = array();
                $where_grupos[] = " (gru_area = 'TODAS') ";
                foreach ($grupos as $g) {
                    $where_grupos[] = " (gru_id = '$g') ";
                }
                $where_grupos = implode(" OR ", $where_grupos);

                //Obtendo os ids dos sitemas permitidos ao usuario
                $qry = "SELECT distinct sis_id FROM tbl_grupo WHERE ($where_grupos) AND ($where_areas)";
                $rs = mysqli_query($conecta, $qry) or die('Erro ao recuperar os sistemas do usuario. Descri&ccedil;&atilde;o: ' . mysqli_error($conecta));

                $dados_usuario['SISTEMA_USUARIO'] = array();
                while ($row = mysqli_fetch_assoc($rs)) {
                    $dados_usuario['SISTEMA_USUARIO'][] = $row['sis_id'];
                }

                //Libera o resultado da consulta
                mysqli_free_result($rs);

                $dados_usuario['SISTEMA_USUARIO'] = implode('|', $dados_usuario['SISTEMA_USUARIO']);


                $hash = time();

                // Atualiza os dados do usuário logado.
                $qry = "UPDATE tbl_usuario SET usr_ip_atual = '{$_SERVER['REMOTE_ADDR']}', usr_ultimo_acesso = Now(), usr_hash = '$hash' WHERE usr_id = '{$dados_usuario['usr_id']}' ";
                mysqli_query($conecta, $qry) or die('Erro ao atualizar os dados de acesso do usuario. Descri&ccedil;&atilde;o: ' . mysqli_error($conecta));

                $session_id = Verificar_id_session_php();

                //Criando a sessao do sigo

                unset($_SESSION);

                $_SESSION['SIGO'] = array();
                $_SESSION['SIGO']['AJAX'] = 1;

                $_SESSION['SIGO']['ACESSO'] = array(
                    'ID_USUARIO' => $dados_usuario['usr_id'],
                    'USUARIO' => $dados_usuario['usr_usuario'],
                    'EMAIL' => $dados_usuario['usr_email'],
                    'ATUACAO_OLD' => $dados_usuario['usr_atuacao_old'],
                    'SENHA_PADRAO' => $dados_usuario['usr_senha_padrao'],
                    'ID_GRUPO' => $dados_usuario['gru_id'],
                    'NOME' => $dados_usuario['usr_nome_visitante'],
                    'FILIAL' => $dados_usuario['usr_filial'],
                    'FILIAL_ESTADO' => $dados_usuario['rm_funcionario']['fun_filial'],
                    'SISTEMA_USUARIO' => $dados_usuario['SISTEMA_USUARIO'],
                    'CODFILIAL' => $dados_usuario['rm_funcionario']['fun_codfilial'],
                    'IP_ATUAL' => $_SERVER['REMOTE_ADDR'],
                    'HASH' => $hash,
                    'AREA_SIGO' => $AREA_SIGO,
                    'PHPSESSID' => $session_id,
                    'fil_codigo' => null
                );

                //Cria os cookies do navegador
                $_COOKIE['registro_brt'] = $_SESSION['SIGO']['ACESSO']['USUARIO'];
                $_COOKIE['nome_usuario'] = $_SESSION['SIGO']['ACESSO']['NOME'];
                $_COOKIE['area_atuacao'] = $_SESSION['SIGO']['ACESSO']['ATUACAO_OLD'];
                $_COOKIE['filial'] = $_SESSION['SIGO']['ACESSO']['fil_codigo'];
                $_COOKIE['PHPSESSID'] = $_SESSION['SIGO']['ACESSO']['PHPSESSID'];

                //Serializa a sessao pra salva-la no banco
                $serialize_session = serialize($_SESSION);

                //Apaga os registros de sessao do usuario na tbl_log_acesso
                $qry = "UPDATE tbl_log_acesso 
                        SET lga_session_id = NULL, 
                        lga_session_descricao = NULL, 
			lga_tempo_ini_sessao = NULL, 
			lga_tempo_fim_sessao = NULL, 
			lga_tempo_atual_sessao = NULL 
                        where usr_id = '{$dados_usuario['usr_id']}'";
                mysqli_query($conecta, $qry) or die('Erro remover as sessões antigas do usuario. Descri&ccedil;&atilde;o: ' . mysqli_error($conecta));

                //Insere um registro na tabela de log de acesso com os dados do próprio acesso, o id da sessao, tempo inicio, tempo final e o tempo atual da sessao.
                $qry = "INSERT INTO tbl_log_acesso 
                        (usr_id, lga_ip, lga_dthr_acesso, lga_browser, lga_session_id, lga_tempo_ini_sessao, lga_tempo_fim_sessao, lga_tempo_atual_sessao, lga_session_descricao) 
                        VALUES 
                        ('{$dados_usuario['usr_id']}', '{$_SERVER['REMOTE_ADDR']}', NOW(), '{$_SERVER['HTTP_USER_AGENT']}', '{$session_id}', TIME_TO_SEC(TIME(NOW())), TIME_TO_SEC(TIME(NOW())) + 4320, TIME_TO_SEC(TIME(NOW())),'$serialize_session') ";
                mysqli_query($conecta, $qry) or die('Erro ao registrar o log de acesso do usuario. Descri&ccedil;&atilde;o: ' . mysqli_error($conecta));

                RetornaMenuSigoUtilizado();

                $ret = "logou";
            } else {//Senha inválida
                $ret = "1";
            }
        } else {//Usuario bloqueado no portal
            $ret = '8';
        }
    } else {//Usuario nao ok
        $ret = '9';
    }
} else {//Ocorreu erro na consulta dos dados do usuario
    $ret = '10'; //'Erro ao consultar os usuários na base de dados. Descri&ccedil;&atilde;o: ' . mysqli_error($conecta);
}


if ($ret == "logou") {
    header("Location: ../principal.php");
} else {
    header("Location: ../?login={$login}&msg=$ret");
}
mysqli_close($conecta);

exit();
?>