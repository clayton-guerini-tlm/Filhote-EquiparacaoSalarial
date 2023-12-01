<?php

final class FMysql {

  private static $connections = array();

  public static function getConnection($connectionName, $database) {

    $key = $connectionName . $database;
    

    if(!array_key_exists($key, self::$connections)) {
      $config = self::getConfig($connectionName);
      self::$connections[$key] = self::create($config, $database);
    }

    return self::$connections[$key];

  }

  private static function getConfig($connectionName) {

    $con = Conexoes::buscarConexao($connectionName);

    if(is_null($con)) {
      throw new InvalidArgumentException('ConexÃ£o inexistente');
    }

    return current($con);

  }

  private static function create($config, $database) {

    try {

      $config = (object) $config;

      $host = $config->server;
      $database = $database;
      $username = $config->user;
      $passwd = $config->password;

      $dsn = "mysql:host={$host};dbname={$database}";

      $con = new PDO($dsn, $config->user, $config->password);

      $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $con->exec('set names utf8');

    } catch (Exception $e) {

      if($e->getCode == 2002){

        return null;   
      }

      return $e;
    }

    return $con;

  }

  private function __construct(){}
  private function __wakeup(){}
  private function __clone(){}

}