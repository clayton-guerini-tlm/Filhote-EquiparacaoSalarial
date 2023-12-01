<?php

final class ConexaoWs {

    private $server;
	private $user;
    private $pass;

    public function __construct($server = "producao", $user = 'SIGO', $pass = '94a4964c959e7fafd868b85a301e8d47') {
    	$this->server = $server;
    	$this->user = $user;
    	$this->pass = $pass;
    }

    /**
     * Metodo para realizar chamadas no WS
     * @param string $servico indicação do serviço a ser consultado
     * @param bool $parametro parametros enviados caso método seja post
     * @param bool $associativo indica se o decode da resposta do curl deve ser transformado em array associativo
     * @return object $response
     */
    public function invoke($servico, $parametro = array(), $associativo = false, $arquivo = false) {

    	if($this->server == "producao") {
    		$servicoUrl = 'http://sigo.telemont.com.br:8008/SIGO_INTEGRADO_3/wsTelemont/'.$servico;
    	}
    	else if($this->server == "ws52") {
    		$servicoUrl = 'http://wssigo.telemont.com.br:8090/ws/wsTelemont/'.$servico;
    	}
    	else if($this->server == "homologacao") {
    		$servicoUrl = 'http://sigo.telemont.com.br:8008/SIGO_INTEGRADO_3/teste_wsTelemont/'.$servico;
    	}
        else if($this->server == "homologacao_226") {
            $servicoUrl = 'http://192.168.5.226/SIGO_INTEGRADO_3/wsTelemont/'.$servico;
        }

		if(!$arquivo) {
			$parametro = http_build_query($parametro);
		}

		$curl = curl_init($servicoUrl);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		if(!empty($parametro)) {
			curl_setopt($curl, CURLOPT_POSTFIELDS, $parametro);
		}
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("user:{$this->user}", "pass:{$this->pass}"));

        if(!$associativo) {
            $response = (array) json_decode(curl_exec($curl));
        } else {
            $response = json_decode(curl_exec($curl), true);
        }
        curl_close($curl);

        return $response;
    }

}