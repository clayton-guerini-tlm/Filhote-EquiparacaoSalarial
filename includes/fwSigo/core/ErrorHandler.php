<?php

error_reporting(0);
$GLOBALS['_ERRORS'] = array();

function fatalHandler() {
    
	$errfile = 'unknown file';
    $errstr  = 'shutdown';
    $errno   = E_CORE_ERROR;
    $errline = 0;

    $error = error_get_last();
	
	$errors = array(
	    1 => 'E_ERROR', 2 => 'E_WARNING', 4 => 'E_PARSE', 8 => 'E_NOTICE', 16 => 'E_CORE_ERROR',
		32 => 'E_CORE_WARNING', 64 => 'E_COMPILE_ERROR', 128 => 'E_COMPILE_WARNING', 256 => 'E_USER_ERROR',
		512 => 'E_USER_WARNING', 1024 => 'E_USER_NOTICE', 2048 => 'E_STRICT', 4096 => 'E_RECOVERABLE_ERROR',
		8192 => 'E_DEPRECATED', 16384 => 'E_USER_DEPRECATED', 30719 => 'E_ALL');

    if( $error !== NULL) {
        $errno   = $errors[$error["type"]];
        $errfile = $error["file"];
        $errline = $error["line"];
        $errstr  = $error["message"];
		
		die(json_encode(array('success' => false, 'message' => 'ERROR!', 'data' => array('error' => $error[$errno], 'errno' => $errno, 'errstr' => $errstr, 'errfile' => $errfile, 'errline' => $errline))));
		
    }
}

function errorsHandler($errno, $errstr, $errfile, $errline) {
	
    $errors = array(
        1 => 'E_ERROR', 2 => 'E_WARNING', 4 => 'E_PARSE', 8 => 'E_NOTICE', 16 => 'E_CORE_ERROR',
        32 => 'E_CORE_WARNING', 64 => 'E_COMPILE_ERROR', 128 => 'E_COMPILE_WARNING', 256 => 'E_USER_ERROR',
        512 => 'E_USER_WARNING', 1024 => 'E_USER_NOTICE', 2048 => 'E_STRICT', 4096 => 'E_RECOVERABLE_ERROR',
        8192 => 'E_DEPRECATED', 16384 => 'E_USER_DEPRECATED', 30719 => 'E_ALL');
	
    array_push($GLOBALS['_ERRORS'], array('errno' => $errno, 'error' => $errors[$errno], 'errstr' => $errstr, 'errfile' => $errfile, 'errline' => $errline));
}

register_shutdown_function('fatalHandler');
set_error_handler('errorsHandler');