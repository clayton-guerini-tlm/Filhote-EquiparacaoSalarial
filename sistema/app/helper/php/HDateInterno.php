<?php
class HDateInterno {

	public static function competenciaToDate($competencia) {
		$aux = explode('/', $competencia);
    	return $aux[0].'-'.$aux[1] . '-01';
	}

	public static function dateToCompetencia($date) {
		$aux = explode('-', $date);
    	return $aux[1].'/'.$aux[0];
	}

	public static function competenciaFiltroToDate($competencia) {
		return $competencia . "-01";
	}

	public static function competenciaBrasilToDate($competencia) {
		$aux = explode('/', $competencia);
    	return $aux[1].'-'.$aux[0] . '-01';
	}

}