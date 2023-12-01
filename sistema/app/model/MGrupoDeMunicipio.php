<?php
class MGrupoDeMunicipio extends MBeneficio  {

	
	public function listarGrupoDeMunicipio(Filtro $filtro) {

		$query = "  SELECT
					g.id, 
					g.nome, 
					g.uf_id,
					e.sigla_estado AS 'uf',
					GROUP_CONCAT(c.cid_descricao SEPARATOR ' , ') as 'cidades'		

					FROM tbl_grupo_municipio g
					INNER JOIN sigo_integrado.tbl_estados e ON e.id_estado = g.uf_id
					INNER JOIN tbl_grupos_de_municipio gm ON gm.idGrupoMunicipio = g.id
					INNER JOIN sigo_integrado.tbl_cidade c ON gm.idCidade = c.cid_id

					{$filtro->getStringSql('WHERE ')}

					GROUP BY g.id
				";

		/**
		 * Executar query
		 */
		$statement = $this->getConnection('modulo_beneficios')->prepare($query);		
		$statement->execute($filtro->getValores());

		return $statement->fetchAll(PDO::FETCH_CLASS, 'GrupoMunicipio');

	}


	/**
	* salvar grupo
	*/
	public function salvarGrupoMunicipio($Obj) {

		try {

			$con = $this->getConnection('modulo_beneficios');
			$con->beginTransaction();

			//verifica se tem id, caso tenha é um update
			if (isset($Obj->id) && $Obj->id != null) {

				$query = 'UPDATE tbl_grupo_municipio 
					SET	nome = :nome,
						uf_id = :uf_id
					WHERE id = :id';

				$statement = $con->prepare($query);

				$statement->bindParam(':nome', $Obj->nome, \PDO::PARAM_STR);
				$statement->bindParam(':uf_id', $Obj->uf_id, \PDO::PARAM_INT);
				$statement->bindParam(':id', $Obj->id, \PDO::PARAM_INT);

				$statement->execute();
				$idGrupoMunicipio = $Obj->id;

				//deletar vinculos anteriores
				$query = 'DELETE FROM tbl_grupos_de_municipio
							WHERE idGrupoMunicipio = :id
				';
				$statement = $con->prepare($query);

				$statement->bindParam(':id', $Obj->id, \PDO::PARAM_INT);
				$statement->execute();

			} else {

				$query = 'INSERT IGNORE INTO tbl_grupo_municipio 
					(
						nome,
						uf_id
					)
					VALUES (
						:nome,
						:uf_id
					)';

				$statement = $con->prepare($query);

				$statement->bindParam(':nome', $Obj->nome, \PDO::PARAM_STR);
				$statement->bindParam(':uf_id', $Obj->uf_id, \PDO::PARAM_INT);
				
				$statement->execute();
				$idGrupoMunicipio = $con->lastInsertId();
			}

			/**
			 * Municipio
			 */
			$arrayGrupoMunicipios = $Obj->cidades;

			$query = '	INSERT INTO tbl_grupos_de_municipio (
							idGrupoMunicipio,
							idCidade
						)
						VALUES (
							:idGrupoMunicipio,
							:idCidade
						)';


			$statement = $con->prepare($query);

			foreach($arrayGrupoMunicipios as $idCidade) {

				$statement->bindParam(':idGrupoMunicipio', $idGrupoMunicipio, \PDO::PARAM_INT);
				$statement->bindParam(':idCidade', $idCidade, \PDO::PARAM_INT);
				$statement->execute();
			}

		} catch (Exception $e) {
			$con->rollBack();
			throw $e;
		}

		return $con->commit();

	}


	/**
	* remover Grupo de municipio
	*/
	public function removerGrupoMunicipio($idGrupo) {

		try {

			$con = $this->getConnection('modulo_beneficios');
			$con->beginTransaction();

			$query = 'DELETE FROM tbl_grupo_municipio
							WHERE id = :id';
			$statement = $con->prepare($query);
				
			$statement->bindParam(':id', $idGrupo, \PDO::PARAM_INT);
			$statement->execute();

		} catch (Exception $e) {
			$con->rollBack();
			throw $e;
		}

		return $con->commit();

	}

}