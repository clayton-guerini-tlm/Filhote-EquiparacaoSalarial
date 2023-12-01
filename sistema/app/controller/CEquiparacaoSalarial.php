<?php
final class CEquiparacaoSalarial extends Controller {

	/**
	*	Faz busca dos dados de equiparacao salarial
	*/
	public static function listarFuncionariosEquiparacao() {

		$args = self::$args;

		$filtro = new Filtro();
		/**
		 * Filtrar equiparacoes
		 */
		$filtro->add(new FiltroString(), 'e.validado', 9, '<>');

		if(!empty($args['codColigada'])) {
			$filtro->add(new FiltroString(), 'e.codColigada', $args['codColigada']);
		}
		if(!empty($args['chapa'])) {
			$filtro->add(new FiltroString(), 'e.chapa', $args['chapa']);
		}
		if(!empty($args['chapaLider'])) {
			$filtro->add(new FiltroString(), 'e.chapaLider', $args['chapaLider']);
		}
		if(!empty($args['idFilial'])) {
			$filtro->add(new FiltroString(), 'e.idFilial', $args['idFilial']);
		}
		if(!empty($args['secao'])) {
			$filtro->add(new FiltroString(), 'e.codSecao', $args['codSecao']);
		}
		if(!empty($args['funcao'])) {
			$filtro->add(new FiltroLike(), 'e.funcao', "%{$args['funcao']}%");
		}
		if(!empty($args['nomeFuncionario'])) {
			$filtro->add(new FiltroLike(), 'e.nomeFuncionario', "%{$args['nomeFuncionario']}%");
		}

		$filiaisPermissao = self::listaFiliaisUsuarioLogado();
		//$filtro->add(new FiltroString(), 'e.nomeFuncionario', array($filiaisPermissao));

		$mEquiparacaoSealarial = new MEquiparacaoSalarial();
		$mBeneficioRm = new MBeneficioRM();

		//busca dados dos funcionarios no RM
		$funcionariosColigadaRm = $mBeneficioRm->buscarDadosDetalhadosFuncionarioColigada($args['codColigada'],$args['chapaDiretor']);
		
		//busca dados de equiparacao salarial
		$rsEquiparacaoes = $mEquiparacaoSealarial->listarFuncionariosEquiparacao($filtro, $filiaisPermissao);

		$dataSet = array();

		foreach ($rsEquiparacaoes as $resp) {
			
			$obj = new EquiparacaoSalarial();
            $obj = $resp;

			if (isset($funcionariosColigadaRm[$obj->chapa])) {
				
				$obj->salario = $funcionariosColigadaRm[$obj->chapa]['SALARIO'];
				//$obj->chapaDiretor = $funcionariosColigadaRm[$obj->chapa]['CHAPADIRETOR'];
		
				$data1 = new DateTime($obj->dtAdmissao);
				$data2 = new DateTime();
				$intervaloAdmissao = $data1->diff( $data2 );
				$obj->dtAdmissao = date('d/m/Y', strtotime($obj->dtAdmissao)) . " {$intervaloAdmissao->y} anos e {$intervaloAdmissao->m} meses"; 

				$data1 = new DateTime($obj->dtInicioFuncao);
				$intervaloFuncao = $data1->diff( $data2 );
				$obj->dtInicioFuncao = date('d/m/Y', strtotime($obj->dtInicioFuncao)) . " {$intervaloFuncao->y} anos e {$intervaloFuncao->m} meses"; 

				switch ($obj->validado) {
					case 1:
						$obj->validado = "APV DIRETORIA";
						break;
					case 2:
						$obj->validado = "EM ANÁLISE";
						break;
					case 3:
						$obj->validado = "DESLIGAMENTO";
						break;
					case 4:
						$obj->validado = "ESCALONAMENTO";
						break;
					case 5:
						$obj->validado = "PROMOÇÃO";
						break;
					case 8:
						$obj->validado = "REFERÊNCIA";
						break;
					case 6:
						$obj->validado = "RES SINDICAL";
						break;
					case 7:
						$obj->validado = "RES MÉDICA";
						break; 
					case 0:
						$obj->validado = "";
						break;	         
				}

				unset($obj->codColigada);	
				unset($obj->idEquiparacao);
				$dataSet[] = $obj;

			}
		}

		return self::response(true, null, $dataSet);
	}


	
	/**
	*	Faz busca dos dados de equiparacao salarial
	*/
	public static function buscaCentrosDeCustosPorChapa() {

		$args = self::$args;

		//$mSigo = new MSigoIntegrado();
		//$dataSet = $mSigo->buscarCentrosCustoLider($args['chapaLider'], $args['codColigada']);

		$mBeneficioRm = new MBeneficioRM();
		$dataSet = $mBeneficioRm->buscarSecaoFuncaoVinculada($args['chapaLider'], $args['codColigada']);

		return self::response(true, null, $dataSet);
	}

