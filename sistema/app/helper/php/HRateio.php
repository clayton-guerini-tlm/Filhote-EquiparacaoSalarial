<?php

/**
 *
 */
final class HRateio
{

    /**
    * @method Retorna dados adicionais da nota
    */
    public static function salvarDadosAdicionais($args) {

        $mDadosAdicionais = new MOperadoraNota();

        $dados = new Teste();
        $dados->operadora = $args['numOperadora'];
        $dados->nota = $args['numNota'];
        $dados->competencia  = $args['numCompetencia'];
        $dados->valor  = $args['numValorNota'];

        //verifica se os dados adicionais já existem
        $dadosAdicionaisExist = $mDadosAdicionais->buscarDadosAdicionais($dados);

        if (!empty($dadosAdicionaisExist) && count($dadosAdicionaisExist) > 0) {

            //atualiza dados adicionais
            return $mDadosAdicionais->updateDadosAdicionais($dadosAdicionaisExist[0]->id, $dados);

        } else {

            //salva dados adicionais
            return $mDadosAdicionais->salvarDadosAdicionais($dados);
        }
    }


    /**
    * @method Retorna dados adicionais da nota
    */
    public static function movimentaArquivosPasta($args, $files) {

       //verifica se a pasta já exixte
        if (!file_exists('../../SIGO_MG/sigo_ddg/PLANO_SAUDE/includes/operadoras/uploads/'.$args['numNota'].'/')){

            //se a pasta não existe cria a pasta
            mkdir('../../SIGO_MG/sigo_ddg/PLANO_SAUDE/includes/operadoras/uploads/'.$args['numNota'].'/', 0777, true);
        } else {

            //limpa a pasta antes de gerar novos arquivos
            $limpaPasta = HRateio::limpaPastaNota($args);
        }

        //foreach para mover arquivos para a pasta
        foreach ($files as $key => $file) {

            $uploaddir = '../../SIGO_MG/sigo_ddg/PLANO_SAUDE/includes/operadoras/uploads/'.$args['numNota'].'/';
            $uploadfile = $uploaddir . basename($file['name']);

            $upload = move_uploaded_file($file['tmp_name'], $uploadfile);

            if (!$upload) {
                return self::response(false, $e->getMessage(), null);
            }
        }

        return true;
    }

	/**
    * @method Retorna rateios por nota
    */
    public static function buscaRateiosPorNota($args){


    	$mCentroCusto = HRateio::selecionaCentroCusto($args['tipo']);
       	$chapasporNota = $mCentroCusto->buscaChapasPorNota($args);
		$centrosCusto = HRateio::buscarCentroCusto($chapasporNota);
		$rateiorCalculados = HRateio::buscaRateiosPorCentroCusto($args, $centrosCusto, $mCentroCusto);
        $geraCsvRateios = HRateio::criaCsvRateios($args['nota'],$args['competencia'], $rateiorCalculados);

		return $rateiorCalculados;
    }


	/**
    * @method Retorna chapas por nota
    */
    public static function selecionaCentroCusto($tipo){

        //seleciona o model a ser utilizado  			
		if ($tipo == 'coparticipacao') {
			$mCentroCusto = new MCoparticipacao();
		} else {
			$mCentroCusto = new MMensalidade();
		}
		return $mCentroCusto;
    }



    /**
    * @method Retorna centros de custo por chapa
    */
    public static function buscarCentroCusto($chapasporNota) {

    	$arrayChapas = array();

		foreach ($chapasporNota as $chapa) {
			
			$arrayChapas[] = $chapa->chapa;
		}

		$listaDeChapas = implode("','", $arrayChapas);

		//busca centros de custo por chapa
		$mCentroCustoRM = new MCentroDeCustoRM();
		return $mCentroCustoRM->buscaCentrosDeCustosPorChapas($listaDeChapas);
    }

    /**
    * @method Retorna rateios por nota e centro de custo
    */
    public static function buscaRateiosPorCentroCusto($args, $centrosCusto, $mCentroCusto){

    	$rateios = array();


        $porcentagemNcalculada = 0;
        $porcentagemTratada = 0;
        $porcentagemTotal = 0;

    	foreach ($centrosCusto as $key => $centroCusto) {

            //funcao responsavel por buscar centros de custo pelos parametros operadora, comperencia nota e chapa
    		$results = $mCentroCusto->buscarCentroCustoPorNotaChapa(
    			$args['operadora'],
    			$args['competencia'],
    			$args['nota'], 
    			$centroCusto->chapa
    		);

            //preenche os objetos de rateio para retorno (feito dessa forma caso haja mais de um rateio por chapa)
    		foreach ($results as $key => $result) {

                $obj = new Rateio();
                $obj->centroCusto = $centroCusto->centroCusto;
                $obj->valor = ($result->valor * ($centroCusto->porcentagem) / 100);
                $obj->porcentagem =  round((($obj->valor / $args['valor']) * 100), 2);
			    $rateios[] = $obj;
    		}
    	}

        return $rateios;
    }


