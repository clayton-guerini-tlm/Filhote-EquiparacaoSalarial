<?php

// ATENCAO funcao de conexao esta no arquivo incluido abaixo //
include "banco.php";

//set_time_limit(0);

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

function TempoEmExtenso($tempo, $tipo = "minutos") {
    if ($tipo == "minutos") {

        //--> FUNÇÃO PARA CONVERTER UM TEMPO (EM MINUTOS) PARA FORMATO EM EXTENSO: EXEMPLO: 1500 MINUTOS = 1 DIA E 1 HORA
        if ($tempo != '-' && $tempo != '0') {

            $dia = floor($tempo / 1440);
            $resto = $tempo % 1440;
            $hora = floor($resto / 60);
            $minuto = floor($resto % 60);


            if ($dia > 0)
                $extenso = $dia . 'D ';
            if ($hora > 0)
                $extenso .= $hora . 'Hr(s) ';
            if ($minuto > 0)
                $extenso .= $minuto . 'Min(s)';
        }else {
            $extenso = '-';
        }
    } elseif ($tipo == "minutos_discreto") {
        if ($tempo != '-' && $tempo != '0') {

            $dia = floor($tempo / 1440);
            $resto = $tempo % 1440;
            $hora = floor($resto / 60);
            $minuto = floor($resto % 60);
            $extenso = (str_pad($dia, 3, 0, STR_PAD_LEFT)) . 'd ' . (str_pad($hora, 2, 0, STR_PAD_LEFT)) . ':' . (str_pad($minuto, 2, 0, STR_PAD_LEFT)) . 'h';
        } else {
            $extenso = '&nbsp;';
        }
    } else if ($tipo == "segundos") {

        $resultado = $tempo;

        $dia = floor($resultado / 86400);
        $resto = floor($resultado % 86400);

        $hora = floor($resto / 3600);
        $resto = $resto % 3600;

        $minuto = floor($resto / 60);
        $resto = $resto % 60;
        $segundo = $resto;
        $extenso_texto = "";
        if ($dia > 0)
            $extenso_texto = $dia . 'Dia(s) ';
        if ($hora > 0)
            $extenso_texto .= $hora . 'Hr(s) ';
        if ($minuto > 0)
            $extenso_texto .= $minuto . 'Min(s) ';
        if ($segundo > 0)
            $extenso_texto .= $segundo . 'Seg(s)';

        $extenso = array();

        $extenso['extenso'] = $extenso_texto;
        $extenso['dia'] = $dia;
        $extenso['hora'] = $hora;
        $extenso['minuto'] = $minuto;
        $extenso['segundo'] = $segundo;
    }
    return $extenso;
}

function GeraPalavraAleatoria($tamanho = 10, $numero = false) {
    $array_caracteres = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    if ($numero) {
        array_push($array_caracteres, 2);
        array_push($array_caracteres, 3);
        array_push($array_caracteres, 4);
        array_push($array_caracteres, 5);
        array_push($array_caracteres, 6);
        array_push($array_caracteres, 7);
        array_push($array_caracteres, 8);
        array_push($array_caracteres, 9);
    }
    $ret = "";
    for ($i = 0; $i < $tamanho; $i++) {
        $rand = rand(0, sizeof($array_caracteres));
        $ret.= $array_caracteres[$rand];
    }

    return $ret;
}

function SubstituiChar($texto) {
    //
    // Substitui caracteres especiais dentro da String 
    //
  $ant = $texto;
  $texto = @preg_replace("/[??ÀÂÃ]/", "A", $texto);
  $texto = @preg_replace("/[áàâãª]/", "a", $texto);
  $texto = @preg_replace("/[ÉÈÊ]/", "E", $texto);
  $texto = @preg_replace("/[éèê]/", "e", $texto);
  $texto = @preg_replace("/[??ÌÎ]/", "I", $texto);
  $texto = @preg_replace("/[íìî]/", "i", $texto);
  $texto = @preg_replace("/[ÓÒÔÕ]/", "O", $texto);
  $texto = @preg_replace("/[óòôõº]/", "o", $texto);
  $texto = @preg_replace("/[ÚÙÛ]/", "U", $texto);
  $texto = @preg_replace("/[úùû]/", "u", $texto);
  $texto = @preg_replace("/[Ç]/", "C", $texto);
  $texto = @preg_replace("/[ç]/", "c", $texto);

    return $texto;
}

function GeraOptionGenerico($rs_tabela, $campo_id, $campo_nome, $selecionado = '', $vazio = '') {
    $options = "";
    if (!empty($vazio)) {
        if ($vazio == "TELEMONT") {
            $vazio_valor = "TODOS";
        } else if ($vazio == "TODOS MENUS") {
            $vazio_valor = "0";
        } else {
            $vazio_valor = "";
        }
        
        $options .= <<<opt
        	<option value="$vazio_valor" > $vazio </option>
opt;
        
//         $options.= "<option value='$vazio_valor'>$vazio</option>";
    }

    while ($row_tabela = @mysqli_fetch_array($rs_tabela)) {
        $valor_id = $row_tabela["$campo_id"];
        $valor_nome = $row_tabela["$campo_nome"];

        $selecionado_tmp = str_replace(" ", "", $selecionado);
        $valor_id_tmp = str_replace(" ", "", $valor_id);

        //echo $selecionado_tmp . " - " . $valor_id_tmp . " <br>";

        if ($selecionado_tmp == $valor_id_tmp && $selecionado_tmp != "") {
            $valor_selecionado = "selected";
        } else {
            $valor_selecionado = "";
        }
        $options.= "<option $valor_selecionado value=\"$valor_id\">$valor_nome</option>";
    }

    return $options;
}

function sksort(&$array, $subkey = "id", $sort_ascending = false) {

    if (count($array))
        $temp_array[key($array)] = array_shift($array);

    foreach ($array as $key => $val) {
        $offset = 0;
        $found = false;
        foreach ($temp_array as $tmp_key => $tmp_val) {
            if (!$found and strtolower($val[$subkey]) > strtolower($tmp_val[$subkey])) {
                $temp_array = array_merge((array) array_slice($temp_array, 0, $offset), array($key => $val), array_slice($temp_array, $offset)
                );
                $found = true;
            }
            $offset++;
        }
        if (!$found)
            $temp_array = array_merge($temp_array, array($key => $val));
    }

    if ($sort_ascending)
        $array = array_reverse($temp_array);
    else
        $array = $temp_array;
}

function QuantidadeDiasMes($competencia, $forcar = false) {

    //--> para capturar a qnt de dias do mês atual, passar o parâmetro $forcar = true; caso contrário a função retornará o dia atual.

    $mes_atual = date("m");
    $dia_atual = date("d");
    $mes_escolhido = substr($competencia, 5, 2);
    $ano_escolhido = substr($competencia, 0, 4);

    if ($mes_atual == $mes_escolhido && !$forcar) {
        $dias_mes = $dia_atual;
    } else {
        $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes_escolhido, $ano_escolhido);
    }

    return $dias_mes;
}

function RetornaOptionsCompetencia($conn, $escolhida = '') {

    $options = "<option selected value=\"\">Selecione</option>";

    $mes_atual = date("m");
    $ano_atual = date("Y");

    $link_interno = RetornaConexaoMysql('serveres', 'modulo_gestao_operacional_la');

    $Sql = "SELECT SUBSTRING(data_fechamento, 1, 4) as ano, SUBSTRING(data_fechamento, 6, 2) as mes
			FROM tbl_relatorios_base
			WHERE data_fechamento <= '$ano_atual-$mes_atual-31 23:59:59' and data_fechamento <> '0000-00-00 00:00:00'
			GROUP BY SUBSTRING(data_fechamento, 1, 4),
			SUBSTRING(data_fechamento, 6, 2) ORDER BY SUBSTRING(data_fechamento, 1, 4) DESC,
			SUBSTRING(data_fechamento, 6, 2) DESC";

    $rs_competencia = mysqli_query($conn, $Sql);
    while ($row_competencia = mysqli_fetch_array($rs_competencia)) {

        $mes = $row_competencia['mes'];
        $ano = $row_competencia['ano'];



        if ($escolhida == "$ano-$mes") {
            $selected = "selected";
        } else {
            $selected = "";
        }

        $data_label = RetornaNomeMes($mes) . "/$ano";

        $options.= "<option $selected value=\"$ano-$mes\">$data_label</option>";
    }

    //mysqli_close($link_interno);

    return $options;
}

function RetornaNomeMes($mes) {

    switch ($mes) {
        case "01":$mes_label = "JANEIRO";
            break;
        case "02":$mes_label = "FEVEREIRO";
            break;
        case "03":$mes_label = "MAR&Ccedil;O";
            break;
        case "04":$mes_label = "ABRIL";
            break;
        case "05":$mes_label = "MAIO";
            break;
        case "06":$mes_label = "JUNHO";
            break;
        case "07":$mes_label = "JULHO";
            break;
        case "08":$mes_label = "AGOSTO";
            break;
        case "09":$mes_label = "SETEMBRO";
            break;
        case "10":$mes_label = "OUTUBRO";
            break;
        case "11":$mes_label = "NOVEMBRO";
            break;
        case "12":$mes_label = "DEZEMBRO";
            break;
    }
    return $mes_label;
}

function RetornaAbreviacaoMes($mes) {

    switch ($mes) {
        case "01":$mes_label = "JAN";
            break;
        case "02":$mes_label = "FEV";
            break;
        case "03":$mes_label = "MAR";
            break;
        case "04":$mes_label = "ABR";
            break;
        case "05":$mes_label = "MAI";
            break;
        case "06":$mes_label = "JUN";
            break;
        case "07":$mes_label = "JUL";
            break;
        case "08":$mes_label = "AGO";
            break;
        case "09":$mes_label = "SET";
            break;
        case "10":$mes_label = "OUT";
            break;
        case "11":$mes_label = "NOV";
            break;
        case "12":$mes_label = "DEZ";
            break;
    }
    return $mes_label;
}

function ConvertDataHoraMysql($dthr, $formato = 'mysql', $tem_hora = false) {

    if (!empty($dthr)) {
        switch ($formato) {
            case 'mysql':

                if ($tem_hora) {
                    $vetorhr = explode(" ", $dthr);
                    $vetordt = explode("/", $vetorhr[0]);
                    $dthr_nova = $vetordt[2] . '-' . $vetordt[1] . '-' . $vetordt[0] . ' ' . $vetorhr[1];
                } else {
                    $vetordt = explode("/", $dthr);
                    $dthr_nova = $vetordt[2] . '-' . $vetordt[1] . '-' . $vetordt[0];
                }

                break;


            case 'normal':

                $dthr_nova = substr($dthr, 8, 2) . "/" . substr($dthr, 5, 2) . "/" . substr($dthr, 0, 4);

                if ($tem_hora) {
                    $dthr_nova.= " " . substr($dthr, 11);
                }

                break;
        }
    } else {
        $dthr_nova = "n/a";
    }

    return $dthr_nova;
}

function AdicionaSubstringDeLinha($str, $substring, $quant) {

    $vet_str = str_split($str);
    $str_tmp = "";

    for ($i = 0; $i < sizeof($vet_str); $i++) {
        $str_tmp.= $vet_str[$i];
        $j = $i + 1;
        if ($j % $quant == 0 && $i != 0) {
            $str_tmp.=$substring;
        }
    }

    return $str_tmp;
}

