<?php
final class FiltroBetween implements IFiltro {

	public function gerarSql($campo, $valores){

		return "{$campo} BETWEEN ? AND ?";

	}

}