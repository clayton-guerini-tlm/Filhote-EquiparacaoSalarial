<?php
final class Filtro {

	private $arraySql;
	private $arrayValores;

	public function __construct() {
		$this->arraySql = array();
		$this->arrayValores = array();
	}

	public function add(IFiltro $iFiltro, $campo, $valores, $operador = null) {
		$sql = ($operador === null) ? $iFiltro->gerarSql($campo, $valores) : $iFiltro->gerarSql($campo, $valores, $operador);
		array_push($this->arraySql, $sql);
		if ($valores !== null){
			is_array($valores) ? $this->arrayValores = array_merge($this->arrayValores, $valores) : array_push($this->arrayValores, $valores);
		}
	}

	public function getArraySql() {
		return (array) clone (object) $this->arraySql;
	}

	public function getStringSql($stringBefore = null, $implode = 'AND', $stringAfter = "") {
		$sql = (array) clone (object) $this->arraySql;
		return !empty($sql) ? $stringBefore . ' ' . implode(' ' . $implode . ' ', $sql) . ' ' . $stringAfter : '';
	}

	public function getValores() {
		return (array) clone (object) $this->arrayValores;
	}

	public function destroy() {
		$this->arraySql = array();
	}

}