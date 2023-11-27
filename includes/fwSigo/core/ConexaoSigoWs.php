<?php

final class ConexaoSigoWs {

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
     * @param metodo Requisição GET ou POST ,default GET 
     * @return object $response
     */
    public function invoke($servico, $parametro = array(), $associativo = false, $arquivo = false,$method='GET') {

    	$servicoUrl = $this->setUrlService($servico);

		if(!$arquivo) {
			$parametro = http_build_query($parametro);
		}

		$curl = curl_init($servicoUrl);

        curl_setopt($curl, CURLOPT_HTTPHEADER, array("user:{$this->user}", "pass:{$this->pass}"));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        /**
         * Define requisição "POST" e "GET"
         */
        if(strtoupper($method) == 'GET'){
            curl_setopt($curl, CURLOPT_HTTPGET, true);     
        }
        else if(strtoupper($method) == 'POST'){
            curl_setopt($curl, CURLOPT_POST, true);
        }
        /**Parâmetros*/
		if(!empty($parametro)) {
			curl_setopt($curl, CURLOPT_POSTFIELDS, $parametro);
		}

        curl_setopt($curl, CURLOPT_SAFE_UPLOAD, false);

  
        
        if(!$associativo) {
            $response = (array) json_decode(curl_exec($curl));
        } else {
            $response = json_decode(curl_exec($curl), true);
        }

        /**Verifica Erro */
        if(curl_errno($curl)){
            return curl_error($curl);
        }

        curl_close($curl);

        return $response;
    }


    public function setUrlService($servico){

        switch ($this->server) {
            case 'producao':
                return 'http://sigo.telemont.com.br:8008/SIGO_INTEGRADO_3/wsTelemont/'.$servico;
            break;
            case 'homologacao':
                return 'http://sigo.telemont.com.br:8008/SIGO_INTEGRADO_3/homologacao_wsTelemont/'.$servico;
            break;
            case 'homologacao_226':
                return 'http://192.168.5.226/SIGO_INTEGRADO_3/wsTelemont/'.$servico;
            break;
            case 'ws52':
                return 'http://wssigo.telemont.com.br:8090/ws/wsTelemont/'.$servico;
            break;
            case 'teste':
                return 'http://sigo.telemont.com.br:8008/teste_wsTelemont/'.$servico;
            break;
            case 'local':
                return 'http://localhost/wsTelemont/'.$servico;
            break;
            default:
                throw new \Exception('Nenhum serve selecionado');
                break;
        }

    }
}