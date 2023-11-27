<?php
final class FiltroString implements IFiltro {

	public function gerarSql($campo, $valores, $operador = '='){

		if($operador == '=') {
			if(is_array($valores)) {
				return "{$campo} IN (" . str_repeat('?,', count($valores) - 1) . "?)";
			}else {
				return "{$campo} = ?";
			}
		}else {
			return "{$campo} {$operador} ?";
		}

	}

}