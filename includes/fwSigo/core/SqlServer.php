<?php

abstract class SqlServer {
    
    protected $connection;
    
    protected $host;
    protected $username;
    protected $password;
    
    public function __construct() {
        
        try {
            
            //$this->connection = odbc_connect('rm_teste','sigo','ff@21011983982', SQL_CUR_USE_ODBC);
            //$this->connection = odbc_connect('rm','sigo','ff@21011983982', SQL_CUR_USE_ODBC);
            $this->connection = odbc_connect($this->host, $this->username, $this->password, SQL_CUR_USE_ODBC);
            // $this->connection = odbc_connect('Driver={SQL Server};Server=SERVDBSQL2TMT\TOTVS;Database=CORPORE','sigo','ff@21011983982', SQL_CUR_USE_ODBC);

            if($this->connection === false) {
                $this->connection = odbc_connect("Driver={SQL Server Native Client 11.0}; Server=SERVDBSQL2TMT\TOTVS;Integrated Security=SSPI", $this->username, $this->password, SQL_CUR_USE_ODBC);
            }
            
            $odbc_error = odbc_error();
            $odbc_errormsg = odbc_errormsg();
            
            if(!$this->connection || !empty($odbc_error) || !empty($odbc_errormsg)) {
                throw new \Exception(odbc_errormsg());
            }
            
        } catch (\Exception $e) {
            
            // throw new \Exception($e->getMessage(), $e->getCode(), $e);
            throw new \Exception('Erro ao realizar a conexão no banco sqlserver: ' . $this->host);
            
        }
        
    }
    
    /**
     * @return resource
     */
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
    
}