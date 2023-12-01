<?php

/**
 * @property $connection \PDO
 */
class DAO {

    protected $connection;

    protected function execute($statement) {

        try {

        	if($statement instanceof \PDOStatement) {

	            $statement->execute();

	            $error = $statement->errorInfo();

	            if(!is_null($error[2])) {
	                throw new \Exception($error[2], $error[1]);
	            }

	            return $statement;

	        }
	        else if($statement instanceof conexaoCONORTE) {
	        	return $statement->execute();
	        }

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e);
        }

    }

    protected function executeServer($query, $fetchClass = 'stdClass') {

        ini_set('memory_limit', '4000M');

        try {

            $result = odbc_exec($this->connection, $query);

            $error = odbc_error();
            $errorMsg = odbc_errormsg();

            if(!empty($error) || !empty($errorMsg)) {
                throw new \Exception($error . ' -- ' . $errorMsg);
            }

            $data = array();

            while($row = odbc_fetch_array($result)) {

            	if($fetchClass != 'stdClass') {

                	$class = new $fetchClass();

                	foreach($row as $prop => $val) {
                		if(property_exists(get_class($class), $prop)) {
				            $class->$prop = utf8_encode($val);
				        }
                	}
                	array_push($data, $class);
                }
                else {
                	$aux = array_map('utf8_encode', $row);
                	array_push($data, $aux);
                }
            }

            return $data;

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e);
        }

    }

    protected function delExecuteServer($query) {

        try {

            $result = odbc_exec($this->connection, $query);

            $error = odbc_error();
            $errorMsg = odbc_errormsg();

            if(!empty($error) || !empty($errorMsg)) {
                throw new \Exception($error . ' -- ' . $errorMsg);
            }

            return odbc_num_rows($result);

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e);
        }

    }

    protected function executeSqlServer($query) {

        try {

            $result = odbc_exec($this->connection, $query);

            $error = odbc_error();
            $errorMsg = odbc_errormsg();

            if(!empty($error) || !empty($errorMsg)) {
                throw new \Exception($error . ' -- ' . $errorMsg);
            }

            $data = array();

            while($row = odbc_fetch_array($result)) {
                $aux = array_map('utf8_encode', $row);
                array_push($data, $aux);
            }

            $ret['num_rows']    = odbc_num_rows($result);
            $ret['data']        = $data;

            return $ret;

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e);
        }

    }

    public static function imprimirQuery($query, $valores){
		foreach($valores as $valor) {
			$aux = '/'.preg_quote('?', '/').'/';
			$query = preg_replace($aux, "'" . $valor . "'", $query, 1);
	    }
	    echo "<pre>";print_r ($query);echo "</pre>";exit;
    }

}