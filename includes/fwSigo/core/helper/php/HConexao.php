<?php
class HConexao  {

	public static function conexaoPonto() {
		return array(
			array(
				'conexao' => 'sigo_ponto',
				'modulo' => 'modulo_ponto'
			),
			array(
				'conexao' => 'serversp',
				'modulo' => 'modulo_ponto'
			),
			array(
				'conexao' => 'sigo_ponto_conorte',
				'modulo' => 'modulo_ponto'
			),
			array(
				'conexao' => 'sigo_ponto',
				'modulo' => 'modulo_ponto_personal'
			)
		);
	}

	public static function conexaoPorFilial($siglaEstado) {

		switch($siglaEstado) {

			case "MG":
			case "ES":
				$conexao = "sigo_ponto";
				break;

			case "SP":
				$conexao = "serversp";
				break;

			case "AC":
			case "AL":
			case "AM":
			case "BA":
			case "DF":
			case "GO":
			case "MS":
			case "MT":
			case "PI":
			case "PR":
			case "RO":
			case "RR":
			case "TO":
			case "CE":
				$conexao = "sigo_ponto_conorte";
				break;

			default:
				throw new Exception("Filial inválida: {$siglaEstado}");

		}

		return $conexao;
	}

	public static function conexaoPontoPorFilial($siglaEstado, $codColigada = 2) {

		if($codColigada == 2) {
			switch($siglaEstado) {

				case "MG":
				case "ES":
					$conexao = array(
						'conexao' => 'sigo_ponto',
						'modulo' => 'modulo_ponto'
					);
					break;

				case "SP":
					$conexao = array(
						'conexao' => 'serversp',
						'modulo' => 'modulo_ponto'
					);
					break;

				case "AC":
				case "AL":
				case "AM":
				case "BA":
				case "DF":
				case "GO":
				case "MS":
				case "MT":
				case "PI":
				case "PR":
				case "RO":
				case "RR":
				case "TO":
				case "CE":
					$conexao = array(
						'conexao' => 'sigo_ponto_conorte',
						'modulo' => 'modulo_ponto'
					);
					break;

				default:
					throw new Exception("Filial inválida: {$codColigada} - {$siglaEstado}");

			}

		} else if($codColigada == 3) {
			switch($siglaEstado) {
				case "MG":
					$conexao = array(
						'conexao' => 'sigo_ponto',
						'modulo' => 'modulo_ponto_personal'
					);
					break;

				default:
					throw new Exception("Filial inválida: {$codColigada} - {$siglaEstado}");
			}
		}

		return $conexao;
	}

}