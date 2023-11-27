<?php
class HNumero {

	public static function decimalVirgula($valor) {
		return str_replace('.', ',', $valor);
	}

	public static function decimalMysql($valor) {
		if($valor === '') {
			return null;
		}
		return str_replace(',', '.', $valor);
	}

	public static function moedaBr($valor) {
		$valor  = trim($valor);
		return number_format($valor, 2,',','.');
	}

}
