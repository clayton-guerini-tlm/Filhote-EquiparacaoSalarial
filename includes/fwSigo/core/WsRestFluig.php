<?php

/**
 * CLASSE RESPONSÁVEL POR EXECUTAR SERVIÇOS REST DO FLUIG
 * @param (string) $method: GET | POST
 * @param (string) $endpoint: url do serviço sendo invocado
 * Exemplo de chamada:
 * 		$wsRestFluig = new WsRestFluig("GET", "2.0/documents/getDownloadURL/1255445");
		$rs = $wsRestFluig->invoke();
 */
class WsRestFluig {

	protected $url;
	protected $endpoint;
	protected $service;
	protected $method;
	protected $consumerKey;
	protected $consumerSecret;
	protected $accessToken;
	protected $tokenSecret;
	protected $signatureMethod;
	protected $currentTime;
	protected $nonce;
	protected $version;

    public function __construct($method, $endpoint) {

    	$this->url = "https://fluig.telemont.com.br:8443/api/public/";
    	$this->endpoint = $endpoint;
    	$this->service = $this->url . $endpoint;
		$this->method = $method;
		$this->consumerKey = "53e9e240-9f1b-11e8-98d0-529269fb1459";
		$this->consumerSecret = "6de36f2c-9f1b-11e8-98d0-529269fb1459-779df0f0-9f1b-11e8-98d0-529269fb1459";
		$this->accessToken = "cd0f81bb-57da-4b3f-9360-ea306705e525";
		$this->tokenSecret = "1825b339-4710-4179-bcce-e7cfa01800495c79cc8d-e333-4031-99e4-4e80c0e1f896";
		$this->signatureMethod = "HMAC-SHA1";
		$this->currentTime = time();
		$this->version = "1.0";
		$this->nonce = md5(mt_rand());
		$this->criarSignature();

    }

    public function getParametro() {
    	$parametro = array();
    	$parametro['oauth_consumer_key'] = $this->consumerKey;
		$parametro['oauth_token'] = $this->accessToken;
		$parametro['oauth_signature_method'] = $this->signatureMethod;
		$parametro['oauth_timestamp'] = $this->currentTime;
		$parametro['oauth_version'] = $this->version;
		$parametro['oauth_nonce'] = $this->nonce;

		return $parametro;
    }

    public function criarSignature() {

    	$parametro = $this->getParametro();
		ksort($parametro);

		$base = $this->method."&".urlencode($this->service)."&";

		$itens = array();
		foreach($parametro as $key => $value){
			$itens[] = $key."=".$value;
		}

		$base .= urlencode(implode('&', $itens));

		$signature = base64_encode(hash_hmac('SHA1', $base, $this->consumerSecret."&".$this->tokenSecret, true));
		$this->signature = $signature;

	}

	function criarUrl($parametro) {

		$itens = array();
		foreach($parametro as $key => $value){
			$itens[] = "{$key}={$value}";
		}

		$urlParam = implode('&', $itens);
		return $this->service."?".$urlParam;
	}

	public function invoke() {

		$parametro = $this->getParametro();
		$parametro['oauth_signature'] = $this->signature;
		$url = $this->criarUrl($parametro);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_HTTPHEADER, array());
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		$rsRequest = curl_exec($curl);
		$erro = curl_error($curl);
		curl_close($curl);

		if($erro) {
			throw new Exception($erro);
		}

		return json_decode($rsRequest);

	}



}