<?php

class ConnectionRM {
    
    private static $instance;
    private $connection;
    
    public static function getInstance() {
        
        if(!self::$instance) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
    	
        //$this->connection = odbc_connect('rm_teste','sigo','ff@21011983982', SQL_CUR_USE_ODBC);
        // $this->connection = odbc_connect('rm','sigo','ff@21011983982', SQL_CUR_USE_ODBC);
        $this->connection = odbc_connect('RM', 'usr_TELEMONT', 'UB3HBP58xsw3kgV', SQL_CUR_USE_ODBC);
        // $this->connection = odbc_connect('Driver={SQL Server};Server=SERVDBSQL2TMT\TOTVS;Database=CORPORE','sigo','ff@21011983982', SQL_CUR_USE_ODBC);
        $odbc_error = odbc_error();
        $odbc_errormsg = odbc_errormsg();
        
        if(!$this->connection || !empty($odbc_error) || !empty($odbc_errormsg)) {
            throw new Exception(odbc_errormsg());
        }
        
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Desabilita o auto-commit e inicia a transação implicitamente
     */
    public function beginTransaction(){
        odbc_autocommit(self::getConnection(), false);
    }
    
    /**
     * Executa o commit no banco de dados e habilita o auto-commit
     */
    public function commit(){
        odbc_commit(self::getConnection());
    }
    
    /**
     * Executa o rollback no banco de dados e habilita o auto-commit
     */
    public function rollBack(){
        odbc_rollback(self::getConnection());
    }
    
    public function __destruct(){}
    private function __clone() {}
    
}