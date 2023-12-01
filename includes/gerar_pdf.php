<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

@ob_clean();

require_once 'biblioteca/dompdf/dompdf_config.inc.php';

//Recebendo o conteudo HTML que serÃ¡ impresso
$html = $_SESSION['PDF']['HTML'];

//Destruindo a sessao
$_SESSION['PDF']['CONTEUDO'] = null;
unset($_SESSION['PDF']['CONTEUDO']);

$dompdf = new DOMPDF();
$dompdf->load_html($html);
$dompdf->set_paper('sra4', 'portrait');
$dompdf->render();

$rand = strtoupper(substr(md5(rand(1,99999)),0,5));

$dompdf->stream("documento_$rand.pdf");
?>