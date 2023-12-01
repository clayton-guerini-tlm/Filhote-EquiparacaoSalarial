<?php

final class Conexoes {

    public static function buscarConexao($nomeConexao) {

        $conRet = null;

        $cons = '../../includes/banco.php';
        $handle = fopen($cons, 'r');
		$cons = array();

        if ($handle) {
            while (($buffer = fgets($handle, 4096)) !== false) {
				
				$identifier = trim(str_replace(array("'", 'case', ':'), '', $buffer));
                $con = array($identifier => array());
				
				if(strpos($buffer, 'case') !== false) {

					$identifier = trim(str_replace(array("'", 'case', ':'), '', $buffer));
					$con = array($identifier => array());

					while(strpos($buffer, '$host') === false) {
						$buffer = fgets($handle, 4096);
						break;
					}

					$con[$identifier]['server'] = trim(str_replace(array("'", '$host', ';', '='), '', $buffer));

					while(strpos($buffer, '$user') === false) {
						$buffer = fgets($handle, 4096);
						break;
					}

					$con[$identifier]['user'] = trim(str_replace(array('$user', '=', ';', "'"), '', $buffer));

					while(strpos($buffer, '$pass') === false) {
						$buffer = fgets($handle, 4096);
						break;
					}

					$passAux = trim(str_replace(array('$pass', '=', "'", ';', ' '), '', $buffer));
					$con[$identifier]['password'] = strpos($passAux, '//') ? substr($passAux, 0, strpos($passAux, '//')) : $passAux;
					
					array_push($cons, $con);

				}

				if(strpos($buffer, 'default:') !== false) {
					break;
				}

                if(array_key_exists($nomeConexao, $con)) {
                    $conRet = $con;
                }

            }

            fclose($handle);

        }

        return $conRet;

    }

    private function __construct(){}
    private function __wakeup(){}
    private function __clone(){}

}