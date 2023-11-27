<?php
	include "includes/funcoes.php";
	include "includes/valida_sessao.php";
	
	$filial = $_GET['filial_escolhida'];
	
	AbrirTicketAcesso($filial);

?>