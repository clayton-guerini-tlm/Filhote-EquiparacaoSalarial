<?php

abstract class ModelDW {
    
    protected function execute($query, $fetchClass = 'stdClass') {
        
        try {
            
            $con = ConnectionRM::getInstance()->getConnection();
            $result = odbc_exec($con, $query);
            
            $error = odbc_error();
            $errorMsg = odbc_errormsg();
            
            if(!empty($error) || !empty($errorMsg)) {
                throw new Exception($error . ' -- ' . $errorMsg);
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
                	$aux = array_map(function($value){
	                    return utf8_encode($value);
	                }, $row);
                	array_push($data, (object) $aux);
                }
                
            }
            
            return $data;
            
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
        
    }
    
}