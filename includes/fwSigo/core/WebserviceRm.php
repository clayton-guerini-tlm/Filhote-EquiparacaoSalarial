<?php
class WebserviceRm {

	protected $url;
	protected $connection;
	protected $wsdl;
	protected $location;

    public function __construct($server = 'producao', $tipo = 'wsDataServer') {

    	/**
    	 * SERVIDOR
    	 */
    	//PRODUÇÃO
    	if($server == 'producao') {
			$this->url = 'http://jvunuh.prd.ws.totvscloud.com.br:8083';
		}
		//HOMOLOGAÇÃO
		else if($server == 'homologacao') {
			$this->url = 'http://jvunuh-hom-ws.totvscloud.com.br:37080';
		}
		//TESTE
		else if($server == 'teste') {
			$this->url = 'http://jvunuh-tst-ws.totvscloud.com.br:26080';
		}
		//DESENVOLVIMENTO
		else if($server == 'desenvolvimento') {
			$this->url = 'http://jvunuh-dev-ws.totvscloud.com.br:37380';
		}

		/**
		 * TIPO SERVIÇO
		 */
    	if($tipo == 'wsDataServer') {
			$this->wsdl = '/wsDataServer/MEX?wsdl';
		}
		else if($tipo == 'wsProcess') {
			$this->wsdl = '/wsProcess/MEX?wsdl';
		}

		$this->connection = new \SoapClient($this->url.$this->wsdl, array('login'=>'mestre', 'password'=>'t3l3m0nt@mestre@'));
    }

    public function getUrl() {
    	return $this->url;
    }

    public function getConnection() {
    	return $this->connection;
    }

}