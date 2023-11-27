<?php
class HQuery  {

	/**
	 * Substitui as "?" na query pelos valores
	 * @param (string) $query: query com as "?"
	 * @param (array) $valores: valores que devem ser substituidos na query na ordem em que as "?" aparecem
	 */
	public static function imprimirQueryPDO($query, $valores){
		foreach($valores as $valor) {
			$aux = '/'.preg_quote('?', '/').'/';
			$query = preg_replace($aux, "'" . $valor . "'", $query, 1);
	    }
	    return $query;
    }

}