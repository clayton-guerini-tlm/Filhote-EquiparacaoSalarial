<?php
final class FiltroStringNotIn implements IFiltro {

	public function gerarSql($campo, $valores){

		if(count($valores) >= 2) {
			return "{$campo} NOT IN (" . str_repeat('?,', count($valores) - 1) . "?)";
		}else {
			return "{$campo} <> ?";
		}

	}

}