function RetornaIdsRelacionados($conn, $tabela, $lingua = NULL) {

    $vet_relacionamento = array();

    if ($lingua){
    	$lingua = " AND flag_lingua = '$lingua'";
    }
    
    $Sql = "SELECT rel_origem, rel_destino, rel_campo, rel_destino_nome FROM tbl_relacionamento WHERE rel_origem='$tabela' $lingua";

    //echo $Sql;exit;

    if ($rs_rel = mysqli_query($conn, $Sql)) {
        while ($row_rel = mysqli_fetch_assoc($rs_rel)) {

            if ($row_rel['rel_origem'] != $row_rel['rel_destino']) {
                $Sql = "SELECT DISTINCT {$row_rel['rel_campo']} FROM {$row_rel['rel_destino']}";
            } else {
                $Sql = "SELECT DISTINCT {$row_rel['rel_destino_campo']} FROM {$row_rel['rel_destino']}";
                //echo $Sql;exit;
            }

            //echo $Sql;exit;
            if ($rs_rel2 = mysqli_query($conn, $Sql)) {
                while ($row_rel2 = mysqli_fetch_array($rs_rel2)) {

                    if ($tabela != "tbl_monitoria_questao") {
                        if ($row_rel['rel_origem'] != $row_rel['rel_destino']) {
                            $id = $row_rel2[$row_rel['rel_campo']];
                        } else {
                            $id = $row_rel2[$row_rel['rel_destino_campo']];
                        }
                        $tabela_dest = $row_rel['rel_destino'];
                        $descricao = $row_rel['rel_destino_nome'];
                        $vet_temp['tabela'] = $tabela_dest;
                        $vet_temp['descricao'] = $descricao;
                        $vet_temp['id'] = $id;

                        array_push($vet_relacionamento, $vet_temp);
                    } else {
                        $id = $row_rel2[$row_rel['rel_campo']];
                        $tabela_dest = $row_rel['rel_destino'];
                        $descricao = $row_rel['rel_destino_nome'];
                        $ids = explode(",", $id);

                        foreach ($ids as $id) {
                            $vet_temp['tabela'] = $tabela_dest;

                            $vet_temp['id'] = $id;
                            array_push($vet_relacionamento, $vet_temp);
                        }
                    }
                }
            }
        }
    }
    return $vet_relacionamento;
}

function BuscaItemRelacionamento($vet_relacionamento, $id) {
    $achou = false;
    $tabela_encontrado = '';
    if(is_array($vet_relacionamento)){
        foreach ($vet_relacionamento as $vet) {
            if ($vet['id'] == $id) {
                $achou = true;
                $tabela_encontrado .= $vet['descricao'] . ', ';
            }
        }
    }
    if ($achou) {
        $tabela_encontrado = substr($tabela_encontrado, 0, -2);
    } else {
        $tabela_encontrado = "nenhum";
    }

    return $tabela_encontrado;
}


function RetornaMenuSigo($identificador) {

//    echo $identificador;exit;
    $Vet_submenu  = $_SESSION['SIGO']['MENU'][$identificador];
    $aumenta_menu = "";
    #echo "<pre>";
    #print_r($_SESSION['SIGO']['MENU']);exit;
    if (sizeof($Vet_submenu) > 0) {

        $menu = "<ul id=\"menu\">";

        foreach ($Vet_submenu as $Vet_aplicacao) {

            if (strtolower(substr($Vet_aplicacao['LINK'], 0, 4)) == "http") {
                $target = "target=\"_blank\"";
            } else {
                $target = "";
            }

            $menu.= "<li><a $target href=\"{$Vet_aplicacao['LINK']}\">{$Vet_aplicacao['NOME']}</a>";

            if (isset($Vet_aplicacao['APP']) and sizeof($Vet_aplicacao['APP']) > 0) {
                $apps = "";
                
                foreach ($Vet_aplicacao['APP'] as $v) {
                    if (strlen($v['NOME']) > 20) {
                        $aumenta_menu = "style=\"width:250px;\"";
                    }
                }

                foreach ($Vet_aplicacao['APP'] as $v) {
                    if (strtolower(substr($v['LINK'], 0, 4)) == "http") {
                        $target = "target=\"_blank\"";
                    } else {
                        $target = "";
                    }
                    $apps.= "<li $aumenta_menu><a $target href=\"{$v['LINK']}\">{$v['NOME']}</a></li>";
                }
                if (!empty($apps)) {
                    $menu.= "<ul>";
                    $menu.= $apps;
                    $menu.= "</ul>";
                }
            }
            $menu.= "</li>";
        }

        // LINK 'VOLTAR' FOI INSERIDO COMO UM SUBMENU         

        $quant_sis = sizeof(explode("|", $_SESSION['SIGO']['ACESSO']['SISTEMA_USUARIO']));

        $menu_inicial = strpos(strtoupper($identificador), "INICIAL");

        if ($menu_inicial !== false && $quant_sis > 1) {
            $menu.= "<li $aumenta_menu ><a href=\"../../sistema.php\">VOLTAR</a></li>";
        }

        $menu.= "</ul>";
    } else {

        $menu = "Erro ao retornar o MENU. Usuário não possui permissão para visualizar o MENU.";

        //Incluido para bloquear o acesso aos usuarios que utilizam ferramentas do portal
        //sem permissão para tal. O identificador SIGO_ADM_LOGOUT fica fora da condição pois é 
        //exibido para todos os colaboradores que possuem mais de uma filial, nao sendo viavel dar permissão para 
        //cada grupo especifico para este menu.
        if ($identificador != 'SIGO_ADM_LOGOUT') {
            throw new exception($menu);
        }
    }
    return $menu;
}

/* function RetornaMenuSigoJmenu($identificador){
  $Vet_submenu = $_SESSION['SIGO']['MENU'][$identificador];

  //echo "<pre>";print_r($_SESSION['SIGO']['MENU']);exit;
  //echo sizeof($Vet_submenu);
  //echo "$identificador";exit;
  if(sizeof($Vet_submenu) > 0){
  $menu = "<ul id=\"jMenu\">";
  foreach ($Vet_submenu as $Vet_aplicacao) {
  if (strtolower(substr($Vet_aplicacao['LINK'],0,4)) == "http"){
  $target = "target=\"_blank\"";
  }else{
  $target = "";
  }
  //echo "<pre>";print_r($Vet_aplicacao);//exit;
  $menu.= "<li><a $target class=\"fNiv\" href=\"{$Vet_aplicacao['LINK']}\">{$Vet_aplicacao['NOME']}</a>";

  if(isset($Vet_aplicacao['APP']) and sizeof($Vet_aplicacao['APP']) > 0){
  $apps = "<li class=\"arrow\"></li>";
  $aumenta_menu = "";
  foreach ($Vet_aplicacao['APP'] as $v) {
  if (strlen($v['NOME'])>20) {
  $aumenta_menu = "style=\"width:250px;\"";
  }
  }

  foreach ($Vet_aplicacao['APP'] as $v) {
  if (strtolower(substr($v['LINK'],0,4)) == "http"){
  $target = "target=\"_blank\"";
  }else{
  $target = "";
  }
  $apps.= "<li $aumenta_menu><a $target href=\"{$v['LINK']}\">{$v['NOME']}</a></li>";
  }
  if(!empty($apps)){
  $menu.= "<ul>";
  $menu.= $apps;
  $menu.= "</ul>";
  }

  }
  $menu.= "</li>";
  }

  /// LINK 'VOLTAR' FOI INSERIDO COMO UM SUBMENU

  $quant_sis = sizeof(explode("|",$_SESSION['SIGO']['ACESSO']['SISTEMA_USUARIO']));

  $menu_inicial = strpos(strtoupper($identificador),"INICIAL");
  //echo $menu_inicial; exit;
  if ($menu_inicial !== false && $quant_sis>1) {
  $menu.= "<li $aumenta_menu ><a href=\"../../sistema.php\">VOLTAR</a></li>";
  }

  $menu.= "</ul>";
  }else{
  //echo "entrei aki"; exit;
  $menu = "Erro ao retornar o MENU. Usuário não possui permissão para visualizar o MENU.";
  }
  $ret = $menu;
  return $ret;
  } */