    /**
    * @method Cria cvs de arquivos de ratio caso não existam
    */
    public static function criaCsvRateios($nota, $competencia, $rateiorCalculados){

        $nomeArquivo = $nota.'-'.$competencia.'.csv';
        $nomeCaminho = '../../SIGO_MG/sigo_ddg/PLANO_SAUDE/includes/operadoras/uploads/'.$nota.'/'.$nomeArquivo;

        //verifica se o arquivo já existe
        if (!file_exists($nomeCaminho)){

            $linhas = array();
            // Abrir/criar arquivo
            $arquivo = fopen($nomeCaminho, 'w');

            foreach ($rateiorCalculados as $key => $obj) {

                $linha = (array) $obj;
                fputcsv($arquivo, $linha);
            }           

            // Fechar o arquivo
            fclose($arquivo);
        }

        return true;
    }


    /**
    * @method Retorna lista de arquivos salvos no diretorio da nota
    */
    public static function listarArquivosDiretorioNota($nota) {

        $caminhoPasta = '../../SIGO_MG/sigo_ddg/PLANO_SAUDE/includes/operadoras/uploads/'.$nota.'/';
        $diretorio = dir($caminhoPasta);

        $listaArquivos = array();

        while($arquivo = $diretorio -> read()){

            if(strlen($arquivo) > 2) {

                $temp = array('nome' => $arquivo, 'link' => $caminhoPasta.$arquivo);
                $file = new stdClass();
                $file = $temp;
                $listaArquivos[] = $file;   
            }
        }

        $diretorio -> close();
      
        return $listaArquivos;
    }


    /**
    * @method Veficica se a solicitação de pagamento pode ser realizada
    */
    public static function verificaCondicoesPagamento($args) {

        //verifica se o valor é válido
        $diferenca = HRateio::verificaValorTotalNota($args);
        //verifica se os arquivos necessários existem
        $dadosArquivos = HRateio::verificaArquivosNota($args['nota']);

        return array('dadosArquivos' => $dadosArquivos, 'diferenca' => $diferenca);
    }

    /**
    * @method Veficica se arquivos que devem ser gerados existem no diretorio
    */
    public static function verificaArquivosNota($nota) {

        //busca arquivos salvod no diretorio da nota
        $arquivos = HRateio::listarArquivosDiretorioNota($nota);

        //variaveis para quantificaçaõ de arquivos
        $nPdfs = 0;
        $nCsvs = 0;

        //verifica a quantidade de pdfs e csvs
        foreach ($arquivos as $key => $arquivo) {

            if (substr($arquivo['nome'], -4) == '.pdf') {

                $nPdfs++;
            }
            if (substr($arquivo['nome'], -4) == '.csv') {

                $nCsvs++;
            }
        }

        //caso o numero de pdfs (nota e boleto), seja menor que 2
        if ($nPdfs < 2) {

            throw new Exception('Está faltando a Nota Fiscal ou Boleto, verifique os anexos.');
        }

        //caso exista csv de rateio
        if ($nCsvs == 0) {

            throw new Exception('Está faltando o Arquivo de rateio, verifique os anexos, abra a janela de Rateio (um novo arquivo sera criado automaticamente).');
        }

        return true;
    }

    /**
    * @method Veficica se o valor total da nota está vazio ou se diverge 
    * muito do valor do arquivo
    */
    public static function verificaValorTotalNota($args) {

        if($args['valorNota'] == 0 || $args['valorNota'] == '0' || $args['valorNota'] == null) {
            throw new Exception('Valor da nota não pode ser igual a 0. Defina um valor em Dados adicionais.');
        }

        $diff = $args['valorNota'] - $args['valorArquivo'];

        if ($diff < 0) {
            $diff = $diff * (-1);
        }

        if ($diff > 100) {
            
            $mensagem = "Existe uma divergência de R$".$diff." entre o valor da nota e o valor do arquivo, deseja continuar?";
            return array(
                'diferenca' => $diff, 
                'alerta' => true, 
                'mensagem' => ''
            );
        } else {
            return array('diferenca' => $diff, 'alerta' => false);
        }        
    }


    //Deletar arquivo da pasta
    public static function deletaArquivo($arquivo) {

        try {
            
            if (file_exists($arquivo)) {

                $arquivo = unlink($arquivo);
            }

            return true;

        } catch (Exception $e) {
            return self::response(false, $e->getMessage(), null);
        }
    }


    //Deletar arquivo da pasta
    public static function limpaPastaNota($args) {

        try {
            
            //busca arquivos salvod no diretorio da nota
            $arquivos = HRateio::listarArquivosDiretorioNota($args['numNota']);

            //verifica a quantidade de pdfs e csvs
            foreach ($arquivos as $key => $arquivo) {

                if (substr($arquivo['nome'], -4) == '.pdf') {

                    $arquivo = HRateio::deletaArquivo($arquivo['link']);
                }

                if (($args['numValorNota'] != $args['numValorNotaOriginal']) 
                    && (substr($arquivo['nome'], -4) == '.csv')) {

                    $arquivo = HRateio::deletaArquivo($arquivo['link']);
                }    
            }

            return true;

        } catch (Exception $e) {
            return self::response(false, $e->getMessage(), null);
        }      
    }

}