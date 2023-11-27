<?php
/** Versão 3.2.0 * */
$root = './../../../';
define('CAMINHO_RAIZ', '../../../');

include "{$root}includes/funcoes.php";
include "{$root}includes/valida_sessao.php";
include "{$root}includes/fwSigo/core/ImportApp.php";

define("BANNERSIA", $_SESSION['SIGO']['ACESSO']['IMG_SIA']);
define("BANNERSIGO", $_SESSION['SIGO']['ACESSO']['IMG_SIGO']);

ini_set('memory_limit', '1024M');
ini_set('max_execution_time', 500);
set_time_limit(500);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('upload_max_filesize', '100M');
ini_set('post_max_size', '100M');

error_reporting(0);

//$menuSigo = RetornaMenuSigo('SIGO_AREA1_MG_DDG_EQUIPARACAO_SALARIAL');

$menuSigo = RetornaMenuSigo('SIA_PROMOCAO');

if (!isset($_GET['mainapp']) || !isset($_GET['app'])) {
	header("Location: ?mainapp=home&app=home");
}

?>

<!DOCTYPE html>
<html>
    <head>
        <title>:: SIGO - EQUIPARAÇÃO SALARIAL ::</title>

        <link type="image/x-icon" rel="shortcut icon" href="<?= $root ?>favicon.ico" />

        <meta http-equiv="Content-Type" content="text/html" charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <meta http-equiv="pragma" content="no-cache">
        <meta http-equiv="expires" content="-1">
        <meta name="robots" content="noindex,nofollow">

        <?php echo ImportApp::import(array(
			'FwSigo',
			'DataTable',
			'Select2',
            'Datepicker',
            'bootstrap-4.2.1'
        )); ?>
       
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

        <!-- <script type="text/javascript" src="<?= $root ?>includes/fwSigo/library/jquery-3.3.1.min.js"></script> -->
        <script type="text/javascript">

            const TITULO_DEFAULT = 'SIGO - EQUIPARAÇÃO SALARIAL';
            const CAMINHO_RAIZ = "../../../";
            const MSG_ERRO_PADRAO = 'Ocorreu um erro crítico ao executar a solicitação.<br />Entre em contato com a equipe de desenvolvimento';

            console.log('./../../../includes/fwSigo/ajax.php?classUrl=' + window.location.pathname + 'app/');
            
            $.ajaxSetup({

                url: './../../../includes/fwSigo/ajax.php?classUrl=' + window.location.pathname + 'app/',
                dataType: 'json',
                cache: false,
                type: 'POST',
                error: function (objectError, error, message) {
                    alerta('error', 'Ocorreu um erro fatal.', function () {
                        console.log(objectError, error, message);
                    });
                }

            });

        </script>

        <script type="text/javascript" src="<?= $root ?>includes/fwSigo/library/modal/redmon-jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
        <script type="text/javascript" src="<?= $root ?>includes/fwSigo/library/jquery-confirm.min.js"></script>
        <script type="text/javascript" src="<?= $root ?>includes/fwSigo/library/mensagem.js"></script>
        <script type="text/javascript" src="<?= $root ?>includes/fwSigo/library/modal/janela.js"></script>
        <script type="text/javascript" src="<?= $root ?>includes/fwSigo/library/string.js"></script>
        <script type="text/javascript" src="<?= $root ?>includes/fwSigo/library/chartjs/2.7.2/Chart.min.js"></script>

        <link type="text/css" rel="stylesheet" href="<?= $root ?>includes/fwSigo/library/modal/redmon-jquery-ui-1.12.1.custom/jquery-ui.min.css">
        <link type="text/css" rel="stylesheet" href="<?= $root ?>includes/fwSigo/library/jquery-confirm.min.css">

        <link type="text/css" rel="stylesheet" href="app/biblioteca/css/menu.css">
        <link type="text/css" rel="stylesheet" href="app/biblioteca/css/style.css">
        <!--<link href="<?= $root ?>css/index.css" rel="stylesheet" type="text/css" />-->

        <script type="text/javascript" src="app/helper/js/HPlanoSaude.js"></script>

        <script type="text/javascript" src="app/biblioteca/js/menu.js"></script>
        <script type="text/javascript" src="app/biblioteca/js/Aguardando.js"></script>


        <link href="app/biblioteca/css/bootstrap-datepicker.standalone.min.css" rel="stylesheet">
        <script src="app/biblioteca/js/bootstrap-datepicker.mim.js"></script>
        <script src="app/biblioteca/js/bootstrap-datepicker.pt-BR.min.js"></script>


    </head>
    <body>
        <div id="divGeral">
            <div class="sg-banner">
                <a href="?mainapp=home&amp;app=home">
                    <img src="<?= $root ?>imagens/banner-sigo/<?php echo BANNERSIA;?>" id="BannerSigoTelemont" alt="Banner Sigo Telemont" >
                </a>
            </div>
            <div class="sg-menu">
                <?php echo utf8_encode($menuSigo); ?>
            </div>
            <div class="sg-loading" style="display: none">
                <div class="sg-loader">
                </div>
            </div>

            <div class="div_meio">
                <?php
                include_once "./includes/{$_GET['mainapp']}/inc_{$_GET['app']}.php";
                $noCache = microtime(true);

                $jsFile = "./includes/{$_GET['mainapp']}/assets/{$_GET['app']}.js";
                $cssFile = "./includes/{$_GET['mainapp']}/assets/{$_GET['app']}.css";

                if (file_exists($jsFile)) {
                    echo '<script type="text/javascript" src="' . $jsFile . '?' . $noCache . '"></script>';
                }

                if (file_exists($cssFile)) {
                    echo '<link type="text/css" rel="stylesheet" href="' . $cssFile . '?' . $noCache . '">';
                }
                ?>
            </div>
        </div>


    </body>
</html>