function RetornaMenuSigoUtilizado() {

    ini_set('max_execution_time', 86400);

    $conecta 				= RetornaConexaoMysql('local', 'sigo_integrado');
    $_SESSION['SIGO']['MENU'] = null;
    $_SESSION['SIGO']['PERMISSAO'] = null;

    $where_area = "";

    if (!($_SESSION['SIGO']['ACESSO']['ID_GRUPO'] == 2 || $_SESSION['SIGO']['ACESSO']['ID_GRUPO'] == 3 || $_SESSION['SIGO']['ACESSO']['ID_GRUPO'] == 4 || $_SESSION['SIGO']['ACESSO']['ID_GRUPO'] == 67)) {

        $filiais = explode("|", $_SESSION['SIGO']['ACESSO']['FILIAL']);
        $condicoes_area = array();

        foreach ($filiais as $f) {
            $condicoes_area[] = " men_area = '$f' ";
        }

        $where_area = " WHERE " . implode(' OR ', $condicoes_area);
    }

    $vet_permissao_gru = RetornaPermissao($conecta, 'grupo', $_SESSION['SIGO']['ACESSO']['ID_GRUPO']);
    $vet_permissao_usr = RetornaPermissao($conecta, 'usuario', $_SESSION['SIGO']['ACESSO']['ID_USUARIO']);
   
    //Recupera os menus de acordo com a area do colaborador 
    //Para os usuarios de perfil administrativos enxergam todas as áreas

    $Sql = "SELECT * FROM tbl_menu $where_area";
    $vet_ret_menu = array();
    if ($rs_menu = $conecta->query($Sql)) {
        while ($row_menu = $rs_menu->fetch_assoc()) {
            $vet_ret_menu[] = $row_menu;
        }
    }


    if(sizeof($vet_ret_menu)>0) {


        $Sql = "SELECT * FROM tbl_menu_submenu WHERE smu_mostrar = 1 ORDER BY smu_ordem ASC";  
        $vet_ret_submenus = array();
        if ($rs_submenu = $conecta->query($Sql)) {
            while ($row_submenu = $rs_submenu->fetch_assoc()) {
                $vet_ret_submenus[$row_submenu['men_id']][] = $row_submenu;
            }
        }

        $Sql = "SELECT * FROM tbl_menu_aplicacao WHERE apl_mostrar=1 ORDER BY apl_ordem ASC";
        $vet_ret_aplicacao = array();
        if ($rs_aplicacao = $conecta->query($Sql)) {
            while ($row_aplicacao = $rs_aplicacao->fetch_assoc()) {
                $vet_ret_aplicacao[$row_aplicacao['smu_id']][] = $row_aplicacao;
            }
        }

        foreach ($vet_ret_menu as $k1 => $row) {

            $menu_id = $row['men_id'];
            $menu_identificador = $row['men_resumo'];
           //$_SESSION['SIGO']['ACESSO']['ID_USUARIO'] = 3234;

          
            //Obtendo os submenus de acordo com o menu
            if(isset($vet_ret_submenus[$menu_id])) {

                foreach ($vet_ret_submenus[$menu_id] as $k2 => $row_submenu) {

                    $submenu_id = $row_submenu['smu_id'];

                    $submenu_nome = $row_submenu['smu_nome'];
                    $submenu_link = $row_submenu['smu_link'];

                    if(!empty($vet_permissao_usr) || !empty($vet_permissao_gru)){
                        if(isset($vet_permissao_gru['per_habilitar'][0][$submenu_id]) && $vet_permissao_gru['per_habilitar'][0][$submenu_id] == 1){
                            $marcado = 1;
                        }
                        else if(isset($vet_permissao_usr['per_habilitar'][0][$submenu_id]) && $vet_permissao_usr['per_habilitar'][0][$submenu_id] == 1){
                            $marcado = 1;
                        }
                    }

                    if ($_SESSION['SIGO']['ACESSO']['ID_GRUPO'] == 1 || $_SESSION['SIGO']['ACESSO']['ID_GRUPO'] == 2 || $_SESSION['SIGO']['ACESSO']['ID_GRUPO'] == 3 || $_SESSION['SIGO']['ACESSO']['ID_GRUPO'] == 4 || $_SESSION['SIGO']['ACESSO']['ID_GRUPO'] == 67) {
                        $marcado = 1;
                        $vet_permissao_gru['per_nivel'][$submenu_id][0] = 99;
                    }

                    if ($marcado) {

                        $_SESSION['SIGO']['MENU'][$menu_identificador][$submenu_id]['NOME'] = $submenu_nome;
                        $_SESSION['SIGO']['MENU'][$menu_identificador][$submenu_id]['LINK'] = $submenu_link;

                        $pos_interrogacao = strpos($submenu_link, "?");
                        if ($pos_interrogacao !== FALSE) {

                            $str_apps = substr($submenu_link, $pos_interrogacao);
                            $str_apps = explode("&", $str_apps);
                            $mainapp = explode("=", $str_apps[0]);
                            $mainapp = $mainapp[1];
                            $app = explode("=", $str_apps[1]);
                            $app = $app[1];

                            if (isset($vet_permissao_usr['per_nivel'][0][$submenu_id]) && $vet_permissao_usr['per_nivel'][0][$submenu_id] > $vet_permissao_gru['per_nivel'][0][$submenu_id]) {
                                $_SESSION['SIGO']['PERMISSAO'][$menu_identificador][$mainapp][$app] = $vet_permissao_usr['per_nivel'][0][$submenu_id];
                            } else if(isset($vet_permissao_gru['per_nivel'][0][$submenu_id])){
                                $_SESSION['SIGO']['PERMISSAO'][$menu_identificador][$mainapp][$app] = $vet_permissao_gru['per_nivel'][0][$submenu_id];
                            }
                        }

                        if(isset($vet_ret_aplicacao[$submenu_id])) {

                            foreach ($vet_ret_aplicacao[$submenu_id] as $k3 => $row_aplicacao) {

                                $aplicacao_id = $row_aplicacao['apl_id'];
                                $aplicacao_nome = $row_aplicacao['apl_nome'];
                                $aplicacao_link = $row_aplicacao['apl_link'];
                                
                                if(isset($vet_permissao_usr['per_habilitar'])){
                                    $marcado = ($vet_permissao_usr['per_habilitar'][$aplicacao_id][0] == 1 || $vet_permissao_gru['per_habilitar'][$aplicacao_id][0] == 1);
                                }

                                if ($_SESSION['SIGO']['ACESSO']['ID_GRUPO'] == 1 || $_SESSION['SIGO']['ACESSO']['ID_GRUPO'] == 2 || $_SESSION['SIGO']['ACESSO']['ID_GRUPO'] == 3 || $_SESSION['SIGO']['ACESSO']['ID_GRUPO'] == 4 || $_SESSION['SIGO']['ACESSO']['ID_GRUPO'] == 67) {
                                    $marcado = 1;
                                    $vet_permissao_gru['per_nivel'][$aplicacao_id][0] = 99;
                                }

                                if ($marcado) {
                                    $_SESSION['SIGO']['MENU'][$menu_identificador][$submenu_id]['APP'][$aplicacao_id]['NOME'] = $aplicacao_nome;
                                    $_SESSION['SIGO']['MENU'][$menu_identificador][$submenu_id]['APP'][$aplicacao_id]['LINK'] = $aplicacao_link;

                                    $pos_interrogacao = strpos($aplicacao_link, "?");

                                    if ($pos_interrogacao !== FALSE) {

                                        $str_apps = substr($aplicacao_link, $pos_interrogacao);
                                        $str_apps = explode("&", $str_apps);
                                        $mainapp = explode("=", $str_apps[0]);
                                        if(isset($mainapp[1])){
                                            $mainapp = $mainapp[1];
                                        }
                                        
                                        if(isset($str_apps[1])){
                                            $app = explode("=", $str_apps[1]);
                                            if(isset($app[1])){
                                                $app = $app[1];
                                            }
                                        }

                                        if (isset($vet_permissao_usr['per_nivel'][$aplicacao_id][0]) && $vet_permissao_usr['per_nivel'][$aplicacao_id][0] > $vet_permissao_gru['per_nivel'][$aplicacao_id][0]) {
                                            $_SESSION['SIGO']['PERMISSAO'][$menu_identificador][$mainapp][$app] = $vet_permissao_usr['per_nivel'][$aplicacao_id][0];
                                        } else {
                                            $_SESSION['SIGO']['PERMISSAO'][$menu_identificador][$mainapp][$app] = $vet_permissao_gru['per_nivel'][$aplicacao_id][0];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                $erro = "Erro ao retornar o MENU. " /* . mysqli_error($conecta)*/;
                ;
            }
        }
    } else {
        $erro = "Erro ao retornar o MENU. " /*. mysqli_error($conecta)*/;
    }
}

function GravaLogSentenca($conn, $Sentenca, $usr_id = 0, $serverrj = 1) {
    $link2 = RetornaConexaoMysql('serverdge', 'sigo_integrado');
    if (isset($_SESSION['SIGO']['ACESSO']['ID_USUARIO'])) {
        $usr_id = $_SESSION['SIGO']['ACESSO']['ID_USUARIO'];
    }

    $Sql = "INSERT INTO tbl_log_sentenca (sen_id, usr_id, sen_sentenca, sen_dt_execucao,sen_executado_dge, sen_usr_ip) VALUES (sen_id,$usr_id,\"$Sentenca\", Now(), '$serverrj', '{$_SERVER['REMOTE_ADDR']}')";
    mysqli_query($link2, $Sql);
}

function AbrirTicketAcesso($destino, $param = '') {

    $destino = trim($destino);
    $param = trim($param);


    //	$usr_id = $_SESSION['SIGO']['ACESSO']['ID_USUARIO'];


    /*     * *
     * 
     * Atenção desenvolvedores da DGE. A variavel usr_id é o FUN_ID da tabela rm_funcionario.
     * Gentileza ficarem atentos.
     * Este comentario foi inserido pq o Lud ficou no meu pé.
     * 
     * Walter Reis 
     * 19/11/2010
     * 
     */

    //Alterado por walter devido a alteracao do lud que alterou os usr_id para fun_id como registro responsavel pelos joins entre as tabelas.


    $usr_id = $_SESSION['SIGO']['ACESSO']['ID_FUNCIONARIO'];
    

    $ticket = md5(date("Y-m-d H:i:s.u") . $usr_id);
    $link_remoto = false;

    switch ($destino) {
        case "AREA1_MG":
            $link_remoto = RetornaConexaoMysql('serveradsl', 'sigo_integrado');
            break;
        case "AREA1_RJ":
            $link_remoto = RetornaConexaoMysql('serverrj', 'sigo_integrado');
            break;
        case "AREA1_ES":
            $link_remoto = RetornaConexaoMysql('serveres', 'sigo_integrado');
            break;
        case "AREA3_SP":
            $link_remoto = RetornaConexaoMysql('serversp', 'sigo_integrado');
            break;
        case "AREA2":
            $link_remoto = RetornaConexaoMysql('local', 'sigo_integrado');
            break;
        case "seguro":
            $link_remoto = RetornaConexaoMysql('seguro', 'sigo_integrado');
            break;
        case 'TROCAR_SENHA_AREA2':
            $msg = $_GET['msg'];
            header('Location: http://sigo.telemont.com.br/SIGO_INTEGRADO_3/sigo_integrado_ticket.php?p=abrir_trocar_senha_area2&u=' . $usr_id . '&msg=' . $msg);
            exit();
            break;
        case 'TROCAR_SENHA_AREA1RJ':
            $msg = $_GET['msg'];
            header('Location: http://sigo.telemont.com.br/SIGO_INTEGRADO_3/sigo_integrado_ticket.php?p=abrir_trocar_senha_area1rj&u=' . $usr_id . '&msg=' . $msg);
            exit();
            break;
        default:
            header("Location: ../index.php?msg=6");
            exit();
            break;
    }

    if (!$link_remoto) {
        exit('Nao foi possivel efetuar conexao com o link remoto.');
    }

    echo $Sql = "INSERT INTO tbl_ticket_acesso (tic_hash, usr_id) VALUES ('$ticket','$usr_id')";die;
    if (!$link_remoto->query($Sql)) {
        $destino = "ERRO";
        die();
    }
   // mysqli_close($link_remoto);

    $location = '';
    switch ($destino) {
        case "AREA1_MG":
            $host = 'servereshost';
            $location = "Location: http://$host/SIGO_INTEGRADO_3/sigo_integrado_ticket.php?t=$ticket";
            break;
        case "AREA1_RJ":
            if (substr($_SERVER['HTTP_HOST'], 0, 3) == '127') {
                $host = '127.0.0.1';
            } elseif (substr($_SERVER['REMOTE_ADDR'], 0, 3) == "172") {
                $host = '172.17.51.102';
            } else {
                $host = 'sigo.telemontrio.com.br';
            }
            $location = "Location: http://$host/SIGO_INTEGRADO_3/sigo_integrado_ticket.php?t=$ticket";
            break;
        case "AREA1_ES":
            if (substr($_SERVER['REMOTE_ADDR'], 0, 3) == "192") {
                $host = '192.168.5.8';
            } else {
                $host = 'sigoes.telemont.com.br';
            }
            $location = "Location: http://$host/SIGO_INTEGRADO_3/sigo_integrado_ticket.php?t=$ticket";
            break;
        case "AREA3_SP":
            $host = '172.18.1.103';
            $location = "Location: http://$host/SIGO_INTEGRADO_3/sigo_integrado_ticket.php?t=$ticket";
            break;
        case "AREA2":

            if (substr($_SERVER['REMOTE_ADDR'], 0, 3) != "10.") {
                $host = '189.74.128.245';
                $host = 'localhost:8090';
            } else {
                $host = "10.59.99.217";
            }

            if ($host == '189.74.128.245' && $param == 'forum') {
                $location = "Location: http://$host/agendador/sigo_integrado_ticket.php?t=$ticket&p=forum";
            } else {
                $location = "Location: http://$host/agendador/sigo_integrado_ticket.php?t=$ticket";
            }
            break;
        case "seguro":
            $location = "Location: http://192.168.5.54/SIGO_INTEGRADO_3/sigo_integrado_ticket.php?t=$ticket";
            break;
        case "TROCAR_SENHA_AREA2":
            $host = 'sigo.telemont.com.br';
            $location = "Location: http://$host/SIGO_INTEGRADO_3/sigo_integrado_ticket.php?t=$ticket&p=trocar_senha_area2";
            break;
        default:
            $location = "Location: ../index.php?msg=6";
            break;
    }

    header($location);

    exit();
}

function RetornaPermissao($conecta, $tipo, $id_selecionado) {
    // $conecta = RetornaConexaoMysql('local', 'sigo_integrado');
    $vet_permissao = array();

    if ($tipo == "usuario") {
        $campo_id = "usr_id";
        $where_tipo = "$campo_id=$id_selecionado";
    } else {
        $campo_id = "gru_id";
        $grupos = explode("|", $id_selecionado);
        $where_tipo = "";
        foreach ($grupos as $g) {
            $gp = trim($g);
            if (!empty($gp))
                $where_tipo.=" $campo_id='$g' OR ";
        }
        $where_tipo = substr($where_tipo, 0, -3);
    }

    $Sql = "SELECT $campo_id, apl_id, smu_id, per_habilitar, per_nivel FROM tbl_permissao WHERE $where_tipo"; 
    
    $rs = $conecta->query($Sql) /* or die(mysqli_error($conecta))*/;
    while ($row_permissao = $rs->fetch_assoc()) {
        if(isset($row_permissao['per_nivel'])){
            $vet_permissao['per_habilitar'][$row_permissao['apl_id']][$row_permissao['smu_id']] = $row_permissao['per_habilitar'];
            if (isset($vet_permissao['per_nivel'][$row_permissao['apl_id']][$row_permissao['smu_id']]) && $vet_permissao['per_nivel'][$row_permissao['apl_id']][$row_permissao['smu_id']] < $row_permissao['per_nivel']) {

                $vet_permissao['per_nivel'][$row_permissao['apl_id']][$row_permissao['smu_id']] = $row_permissao['per_nivel'];

            }
        }
    }
    
    return $vet_permissao;
}

function RetornaCampoGenerico($tabela, $campo_retorno, $campo_id, $valor_id) {
    $conecta 				= RetornaConexaoMysql('local', 'sigo_integrado');
    $Sql = "SELECT $campo_retorno FROM $tabela WHERE $campo_id = $valor_id";
    //echo $Sql;exit;
    $rs = $conecta->query($Sql);
    $row = $rs->fetch_array();
    return $row[$campo_retorno];
}

function Ping($ip, $porta) {
    $fp = @fsockopen($ip, $porta, $errno, $errstr, 10);
    if (!$fp) {
        return "off";
    } else {
        fclose($fp);
        return "on";
    }
}

function RetornaOptionUsuarioFilial() {

    $options_usuario_area = <<< EOF
				<option value="">Selecione</option>			
				<option value="AREA1_MG">&Aacute;REA 1 MG</option>
				<option value="AREA1_RJ">&Aacute;REA 1 RJ</option>
				<option value="AREA1_ES">&Aacute;REA 1 ES</option>
				<option value="AREA2">&Aacute;REA 2</option>
				<option value="AREA3_SP">&Aacute;REA 3 SP</option>
				<option value="SEGURO">SEGURO</option>
EOF;

    return $options_usuario_area;
}

/**
 * Funcao para o envio de email utilizando a classe PHPMAILER
 * @author Danilo Azevedo
 * @version 2.0
 * @param string ou vetor $nome
 * @param string ou vetor $email
 * @param string $assunto
 * @param string $texto
 * @param string $anexo
 * @param string $email_admin
 * @param string $nome_admin
 * @return boolean
 * 
 * 	variavel $nome eh um vetor com os nomes ou somente um nome caso tenha apenas um nome
  variavel $email eh um vetor com os emails ou somente um email caso tenha apenas um email
 * 
 */
function EnviarEmail($nome, $email, $assunto, $texto, $anexo = "", $email_admin = "sigo@telemont.com.br", $nome_admin = "SIGO TELEMONT", $copia_oculta_para = "", $copia_para = "") {

    //echo 'em manutencao';exit;

    require_once('phpmailer/class.phpmailer.php');

    $mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

    $mail->IsSMTP(); // telling the class to use SMTP

    try {
        $mail->SMTPDebug = 0;              // enables SMTP debug information (for testing)
        $mail->SMTPAuth = true;            // enable SMTP authentication
        $mail->Host = "192.168.0.204";     // sets the SMTP server
        $mail->Port = 587;                 // set the SMTP port for the GMAIL server
        $mail->Username = "filipe.brener";     // SMTP account username
        $mail->Password = "exchange.2010";        // SMTP account password
        $mail->AddReplyTo($email_admin, $nome_admin);

        $nomes = explode(",", $nome);
        $emails = explode(",", $email);

        if (sizeof($emails) > 0) {
            foreach ($emails as $key => $em) {
                if (sizeof($nomes) > 0) {
                    $mail->AddAddress($em, $nomes[$key]);
                } else {
                    $mail->AddAddress($em, $nome);
                }
            }
        } else {
            $mail->AddAddress($email, $nome);
        }

        //Permite múltiplos destinatários como cópia (CC)
        if ($copia_para != null) {
            $emails_cc = explode(",", $copia_para);

            if (sizeof($emails_cc) > 0) {
                foreach ($emails_cc as $cc) {
                    $mail->AddCC($cc);
                }
            }
        }

        //Faz o mesmo para cópia oculta (BCC)
        if ($copia_oculta_para != null) {
            $emails_bcc = explode(",", $copia_oculta_para);

            if (sizeof($emails_bcc) > 0) {
                foreach ($emails_bcc as $bcc) {
                    $mail->AddBCC($bcc);
                }
            }
        }

        if (is_string($anexo) && !empty($anexo)) {
            $mail->AddAttachment($anexo);
        } else if (is_array($anexo) && !empty($anexo)) {
            foreach ($anexo AS $key => $arquivo) {
                $mail->AddAttachment($arquivo);
            }
        }

        $mail->SetFrom($email_admin, $nome_admin);
        $mail->Subject = $assunto;
        //$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
        $mail->MsgHTML($texto);
        $mail->Send();
        return true;
    } catch (phpmailerException $e) {
        $e->errorMessage(); //Pretty error messages from PHPMailer
        return false;
    } catch (Exception $e) {
        $e->getMessage(); //Boring error messages from anything else!
        return false;
    }
}

function RetornaIdAplicacao($conn, $link_app, $menu) {

    $sql = "select a.apl_id from sigo_integrado.tbl_menu_aplicacao a where a.apl_link = '$link_app' 
    and a.smu_id = any(select s.smu_id from sigo_integrado.tbl_menu_submenu s join sigo_integrado.tbl_menu m on 
    s.men_id = m.men_id where m.men_resumo = '$menu')";

    $retorno = mysqli_query($conn, $sql);

    $results=mysqli_fetch_array($retorno);
    return $results['apl_id'];
}

function RetornaIdSubmenu($conn, $link_sub, $menu_pai) {

    $sql = "select s.smu_id from sigo_integrado.tbl_menu_submenu s join sigo_integrado.tbl_menu m on 
    s.men_id = m.men_id where s.smu_link = '$link_sub' and m.men_resumo = '$menu_pai';";

    echo $sql;

    $retorno = mysqli_query($conn, $sql);

    $results=mysqli_fetch_array($retorno);
    return $results['smu_id'];
}

function moeda($valor) {   // Função que transforma um número em valor monetário com ponto ou vírgula.
    define($valor, string);
    $posicao = strpos($valor, '.');
    try {
        if (!$posicao) {
            $posicao = strpos($valor, ',');
            if (!$posicao) {
                $valor.= '.00';
            } else {
                throw new Exception('possui vírgula.'); // Gera excessão se possuir vírgula.
            }
        } else {
            throw new Exception('possui ponto.'); // Gera excessão se possuir ponto.
        }
    } catch (Exception $e) {
        if (strlen($valor) == ($posicao + 2)) {
            $valor .= '0';
        }
    }
    return $valor;
}

function moedaReal($valor) {
    return str_replace('.', ',', moeda($valor));
}

function RetornaDiaSemana($dia) {

    switch ($dia) {
        case 1:$ret = "Seg";
            break;
        case 2:$ret = "Ter";
            break;
        case 3:$ret = "Qua";
            break;
        case 4:$ret = "Qui";
            break;
        case 5:$ret = "Sex";
            break;
        case 6:$ret = "Sab";
            break;
        case 7:$ret = "Dom";
            break;
        case 0:$ret = "Dom";
            break;
    }

    return $ret;
}

function RetornaOptionsCompetenciaFixa($escolhida = '', $quant_mes = 8) {
    $options = "<option selected value=\"\">Selecione</option>";

    $flag_passou = 0;
    $flag_entrou = 0;

    $mes_atual = date("m");
    $ano_atual = date("Y");
    $dia_atual = date("d");

    for ($i = 1; $i <= $quant_mes; $i++) {

        if ($dia_atual > 21 && $flag_passou == 0) {
            if ($mes_atual == 12) {
                $mes_atual_proximo = "01";
            } else {
                $mes_atual_proximo = $mes_atual + 1;
                $mes_atual_proximo = "0" . $mes_atual_proximo;
                $label_mes_proximo = RetornaNomeMes($mes_atual_proximo);
                $options .= "<option value=\"$ano_atual-$mes_atual_proximo\">$label_mes_proximo/$ano_atual</option>";
                $flag_passou = 1;
            }
        }

        if (strlen($mes_atual) == 1)
            $mes_atual = "0" . "$mes_atual";
        //echo "$escolhida == $ano_atual-$mes_atual<br>";
        if ($mes_atual == 12) {
            $ano = $ano_atual + 1;
            if ($escolhida == "$ano-01") {
                $selected = "";
            } else {
                $selected = "";
            }
        } else {
            if ($escolhida == "$ano_atual-$mes_atual") {
                $selected = "selected";
            } else {
                $selected = "";
            }
        }

        $label_mes = RetornaNomeMes($mes_atual);
        //echo $mes_atual;
        if ($flag_entrou <> 1) {
            if ($mes_atual == 1 && $flag_entrou <> 1) {
                $selected = "selected";
                $ano_atual_adicional = $ano_atual;
                $label = RetornaNomeMes(01);
                $options .= "<option $selected value=\"$ano_atual_adicional-01\">$label/$ano_atual_adicional</option>";
                $flag_entrou = 1;
            } else {
                if ($flag_entrou <> 1)
                    if ($dia_atual > 21 && $mes_atual == "12") {
                        $ano_atual_adicional = $ano_atual + 1;
                        $label = RetornaNomeMes("01");
                        $options .= "<option $selected value=\"$ano_atual_adicional-01\">$label/$ano_atual_adicional</option>";
                    } else {
                        $label = RetornaNomeMes($mes_atual);
                        $options .= "<option $selected value=\"$ano_atual-$mes_atual\">$label/$ano_atual</option>";
                        $flag_entrou = 1;
                    }
            }
        } else {
            $options .= "<option $selected value=\"$ano_atual-$mes_atual\">$label_mes/$ano_atual</option>";
        }

        $mes_atual--;

        if ($mes_atual == 0) {
            $mes_atual = 12;
            $ano_atual--;
        }
    }
    return $options;
}

function sql_to_date($datasql) {

    $datasql = trim($datasql);

    $data = $datasql;
    $tempo = '';

    if (strlen($datasql) > 10) {

        list($data, $tempo) = @explode(' ', $datasql);

        $data = trim($data);
        $tempo = trim($tempo);
    }

    list($ano, $mes, $dia) = @explode('-', $data);

    $retorno = "$dia/$mes/$ano";
    if ($tempo != '')
        $retorno .= " $tempo";

    return $retorno;
}

function date_to_sql($dt) {
    list($dia, $mes, $ano) = explode("/", substr(trim($dt), 0, 10));
    $dtsql = "$ano-$mes-$dia" . substr(trim($dt), 10, strlen(trim($dt)));
    return trim($dtsql);
}

function date_to_sql_com_hora($dt) {

    list($dia, $mes, $ano) = explode('/', $dt);

    $separar = explode(' ', $ano);
    $ano = $separar[0];
    $hora = $separar[1];

    $dtsql = $ano . '-' . $mes . '-' . $dia . ' ' . $hora;

    return trim($dtsql);
}

function converte_data_curta($dt) { //tem que vir no formato sql
    if (trim($dt) == '') {
        return '';
    };
    $yr = strval(substr($dt, 0, 4));
    $mo = strval(substr($dt, 5, 2));
    $da = strval(substr($dt, 8, 2));
    $hr = strval(substr($dt, 11, 2));
    $mi = strval(substr($dt, 14, 2));
    return date("d/m H:i", mktime($hr, $mi, 0, $mo, $da, $yr));
}

function ConverteHoraMysql($hora, $formato = 'normal') {
    switch ($formato) {
        case 'normal':
            if (!empty($hora) && $hora != "00:00:00") {
                $hora = substr($hora, 0, -3);
            } else {
                $hora = "";
            }

            break;

        case 'mysql':

            break;
    }

    return $hora;
}

function RetornaMinutoHora($tempo, $tipo = "minuto") {

    switch ($tipo) {
        case "minuto":
            if (empty($tempo)) {
                $minutos = 0;
            } else {
                $tempo = explode(":", $tempo);
                $minutos = ($tempo[0] * 60) + $tempo[1];
            }
            return $minutos;
            break;

        case "hora":
            if (empty($tempo)) {
                $hora = "00:00";
            } else {
                $tempo_hora = floor($tempo / 60);
                $tempo_minuto = $tempo % 60;
                if ($tempo_hora < 10) {
                    $tempo_hora = "0" . $tempo_hora;
                }
                if ($tempo_minuto < 10) {
                    $tempo_minuto = "0" . $tempo_minuto;
                }
                $hora = "$tempo_hora:$tempo_minuto";
            }
            return $hora;
            break;
    }
}

function RetornaDiaSemanaPorData($data, $tipo = 'completo') {
    $ano = substr("$data", 0, 4);
    $mes = substr("$data", 5, -3);
    $dia = substr("$data", 8, 9);

    $diasemana = date("w", mktime(0, 0, 0, $mes, $dia, $ano));

    switch ($diasemana) {
        case"0": $diasemana = $tipo == 'completo' ? "Domingo" : "Dom";
            break;
        case"1": $diasemana = $tipo == 'completo' ? "Segunda-Feira" : "Seg";
            break;
        case"2": $diasemana = $tipo == 'completo' ? "Ter?a-Feira" : "Ter";
            break;
        case"3": $diasemana = $tipo == 'completo' ? "Quarta-Feira" : "Qua";
            break;
        case"4": $diasemana = $tipo == 'completo' ? "Quinta-Feira" : "Qui";
            break;
        case"5": $diasemana = $tipo == 'completo' ? "Sexta-Feira" : "Sex";
            break;
        case"6": $diasemana = $tipo == 'completo' ? "S?bado" : "Sab";
            break;
    }

    return "$diasemana";
}

function MontaArvoreMenus($vet_menu, $pai, $nivel, $vet_relacionamento, $estilo = '', $inicio = '', $caminho_raiz = '') {
    $trs = "";
    foreach ($vet_menu as $key => $v) {
        $menu_id = $v['id'];
        $menu_nome = $v['nome'];
        $menu_pai = $v['pai'];
        if (($menu_pai == $pai && empty($inicio))) {
            //echo ok;exit;			
            $espacamento = "";
            if ($nivel == 1) {

                if ($estilo == "tr_cor_cinza") {
                    $estilo = "tr_cor_branco";
                } else {
                    $estilo = "tr_cor_cinza";
                }
            } else {
                for ($i = 1; $i < $nivel; $i++) {
                    $espacamento.="&nbsp;&nbsp;&nbsp;&nbsp;&middot;&nbsp;&nbsp;&nbsp;&nbsp;";
                }
                $espacamento = "$espacamento ";
            }

            $tabela_encontrado = BuscaItemRelacionamento($vet_relacionamento, $menu_id);

            if ($tabela_encontrado != "nenhum") {
                $onclick = "onclick=\"alert('Exclusão não permitida.\\nItem utilizado na(s) seguinte(s) tabela(s): \\n\\n $tabela_encontrado')\"";
                $src = "src=\"{$caminho_raiz}imagens/del_disabled.gif\"";
            } else {
                $onclick = "onclick=\"ExcluirItem('$menu_id', '$menu_nome', 'menu', 'men_id', 'Menu')\"";
                $src = "src=\"{$caminho_raiz}imagens/del.gif\"";
            }

            $trs.="<tr class=\"$estilo\">";
            $trs.="<td align=\"center\" nowrap>$menu_id</td>";
            $trs.="<td align=\"left\" nowrap>{$espacamento}$menu_nome</td>";
            $trs.="<td><a href=\"?mainapp=aplicativo&app=cadastro_submenu&menu=$menu_id\"><img title=\"Ver Submenus\" src=\"{$caminho_raiz}imagens/lupa.gif\" border=0 /></a></td>";
            $trs.="<td><img style=\"cursor:pointer;\" onclick=\"EditarItem('$menu_id', 'men_', 'menu', 'Menu', 'men_resumo')\" title=\"Editar\"  src=\"{$caminho_raiz}imagens/bt_editar.gif\" border=0 /></td>";
            $trs.="<td><img style=\"cursor:pointer;\" $onclick title=\"Excluir\" $src border=0 /></td>";
            $trs.="</tr>";
            $retorno = MontaArvoreMenus($vet_menu, $menu_id, $nivel + 1, $vet_relacionamento, $estilo, $inicio, $caminho_raiz);
            $trs.=$retorno;

            if (!empty($retorno)) {
                $trs.="<tr class=\"$estilo\">";
                $trs.="<td>-</td><td colspan=4 align=\"left\">$espacamento<i>-------- FIM --------</i></td>";
                $trs.="</tr>";
            }
        }
    }
    return $trs;
}

function valorOrdinal($num, $f) { // Transforma um número numa posição cardinal. Ex. 1 -> Primeiro, 2 -> Segundo, 31 -> Trigésimo primeiro.
// Variável $F define se o resultado será um valor Feminino.
    $num = "" . $num;
    $caracs = strlen($num); // Captura o número de caracteres.

    if ($f == false) {
        $a = Array('', 'primeiro', 'segundo', 'terceiro', 'quarto', 'quinto', 'sexto', 'sétimo', 'oitavo', 'nono');
        $a1 = Array('', 'Primeiro', 'Segundo', 'Terceiro', 'Quarto', 'Quinto', 'Sexto', 'Sétimo', 'Oitavo', 'Nono');
        $b = Array('', 'décimo', 'vigésimo', 'trigésimo', 'quadragésimo', 'quinquagésimo', 'sexagésimo', 'septuagésimo', 'octagésimo', 'nonagésimo');
        $c = Array('', 'centésimo', 'ducentésimo', 'trecentésimo', 'quadringentésimo', 'quingentésimo', 'seiscentésimo', 'setigentésimo', 'octingentésimo', 'nongentésimo');
    } else {
        $a = Array('', 'primeira', 'segunda', 'terceira', 'quarta', 'quinta', 'sexta', 'sétima', 'oitava', 'nona');
        $a1 = Array('', 'Primeira', 'Segunda', 'Terceira', 'Quarta', 'Quinta', 'Sexta', 'Sétima', 'Oitava', 'Nona');
        $b = Array('', 'décima', 'vigésima', 'trigésima', 'quadragésima', 'quinquagésima', 'sexagésima', 'septuagésima', 'octagésima', 'nonagésima');
        $c = Array('', 'Centésima', 'Ducentésima', 'Trecentésima', 'Quadringentésima', 'Quingentésima', 'Seiscentésima', 'Setigentésima', 'Octingentésima', 'Nongentésima');
    }

    switch ($caracs) {
        case 3:
            $valor = $c[$num[0]];
            $valor .= " " . $b[$num[1]];
            $valor .= " " . $a[$num[2]];
            break;

        case 2:
            $valor = $b[$num[0]];
            $valor .= " " . $a[$num[1]];
            break;

        case 1:
            $valor = $a1[$num[0]];
            break;
    }
    return $valor;
}

/**
 * Funcao para o retonar a data do proximo dia da semana informado
 * @author Danilo Azevedo
 * @version 1.0
 * @param string $dia 
 * @return string
 * 
 */
function ProximaDataSemana($dia) {
    switch ($dia) {
        case "dom": $dia_escolhido = "0";
            break;
        case "seg": $dia_escolhido = "1";
            break;
        case "ter": $dia_escolhido = "2";
            break;
        case "qua": $dia_escolhido = "3";
            break;
        case "qui": $dia_escolhido = "4";
            break;
        case "sex": $dia_escolhido = "5";
            break;
        case "sab": $dia_escolhido = "6";
            break;

        default:
            break;
    }

    $data_completa = date("Y-m-d");
    do {
        $data_completa = date("Y-m-d", strtotime(date("Y-m-d", strtotime($data_completa)) . " +1 day"));

        $dia_semana = date("w", strtotime($data_completa));
    } while ($dia_semana != $dia_escolhido);


    return $data_completa;
}

/*
  ------------------------------------------------------------------------------------------
  /**
 * Funcões de Alerta
 * @author Jorge Luiz - DGE - Matriz
 * @version 1.0
 * Requisitos: Inclusão do CSS: [SIGO_RAIZ]/includes/biblioteca/jquery/css/redmond/jquery-ui-1.7.2.custom.css
 * 
 * Descricao: Funções de Alerta para operacoes realizadas com Sucesso(ok), operacoes com alertas(aviso) e operacoes com erro(erro).
 */

function ok($msg) { // Imprime a mesagem de sucesso dentro de uma caixa persolanizada
    $msg = <<< EOF
	<div class="ui-state-default ui-corner-all" style="margin-top: 20px; padding: 0 .7em; font:12px Verdana, Geneva, sans-serif;" align="left" id="div_ok" > 
    	<p><span class="ui-icon ui-icon-check" style="float: left; margin-right: .3em;" ></span>{$msg}</p>
	</div>
EOF;
    echo $msg;
}

function erro($msg) { // Imprime a mesagem de erro dentro de uma caixa persolanizada
    $msg = <<< EOF
	<div class="ui-state-error ui-corner-all" style="margin-top: 20px; padding: 0 .7em; font:12px Verdana, Geneva, sans-serif;" align="left" id="div_erro" > 
    	<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;" ></span>{$msg}</p>
	</div>
EOF;
    echo $msg;
}

function aviso($msg) { // Imprime a mesagem de aviso dentro de uma caixa persolanizada
    $msg = <<< EOF
	<div class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0 .7em; font:12px Verdana, Geneva, sans-serif;" align="left"  > 
      	<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;" ></span>{$msg}</p>
     </div>
EOF;
    echo $msg;
}

// -------------------------------- Fim Funcoes Alerta -----------------------------------

function valint($str) { // Limpa caracteres diferentes de número de uma string e retorna um número inteiro.
    return intval(preg_replace('/[[:alpha:]]*|[[:punct:]]*|[[:space:]]*/', '', $str)); // Uso de Expressão Regular.
}

function link_sip($f) { // gera link para softfone, x-lite, caso Não seja um número válido aenas mostra as informações
    $imagem = '<img src="../imagens/Crystal_Clear_app_sipphone.png" width="20" height="20" alt="Ligar para ' . $f . '" border=0 />';
    $t = substr(preg_replace('/[[:punct:]]*|[[:alpha:]]*|[[:space:]]*/', '', $f), -10, 10);
    $ddd = substr($t, 0, 2);
    $fone = substr($t, 2, 8);
    if (($ddd > 60) and ($ddd < 70) and ( strlen(trim($fone)) == 8 )) {
        if (($ddd == 67) and ((substr($fone, 0, 2) == '33') or (substr($fone, 0, 1) <> '3'))) {
            $f = '<a href="sip:' . $fone . '@10.67.149.11" title="Ligar para ' . $f . '">' . $f . '&nbsp;' . $imagem . '</a>';
        } else {
            $f = '<a href="sip:0' . $ddd . $fone . '@10.67.149.11" title="Ligar para ' . $f . '">' . $f . '&nbsp;' . $imagem . '</a>';
        };
    };
    return $f;
}

;

/**
 * Funcao para somar dias, meses e/ou anos a uma data. (Formatos: dd/mm/yyyy e yyyy/mm/dd)
 * @author Lud Akell
 * @version 1.0
 * @obs: Aceita data nos formatos dd/mm/yyyy e yyyy/mm/dd
 */
function SomaData($data, $dias = 0, $meses = 0, $ano = 0) {
    $data = trim($data);
    $data = explode("/", $data);

    //se nao havia o caracter "/", ira separar por "-"
    if (!isset($data[1]))
        $data = explode("-", $data[0]);

    $newData = "";

    //identifica o formato da data
    if (strlen($data[0]) == 2) {
        //formato dd/mm/yyyy 
        $newData = date("d/m/Y", mktime(0, 0, 0, $data[1] + $meses, $data[0] + $dias, $data[2] + $ano));
    } else if (strlen($data[0]) == 4) {
        //formato yyyy/mm/dd 
        $newData = date("d/m/Y", mktime(0, 0, 0, $data[1] + $meses, $data[2] + $dias, $data[0] + $ano));
    }

    return $newData;
}

/**
 * Abre conexao com o banco de dados utilizando a classe mysqli para controle de transações.<br />
 * <br />
 * Todos os parametros são opcionais.<br />
 * <br />
 * Nenhum comando enviado a base de dados será confirmado caso não seja executado o commit.<br />
 * <br />
 * Os comandos devem ser enviados à base com utilização das funções especificas da classe mysqli<br />
 * que já vem habilitada no php.ini desde a versão 4.1 do php.<br />
 * <br />
 * Para mais informações sobre os comandos da classe consulte o <a href="http://br3.php.net/manual/pt_BR/book.mysqli.php">manual do php</a><br />
 * <br />
 * 
 * @author Walter Thiago
 * @version 1.0 
 * 
 * @param String $pc
 * @param String $dataBaseName
 * 
 * @return Socket $conexao
 * 		Conexao com o banco de dados estabelecido ou mensagem de erro matando a execução do código caso não seja possivel
 * 		estabelecer conexão.
 * 
 */
function abreConexaoMySqlI($pc, $dataBaseName, $autoComit = false) {

    switch ($pc) {
        case 2900:
            $host = '10.67.149.17';
            $user = 'root';
            $pass = 'S1g027#ZZ3FRR';
            break;

        case 2950:
            $host = '10.67.149.18';
            $user = 'root';
            $pass = 'S1g027#ZZ3FRR';
            break;

        case 1900:
            $host = '10.67.149.16';
            $user = 'root';
            $pass = 'S1g027#,ZZ3FRR';
            break;

        case 'sigoaline':
            $host = '10.67.149.15';
            $user = 'walter';
            $pass = '448300';
            break;

        case 'serveradsl':
            $host = '192.168.1.22';
            $user = 'root';
            $pass = 'qwerty';
            break;

        case 'serverdge':
            $host = '192.168.5.220';
            $user = 'dge';
            $pass = '@dge2011%_';
            break;

        case 'serverrj':
            $host = '172.17.51.100';
            $user = 'dge';
            $pass = '$dge_';
            break;

        case 'vm_adsl_dados':
            $host = '192.168.5.220';
            $user = 'apache_web';
            $pass = '09KJL09lkjIPOU90';
            break;


        case 'serversp':
            $host = '172.18.1.101';
            $user = 'root';
            $pass = 'dge@2011%_';
            break;

        case 'local':
            $host = '127.0.0.1';
            $user = 'root';
            $pass = '';
            break;

        default:
            break;
    }

    //Estabelece conexao com o banco de dados.
    $conexao = mysqli_connect($host, $user, $pass, $dataBaseName) or die('Erro ao conectar-se a base de dados. ' . mysqli_error($conn));
    mysqli_autocommit($conexao, $autoComit);
    return $conexao;
}

/**
 * Encerra conexao da classe mysqli com o banco de dados.<br />
 * <br />
 * @author Walter Thiago
 * @version 1.0
 * @param Socket $conexao
 * 
 * @return void
 * 
 */
function fechaConexaoMySqlI($conexao) {
    @mysqli_close($conexao);
}

/**
 * 
 * @param $numero
 * @param $campoTipo
 * @param $tipoFormato
 */
function formataNumeroPorTipo($numero, $campoTipo, $valorCampo, $casasDecimais = 2, $extenso = true) {

    if ($numero == "" || $numero == null || !is_numeric($numero)) {
        $numero = 0;
    }

    switch ($campoTipo) {
        case "id":

            switch ($valorCampo) {
                case 1:
                    //Reais - R$
                    if ($extenso)
                        return " R$ " . number_format($numero, $casasDecimais, ',', '.');
                    else
                        return round($numero, $casasDecimais);
                case 2:
                    //Percentual
                    if ($extenso)
                        return number_format($numero, $casasDecimais, ',', '.') . " %";
                    else
                        return round($numero, $casasDecimais);
                case 3:
                    //Valor inteiro
                    return round($numero, 0);
                case 4:
                    //Valor real
                    if ($extenso)
                        return number_format($numero, $casasDecimais, ',', '.');
                    else
                        return round($numero, $casasDecimais);
                default:
                    throw new Exception("Valor de campo indefinido.");
                    break;
            }

            break;
        case "nome":
            switch ($valorCampo) {
                case "Reais":
                    //Reais - R$
                    if ($extenso)
                        return " R$ " . number_format($numero, $casasDecimais, ',', '.');
                    else
                        return round($numero, $casasDecimais);
                case "Percentual":
                    //Percentual
                    if ($extenso)
                        return number_format($numero, $casasDecimais, ',', '.') . " %";
                    else
                        return round($numero, $casasDecimais);
                case "Valor Inteiro":
                    //Valor inteiro
                    return $numero;
                case "Valor Real":
                    //Valor real
                    if ($extenso)
                        return number_format($numero, $casasDecimais, ',', '.');
                    else
                        return round($numero, $casasDecimais);
                default:
                    throw new Exception("Valor de campo indefinido.");
                    break;
            }
            break;
        case "sigla":
            switch ($valorCampo) {
                case "R$":
                    //Reais - R$
                    if ($extenso)
                        return " R$ " . number_format($numero, $casasDecimais, ',', '.');
                    else
                        return round($numero, $casasDecimais);
                case "%":
                    //Percentual
                    if ($extenso)
                        return number_format($numero, $casasDecimais, ',', '.') . " %";
                    else
                        return round($numero, $casasDecimais);
                case "I":
                    //Valor inteiro
                    return $numero;
                case "R":
                    //Valor real
                    if ($extenso)
                        return number_format($numero, $casasDecimais, ',', '.');
                    else
                        return round($numero, $casasDecimais);
                default:
                    throw new Exception("Valor de campo indefinido.");
                    break;
            }
            break;
        default:
            throw new Exception("Tipo de campo indefinido.");
            break;
    }
}

function GeraOptionMes($selected = '') {

    $select[$selected] = 'selected="selected"';

    $option_mes = '
		<option ' . $select[1] . ' value="1">JANEIRO</option>
		<option ' . $select[2] . ' value="2">FEVEREIRO</option>
		<option ' . $select[3] . ' value="3">MARÇO</option>
		<option ' . $select[4] . ' value="4">ABRIL</option>
		<option ' . $select[5] . ' value="5">MAIO</option>
		<option ' . $select[6] . ' value="6">JUNHO</option>
		<option ' . $select[7] . ' value="7">JULHO</option>
		<option ' . $select[8] . ' value="8">AGOSTO</option>
		<option ' . $select[9] . ' value="9">SETEMBRO</option>
		<option ' . $select[10] . ' value="10">OUTUBRO</option>
		<option ' . $select[11] . ' value="11">NOVEMBRO</option>
		<option ' . $select[12] . ' value="12">DEZEMBRO</option>';

    return $option_mes;
}

function GeraOptionAno($selected = '') {

    /**
     * Senhores, favor atualizar em 2015
     * Nao sou o autor da função, mas a utilizei com louvor.
     * Walter.Reis - 26/02/2013
     */
	 
    $select[$selected] = 'selected="selected"';

	$cont_ano = date("Y");
	for($i = 0; $i < 7; $i++){
		
		$option_ano .= <<< EOF
			<option {$select[$cont_ano]} value="{$cont_ano}">{$cont_ano}</option>
EOF;

		$cont_ano--;
	}
	
    return $option_ano;
}

function retornaTotalSemanasPeriodo($periodo_inicial = null, $periodo_final = null) {

    //Validação das datas

    $pos = strpos($periodo_inicial, '-');
    $delimitadorIni = "";
    if ($pos === false) {
        $pos = strpos($periodo_inicial, '/');
        if ($pos === false) {
            die('Data Inicial inválida');
        } else {
            $delimitadorIni = "/";
        }
    } else {
        $delimitadorIni = "-";
    }

    $pos = strpos($periodo_final, '-');
    $delimitadorFin = "";
    if ($pos === false) {
        $pos = strpos($periodo_final, '/');
        if ($pos === false) {
            die('Data Inicial inválida');
        } else {
            $delimitadorFin = "/";
        }
    } else {
        $delimitadorFin = "-";
    }

    $dataI = explode("$delimitadorIni", "$periodo_inicial");
    $dI = $dataI[0];
    $mI = $dataI[1];
    $yI = $dataI[2];
    if (!checkdate($mI, $dI, $yI)) {
        die('Data inicial inválida');
    }

    $dataF = explode("$delimitadorFin", "$periodo_final");
    $dF = $dataF[0];
    $mF = $dataF[1];
    $yF = $dataF[2];
    if (!checkdate($mF, $dF, $yF)) {
        die('Data final inválida');
    }

    $timeStampI = mktime(0, 0, 0, $mI, $dI, $yI);
    $timeStampF = mktime(0, 0, 0, $mF, $dF, $yF);

    $contaSemanas = 0;

    $semana = date('W', $timeStampI);

    while ($timeStampI < $timeStampF) {

        $semanaAnt = date('W', $timeStampI);
        if ($semanaAnt != $semana) {
            $contaSemanas++;
        }
        $semana = $semanaAnt;
        $dI++;
        $timeStampI = mktime(0, 0, 0, $mI, $dI, $yI);
    }
    return $contaSemanas;
}

function soma_data($data, $dia = 0, $mes = 0, $ano = 0) {
    $cd = strtotime($data);
    $newdate = date('Y-m-d h:i:s', mktime(date('h', $cd), date('i', $cd), date('s', $cd), date('m', $cd) + $mes, date('d', $cd) + $dia, date('Y', $cd) + $ano));
    return $newdate;
}

/**
 *
 * 	Se data1 maior que data2 a função retorna "true", senão retorna "false".
 * 	Obs: independe se a data está no formato AAAA-MM-DD ou DD/MM/AAAA.
 */
function data1_maior($data1, $data2) {
    // se data no formato DD/MM/AAAA então converte para AAAA-MM-DD
    if (strstr($data1, "/")) {
        $aux = explode("/", $data1);
        $data1 = $aux[2] . "-" . $aux[1] . "-" . $aux[0];
    }
    // se data no formato DD/MM/AAAA então converte para AAAA-MM-DD
    if (strstr($data2, "/")) {
        $aux = explode("/", $data2);
        $data2 = $aux[2] . "-" . $aux[1] . "-" . $aux[0];
    }
    // verifica se data1 é maior que data2
    if ($data1 > $data2)
        return true;
    else
        return false;
}

/**
 * Função para formatar número decimal do banco (transforma ponto em vírgula). 
 * Se o número não tiver casas decimais, devolve o próprio número.
 * @author Lud Akell
 * @version 1.0
 * @param float $numero     
 * @param int $casasDecimais
 */
function FormataNumDecimal($numero, $casasDecimais) {
    $confereExisteDecimal = explode('.', $numero);

    if (count($confereExisteDecimal) == 1)
        return $numero;
    else
        return number_format($numero, $casasDecimais, ',', '.');
}

/**
 * Função para formatar número decimal para o banco (transforma vírgula em ponto). 
 * Se o número não tiver casas decimais, devolve o próprio número.
 * @author Lud Akell
 * @version 1.0
 * @param float $numero     
 * @param int $casasDecimais
 */
function FormataNumDecimalParaBD($numero, $casasDecimais) {
    $confereExisteDecimal = explode(',', $numero);

    if (count($confereExisteDecimal) == 1)
        return $numero;
    else {
        //Se o número tiver ponto na casa de milhar, retira esse ponto
        $numero = str_replace(".", "", $numero);

        //Transforma a vírgula em ponto
        $numero = str_replace(",", ".", $numero);

        //Formata o número, acrescentando o total de casas decimais
        return number_format($numero, $casasDecimais, '.', '');
    }
}

/**
 * 
 * @param $sDataInicial - String com a data inicial
 * @param $sDataFinal - String com a data final
 * @param $formato_data - formato das datas. Pode ser BR (Formato brasileiro - dd/mm/YYYY) ou MYSQL (Formato de data do mysql YYYY/mm/dd)
 * 
 * @returns Retorna a diferença entre os dias sempre positivo.
 * 
 */
function dateDiff($sDataInicial, $sDataFinal, $formato_data = 'BR') {

    //die('Data inicial: '.$sDataInicial);
    //die('Data final: '.$sDataFinal);
    //die('Formato de data: '.$formato_data);

    if ($formato_data == 'BR') {
        list($dI, $mI, $aI) = explode("/", $sDataInicial);
        list($dF, $mF, $aF) = explode("/", $sDataFinal);
    } elseif ($formato_data == 'MYSQL') {
        list($aI, $mI, $dI) = explode("-", $sDataInicial);
        list($aF, $mF, $dF) = explode("-", $sDataFinal);
    } else {
        die('Formato de data inesperado.');
    }

    /* echo "<pre>";
      print_r(array($aI,$mI,$dI));
      exit(); */

    $nDataInicial = mktime(0, 0, 0, $mI, $dI, $aI);
    $nDataFinal = mktime(0, 0, 0, $mF, $dF, $aF);

    return ($nDataInicial > $nDataFinal) ? floor(($nDataInicial - $nDataFinal) / 86400) : floor(($nDataFinal - $nDataInicial) / 86400);
}

/**
 * Faz o tratamento do post removendo espacos vazios, tags maliciosas e escapando aspas simples.
 * Deve ser chamado somente em no envio de forms via post
 */
function trataPost($conn) {

    $form = $_POST;
    $formTratado = null;

    if ($form) {

        $formTratado = array();

        foreach ($form as $field => $value) {
            if (!is_array($value)) {
                $formTratado[$field] = mysqli_real_escape_string($conn, strip_tags(trim($value), '<>'));
            } else {
                $array_tmp = array();
                foreach ($value as $v) {
                    $array_tmp[] = mysqli_real_escape_string($conn, strip_tags(trim($v), '<>')); //$v."--";
                }
                $formTratado[$field] = $array_tmp;
            }
        }
    }

    return $formTratado;
}

/**
 * Faz o tratamento do get removendo espacos vazios, tags maliciosas e escapando aspas simples.
 * Deve ser chamado somente em no envio de forms via get
 */
function trataGet($conn) {

    $url = $_GET;
    $urlTratada = null;

    if ($url) {

        $urlTratada = array();

        foreach ($url as $field => $value) {
            if (!is_array($value)) {
                $urlTratada[$field] = mysqli_real_escape_string($conn, strip_tags(trim(urldecode($value)), '<>'));
            } else {
                $array_tmp = array();
                foreach ($value as $v) {
                    $array_tmp[] = mysqli_real_escape_string($conn, strip_tags(trim(urldecode($v)), '<>')); //$v."--";
                }
                $urlTratada[$field] = $array_tmp;
            }
        }
    }

    return $urlTratada;
}

function AdicionarDiaData($date, $days) {
    $thisyear = substr($date, 0, 4);
    $thismonth = substr($date, 5, -3);
    $thisday = substr($date, 8);
    $nextdate = mktime(0, 0, 0, $thismonth, $thisday + $days, $thisyear);
    return strftime("%Y-%m-%d", $nextdate);
}

function SubtraiDiaData($date, $days) {
    $thisyear = substr($date, 0, 4);
    $thismonth = substr($date, 5, -3);
    $thisday = substr($date, 8);
    $nextdate = mktime(0, 0, 0, $thismonth, $thisday - $days, $thisyear);
    return strftime("%Y-%m-%d", $nextdate);
}

/**
 * 
 * @param $sDataInicial
 * @param $sDataFinal
 * @return inteiro mostrando a diferença entre os dias inicial e final independente da ordem em que foram passados. 
 */
function dateDiffHoraAdsl($sDataInicial, $sDataFinal) {
    list($anoI, $mesI, $diaI) = explode("-", trim($sDataInicial));
    list($anoF, $mesF, $diaF) = explode("-", $sDataFinal);

    $nDataInicial = mktime(0, 0, 0, $mesI, $diaI, $anoI);
    $nDataFinal = mktime(0, 0, 0, $mesF, $diaF, $anoF);

    return (($nDataInicial > $nDataFinal) ?
                    floor(($nDataInicial - $nDataFinal) / 86400) : floor(($nDataFinal - $nDataInicial) / 86400));
}

/**
 * 
 * @param A data a ser comparada. Deve estar no formato string em 'Y-m-d' $data_referencia
 * @param A data inicial de comparacao. Deve estar no formato string em 'Y-m-d' $data_inicial
 * @param A data final de comparacao. Deve estar no formato string em 'Y-m-d' $data_final
 */
function between($data_referencia, $data_inicial, $data_final) {
    if (!isset($data_referencia) || !isset($data_inicial) || !isset($data_final)) {
        return false;
    }

    list($aR, $mR, $dR) = @explode('-', $data_referencia);
    list($aI, $mI, $dI) = @explode('-', $data_inicial);
    list($aF, $mF, $dF) = @explode('-', $data_final);
    if (!@checkdate((int) $mR, (int) $dR, (int) $aR) || !@checkdate((int) $mI, (int) $dI, (int) $aI) || !@checkdate((int) $mF, (int) $dF, (int) $aF)) {
        return false;
    }

    $tsR = @mktime(0, 0, 0, $mR, $dR, $aR);
    $tsI = @mktime(0, 0, 0, $mI, $dI, $aI);
    $tsF = @mktime(0, 0, 0, $mF, $dF, $aF);

    if ($tsR >= $tsI && $tsR <= $tsF) {
        return true;
    } else {
        return false;
    }
}

function extenderPermissoes($host, $data_base, $chapa, $string_modulo) {
    $conecta = RetornaConexaoMysql($host, $data_base);
    $condicao_grupo = array();
    $grupos = array();
    $id_grupos = "";
    $where = "";

    $qry_busca = "SELECT  * 
                    FROM tbl_substituicao_ponto 
                    WHERE sp_chapa_substituicao = " . $chapa;
    $rs_busca = mysqli_query($conecta, $qry_busca);

    while ($row_busca = mysqli_fetch_assoc($rs_busca)) {
        $chapa_substituido = $row_busca["sp_chapa_superior"];
        $chapa_substituto = $row_busca["sp_chapa_substituicao"];

        if (date("Y-m-d") >= $row_busca['sp_data_inicio'] && date("Y-m-d") <= $row_busca['sp_data_final']) {
            if ($row_busca['sp_tipo_substituicao'] == 1 || $row_busca['sp_tipo_substituicao'] == 11) {
                extenderPermissoesPonto($conecta, $chapa_substituido, $chapa_substituto, $row_busca['sp_fim_periodo_retorno']);
            }
            if ($row_busca['sp_tipo_substituicao'] == 10 || $row_busca['sp_tipo_substituicao'] == 11) {
                extenderPessoasPonto($conecta, $chapa_substituido, $chapa_substituto, $row_busca['sp_fim_periodo_retorno']);
            }
        } else if ($row_busca['sp_data_final'] < date("Y-m-d")) {
            if ($row_busca['sp_tipo_substituicao'] == 1 || $row_busca['sp_tipo_substituicao'] == 11) {
                retornarPermissoesPonto($conecta, $chapa_substituido, $chapa_substituto, $row_busca['sp_fim_periodo_retorno']);
            }
            if ($row_busca['sp_tipo_substituicao'] == 10 || $row_busca['sp_tipo_substituicao'] == 11) {
                retornarPessoasPonto($conecta, $chapa_substituido, $chapa_substituto, $row_busca['sp_fim_periodo_retorno'], $row_busca["sp_id"]);
            }
        }
    }
}

function extenderPessoasPonto($conn, $chapa_substituido, $chapa_substituto, $fim_periodo) {
    if ($fim_periodo == "0") {
        $sqlGetSubordinados = "SELECT fpo_id FROM modulo_ponto.tbl_funcionario_ponto WHERE fpo_supervisor = '" . $chapa_substituido . "'";
        $exeGetSubordinados = mysqli_query($conn, $sqlGetSubordinados) OR DIE(mysqli_error($conn));

        $ids = array();
        while ($row = mysqli_fetch_assoc($exeGetSubordinados)) {
            $ids[] = $row["fpo_id"];
            $sqlUpdateSubordinado = "UPDATE modulo_ponto.tbl_funcionario_ponto SET fpo_supervisor = '" . $chapa_substituto . "' WHERE fpo_id = " . $row["fpo_id"] . " LIMIT 1";
            mysqli_query($conn, $sqlUpdateSubordinado) OR DIE(mysqli_error($conn));
        }
        $sqlUpdateSubordinado = "UPDATE modulo_ponto.tbl_substituicao_ponto SET sp_pessoal_antigo = '" . json_encode($ids) . "' WHERE sp_chapa_substituicao = '" . $chapa_substituto . "' AND sp_chapa_superior = '" . $chapa_substituido . "' LIMIT 1";
        mysqli_query($conn, $sqlUpdateSubordinado) OR DIE(mysqli_error($conn));
    }
}

function retornarPessoasPonto($conn, $chapa_substituido, $chapa_substituto, $fim_periodo, $sp_id) {
    if ($fim_periodo == "0") {
        $sqlGetSubordinados = "SELECT sp_pessoal_antigo FROM modulo_ponto.tbl_substituicao_ponto WHERE sp_id = '" . $sp_id . "' LIMIT 1";
        $exeGetSubordinados = mysqli_query($conn, $sqlGetSubordinados) OR DIE(mysqli_error($conn));
        $rowGetSubordinados = mysqli_fetch_assoc($exeGetSubordinados);

        $ids = json_decode($rowGetSubordinados["sp_pessoal_antigo"]);
        foreach ($ids as $key => $id) {
            $sqlUpdateSubordinado = "UPDATE modulo_ponto.tbl_funcionario_ponto SET fpo_supervisor = '" . $chapa_substituido . "' WHERE fpo_id = " . $id . " LIMIT 1";
            mysqli_query($conn, $sqlUpdateSubordinado) OR DIE(mysqli_error($conn));
        }
        $sqlUpdateSubordinado = "UPDATE modulo_ponto.tbl_substituicao_ponto SET sp_fim_periodo_retorno = 1 WHERE sp_chapa_substituicao = '" . $chapa_substituto . "' AND sp_chapa_superior = '" . $chapa_substituido . "' LIMIT 1";
        mysqli_query($conn, $sqlUpdateSubordinado) OR DIE(mysqli_error($conn));
    }
}

function retornarPermissoesPonto($conn, $chapa_substituido, $chapa_substituto, $fim_periodo) {
    $permissoes_antigas = getPermissoesAntigas($chapa_substituido, $chapa_substituto);
    $fim_periodo = getFimPeriodo($chapa_substituido, $chapa_substituto);

    if (!is_null($permissoes_antigas) && $fim_periodo == "0") {
        $update_sub = " UPDATE 
                            tbl_substituicao_ponto 
                        SET 
                            sp_fim_periodo_retorno = '1'
                        WHERE
                            sp_chapa_superior = '{$chapa_substituido}' AND 
                            sp_chapa_substituicao = '{$chapa_substituto}'";
        mysqli_query($conn, $update_sub);

        $permissoes_antigas = implode("|", $permissoes_antigas);
        setPermissoesUsuario($conn, $chapa_substituto, $permissoes_antigas);
    }
}

function extenderPermissoesPonto($conn, $chapa_substituido, $chapa_substituto, $fim_periodo) {
    if ($fim_periodo == "0") {
        $grupos_substituido = getPermissoesUsuarioModulo($chapa_substituido, 'PONTO');
        $grupos_substituto = getPermissoesUsuario($chapa_substituto);

        $cont = 0;
        $novos_grupos_substituto_array = array();

        foreach ($grupos_substituido as $key => $grupo) {
            $novos_grupos_substituto[$grupo] = $cont++;
        }
        foreach ($grupos_substituto as $key => $grupo) {
            $novos_grupos_substituto[$grupo] = $cont++;
        }
        $update_sub = "	UPDATE 
		                   tbl_substituicao_ponto 
	                    SET 
						   sp_permissoes_antigas = '" . implode('|', $grupos_substituto) . "'
	                    WHERE
						   sp_chapa_superior = '{$chapa_substituido}' AND 
						   sp_chapa_substituicao = '{$chapa_substituto}'";
        mysqli_query($conn, $update_sub);
        $novo_grupo_substituto = implode('|', array_flip($novos_grupos_substituto));
        setPermissoesUsuario($conn, $chapa_substituto, $novo_grupo_substituto);
    }
}

function setPermissoesUsuario($conn, $chapa = NULL, $novo_grupo_substituto = NULL) {
    $update_gsub = "	UPDATE sigo_integrado.tbl_usuario u, sigo_integrado.tbl_rm_funcionario r SET u.gru_id = '" . $novo_grupo_substituto . "' WHERE u.fun_id = r.fun_id AND r.fun_chapa = '$chapa'";
    $gravou = mysqli_query($conn, $update_gsub);

    $conecta = RetornaConexaoMysql('local', 'sigo_integrado');
    GravaLogSentenca($conecta, $update_gsub);

    $_SESSION['SIGO']['ACESSO']['ID_GRUPO'] = $novo_grupo_substituto;
    return $gravou;
}

function getFimPeriodo($conn, $chapa_substituido = NULL, $chapa_substituto = NULL) {
    $sql_permissoes = "
		SELECT  
			sp_fim_periodo_retorno
		FROM 
			tbl_substituicao_ponto
		WHERE 
			sp_chapa_superior = '$chapa_substituido' AND
			sp_chapa_substituicao = '$chapa_substituto'";

    $rs_permissoes = mysqli_query($conn, $sql_permissoes);

    if ($rs_permissoes) {
        $row_permissoes = mysqli_fetch_assoc($rs_permissoes);
        return $row_permissoes["sp_fim_periodo_retorno"];
    }
    return NULL;
}

function getPermissoesAntigas($conn, $chapa_substituido = NULL, $chapa_substituto = NULL) {
    $sql_permissoes = "
		SELECT  
			sp_permissoes_antigas 
		FROM 
			tbl_substituicao_ponto
		WHERE 
			sp_chapa_superior = '$chapa_substituido' AND
			sp_chapa_substituicao = '$chapa_substituto'";

    $rs_permissoes = mysqli_query($conn, $sql_permissoes);

    if ($rs_permissoes) {
        $row_permissoes = mysqli_fetch_assoc($rs_permissoes);
        return explode('|', $row_permissoes["sp_permissoes_antigas"]);
    }
    return NULL;
}

function getPermissoesUsuario($conn, $chapa_busca = NULL) {
    $sql_permissoes = "
		SELECT  
			u.gru_id 
		FROM 
			sigo_integrado.tbl_usuario u INNER JOIN sigo_integrado.tbl_rm_funcionario r ON u.fun_id = r.fun_id
		WHERE 
			r.fun_chapa = '$chapa_busca'";
    $rs_permissoes = mysqli_query($conn, $sql_permissoes);

    if ($rs_permissoes) {
        $row_permissoes = mysqli_fetch_assoc($rs_permissoes);
        return explode('|', $row_permissoes["gru_id"]);
    }
    return NULL;
}

function getPermissoesUsuarioModulo($conn, $chapa_busca = NULL, $string_modulo = "") {
    $vetor_permissoes = getPermissoesUsuario($conn, $chapa_busca);
    if (!is_null($vetor_permissoes)) {
        $grupos = array();

        foreach ($vetor_permissoes as $key => $grupo) {
            if ($grupo == 2)
                $grupos[] = 2;
            $condicao_grupo[] = "u.gru_id = " . $grupo;
        }

        $WHERE = "";

        if (count($condicao_grupo) > 0) {
            $WHERE = str_replace('Array OR', '', "WHERE (" . implode(' OR ', $condicao_grupo) . ")");
        }

        $qry_verifica = "SELECT  
							*
						 FROM 
							sigo_integrado.tbl_grupo u
						 $WHERE AND u.gru_descricao LIKE '%$string_modulo%'";

        $rs_verifica = mysqli_query($conn, $qry_verifica);
        if (!$rs_verifica)
            return $grupos;

        while ($row_verifica = mysqli_fetch_assoc($rs_verifica)) {
            $grupos[] = $row_verifica['gru_id'];
        }
        return $grupos;
    }
    return NULL;
}

function verifica_ultimo_update_senha($conn, $chapa = "") {

    return true;

    $sql = "SELECT  
				IF ( ISNULL(u.usr_data_troca_senha), '0000-00-00', u.usr_data_troca_senha) AS data_ultima_troca_senha
			FROM
				tbl_usuario AS u
			INNER JOIN 
				tbl_rm_funcionario AS r ON u.fun_id = r.fun_id 
			WHERE 
				r.fun_chapa = '$chapa' AND 
				( 
					u.usr_data_troca_senha <= SUBDATE(NOW(),30) OR 
					u.usr_data_troca_senha IS NULL OR
					u.usr_data_troca_senha = '0000-00-00' OR
					u.usr_senha = MD5('123') 
				)";
    if ($result = mysqli_query($conn, $sql) OR DIE(mysqli_error($conn))) {
        $row = mysqli_fetch_assoc($result);
        return is_null($row["data_ultima_troca_senha"]);
    } else
        return FALSE;
}

function upload_geral($file = NULL, $file_path = "") {
    $old_name = $file['arquivo_fluxo']["name"];
    //var_dump($file);
    $array_name_separado = explode('.', $old_name);
    //print_r($array_name_separado);
    $nome_arquivo = md5($old_name . time()) . "." . $array_name_separado[count($array_name_separado) - 1];
	
    $destination = $file_path . $nome_arquivo;
    
    if (move_uploaded_file($file['arquivo_fluxo']['tmp_name'], $destination)) {
        return $nome_arquivo;
    }
    return NULL;
}

function Verificar_id_session_php() {

    $php_session_id = false;
    if ($_SESSION["SIGO"]["ACESSO"]["USUARIO"] == "brunomt") {
        //EnviarEmail("Marcos", "bruno.tertuliano@telemont.com.br", $_SERVER["HTTP_REFERER"]);
    }

    $cokies_vars = explode(';', $_SERVER['HTTP_COOKIE']);

    foreach ($cokies_vars as $var) {
        if (current(explode('=', $var)) == 'PHPSESSID') {
            $php_session_id = end(explode('=', $var));
            break;
        }
    }

    if (!$php_session_id) {
        $php_session_id = $_SESSION['SIGO']['ACESSO']['PHPSESSID'];
    }

    /* for ($i=0;$i<count($server);$i++){
      if (substr($server[$i],1,9) == 'PHPSESSID' or substr($server[$i],0,9) == 'PHPSESSID') {
      $php_session_id = explode('=',$server[$i]);
      break;
      }
      } */

    return $php_session_id;
}

/**
 * Função para transformar o formato de uma data do banco de dados para o formato necessário para o gráfico de Gantt DHTML
 * @author Lud Akell
 * @version 1.0
 * @obs: Não aceita data no padrão normal (dd/mm/YYYY)
 */
function DataGantt($data) {
    return str_replace('-', ',', $data);
}

function MonitoraAcesso($mainapp, $app, $idmenu = null) {
    $usr_usuario = $_SESSION['SIGO']['ACESSO']['USUARIO'];

    //$status = @explode('  ', @mysqli_stat());

    if (@sizeof($status) < 2) {
        $conexao_monitora_acesso = RetornaConexaoMysql('local', 'sigo_integrado');
    }


    if ($mainapp != "home" and $app != "home") {
        $sql = "INSERT INTO `sigo_integrado`.`tbl_monitora_acesso`
					(
						`id_menu`,
						`aplicacao`,
						`rotina`,
						`usuario`,
						`data_log`
					)
					VALUES
					(
						'{$idmenu}',
						'{$mainapp}',
						'{$app}',
						'{$usr_usuario}',
						CURRENT_TIMESTAMP
					)		";
        $conexao_monitora_acesso->query($sql) or die('Erro... ' . mysqli_error($conexao_monitora_acesso));
    }
    //Se criou uma nova, fecha a conexão
    if (@sizeof($status) < 2) {
        $conexao_monitora_acesso->close();
    }
}

function validaArray($array, $conecta_valida = NULL) {
    $tratado = array();
    $conecta_valida = is_null($conecta_valida) ? RetornaConexaoMysql("local", "sigo_integrado") : $conecta_valida;

    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $tratado[$key] = validaArray($value, $conecta_valida);
        } else {
            $tratado[$key] = strip_tags(trim($value), '<>');
        }
    }

    return $tratado;
}

function mes_extenso($valor) {
    switch ($valor) {
        case "1":
            $resultado = "JANEIRO";
            break;
        case "2":
            $resultado = "FEVEREIRO";
            break;
        case "3":
            $resultado = "MARCO";
            break;
        case "4":
            $resultado = "ABRIL";
            break;
        case "5":
            $resultado = "MAIO";
            break;
        case "6":
            $resultado = "JUNHO";
            break;
        case "7":
            $resultado = "JULHO";
            break;
        case "8":
            $resultado = "AGOSTO";
            break;
        case "9":
            $resultado = "SETEMBRO";
            break;
        case "10":
            $resultado = "OUTUBRO";
            break;
        case "11":
            $resultado = "NOVEMBRO";
            break;
        case "12":
            $resultado = "DEZEMBRO";
            break;
    }

    return $resultado;
}

/**
 *
 * @param int $quantidade - refere-se a quantos meses a função retorna
 * @param type $ano_referencia - refere-se ao ano inicial do combo
 * @param type $mes_referencia - refere-se ao mes inicial do combo
 * @return string 
 */
function RetornaOptionMesAno($quantidade = NULL, $ano_referencia = NULL, $mes_referencia = NULL, $filtro = NULL) {

    if (!$ano_referencia) {
        $ano_referencia = date("Y");
    }

    if (!$mes_referencia) {
        $mes_referencia = date("m");
    }

    if (!$quantidade) {
        $quantidade = 12;
    }


    $data_referencia = date($ano_referencia . "-" . $mes_referencia . "-" . "d");

    $select = "<select id='mesReferencia' name='mesReferencia'><option value=''>Selecione</option>";

    for ($i = 0; $i < $quantidade; $i++) {
        list($a, $m, $d) = explode('-', $data_referencia);
        $ts = mktime(0, 0, 0, $m + $i, $d, $a);
        $mes_select = str_pad($m + $i, 2, "0", STR_PAD_LEFT);
        if (($mes_select . "/" . $a) == $filtro) {
            $selected = 'selected="selected"';
        } else {
            $selected = "";
        }

        $select .= "<option $selected value='" . date('m/Y', $ts) . "'>" . date('m/Y', $ts) . "</option>";
    }

    $select .= "</select>";

    return $select;
}

function RetornaNumeroExtenso($valor = 0, $maiusculas = false) {

    //$singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
    //$plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões", "quatrilhões");

    $c = array("", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
    $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa");
    $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete", "dezoito", "dezenove");
    $u = array("", "um", "dois", "três", "quatro", "cinco", "seis","sete", "oito", "nove");

    $z = 0;
    $rt = "";

    $valor = number_format($valor, 2, ".", ".");
    $inteiro = explode(".", $valor);
    for ($i = 0; $i < count($inteiro); $i++)
        for ($ii = strlen($inteiro[$i]); $ii < 3; $ii++)
            $inteiro[$i] = "0" . $inteiro[$i];

    $fim = count($inteiro) - ($inteiro[count($inteiro) - 1] > 0 ? 1 : 2);
    for ($i = 0; $i < count($inteiro); $i++) {
        $valor = $inteiro[$i];
        $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
        $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
        $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

        $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd &&
                $ru) ? " e " : "") . $ru;
        $t = count($inteiro) - 1 - $i;
        $r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
        if ($valor == "000")
            $z++; elseif ($z > 0)
            $z--;
        if (($t == 1) && ($z > 0) && ($inteiro[0] > 0))
            $r .= (($z > 1) ? " de " : "") . $plural[$t];
        if ($r)
            $rt = $rt . ((($i > 0) && ($i <= $fim) &&
                    ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
    }

    if (!$maiusculas) {
        return($rt ? $rt : "zero");
    } else {

        if ($rt)
            $rt = preg_replace(" E ", " e ", ucwords($rt));
        return (($rt) ? ($rt) : "Zero");
    }
    
    /*
     *EXEMPLO PRINT
        $valor = 10;
        $dim = RetornaNumeroExtenso($valor);
        $dim = preg_replace(" E "," e ",ucwords($dim));
     
        $valor = number_format($valor, 2, ",", ".");

        echo "R$ $valor $dim"; 
     */
}
function debugp($valor) {
    echo"<pre>";
    print_r($valor); die();
}
function debugv($valor) {
    var_dump($valor); die();
}
?>