<?php

abstract class Model {
    
    protected $connections = array();
    
    protected function __construct($connectionName, $database, $identifier) {
        $this->__addConnection($connectionName, $database, $identifier);
    }
    
    protected function __addConnection($connectionName, $database, $identifier) {

        if(!array_key_exists($identifier, $this->connections))
            $this->connections[$identifier] = FMysql::getConnection($connectionName, $database);
            
    }

    /**
     * Retorna a conexão reconhecida pelo identificador
     * @param string $identifier Identificador amigável para reconhecer a conexão. Caso não seja informado é considerado a primeira conexão criada
     * @return PDO
     */
    protected function getConnection($identifier = null) {

        if(is_null($identifier)) {
            return current($this->connections);
        }

        if(!array_key_exists($identifier, $this->connections)) {
            throw new InvalidArgumentException('Identificador de conexão inválido');
        }

        return $this->connections[$identifier];

    }
    
}