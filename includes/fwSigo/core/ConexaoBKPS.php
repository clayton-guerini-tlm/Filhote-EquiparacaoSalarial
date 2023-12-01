<?php

final class ConexaoBKPS {

    private $server;
	private $token;

    public function __construct($server = "producao", $token = 'F1eoqx7A3tVppLyGZy1K3RjA') {
    	$this->server = $server;
    	$this->token = $token;
    }

    /**
     * Metodo para realizar chamadas no WS
     * @param string $servico indicação do serviço a ser consultado
     * @param bool $parametro parametros enviados caso método seja post
     * @param bool $associativo indica se o decode da resposta do curl deve ser transformado em array associativo
     * @return object $response
     */
    public function invoke($servico, $parametro = array(), $associativo = false, $arquivo = false, $request = "") {

    	if($this->server == "producao") {
    		$servicoUrl = 'https://api.platform.brobot.com.br/api/v2/'.$servico;
    	}

		if(!$arquivo) {
			$parametro = http_build_query($parametro);
		}

		$curl = curl_init($servicoUrl);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		if(!empty($parametro)) {
			curl_setopt($curl, CURLOPT_POSTFIELDS, $parametro);
		}
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("token:".$this->token));
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		if(!empty($request)) {
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request);
		}

        $rs = curl_exec($curl);
        if($rs === false) {
        	throw new Exception("Erro ao executar o serviço ($servicoUrl): ". curl_error($curl));
		}

        if(!$associativo) {
            $response = (array) json_decode($rs);
        } else {
            $response = json_decode($rs, true);
        }
        curl_close($curl);

        return $response;
    }

}