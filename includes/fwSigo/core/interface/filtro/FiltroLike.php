<?php
final class FiltroLike implements IFiltro {

	public function gerarSql($campo, $valores){

		if(count($valores) >= 2) {
			$aux = array();
			foreach($valores as $valor) {
				$aux[] = "{$campo} LIKE ?";
			}
			return "(" . implode(' OR ', $aux) . ")";
		}else {
			return "{$campo} LIKE ?";
		}

	}

}