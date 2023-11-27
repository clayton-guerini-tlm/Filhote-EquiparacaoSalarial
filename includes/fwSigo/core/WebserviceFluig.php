<?php

class WebserviceFluig {

	protected $connection;
	protected $username;
	protected $password;
	protected $companyId;

    public function __construct($service) {
		// $this->connection = new \SoapClient("https://192.168.0.91:8443/webdesk/{$service}?wsdl");
		// $this->connection = new \SoapClient("https://telemont-hom.fluig.com:9143/webdesk/{$service}?wsdl");
		$this->connection = new \SoapClient("https://fluig.telemont.com.br:8443/webdesk/{$service}?wsdl");
		$this->username = 'clienteWs';
		$this->password = '265601b31cf3d643b6b699fbd523a2df';
		$this->companyId = 1;
    }

    public function getConnection() {
    	return $this->connection;
    }

    public function getUsername() {
    	return $this->username;
    }

    public function getPassword() {
    	return $this->password;
    }

    public function getCompanyId() {
    	return $this->companyId;
    }

}