<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

$core = '../fwSigo/core/';

require_once $core . 'ErrorHandler.php';
require_once $core . 'Root.php';

spl_autoload_register(function($class) {
	global $core;
    $classPath = $_SERVER['DOCUMENT_ROOT'] . "/Filhote-PlanoSaude";
//	$classPath .= !empty($_POST['classUrl']) ? $_POST['classUrl'] : $_GET['classUrl'];
	$dir = new RecursiveDirectoryIterator($classPath);

	$exists = findClass($dir, $class);

	if(!$exists) {
		$dir = new RecursiveDirectoryIterator($core);
		$exists = findClass($dir, $class);
	}
});
function findClass(RecursiveDirectoryIterator $dir, $class) {
	$classFile = $class . '.php';
	foreach (new RecursiveIteratorIterator($dir) as $path => $obj) {
		$file = $obj->getFilename();
	    if($file == $classFile) {
	       	require_once $path;
	        return true;
	    }
	}
	return false;
}

if(!isset($_POST['funcaoAjax'])) {
	die(json_encode(array('success' => false, 'message' => 'Função não informada.', 'data' => $_POST, 'errors' => $GLOBALS['_ERRORS'])));
}

if(!isset($_POST['classUrl']) && !isset($_GET['classUrl'])) {
	die(json_encode(array('success' => false, 'message' => 'Caminho do projeto não informado.', 'data' => $_POST, 'errors' => $GLOBALS['_ERRORS'])));
}

$controllerName = explode('::', $_POST['funcaoAjax']);
$controllerName = current($controllerName);
$returnRequest = call_user_func("{$controllerName}::executeFunction", $_POST) or
die(json_encode(array('success' => false, 'message' => 'Função inválida', 'data' => $_POST, 'errors' => $GLOBALS['_ERRORS'])));

if (is_array($returnRequest)) {
    die(json_encode($returnRequest));
} else {
    die($returnRequest);
}
