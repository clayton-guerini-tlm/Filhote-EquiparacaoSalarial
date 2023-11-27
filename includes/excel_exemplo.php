<?php

/**
$campos, $label, $label_campos DEVEM SER SEPARADOS SOMENTE POR | (PIPE)

Se $campos = "*" OU se o for passada uma consulta completa, os labels dos campos serao os titulos das colunas no banco e deve ser ignorado o parametro CAMPOS_TIPO

$servidor 	-> servidor onde sera feita consulta, exemplos: 2900, serverdge, local, etc
$banco 		-> banco onde sera feita consulta, exemplos: modulo_co_la, sigo_integrado, etc
$consulta 	-> consulta SQL completa, exemplo SELECT campo1,campo2,campo3 FROM tabela WHERE campo4=2
$campos 	-> campos que serao retornados pela consulta, exemplos: campo1|campo2|campo3|campo4
$label 		-> label dos campos retornados pela consulta, exemplos: Nome do campo 1|Nome do campo 2|Nome do campo 3|Nome do campo 4
$titulo		-> titulo do relatorio

Qualquer duvida veja o arquivo excel_exemplo.php no mesmo diretorio deste arquivo, ou fale com Danilo Azevedo danilo.azevedo@telemont.com.br

*/

$_SESSION['EXCEL']['SERVIDOR'] = 'local';
$_SESSION['EXCEL']['BANCO'] = 'monitoria';
$_SESSION['EXCEL']['CAMPOS'] = "*";
$_SESSION['EXCEL']['CAMPOS_LABEL'] = "MONITOR|NOME|NUMERO BA|DATA DA LIGAÇÃO";
$_SESSION['EXCEL']['CONSULTA'] = "SELECT * FROM `tbl_monitoria_gabarito` LIMIT 0 , 30";
$_SESSION['EXCEL']['TITULO'] = "RELATORIO MONITORIA";

include_once("funcoes.php"); // varia de acordo com o caminho do diretorio atual
include("excel.php");

?>