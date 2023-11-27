<?php
final class CGrupoDeMunicipio extends Controller {

	/**
	*	Faz busca dos dados grupos de municipio
	*/
	public static function buscarGrupos() {

		$args = self::$args;

		$filtro = new Filtro();

		/**
		 * Filtrar grupos
		 */
		if(!empty($args['grupo'])) {
			$filtro->add(new FiltroString(), 'g.nome', $args['grupo']);
		}
		if(!empty($args['uf'])) {
			$filtro->add(new FiltroString(), 'g.uf_id', $args['uf']);
		}
		
		$mGrupoMunicipio = new MGrupoDeMunicipio();

		//busca grupos de municipio
		$rsGrupoMunicipio = $mGrupoMunicipio->listarGrupoDeMunicipio($filtro);

		$dataSet = array();

		foreach ($rsGrupoMunicipio as $resp) {
			
			$obj = new GrupoMunicipio();

			unset($resp->id);
			unset($resp->uf_id);
            $obj = $resp;
			$dataSet[] = $obj;
		}

		return self::response(true, null, $dataSet);
	}


	
	/**
	*	Faz buscar todos os estados
	*/
	public static function buscarUfs() {

		$args = self::$args;

		$mSigo = new MSigoIntegrado();
		$dataSet = $mSigo->buscarUf();
		return self::response(true, null, $dataSet);
	}

	
	/**
	*	Faz buscar cidades
	*/
	public static function buscarCidades() {

		$args = self::$args;

		$mSigo = new MSigoIntegrado();

		if (isset($args['idGrupo']) && $args['idGrupo'] != null) {

			$rs = $mSigo->buscarCidades($args['uf'], $args['idGrupo']);
		} else {
			$rs = $mSigo->buscarCidades($args['uf']);
		}

		foreach ($rs as $resp) {
			
			$obj = new stdClass();

            $obj = $resp;	 
			$dataSet[] = $obj;
		}

		return self::response(true, null, $dataSet);

	}


	/**
	*	Faz buscar salvar grupo
	*/
	public static function salvaGrupo() {

		$args = self::$args;

		
		/**
		 * Filtrar grupos
		 */
		if(empty($args['nome'])) {
			return self::response(false, 'Nome do grupo é obrigatório', 'v');
		}
		if(empty($args['uf_id'])) {
			return self::response(false, 'UF é obrigatório', 'v');
		}
		if(empty($args['cidades'])) {
			return self::response(false, 'Selecionar no minimo duas cidades é obrigatório', 'v');
		}
		
		try {			

			$obj = new GrupoMunicipio();

			$mGrupoMunicipio = new MGrupoDeMunicipio();

			$obj->nome	= $args['nome'];
			$obj->uf_id = $args['uf_id'];
			$obj->cidades = $args['cidades'];

			if (isset($args['id']) && $args['id'] != null) {
				$obj->id	= $args['id'];
			} 
			
			$rs = $mGrupoMunicipio->salvarGrupoMunicipio($obj);
			

			return self::response(true,"Grupo salvo com sucesso", $rs);

		} catch (Exception $e) {
			return self::response(false,"Erro ao tentar salvar grupo",$e->getMessage());
		}
	}

	
	/**
	*	Faz buscar excluir grupo
	*/
	public static function removerGrupo() {

		$args = self::$args;
		
		try {			

			$obj = new GrupoMunicipio();

			$mGrupoMunicipio = new MGrupoDeMunicipio();
			
			$rs = $mGrupoMunicipio->removerGrupoMunicipio($args['id']);
			
			return self::response(true,"Grupo removido com sucesso", $rs);

		} catch (Exception $e) {
			return self::response(false,"Erro ao tentar remover grupo",$e->getMessage());
		}
	}
}
