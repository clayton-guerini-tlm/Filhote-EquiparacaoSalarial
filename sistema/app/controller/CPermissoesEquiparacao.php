<?php
final class CPermissoesEquiparacao extends Controller {

	/**
	*	Faz busca dos dados de equiparacao salarial
	*/
	public static function listarUsuarios() {

		$args = self::$args;

		$filtro = new Filtro();
		/**
		 * Filtrar equiparacoes
		 */
		if(!empty($args['codColigada'])) {
			$filtro->add(new FiltroString(), 'f.fun_cod_coligada', $args['codColigada']);
		}
		if(!empty($args['chapaFuncionario'])) {
			$filtro->add(new FiltroString(), 'f.fun_chapa', $args['chapaFuncionario']);
		}
	
		if(!empty($args['idFilial'])) {
			$filtro->add(new FiltroString(), 'f.fun_filial', $args['idFilial']);
		}

		if(!empty($args['nomeFuncionario'])) {
			$filtro->add(new FiltroLike(), 'f.fun_nome', "%{$args['nomeFuncionario']}%");
		}
		
		$mSigo = new MSigoIntegrado();

		//busca dados de equiparacao salarial
		$rsEquiparacaoes = $mSigo->listarFuncionarios($filtro);

		$dataSet = array();

		foreach ($rsEquiparacaoes as $resp) {
			
			$obj = new Funcionario();

            $obj = $resp;	 
			$dataSet[] = $obj;
		}

		return self::response(true, null, $dataSet);
	}

	/**
	*	Faz busca das filiais
	*/
	public static function buscaFilial() {

		$args = self::$args;

		$mSigo = new MSigoIntegrado();
		

		$rs = $mSigo->buscarFilial();		

		$dataSet = array();

		foreach ($rs as $resp) {
			
			$obj = new stdClass();

            $obj = $resp;	 
			$dataSet[] = $obj;
		}

		return self::response(true, null, $dataSet);

	}


	/**
	*	Faz buscar cidades
	*/
	public static function buscarFiliaisUsuario() {

		$args = self::$args;

		$mSigo = new MSigoIntegrado();

		if (isset($args['chapa']) && $args['chapa'] != null) {

			$rs = $mSigo->buscarFiliaisUsuario($args['chapa']);
		} else {
			$rs = $mSigo->buscarFiliaisUsuario();
		}

		$dataSet = array();

		foreach ($rs as $resp) {
			
			$obj = new stdClass();

            $obj = $resp;	 
			$dataSet[] = $obj;
		}

		return self::response(true, null, $dataSet);

	}


	/**
	*	Faz buscar salvar permissoes
	*/
	public static function salvarPermissao() {

		$args = self::$args;
		
		/**
		 * Filtrar permissoes
		 */
		if(empty($args['chapa'])) {
			return self::response(false, 'Chapa é obrigatório', 'v');
		}
		
		if(empty($args['filiais'])) {
			return self::response(false, 'Selecionar no minimo uma filial é obrigatório', 'v');
		}
				
		try {			

			$obj = new stdClass();

			$mSigo = new MSigoIntegrado();

			$obj->chapa	= $args['chapa'];
			$obj->filiais = $args['filiais'];
			
			$rs = $mSigo->salvarPermissaoEquiparacao($obj);
			
			return self::response(true,"Permissões salvas com sucesso", $rs);

		} catch (Exception $e) {
			return self::response(false,"Erro ao tentar salvar Permissões",$e->getMessage());
		}
	}

}
