<?php
class MEquiparacaoSalarial extends MBeneficio  {

	
	public function listarFuncionariosEquiparacao(Filtro $filtro, $filiaisPermissao) {

		$query = "  SELECT
						e.idEquiparacao,
						e.codColigada, 
						e.chapa, 
						e.codSecao, 
						e.funcao, 
						e.dtAdmissao, 
						e.dtInicioFuncao,
						f.fun_nome AS nomeFuncionario,
						l.fun_nome AS nomeLider,
						fi.fil_nomefantasia AS filial,
						e.validado

					FROM tbl_func_equiparacao_salarial e
					INNER JOIN sigo_integrado.tbl_rm_funcionario f ON (e.chapa = f.fun_chapa)
					INNER JOIN sigo_integrado.tbl_rm_funcionario l ON (e.chapaLider = l.fun_chapa)
					INNER JOIN sigo_integrado.tbl_filial fi ON (e.idFilial = fi.fil_id)

					{$filtro->getStringSql('WHERE ')}

					AND e.idFilial IN ({$filiaisPermissao})

					GROUP BY e.chapa
					ORDER BY e.idEquiparacao ASC
				";

		/**
		 * Executar query
		 */
		$statement = $this->getConnection('modulo_beneficios')->prepare($query);		
		$statement->execute($filtro->getValores());

		return $statement->fetchAll(PDO::FETCH_CLASS, 'EquiparacaoSalarial');

	}


	public function buscarEquiparacao($idEquiparacao) {

		$query = "  SELECT
						e.idEquiparacao,
						e.codColigada, 
						e.chapa, 
						e.codSecao, 
						e.funcao, 
						e.dtAdmissao, 
						e.dtInicioFuncao,
						f.fun_nome AS nomeFuncionario,
						l.fun_nome AS nomeLider,
						fi.fil_nomefantasia AS filial,
						e.justificativa,
						e.validado

					FROM tbl_func_equiparacao_salarial e
					INNER JOIN sigo_integrado.tbl_rm_funcionario f ON (e.chapa = f.fun_chapa)
					INNER JOIN sigo_integrado.tbl_rm_funcionario l ON (e.chapaLider = l.fun_chapa)
					INNER JOIN sigo_integrado.tbl_filial fi ON (e.idFilial = fi.fil_id)

					WHERE e.idEquiparacao = {$idEquiparacao}

					GROUP BY e.chapa
				";

		/**
		 * Executar query
		 */
		$statement = $this->getConnection('modulo_beneficios')->prepare($query);		
		$statement->execute();

		return $statement->fetchAll(PDO::FETCH_CLASS, 'stdClass');

	}

	public function buscarMotivos($chapa) {

		$query = "  SELECT
						chapa,
						nomeFuncionario,
						justificativa,
						validado,
						idValidado,
						nomeValidado,
						DATE_FORMAT(dataCadastro, '%d/%m/%Y') as dataCadastro
					FROM tbl_motivos_nao_equiparacao 
					INNER JOIN tbl_motivos  ON (idValidado = validado)
					WHERE chapa = {$chapa}
				";

		/**
		 * Executar query
		 */
		$statement = $this->getConnection('modulo_beneficios')->prepare($query);		
		$statement->execute();

		return $statement->fetchAll(PDO::FETCH_CLASS, 'stdClass');

	}
	public function salvarEquiparacao(EquiparacaoSalarial $equiparacao) {

		$query = 'UPDATE tbl_func_equiparacao_salarial 
					SET	justificativa = :justificativa,
						validado = :validado
					WHERE idEquiparacao = :idEquiparacao;

				INSERT INTO tbl_motivos_nao_equiparacao(
								idEquiparacao,
								chapa,
								nomeFuncionario,
								justificativa,
								validado,
								dataCadastro
							)
						VALUES (
								:idEquiparacao,
								:chapa,
								:nomeFuncionario,
								:justificativa,
								:validado,
								NOW()
					
						)';

		$statement = $this->getConnection('modulo_beneficios')->prepare($query);

		$statement->bindParam(':idEquiparacao', $equiparacao->idEquiparacao, \PDO::PARAM_STR);
		$statement->bindParam(':chapa', $equiparacao->chapa, \PDO::PARAM_STR);
		$statement->bindParam(':nomeFuncionario', $equiparacao->nomeFuncionario, \PDO::PARAM_STR);
		$statement->bindParam(':justificativa', $equiparacao->justificativa, \PDO::PARAM_STR);
		$statement->bindParam(':validado', $equiparacao->validado, \PDO::PARAM_INT);
	

		return $statement->execute();
	}

}