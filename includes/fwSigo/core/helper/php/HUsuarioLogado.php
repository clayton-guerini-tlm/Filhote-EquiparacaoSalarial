<?php
class HUsuarioLogado {

	public static function getChapa() {
		return $_SESSION['SIGO']['ACESSO']['CHAPA'];
	}

	public static function getColigadaEmpresa() {
		return $_SESSION['SIGO']['ACESSO']['LOGIN_EMPRESA'];
	}

	public static function getColigadaFuncionario() {
		return $_SESSION['SIGO']['ACESSO']['CODCOLIGADA'];
	}

	public static function getGrupo() {
		return explode('|', $_SESSION['SIGO']['ACESSO']['ID_GRUPO']);
	}

	public static function getCodFilial() {
		return $_SESSION['SIGO']['ACESSO']['CODFILIAL'];
	}

	/**
	 * Verificar se o funcionÃ¡rio logado pertence a um dos grupos enviados
	 * @param string ou array: id do grupo
	 * @return boolean
	 */
	public static function pertenceGrupo($idGrupo) {
		$gruposUsuarioLogado = self::getGrupo();

		if(is_array($idGrupo)) {
			return count(array_intersect($idGrupo, $gruposUsuarioLogado)) > 0;
		}else {
			return in_array($idGrupo, $gruposUsuarioLogado);
		}
	}

}