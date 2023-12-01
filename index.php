<?php
header("Programa: no-cache");
header("Cache: no-cache");
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon 26 jul 1997 05:00:00 GMT");
header('Content-Type: text/html; charset=iso-8859-1');

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

$referer = (isset($_SERVER['REQUEST_URI']))? explode("?", $_SERVER['REQUEST_URI']): null;

if(!empty($referer) && count($referer) > 1){
  $referer_args = explode("&",$referer[1]);

  if (substr($referer_args[0],0,8) == "mainapp=" && substr($referer_args[1],0,4) == "app="){
    $referer_link = $referer[0] . "?" . $referer_args[0] . "&" . $referer_args[1];
  }else{
    $referer_link = $referer[0];
  }
}

if (empty($referer_link)){
  $referer_link = "../principal.php";
}

$_SESSION['SIGO']['AJAX'] = true;
$_SESSION['SIGO']['LOGIN']['REFERER'] = $referer_link;
$_SESSION['SIGO']['LOGIN']['MSG'] = (isset($_GET['msg']))? $_GET['msg']: false;

if (isset($_GET['msg'])){
  $msg = $_GET['msg'];
  $display_msg = "block";
  switch ($msg) {
    case 1:
      $msg_home = "USUÁRIO OU SENHA INCORRETA.";
      break;
    case 2:
      $msg_home = "ACESSO NÃO AUTORIZADO. FAVOR LOGAR NO PORTAL.";
      break;
    case 3:
      $msg_home = "ACESSO NÃO AUTORIZADO. FAVOR LOGAR NO PORTAL.";
      break;
    case 4:
      $msg_home = "USUÁRIO CADASTRADO COM ÊXITO.";
      break;
    case 5:
      $msg_home = "USUÁRIO UTILIZADO EM OUTRO COMPUTADOR.";
      break;
    case 6:
      $msg_home = "PERFIL DE USUÁRIO INVÁLIDO.";
      break;
    case 7:
      $msg_home = "TICKET DE ACESSO INVÁLIDO.";
      break;
    case 8:
      $msg_home = "ERRO AO RETORNAR OS DADOS DO USUÁRIO. USUÁRIO BLOQUEADO.";
      break;
    case 9:
      $msg_home = "USUÁRIO NÃO ENCONTRADO NA EMPRESA INFORMADA.";
      break;
    case 10:
      $msg_home = "ERRO AO RETORNAR OS DADOS DO USUÁRIO. TENTE NOVAMENTE E CASO O ERRO PERSISTA CONTATE A DGE."; //. '<br />' . $_SESSION['ERRO'] ;
      break;
    case 11:
      $msg_home = "SESSÃO INVÁLIDA. FAVOR LOGAR NO PORTAL";
      break;
    case 12:
      $msg_home = "NÃO FOI POSSÍVEL FAZER O REDIRECIONAMENTO AUTOMÁTICO. POR FAVOR INFORME NOVAMENTE SUA SENHA.";
      break;  
    case 13:
      $msg_home = "CAPTCHA INVÁLIDO!";
      break;
  }
}else{
  $display_msg = "none";
  $msg_home = "";
}

if(isset($_GET['mainapp'])){
  $mainapp = $_GET['mainapp'];

  if(isset($_GET['app'])){
    $app = $_GET['app'];
    $focus = "";
  }else{
    $mainapp = "home";
    $app = "home_login";
  }
}else{
  $mainapp = "home";
  $app = "home_login";
}

switch ($app) {
  case "cadastro":
    $focus = "onload=\"document.getElementById('cpf').focus()\"";
  break;
  case "home_login":
    if (isset($_GET['login'])){
      $focus = "onload=\"document.getElementById('senha').focus()\"";
    }else{
      $focus = "onload=\"document.getElementById('login').focus()\"";
    }
  break;
}

?>

<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <?php if($_SERVER['SERVER_NAME'] == 'localhost'): ?>      
      <link rel="icon" href="./favicon.ico">
    <?php else: ?>
      <link rel="icon" href="../../../../favicon.ico">
    <?php endif; ?>

    <title>:: SISTEMA INTEGRADO DE GER&Ecirc;NCIA OPERACIONAL ::</title>

    <!-- Bootstrap core CSS -->
    <link href="./includes/fwSigo/library/bootstrap/bootstrap-4.2.1/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="./includes/fwSigo/library/template/sigo/css/signin.css" rel="stylesheet">

  </head>
    <body class="text-center" <?php echo $focus;?> >
       <div class="login-web banner">
        <!-- <img src="imagens/banner_topo.jpg" border="0" /> -->
        <img src="imagens/banner-sigo/Banner SIGO-01.png" border="0" />
      </div>
      <?php include("includes/$mainapp/inc_$app.php") ?>

    </body>
</html>

<script type="text/javascript" src="js/formatacaixadetexto.js"></script>
<script type="text/javascript" src="js/funcoes.js"></script>

<!-- <script src="https://www.google.com/recaptcha/api.js?render=<?php // echo SITE_KEY; ?>"></script> -->
<!-- <script type="text/javascript" src="./../js/login.js"></script> - arquivo não existe no caminho - -->

<!-- <script type="text/javascript">
  
  grecaptcha.ready(function() {
    grecaptcha.execute('6LfIiIsUAAAAAEuaDkauRQZp3BjDT8TM3SRt2pYV', {action: 'login'})
    .then(function(token) {
      document.getElementById('g-recaptcha-response').value = token;
      document.getElementById('g-recaptcha-response-mob').value = token;
    });
  });
</script> -->

<!-- <script src='https://www.google.com/recaptcha/api.js'></script> -->
