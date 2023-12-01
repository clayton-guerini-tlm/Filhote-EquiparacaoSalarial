<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


// Conexao com o Banco de Dados
$conecta = RetornaConexaoMysql('serverdge', 'sigo_integrado');

// Tratamento dos Dados do Form

$fun_chapa = $_POST['chapa'];
$chkAtualizar1 = $_POST['chkAtualizar1'];
$chkAtualizar2 = $_POST['chkAtualizar2'];
$chkAtualizar3 = $_POST['chkAtualizar3'];
$chkAtualizar4 = $_POST['chkAtualizar4'];

// Dados da Session
$registro_usuario = $_SESSION['SIGO']['ACESSO']['USUARIO'];

if(!empty($chkAtualizar1)) {
    $sql = "insert into tbl_sincronismo_urgente (fun_chapa,tipo_sentenca,data_inclusao,solicitante) values ('$fun_chapa','$chkAtualizar1',now(),'$registro_usuario')";
    $SQL = mysqli_query($conecta, $sql) or die(mysqli_error($conecta));
}

if(!empty($chkAtualizar2)) {
    $sql = "insert into tbl_sincronismo_urgente (fun_chapa,tipo_sentenca,data_inclusao,solicitante) values ('$fun_chapa','$chkAtualizar2',now(),'$registro_usuario')";
    $SQL = mysqli_query($conecta, $sql) or die(mysqli_error($conecta));
}

if(!empty($chkAtualizar3)) {
    $sql = "insert into tbl_sincronismo_urgente (fun_chapa,tipo_sentenca,data_inclusao,solicitante) values ('$fun_chapa','$chkAtualizar3',now(),'$registro_usuario')";
    $SQL = mysqli_query($conecta, $sql) or die(mysqli_error($conecta));
}

if(!empty($chkAtualizar4)) {
    $sql = "insert into tbl_sincronismo_urgente (fun_chapa,tipo_sentenca,data_inclusao,solicitante) values ('$fun_chapa','$chkAtualizar4',now(),'$registro_usuario')";
    $SQL = mysqli_query($conecta, $sql) or die(mysqli_error($conecta));
}

// Fecha conexão com o Banco de Dados
mysqli_close($conecta);

// Script de redirecionamento de página
echo "<script laguange='javascript'>alert('Caro usuário, a solicitação de sincronismo urgente foi realizada com sucesso!');window.location.href='?mainapp=sincronismo&app=cadastro'</script>";


?>
