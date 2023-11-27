<?php
class HDate {

	public static function dateToSql($date) {
		$date = trim($date);

		if(empty($date)) {
			return "";
		}

		list($dia, $mes, $ano) = explode("/", substr(trim($date), 0, 10));
	    $dtsql = "$ano-$mes-$dia" . substr(trim($date), 10, strlen(trim($date)));
	    return trim($dtsql);
	}

	public static function sqlToDate($datasql) {
		$datasql = trim($datasql);

		if(empty($datasql)) {
			return "";
		}

		$data = $datasql;
		$tempo = '';

	    if (strlen($datasql) > 10) {

	        list($data, $tempo) = @explode(' ', $datasql);

	        $data = trim($data);
	        $tempo = trim($tempo);
	    }

	    list($ano, $mes, $dia) = @explode('-', $data);

	    $retorno = "$dia/$mes/$ano";
	    if ($tempo != '')
	        $retorno .= " $tempo";

		return $retorno;
	}

	public static function diasEntreDuasDatas($dataInicio, $dataFim, $diasDaSemanaIgnorar = array(), $datasIgnorar = array()){

		$dataInicio = new \DateTime($dataInicio);
		$dataFim    = new \DateTime($dataFim);
		$dataFim    = $dataFim->modify('+1 day');

		$intervalo  = \DateInterval::createFromDateString('1 day');
		$periodo    = new \DatePeriod($dataInicio, $intervalo, $dataFim);
		$datas 		= array();

		foreach($periodo as $data ) {

			$diaDaSemana = date('w', strtotime($data->format('Y-m-d')));

			if(in_array($diaDaSemana, $diasDaSemanaIgnorar)) {
				continue;
			}

			if(in_array($data->format('Y-m-d'), $datasIgnorar)) {
				continue;
			}

			$datas[] = $data->format('Y-m-d');

		}

		return $datas;

	}

	public static function mesesEntreDuasDatas($dataInicio, $dataFim){

		$dataInicio = new \DateTime($dataInicio);
		$dataFim    = new \DateTime($dataFim);
		$dataFim    = $dataFim->modify('+1 month');

		$intervalo  = \DateInterval::createFromDateString('1 month');
		$periodo    = new \DatePeriod($dataInicio, $intervalo, $dataFim);
		$datas 		= array();

		foreach($periodo as $data) {

			$datas[] = $data->format('Y-m-d');

		}

		return $datas;

	}


}