	/**
	*	Faz busca das filiais
	*/
	public static function buscaFilial() {

		$args = self::$args;

		$mSigo = new MSigoIntegrado();
		$usuarioDDG = self::buscarUsuarioDDG();

		if (isset($args['chapaDiretor']) && $args['chapaDiretor'] != null) {

			$rs = $mSigo->buscarFilialDiretor($args['chapaDiretor']);

		} else {

			if ($usuarioDDG) {

				$rmBen = new MBeneficioRM();
				$rs = $rmBen->buscarFiliaisAtivas();


				// $rs = $mSigo->buscarFilial();
	
			} else {
	
				$chapa = self::retornaChapaUsuarioLogado();
				$rs = $mSigo->buscarFiliaisUsuarioPermissao($chapa);
			}	
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
	*	Faz busca dos gerentes
	*/
	public static function buscaLideres() {

		$args = self::$args;

		$listaFiliaisPermissao = self::listaFiliaisUsuarioLogado();
		$usuarioDDG = self::buscarUsuarioDDG();

		$mSigo = new MSigoIntegrado();
		$mRm = new MBeneficioRM();

		$dataSet = array();

		if ($usuarioDDG) {

			if (isset($args['idFilial'])) {
				$filial = $args['idFilial'];
			} else {
				$filial = null;
			}
			if (isset($args['chapaDiretor'])) {
				$diretor = $args['chapaDiretor'];
			} else {
				$diretor = null;
			}

			$rs = $mRm->buscarHierarquiaLideres($listaFiliaisPermissao, $filial, $diretor);	

		} else {

			$chapa = self::retornaChapaUsuarioLogado();
			$coligada = self::codColigadaEmpresa();
			$rs = $mSigo->buscarDadosUsuarioLogado($chapa, $coligada);
		}
		
		foreach ($rs as $resp) {
				
			$obj = new stdClass();
			$obj = $resp;	 
			$dataSet[] = $obj;
		}

		return self::response(true, null, $dataSet);
	}

	/**
	*	Faz busca dos gerentes
	*/
	public static function buscaDiretorias() {

		$args = self::$args;

		$listaFiliaisPermissao = self::listaFiliaisUsuarioLogado();
		$usuarioDDG = self::buscarUsuarioDDG();

		$mSigo = new MSigoIntegrado();
		$mRm = new MBeneficioRM();

		$dataSet = array();

		if (isset($args['idFilial'])) {

			$filial = $args['idFilial'];
		} else {
			$filial = null;
		}

		$rs = $mRm->buscarDiretores($listaFiliaisPermissao, $filial);		

		if ($usuarioDDG) {

			if (isset($args['idFilial'])) {

				$filial = $args['idFilial'];
			} else {
				$filial = null;
			}

			$rs = $mRm->buscarDiretores($listaFiliaisPermissao, $filial, $args['codColigada']);	

		} else {

			$chapa = self::retornaChapaUsuarioLogado();
			$coligada = self::codColigadaEmpresa();
			$rs = $mSigo->buscarDadosUsuarioLogado($chapa, $coligada);
		}
		
		foreach ($rs as $resp) {
				
			$obj = new stdClass();
			$obj = $resp;	 
			$dataSet[] = $obj;
		}

		return self::response(true, null, $dataSet);
	}

	function buscarUsuarioDDG() {

        $grupos = explode('|', $_SESSION['SIGO']['ACESSO']['ID_GRUPO']);

		if (in_array(1444, $grupos) || in_array(2, $grupos) || in_array(3, $grupos) || in_array(875, $grupos)) {
            return true;
        } else {
            return false;
        }
    }

    public static function retornaChapaUsuarioLogado() {
        return $_SESSION['SIGO']['ACESSO']['CHAPA'];
    }

    public static function retornaCodigoFilialUsuarioLogado() {
        return $_SESSION['SIGO']['ACESSO']['CODFILIAL'];
    }

    public static function codColigadaEmpresa() {
        return $_SESSION['SIGO']['ACESSO']['LOGIN_EMPRESA'];
    }

    //empresa que o funcionário pertence
    public static function codColigadaFuncionarioLogado(){
        return $_SESSION['SIGO']['ACESSO']['CODCOLIGADA'];
    }


	//retorna lista de filiais ao qual o usuario logado possui permissão
	public static function listaFiliaisUsuarioLogado() {

		$usuarioDDG = self::buscarUsuarioDDG();
		$mSigo = new MSigoIntegrado();

		if ($usuarioDDG) {

			$rs = $mSigo->buscarFilial();

		} else {

			$chapa = self::retornaChapaUsuarioLogado();
			$rs = $mSigo->buscarFiliaisUsuarioPermissao($chapa);

		}

		$idsFiliais = array();
		foreach($rs as $resp) {

			$idsFiliais[] = $resp->idFilial;
		}

		$listFiliais = implode(",", $idsFiliais);

		return $listFiliais;
        
    }
		/**
	*	Faz busca dos MOTIVOS
	*/
	public static function buscarMotivos() {

		$args = self::$args;

		$mEquiparacaoSealarial = new MEquiparacaoSalarial();
		$dataSet = $mEquiparacaoSealarial->buscarMotivos($args['chapa']);
		return self::response(true, null, $dataSet);
	}

	/**
	*	Salvar justificativa
	*/
	public static function salvarJustificativa() {

		$args = self::$args;
//print_r($args);
		$mEquiparacaoSealarial = new MEquiparacaoSalarial();

		$obj = new EquiparacaoSalarial();

		$obj->chapa	= $args['chapa'];
		$obj->nomeFuncionario	= $args['nomeFuncionario'];
		$obj->justificativa	= $args['justificativa'];
		$obj->validado = $args['validado'];
		$obj->idEquiparacao = $args['idEquiparacao'];
//print_r($obj);
		$dataSet = $mEquiparacaoSealarial->salvarEquiparacao($obj);
		return self::response(true, null, $dataSet);
	}

	/**
	*	Faz busca dos dados de equiparacao salarial
	*/
	public static function buscarEquiparacao() {

		$args = self::$args;

		$mEquiparacaoSealarial = new MEquiparacaoSalarial();
		$dataSet = $mEquiparacaoSealarial->buscarEquiparacao($args['id']);
		return self::response(true, null, $dataSet);
	}


}
