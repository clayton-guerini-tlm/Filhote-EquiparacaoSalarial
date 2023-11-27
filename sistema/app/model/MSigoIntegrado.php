<?php class MSigoIntegrado extends MBeneficio  {

	public function buscarFilial() {

		$query = "  SELECT
                        tf.fil_id AS 'idFilial',
                        tf.fil_nomefantasia as 'filial',
                        tf.fil_estado AS 'estado'
                    FROM tbl_filial AS tf
                    WHERE tf.ativa = 1
                    ORDER BY tf.fil_estado ASC";

		$con = $this->getConnection('sigo_integrado');
		$statement = $con->prepare($query);

		$statement->execute();

		return $statement->fetchAll(PDO::FETCH_CLASS, 'Filial');

	}


	/**
	 * Busca lista de gerentes
	 */
	public function buscarHierarquiaLideres($listaFiliais){

		$query = "	SELECT
						f.fun_chapa AS 'chapaFuncionario',
						f.fun_nome AS 'nomeFuncionario',
						f.fun_filial AS 'filial',
						f.fun_cod_coligada AS 'codColigada'
						
					FROM sigo_integrado.tbl_rm_funcionario f FORCE INDEX (fun_chapa)
					LEFT JOIN tbl_funcionario_ponto fp
						ON fp.fun_chapa = f.fun_chapa AND fp.codColigada = f.fun_cod_coligada
					LEFT JOIN sigo_integrado.tbl_rm_funcionario f1 ON
						f1.fun_chapa = fp.fpo_supervisor  AND fp.codColigada = f1.fun_cod_coligada
					LEFT JOIN tbl_pessoal_encarregado e ON
						(e.enc_chapa = f.fun_chapa AND e.codColigada = f.fun_cod_coligada)
						OR (e.enc_chapa = fp.fpo_supervisor and e.codColigada = fp.codColigada)
					LEFT JOIN tbl_pessoal_supervisor s ON
						(s.sup_chapa = f.fun_chapa AND s.codColigada = f.fun_cod_coligada)
						OR (s.sup_chapa = fp.fpo_supervisor AND s.codColigada = fp.codColigada)
						OR (s.sup_chapa = e.sup_chapa AND s.codColigada = e.codColigada)
					LEFT JOIN tbl_pessoal_coordenador c ON
						(c.cor_chapa = f.fun_chapa AND c.codColigada = f.fun_cod_coligada)
						OR (c.cor_chapa = fp.fpo_supervisor AND c.codColigada = fp.codColigada)
						OR (c.cor_chapa = s.cor_chapa AND c.codColigada = s.codColigada)
					LEFT JOIN tbl_pessoal_gerente g ON
						(g.ger_chapa = f.fun_chapa AND g.codColigada = f.fun_cod_coligada)
						OR (g.ger_chapa = fp.fpo_supervisor AND g.codColigada = fp.codColigada)
						OR (g.ger_chapa = c.ger_chapa AND g.codColigada = c.codColigada)

					INNER JOIN sigo_integrado.tbl_filial fil ON fil.fil_estado = f.fun_filial
					
					WHERE f.sit_id NOT IN ('D')
					AND f.fun_chapa = g.ger_chapa
					AND fil.fil_id IN ({$listaFiliais})
					
					GROUP BY f.fun_chapa";		

		$con = $this->getConnection('modulo_ponto');
		$statement = $con->prepare($query);
		//$statement->bindParam(':listaFiliais', $listaFiliais, \PDO::PARAM_STR);

		$statement->execute();

		return $statement->fetchAll(PDO::FETCH_CLASS, 'stdClass');
	}

	/**
	 * Busca lista de gerentes
	 */
	public function buscarDadosUsuarioLogado($chapa, $coligada){

		$query = "	SELECT
						f.fun_chapa AS 'chapaFuncionario',
						f.fun_nome AS 'nomeFuncionario',
						f.fun_filial AS 'filial',
						f.fun_cod_coligada AS 'codColigada'
						
					FROM sigo_integrado.tbl_rm_funcionario f FORCE INDEX (fun_chapa)
					WHERE f.sit_id NOT IN ('D')
					AND f.fun_chapa = :chapa
					AND f.fun_cod_coligada = :coligada

					GROUP BY f.fun_chapa";		

		$con = $this->getConnection('modulo_ponto');
		$statement = $con->prepare($query);
		$statement->bindParam(':chapa', $chapa, \PDO::PARAM_STR);
		$statement->bindParam(':coligada', $codColigada, \PDO::PARAM_INT);

		$statement->execute();

		return $statement->fetchAll(PDO::FETCH_CLASS, 'stdClass');
	}

	/**
	 * Retorna centros de custo vinculados ao lider
	 */
	public function buscarCentrosCustoLider($chapa, $codColigada = 2){
		$query = "
					SELECT
						DISTINCT(empregado.fun_codsecao) AS codSecao
					FROM tbl_funcionario_ponto fp
					INNER JOIN sigo_integrado.tbl_rm_funcionario AS empregado
						ON empregado.fun_chapa = fp.fun_chapa
						AND empregado.fun_cod_coligada = :codColigada
					LEFT JOIN tbl_pessoal_encarregado e
						ON(e.enc_chapa = fp.fun_chapa OR e.enc_chapa = fp.fpo_supervisor)
					LEFT JOIN tbl_pessoal_supervisor s
						ON(s.sup_chapa = fp.fun_chapa OR s.sup_chapa = fp.fpo_supervisor OR s.sup_chapa = e.sup_chapa)
					LEFT JOIN tbl_pessoal_coordenador c
						ON(c.cor_chapa = fp.fun_chapa OR c.cor_chapa = fp.fpo_supervisor OR c.cor_chapa = s.cor_chapa)
					LEFT JOIN tbl_pessoal_gerente g
						ON(g.ger_chapa = fp.fun_chapa OR g.ger_chapa = fp.fpo_supervisor OR g.ger_chapa = c.ger_chapa)

					WHERE empregado.sit_id NOT IN ('D', 'I')
					AND g.ger_chapa = :chapa
					GROUP BY empregado.fun_chapa
					ORDER BY empregado.fun_nome
				";

		$con = $this->getConnection('modulo_ponto');
		$statement = $con->prepare($query);

		$statement->bindParam(':chapa', $chapa, \PDO::PARAM_STR);
		$statement->bindParam(':codColigada', $codColigada, \PDO::PARAM_INT);
		
		$statement->execute();
		
		return $statement->fetchAll(PDO::FETCH_CLASS, 'stdClass');
	}



	public function buscarUf() {

		$query = "  SELECT
                        uf.uf_id AS 'id',
                        uf.uf_descricao as 'descricao',
                        uf.uf_sigla AS 'uf'
                    FROM tbl_uf AS uf
                    ORDER BY uf.uf_sigla ASC";

		$con = $this->getConnection('sigo_integrado');
		$statement = $con->prepare($query);

		$statement->execute();

		return $statement->fetchAll(PDO::FETCH_CLASS, 'stdClass');

	}


	/**
	 * Busca lista de cidades por estado
	 */
	public function buscarCidades($ufId, $idGrupo = null){
		
		if ($idGrupo != null) {
			$query = "	SELECT
						c.cid_id AS 'id',
						c.cid_descricao AS 'nome',
						c.uf_id AS 'uf',
						IF(gm.idCidade, 1,0) as marcado
						
					FROM tbl_cidade c
					LEFT JOIN modulo_beneficios.tbl_grupos_de_municipio gm ON gm.idCidade = c.cid_id AND gm.idGrupoMunicipio = :idGrupo
					WHERE c.uf_id = :ufId					
					GROUP BY c.cid_id";

		} else {
			$query = "	SELECT
						c.cid_id AS 'id',
						c.cid_descricao AS 'nome',
						c.uf_id AS 'uf',
						0 AS marcado
						
					FROM tbl_cidade c
					WHERE c.uf_id = :ufId";
		}	
		
		$con = $this->getConnection('sigo_integrado');
		$statement = $con->prepare($query);
		$statement->bindParam(':ufId', $ufId, \PDO::PARAM_STR);
		if ($idGrupo != null) {
			$statement->bindParam(':idGrupo', $idGrupo, \PDO::PARAM_STR);
		}
		$statement->execute();

		return $statement->fetchAll(PDO::FETCH_CLASS, 'stdClass');
	}


	public function listarFuncionarios(Filtro $filtro) {

		$query = "  SELECT
						f.fun_chapa AS 'chapa',
						f.fun_nome AS 'nome',
						f.fun_filial AS 'filial'

					FROM sigo_integrado.tbl_rm_funcionario f FORCE INDEX (fun_chapa)
					LEFT JOIN modulo_beneficios.tbl_permissoes_equiparacao p ON p.chapa = f.fun_chapa
					LEFT JOIN sigo_integrado.tbl_filial fi ON fi.fil_id = p.idFilial

					{$filtro->getStringSql('WHERE ')}
					
					
					GROUP BY f.fun_chapa
				";

		/**
		 * Executar query
		 */
		$statement = $this->getConnection('sigo_integrado')->prepare($query);		
		$statement->execute($filtro->getValores());

		return $statement->fetchAll(PDO::FETCH_CLASS, 'Funcionario');

	}


	/**
	 * Busca lista de filiais usuario
	 */
	public function buscarFiliaisUsuario($chapa = null){
		
		if ($chapa != null) {
			$query = "	SELECT
						f.fil_id AS 'id',
						f.fil_nomefantasia AS 'nomeFilial',
						IF(p.id, 1,0) as marcado
						
					FROM tbl_filial f
					LEFT JOIN modulo_beneficios.tbl_permissoes_equiparacao p ON p.idFilial = f.fil_id AND p.chapa = :chapa
					GROUP BY f.fil_id";

		} else {
			$query = "	SELECT
						f.fil_id AS 'id',
						f.fil_nomefantasia AS 'nomeFilial',
						0 AS marcado
						
					FROM tbl_filial f";
		}	
		
		$con = $this->getConnection('sigo_integrado');
		$statement = $con->prepare($query);
		if ($chapa != null) {
			$statement->bindParam(':chapa', $chapa, \PDO::PARAM_STR);
		}
		$statement->execute();

		return $statement->fetchAll(PDO::FETCH_CLASS, 'stdClass');
	}

	/**
	 * Busca lista de filiais usuario
	 */
	public function buscarFiliaisUsuarioPermissao($chapa){
		
		$query = "	SELECT
						f.fil_id AS 'idFilial',
                        f.fil_nomefantasia as 'filial',
                        f.fil_estado AS 'estado'
												
					FROM modulo_beneficios.tbl_permissoes_equiparacao p
					INNER JOIN sigo_integrado.tbl_filial f ON p.idFilial = f.fil_id
					WHERE p.chapa = :chapa";
		
		$con = $this->getConnection('modulo_beneficios');
		$statement = $con->prepare($query);
		$statement->bindParam(':chapa', $chapa, \PDO::PARAM_STR);
		
		$statement->execute();

		return $statement->fetchAll(PDO::FETCH_CLASS, 'stdClass');
	}


	/**
	* salvar grupo
	*/
	public function salvarPermissaoEquiparacao($Obj) {

		try {

			$con = $this->getConnection('modulo_beneficios');
			$con->beginTransaction();

			/**
			 * Filiais
			 */
			$arrayFiliais = $Obj->filiais;


			//deletar vinculos anteriores
			$query = 'DELETE FROM tbl_permissoes_equiparacao
				WHERE chapa = :chapa
			';

			$statement = $con->prepare($query);

			$statement->bindParam(':chapa', $Obj->chapa, \PDO::PARAM_INT);
			$statement->execute();


			//insere novos registros
			$query = '	INSERT INTO tbl_permissoes_equiparacao (
							idFilial,
							chapa
						)
						VALUES (
							:idFilial,
							:chapa
						)';


			$statement = $con->prepare($query);

			foreach($arrayFiliais as $idFilial) {

				$statement->bindParam(':idFilial', $idFilial, \PDO::PARAM_INT);
				$statement->bindParam(':chapa', $Obj->chapa, \PDO::PARAM_INT);
				$statement->execute();
			}

		} catch (Exception $e) {
			$con->rollBack();
			throw $e;
		}

		return $con->commit();

	}


	/**
	 * Busca lista de filiais usuario
	 */
	public function buscarFilialDiretor($chapa){

		/*$query = "	SELECT
						f.fil_id AS 'idFilial',
                        f.fil_nomefantasia as 'filial',
                        f.fil_estado AS 'estado'
						
					FROM tbl_filial f

					INNER JOIN tbl_rm_funcionario fun ON  f.fil_estado = fun.fun_filial
					WHERE f.ativa = 1
					AND fun.fun_chapa = :chapa

					GROUP BY f.fil_id";
		
		$con = $this->getConnection('sigo_integrado');
		$statement = $con->prepare($query);
		if ($chapa != null) {
			$statement->bindParam(':chapa', $chapa, \PDO::PARAM_STR);
		}
		$statement->execute();

		return $statement->fetchAll(PDO::FETCH_CLASS, 'stdClass');*/

		$query = "	SELECT

						fil.fil_id AS 'idFilial',
						fil.fil_nomefantasia as 'filial',
						fil.fil_estado AS 'estado'
						
						FROM sigo_integrado.tbl_rm_funcionario f FORCE INDEX (fun_chapa)
					LEFT JOIN modulo_ponto.tbl_funcionario_ponto fp
						ON fp.fun_chapa = f.fun_chapa AND fp.codColigada = f.fun_cod_coligada
					LEFT JOIN sigo_integrado.tbl_rm_funcionario f1 ON
						f1.fun_chapa = fp.fpo_supervisor  AND fp.codColigada = f1.fun_cod_coligada
					LEFT JOIN modulo_ponto.tbl_pessoal_encarregado e ON
						(e.enc_chapa = f.fun_chapa AND e.codColigada = f.fun_cod_coligada)
						OR (e.enc_chapa = fp.fpo_supervisor and e.codColigada = fp.codColigada)
					LEFT JOIN modulo_ponto.tbl_pessoal_supervisor s ON
						(s.sup_chapa = f.fun_chapa AND s.codColigada = f.fun_cod_coligada)
						OR (s.sup_chapa = fp.fpo_supervisor AND s.codColigada = fp.codColigada)
						OR (s.sup_chapa = e.sup_chapa AND s.codColigada = e.codColigada)
					LEFT JOIN modulo_ponto.tbl_pessoal_coordenador c ON
						(c.cor_chapa = f.fun_chapa AND c.codColigada = f.fun_cod_coligada)
						OR (c.cor_chapa = fp.fpo_supervisor AND c.codColigada = fp.codColigada)
						OR (c.cor_chapa = s.cor_chapa AND c.codColigada = s.codColigada)
					LEFT JOIN modulo_ponto.tbl_pessoal_gerente g ON
						(g.ger_chapa = f.fun_chapa AND g.codColigada = f.fun_cod_coligada)
						OR (g.ger_chapa = fp.fpo_supervisor AND g.codColigada = fp.codColigada)
						OR (g.ger_chapa = c.ger_chapa AND g.codColigada = c.codColigada)

					INNER JOIN sigo_integrado.tbl_filial fil ON fil.fil_estado = f.fun_filial
					
					WHERE f.sit_id NOT IN ('D')
					AND g.dir_chapa = {$chapa}
					AND fil.ativa = 1
					GROUP BY fil.fil_id
					";		

		$con = $this->getConnection('modulo_ponto');
		$statement = $con->prepare($query);
		$statement->execute();

		return $statement->fetchAll(PDO::FETCH_CLASS, 'stdClass');
	}

}