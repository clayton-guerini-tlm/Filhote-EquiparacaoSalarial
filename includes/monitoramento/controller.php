<?php

    $caminho_sigo = '../';
    require_once "{$caminho_sigo}includes/funcoes.php";
    $conexao = RetornaConexaoMysql('local', 'sigo_integrado');	
            
    
    function buscaDados($condicao=NULL){
        
        if($condicao) foreach($condicao as $k => $v){
            if($v) $cond[] = "{$k} = '{$v}'";
        }
        if($cond){
            $sql_cond = " WHERE ".implode(" AND ", $cond);
        }
        
	$sql = "SELECT *,count(*) as qtd FROM `sigo_integrado`.`tbl_monitora_acesso`".$sql_cond." group by id_menu,aplicacao,rotina order by qtd desc;";	
	$rs = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
	
	while($row = mysqli_fetch_assoc($rs)){
		$rtn["aplicacao"][] = utf8_encode("Aplicação: ".$row["aplicacao"]."  Rotina: ".$row["rotina"]);
		$rtn["qtd"][] = $row["qtd"] / 10; 
        }
        return $rtn;
    }
    function geraMenu($select,$condicao=NULL){
         
        if($condicao) foreach($condicao as $k => $v){
            if($v) $cond[] = "{$k} = '{$v}'";
        }
        if($cond){
            $sql_cond = " WHERE ".implode(" AND ", $cond);
        }
        
        $sql = "SELECT DISTINCT(".$select.") FROM tbl_monitora_acesso".$sql_cond;
        //die($sql);
        
        $rs = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
	
	while($row = mysqli_fetch_assoc($rs)){
            $rtn[] = utf8_encode($row[$select]);
        }
        return $rtn;
    }
    
    if($_POST){
        $post = trataPost($conexao);
        
        $select = "id_menu";
        if($post["id_menu"]){
            $select = "aplicacao";
            $condicoes["id_menu"] = $post["id_menu"];
        }
        if($post["aplicacao"]){
             $select = "rotina";
             $condicoes["aplicacao"] = $post["aplicacao"];
        }
        switch ($post["acao"]){
            case "BuscaDadosGrafico" : $rtn = buscaDados($condicoes); break;
            case "geraMenu" : 
                
                $rtn = geraMenu($select,$condicoes); break;
        }
        echo json_encode($rtn);
    }
    mysqli_close($conexao);
    

?>
