<?php

class Controller {

    protected static $args;

    protected function __construct(){}
    protected function __clone(){}

    #private static $callBack;

    public static function executeFunction() {

        $arguments = self::arguments(func_get_args());

        #self::$callBack = isset($arguments['callBack']) || !empty($arguments['callBack']) ? $arguments['callBack'] : null;
        $controller = explode('::', $arguments['funcaoAjax']);
        $controllerName = $controller[0];
        $functionName = $controller[1];
        $function = "{$controllerName}::{$functionName}";

        unset($arguments['funcaoAjax']);
        unset($arguments['classUrl']);
        #unset($arguments['callBack']);

        self::$args = $arguments;

        try {
            return call_user_func($function);
        } catch (PDOException $e) {

        	if($e->getCode() == 1045) {
        		return self::defaultExceptionReturn(
        			new Exception('Ocorreu um erro ao realizar a conexão.', $e->getCode())
        		);
        	}else {
        		return self::defaultExceptionReturn($e);
        	}

        } catch (Exception $e) {
            return self::defaultExceptionReturn($e);
        }

    }

	protected static function arguments($args) {
		return current($args);
	}

	public static function response($success, $message = null, $data = null) {
	    return array('success' => $success, 'message' => $message, 'data' => $data, 'errors' => $GLOBALS['_ERRORS'] /*, 'callBack' => self::$callBack*/);
	}

	protected static function defaultExceptionReturn(Exception $e) {
	    return self::response(false, 'Ocorreu um erro na requisição', array('errorMessage' => strip_tags($e), 'stackTrace' => $e->getTrace()));
	}

	protected static function defaultPdoExceptionReturn(PDOException $e) {
	    return self::response(false, 'Ocorreu um erro ao realizar a conexão', array('errorMessage' => strip_tags($e), 'stackTrace' => $e->getTrace()));
	}

}