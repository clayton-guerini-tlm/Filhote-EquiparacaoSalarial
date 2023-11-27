<?php
class RM extends SqlServer {

    public function __construct($servidor = "producao") {

    	if($servidor == 'producao') {
    		$this->host = 'RM';
        	$this->username = 'usr_TELEMONT';
        	$this->password = 'UB3HBP58xsw3kgV';
    	}
    	else if($servidor == 'homologacao') {
    		$this->host = 'Driver={SQL Server};Server=187.94.53.48,38001;Database=RM_JVUNUH_HMG';
        	$this->username = 'usr_TELEMONT';
        	$this->password = 'UB3HBP58xsw3kgV';
    	}
    	else if($servidor == 'desenvolvimento') {
    		$this->host = 'Driver={SQL Server};Server=187.94.53.48,38001;Database=RM_JVUNUH_DEV';
        	$this->username = 'usr_TELEMONT';
        	$this->password = 'UB3HBP58xsw3kgV';
    	}
    	else if($servidor == 'teste') {
    		$this->host = 'Driver={SQL Server};Server=187.94.53.48,38001;Database=RM_JVUNUH_TST';
        	$this->username = 'usr_TELEMONT';
        	$this->password = 'UB3HBP58xsw3kgV';
    	}
    	else if($servidor == 'producaoAntiga') {
    		// $this->host = 'Driver={SQL Server};Server=SERVDBSQL2TMT\TOTVS;Database=CORPORE';
    		$this->host = 'Driver={SQL Server};Server=192.168.5.95\TOTVS;Database=CORPORE';
        	$this->username = 'sigo_contracheque';
        	$this->password = 'jlrn8us$11';
    	}

        parent::__construct();

    }
}