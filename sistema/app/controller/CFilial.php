<?php
final class CFilial extends Controller {

	public static function buscarFilial() {
		$mSigo = new MSigoIntegrado();
		$rsFilial = $mSigo->buscarFilial();
		return self::response(true, null, $rsFilial);
	}

	public static function buscarFilialGrupo() {
/*
		$conexaoWs = new ConexaoWs();
		return $conexaoWs->invoke('sigo/adm/plano_saude/buscarFilialGrupo/');
*/

		/**
		 * TESTE
		 */
		$rsFilialGrupo = array(
			array('id' => 1, 'texto' => 'filialGrupo1'),
			array('id' => 2, 'texto' => 'filialGrupo2'),
			array('id' => 3, 'texto' => 'filialGrupo3'),
			array('id' => 4, 'texto' => 'filialGrupo4')
		);

		return self::response(true, null, $rsFilialGrupo);
	}

}
