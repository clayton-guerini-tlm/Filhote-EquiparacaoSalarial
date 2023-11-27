<?php

final class ConexaoNextFleetWs {

    private $server;
	private $token;


    public function __construct($server = 'producao',$token = '76dbcf6b697c85de7da15459e96f39b4') {

    	$this->token    = $token;
        $this->server   = $server; 
    }

    /**
     * @method Metodo para realizar chamadas no WS NextFleetWs
     * @param string $servico indicação do serviço a ser consultado
     * @param bool $parametro parametros enviados caso método seja post
     * @param bool $associativo indica se o decode da resposta do curl deve ser transformado em array associativo
     * @return object $response
     */
    public function invoke($servico, $parametro = array(), $associativo = false, $arquivo = false, $request = "") {

    	if($this->server == "producao") {
    		$servicoUrl = 'https://sisint.nextfleet.online'.$servico;
    	}
         
		// if(!$arquivo) {
		// 	$parametro = http_build_query($parametro);
		// }
        $parametro = !empty($parametro) ? $parametro[0] : '';
           
		$curl = curl_init($servicoUrl);

        #REQUEST POST 
        if(!empty($parametro)) {
			curl_setopt($curl, CURLOPT_POSTFIELDS, $parametro);
            curl_setopt($curl,CURLOPT_POST,1);
		}
        // $parametro = 'period_filter_dt_aprocacao: "2021-12-15":"2021-12-15"'; 
        #HEARDERS
        curl_setopt($curl,CURLOPT_HTTPHEADER,array(
            'Authorization:'.$this->token,
            'Content-Type: application/json',
            'Accept:application/json',
            'Accept-Charset: utf-8',
            'Accept-Encoding: gzip',
            $parametro
            
        ));
        
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
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