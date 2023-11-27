<?php
/*
  DATA...............: 02-09-2009
  SCRIPT.............: CLASS_BANCO.PHP
  DESCRICAO..........: SCRIPT DE BANCO DE DADOS
  TABELA.............: TODAS
  ----------------------------------------------------------------------------------------------------------
  N°   DATA      NOME     ALTERAÇÃO
  ----------------------------------------------------------------------------------------------------------
  001  02-09-09  CLEBER   FUNÇÃO CONECT
  002  02-09-09  CLEBER   FUNÇÃO QUE EXECUTA UMA STRING SQL
  003  02-09-09  CLEBER   FUNÇÃO QUE SETA O PONTEIRO DO RESULTADO DE UMA PESQUISA
  004  02-09-09  CLEBER   FUNÇÃO QUE MOVE O PONTEIRO PARA O PRIMEIRO REGISTRO
  005  02-09-09  CLEBER   FUNÇÃO QUE MOVE O PONTEIRO PARA O PROXIMO REGISTRO
  006  02-09-09  CLEBER   FUNÇÃO QUE MOVE O PONTEIRO PARA O REGISTRO ANTERIOR
  007  02-09-09  CLEBER   FUNÇÃO QUE MOVE O PONTEIRO PARA O ULTIMO REGISTRO
  008  02-09-09  CLEBER   FUNÇÃO QUE SETA O INDICE DO PONTEIRO
  009  02-09-09  CLEBER   FUNÇÃO QUE RETORNA O ULTIMO ID CADASTRADO
  010  15-09-09  CLEBER   FUNÇÃO QUE BUSCA UM VALOR
*/

class cls_mysql
{
	var $socket;
	var $erro;
	var $resultado;
	var $r;
	var $total;
	var $indice;
	var $status;
	var $id;

	// ------------------------------------------------------------------------------------------------------
	// 001
	// ------------------------------------------------------------------------------------------------------

	function __construct($link)
	{
		$this->socket = $link;
	}

	function disconect()
	{
		@mysqli_close($this->socket);
		unset($this->socket,$this->erro,$this->resultado,$this->r,$this->total,$this->indice,$this->status);
	}

	// ------------------------------------------------------------------------------------------------------
	// 002
	// ------------------------------------------------------------------------------------------------------

  	function exec($str_sql)
  	{
		unset($this->r, $this->total);

		$this->primeiro();

		$this->resultado = mysqli_query($this->socket, $str_sql);

		if (!$this->resultado)
		{
			$this->erro 	= mysqli_error($this->socket);
			$this->status 	= 'erro';
			return false;
		}
		else
		{
			if (strtoupper(substr(trim($str_sql),0,6))=="SELECT")
			{
			 	$this->r     = mysqli_fetch_array($this->resultado);
			   	$this->total = mysqli_num_rows($this->resultado);
			}

			$this->ultimo_id();
			$this->status = '';
			return true;
		}
	}

	// ------------------------------------------------------------------------------------------------------
	// 003
	// ------------------------------------------------------------------------------------------------------

	function posicao ($id)
	{
		if (!mysqli_data_seek($this->resultado, $id))
	 	{
			$this->erro 	= pg_last_error();
		  	$this->status  = false;
		  	return false;
	 	}
	 	else
	 	{
		 	$this->r       = mysqli_fetch_array($this->resultado);
		  	$this->erro 	= "";
		  	$this->indice  = $id;
		  	return true;
	  	}
	}

  	// ------------------------------------------------------------------------------------------------------
  	// 004
	// ------------------------------------------------------------------------------------------------------

	function primeiro ()
	{
		if ($this->indice != 0)
		{
			$this->posicao(0);
			$this->indice = 0;
		}
	}

  	// ------------------------------------------------------------------------------------------------------
  	// 005
	// ------------------------------------------------------------------------------------------------------

	function proximo ()
	{
		if ($this->indice+1<$this->total)
		{
	 		$this->posicao($this->indice+1);
		}
	}

	// ------------------------------------------------------------------------------------------------------
	// 006
	// ------------------------------------------------------------------------------------------------------

  	function anterior ()
  	{
		if ($this->indice-1>0)
		{
			$this->posicao($this->indice-1);
	   }
	}

	// ------------------------------------------------------------------------------------------------------
	// 007
	// ------------------------------------------------------------------------------------------------------

	function utlimo ()
  	{
		if ($this->indice!=$this->total)
		{
			$this->posicao($this->total);
			$this->indice = $this->total;
		}
	}

	// ------------------------------------------------------------------------------------------------------
	// 008
	// ------------------------------------------------------------------------------------------------------

	function set_indice($dado)
	{
		$this->indice = $dado;
	}

	// ------------------------------------------------------------------------------------------------------
	// 009
	// ------------------------------------------------------------------------------------------------------

	function ultimo_id()
	{
		$this->id = mysqli_insert_id($this->socket);
	}

	// ------------------------------------------------------------------------------------------------------
	// 010
	// ------------------------------------------------------------------------------------------------------

	function busca($campo, $tabela, $condicao)
	{
		$sql = "SELECT $campo FROM $tabela WHERE $condicao LIMIT 1";
		//echo $sql;exit;
		$this->resultado = mysqli_query($this->socket, $sql);
	 	$this->r = mysqli_fetch_array($this->resultado);
	 	return $this->r["$campo"];
	}
}
?>