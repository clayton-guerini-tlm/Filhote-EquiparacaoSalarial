<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}
 
$Time = new TimeCounter();
$SLOG = @new LogSigo(); 

$contador_micro_time=0;
$microtime_ini=microtime(true);
if ($_GET['debug']==1){
	$dbug_sn=true;
	$tmp_debug['ini']=microtime(true);
};

function micro_time($op) {
    global $temp_microtime, $microtime_ini, $Time, $dbug_sn;
    if ($op=='ini'){
		$temp_microtime['ini']='In&iacute;cio '.utime( ) ; 
    } elseif (($op=='fim') and $dbug_sn){
		debug_ex($op);
		$Time->Exibir_Totais();  // $temp_microtime['ini'].' -  Fim '.utime().' -  Tempo '.utime(microtime(true)-$microtime_ini );
    };
}

function utime($format='H:i:s.u', $utimestamp = null) 
{ 
    global $contador_micro_time;
    $contador_micro_time++;
    if (is_null($utimestamp)) 
        $utimestamp = microtime(true); 
    $timestamp = floor($utimestamp); 
    $milliseconds = round(($utimestamp - $timestamp) * 1000000); 
    return  date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp).' microtime==> '.$contador_micro_time  ; 
} 
 
function debug_ex($txt=''){
    global $tmp_debug, $dbug_sn;
    if ($dbug_sn){
		$tmp_debug[$txt]=microtime(true)-$tmp_debug['ini'];
		if ($txt=='fim'){ ?>
			<div align="center" style="border:medium" >
				<?   echo '<table border=1 >'; foreach ($tmp_debug as $k => $v ){$i++;echo ' <tr  class="tr_cor_'.(($i%2==0)?"cinza":"branco").'" ><td align="left"><pre>'.$k.'</pre></td><td>'.utime($v).'</td></tr>
				';}; echo '
				<tr><td  ><pre>'; print_r($_SESSION['SLOGS']); echo '</pre></td><td  ><pre>';  print_r($_SESSION['SIGO']['ACESSO']); echo '</pre></td></tr></table>';
				     ?>
            </div>
         <? };
    };
};

function roda_mysqli_query($sql){
    global $conecta, $tmp_debug, $dbug_sn, $Time;
    $Time->Ativar('MySQL');
    $mysqli_ini=microtime(true);
    $res=mysqli_query($conecta, $sql);
    $tmp_debug[$sql]=microtime(true)-$mysqli_ini;
    $Time->Ativar('PHP');
    if (!($res)) {
		if ($dbug_sn) {echo "<pre><h3><hr>".mysqli_error($conecta)."<hr>$sql<hr></h3>";};
		echo '<p class="atencao" align="center">Erro ao consultar o banco de dados. <br /> Contate o administrador.</p>'; 
		return false;
    } else {
		return $res;
    };
};

class TimeCounter {
	protected $TempoInicial;
	protected $TempoInicialParcial;
	protected $RelogioAtual; //Pode ser PHP, MySQL, MySQLLog
	protected $TempoCorrido = array(
		"PHP" => 0,
		"MySQL" => 0,
		"MySQLLog" => 0,
	);
	protected $Contagem = array(
		"PHP" => 0,
		"MySQL" => 0,
		"MySQLLog" => 0,
	);
	public function __construct(){
		$this->TempoInicial = microtime(true);
		$this->TempoInicialParcial = $this->TempoInicial;
		$this->RelogioAtual = "PHP"; //Sempre PHP
	}
    protected function ContabilizarTempoCorrido(){
		$TempoParcial = microtime(true) - $this->TempoInicialParcial;
		$this->Contagem[$this->RelogioAtual]++;
		$this->TempoCorrido[$this->RelogioAtual] += $TempoParcial;
		$this->TempoInicialParcial = microtime(true);
    }
    public function Ativar($Relogio) {//Inicia Contagem PHP
		if ($this->RelogioAtual != $Relogio){
			if (!array_key_exists($Relogio,$this->TempoCorrido)) trigger_error("Relógio Inexistente...", E_USER_ERROR);
			$this->ContabilizarTempoCorrido();
			$this->RelogioAtual = $Relogio;
		}
    }
    protected function FormatarTempo($tempo){
		return number_format($tempo, 3, ',', '.');
    }
    public function Exibir_Totais(){
		$this->ContabilizarTempoCorrido();
		
		$PHP = $this->FormatarTempo($this->TempoCorrido["PHP"]);
		$MySQL = $this->FormatarTempo($this->TempoCorrido["MySQL"]);
		$MySQLLog = $this->FormatarTempo($this->TempoCorrido["MySQLLog"]);
		$mysqli_Count = $this->Contagem["MySQL"];
		$Total = $this->FormatarTempo(array_sum($this->TempoCorrido));
		printf('<p class="TempoExecucao">Tempo de execu&ccedil;&atilde;o >>> PHP: %ss - SQL: %ss em %s Comandos - Log: %ss - Total: %ss</p>',$PHP,$MySQL,$mysqli_Count,$MySQLLog,$Total);
    }
}


class LogSigo { /* 
Autor Jean
Esta classe gera logs para as paginas do sigo, basta para isso cadastrar o sistema na tabela sigo_integrado.tbl_log_sigo_controle o resultado sera inserido nas tabelas tbl_log_sigo_controle_*        */
	protected $EnderecoPHP  ;
	protected $DebugAtivo;
 	public function __construct(){
 		if ($_SESSION['SIGO']['ACESSO']['ID_USUARIO']<>'') {
			$this->EnderecoPHP=$_SERVER['PHP_SELF'];
			$this->BaseDeDados='sigo_integrado.';
		 	$this->DebugAtivo=$_GET['debug'];
			if ($this->DebugAtivo){
				echo "<div style='text-align:left'><pre> -Endereco Log- $this->EnderecoPHP<br>";
				//print_r($_SESSION['SLOGS']);
			};
			$this->ProcuraLog();
	
			foreach($_SESSION['SLOGS'] as $k => $v){
				$cpos= stripos($this->EnderecoPHP, $k ); 
				if ($cpos!==false){$this->EnderecoPHP=$k;  };
			};
			$this->ProcuraLog();
			if ($_SESSION['SLOGS'][$this->EnderecoPHP]['ATIVO']===true) {
				$this->GravaNoBanco();
			} ;
			if ($this->DebugAtivo){  echo '</pre></div>';};
 		}
	}
	protected function Banco(){
		global $AREA_SIGO; 
		if ($this->DebugAtivo){echo "<div style='text-align:left'> -Servidor- $AREA_SIGO<br>"; };
		$conecta = RetornaConexaoMysql('local', 'sigo_integrado');
    }
    protected function ProcuraLog(){
		global $AREA_SIGO;
		if ($_SESSION['SIGO']['ACESSO']['ID_USUARIO']<>'') {
			if ($_SESSION['SLOGS'][$this->EnderecoPHP]['ATIVO']==''){
				if ($this->DebugAtivo){echo 'ProcuraLog '.$AREA_SIGO.'<br>';};
				$this->Banco();
				$LogSigo_sql="/* Rotina de Log do Sigo */ SELECT log_id, log_endereco, log_mainapp, log_app, log_nivel FROM {$this->BaseDeDados}tbl_log_sigo_controle  WHERE '".$this->EnderecoPHP."' regexp `log_endereco` ";
				$_SESSION['SLOGS'][$this->EnderecoPHP]['ATIVO']='nao' ;
				$rsx=roda_mysqli_query($LogSigo_sql );
				while ($rsxl=mysqli_fetch_assoc($rsx)){
					$this->EnderecoPHP=$rsxl['log_endereco'];
					$_SESSION['SLOGS'][$this->EnderecoPHP]['ATIVO']=true; 
					$_SESSION['SLOGS'][$this->EnderecoPHP]['ID']=$rsxl['log_id'];
					$_SESSION['SLOGS'][$this->EnderecoPHP]['MAINAPP']=$rsxl['log_mainapp'];
					$_SESSION['SLOGS'][$this->EnderecoPHP]['APP']=$rsxl['log_app'];
					$_SESSION['SLOGS'][$this->EnderecoPHP]['NIVEL']=$rsxl['log_nivel'];
				}
			}
		}
    }
    public function GravaNoBanco() {//Inicia Contagem PHP
		global $conecta;
		if ($this->DebugAtivo){echo '<br>GravaNoBanco  '; };
		if ($_SESSION['SIGO']['ACESSO']['ID_USUARIO']<>'') {
			$this->Banco();
			$id=$_SESSION['SLOGS'][$this->EnderecoPHP]['ID']; 
			$main=$_SESSION['SLOGS'][$this->EnderecoPHP]['MAINAPP'];
			$app=$_SESSION['SLOGS'][$this->EnderecoPHP]['APP'];
			$nivel=$_SESSION['SLOGS'][$this->EnderecoPHP]['NIVEL'];
			$usr=$_SESSION['SIGO']['ACESSO']['ID_USUARIO'];
			
			if(isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != '')
				$query_string = '?'.$_SERVER['QUERY_STRING'];
			else
				$query_string = '';
				
			$url=$_SERVER['PHP_SELF'].$query_string;
			
		 //	print_r($GLOBALS);
			if ($_SESSION['SLOGS'][$this->EnderecoPHP]['NIVEL']==1){ // nível básico apenas matricula e página principal com a quantidade de acessos
				mysqli_query($conecta, "insert into {$this->BaseDeDados}tbl_log_sigo_controle_nivel_1 set log_id=$id, log_id_usuario=$usr, log_vezes=if(log_vezes is null, 1, log_vezes+1), log_dia=CURRENT_DATE  on DUPLICATE key update log_vezes=log_vezes+1");
			} elseif (($_SESSION['SLOGS'][$this->EnderecoPHP]['NIVEL']==2) and (($main==$_GET['mainapp']) and (($app=='') or ($app==$_GET['app'])))) { //nível médio = básico + página específica para testar acesso a programas espeíficos
				mysqli_query($conecta, "insert into {$this->BaseDeDados}tbl_log_sigo_controle_nivel_2 set log_id=$id, log_id_usuario=$usr, log_vezes=if(log_vezes is null, 1, log_vezes+1), log_dia=CURRENT_DATE, log_mainapp='$main', log_app='$app' on DUPLICATE key update log_vezes=log_vezes+1");
			} elseif ($_SESSION['SLOGS'][$this->EnderecoPHP]['NIVEL']==3){ //nível intermediário todos os acessos por página e usuário
				mysqli_query($conecta, "insert into {$this->BaseDeDados}tbl_log_sigo_controle_nivel_3 set log_id=$id, log_id_usuario=$usr, log_vezes=if(log_vezes is null, 1, log_vezes+1), log_dia=CURRENT_DATE, log_mainapp='$main', log_app='$app' on DUPLICATE key update log_vezes=log_vezes+1");			
			} elseif ($_SESSION['SLOGS'][$this->EnderecoPHP]['NIVEL']==4){ //nível avancado todas as chamadas chamadas de página, com a url completa
				mysqli_query($conecta, "insert into {$this->BaseDeDados}tbl_log_sigo_controle_nivel_4 set log_id=$id, log_id_usuario=$usr, log_vezes=if(log_vezes is null, 1, log_vezes+1), log_dia=now(), log_url='$url' on DUPLICATE key update log_vezes=log_vezes+1, log_dia=now()");	
			};
		}
    }
};


?>