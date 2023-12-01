<?php
class HCsv {

	/**
	 * Retorno um array de objetos associativos de acordo com a primeira linha do arquivo
	 * 
	 **/
	public static function gerarObjetoAssociativo($postFile) {

		$arquivo = fopen($postFile['tmp_name'], 'r');

		$retorno = array();

		while($linha = fgets($arquivo)) {

            $linha = str_replace(PHP_EOL, '', $linha);
            $row = explode(';', $linha);

            /**
             * ESTABELECER PRIMEIRA LINHA COMO ATRIBUTOS DO OBJETO
             */
            if(!isset($objBase)) {
            	$objBase = (object) array_fill_keys(array_keys(array_flip($row)), null);
            	continue;
            }

            $obj = clone($objBase);
            $cont = 0;
            foreach ($obj as $key => $value) {
			    $obj->$key = $row[$cont];
			    $cont++;
			}

			$retorno[] = $obj;

        }

        return $retorno;

	}

}