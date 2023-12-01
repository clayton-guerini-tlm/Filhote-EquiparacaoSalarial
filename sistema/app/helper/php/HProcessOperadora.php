<?php 

class HProcessOperadora{


    /**
     * @method Processar Arquivo da Operadora
     */
    public static function processArquivo($args,$arquivo)
    {
     
        if ($args['operadora'] == "2" && $args['layout'] =="mensalidade"){
            
            return self::processCnuMensalidade($args,$arquivo);

        } elseif ($args['operadora'] == "2" && $args['layout'] =="coparticipacao"){

            return self::processCnuCoparticipacao($args,$arquivo);

        } elseif ($args['operadora'] == "1" && $args['layout'] =="mensalidade"){

             return self::processUnimedBhMensalidade($args,$arquivo);

        } elseif ($args['operadora'] == "1" && $args['layout'] =="coparticipacao"){

            return self::processUnimedBhCoparticipacao($args,$arquivo);
        } else {

            return array(false,'Nenhum dos parâmetros');
        }

    }
 
    /**
     * @method Processar Arquivo da Operadora CNU Mensalidade
     * @param {argumentos,arquivo Operadpra}
     */

    public static  function processCnuMensalidade($args,$arquivo)
    {

        $objArray   = array();

        try {

            $dao        = new MImportArquivo();
            $listChapas = $dao->buscarChapasAtivas();
            $i          = 0;

            $hashImportancao       = rand().date('s');

            while (!feof($arquivo)){

                $linha                    = fgets($arquivo);    
                $linha                    = str_replace(PHP_EOL, '', $linha);
                $dados                    = explode(';', $linha); 
                $i++;                 
                
                /**DISPRESA HEAD DO ARQUIVO */
                if($i == 1){
                    continue;
                }    
               
                $objMen = new stdClass();
                
                if (isset($dados[6])) {
                    $objMen->chapa = HUtil::formataChapa($dados[6]);
                } else {
                    $objMen->chapa = null;
                }
                
                $objMen->operadora = $args['operadora'];

                if (isset($dados[24])) {
                    $objMen->nota = $dados[24];
                } else {
                    $objMen->nota = null;
                }
                
                $objMen->cnpj =  preg_replace("/[^0-9]/", "",Enum::cnpjOperadora($args['operadora']));

                if (isset($dados[14])) {
                    $objMen->titular = $dados[14];
                } else {
                    $objMen->titular = null;
                }
                if (isset($dados[11])) {
                    $objMen->cartao = $dados[11];
                } else {
                    $objMen->cartao = null;
                }
                if (isset($dados[14])) {
                    $objMen->beneficiario = $dados[8];
                } else {
                    $objMen->beneficiario = null;
                }
               
                if (isset($dados[21])) {
                     $objMen->valor = HUtil::formatDecimal($dados[21]);
                } else {
                     $objMen->valor = null;
                }
                if (isset($dados[11])) {
                     $objMen->credito = trim(strval($dados[22])) == '+' ? 0 : 1;
                } else {
                     $objMen->credito =  null;
                }
                  
                $objMen->competencia      = $args['competencia'].'-01';
                $objMen->incosistencia    = HUtil::validarChapa($listChapas,$objMen->chapa);
                $objMen->hashImportancao  = $hashImportancao;
  
                array_push($objArray, $objMen);
                unset($objMen) ; 
            }

            fclose($arquivo);
            unset($arquivo);
            $retorno =  $dao->salvarMensalidade($objArray);

        } catch (Exception $e) {

            return array(false,"Erro ao Salvar Registros",$e->getMessage());
        }

        return array('Nota'=>$objArray,'return'=> $retorno);
    }

    /**
     * @method Processar Arquivo da Operadora CNU Coparticipação
     * @param {argumentos,arquivo Operadpra}
     */

    public static  function processCnuCoparticipacao($args,$arquivo)
    {

         
        try {

            $dao        = new MImportArquivo();

            $objArray   = array();
            $listChapas = $dao->buscarChapasAtivas();
            $i          = 0;
            $hashImportancao        = rand().date('s'); 

                
                while (!feof($arquivo)){

                    $linha                    = fgets($arquivo);    
                    $linha                    = str_replace(PHP_EOL, '', $linha);
                    $dados                    = explode(';', $linha); 
                    $i++;                 
                
                    /**DISPRESA HEAD DO ARQUIVO */
                    if($i == 1){
                        continue;
                    }    
                   
                    $objCorp = new Coparticipacao();
    
                    $objCorp->operadora        = $args['operadora'];
                    $objCorp->cnpj             =  preg_replace("/[^0-9]/", "",Enum::cnpjOperadora($args['operadora']));
                    $objCorp->nota             = $dados[46];
                    $objCorp->competencia      = !empty($dados[38])? date('Y-d-m', strtotime($dados[38])):'';
                    $objCorp->cartao           = $dados[18];
                    $objCorp->chapa            = str_pad(trim($dados[13]), 6, 0, STR_PAD_LEFT);
                    $objCorp->titular          = $dados[17];
                    $objCorp->beneficiario     = $dados[19];
                    $objCorp->prestador        = $dados[64];
                    $objCorp->dtAtendimento    = $dados[27];
                    $objCorp->codProcedimento  = $dados[28];
                    $objCorp->descricao        = $dados[29];
                    $objCorp->valor            = number_format(str_replace(',','.',$dados[42]), 2, '.','');
                    $objCorp->incosistencia    = HUtil::validarChapa($listChapas,$objCorp->chapa);
                    $objCorp->hashImportancao  = $hashImportancao;

                    array_push($objArray, $objCorp);
                    unset($objCorp) ; 
                        
                }
                
        fclose($arquivo);
        unset($arquivo);        
        $retorno = $dao->salvarCoparticipacao($objArray);

        } catch (\Exception $e){

            return array(false,"Erro ao salvar Registros",$e->getMessage());
        }

    return array('Nota'=>$objArray[0]->hashNota,'return'=> $retorno);
}
    /**
     * @method Processar Arquivo da Operadora Unimed-BH Mensalidade
     * * @param {argumentos,arquivo Operadpra
     */
        
    public static  function processUnimedBhMensalidade($args,$arquivo)
    {

    try{

            $dao        = new MImportArquivo();

            $objArray               = array();
            $listChapas             = $dao->buscarChapasAtivas();
            $hashImportancao        = rand().date('s'); 

            $i = 0;
            
            while (!feof($arquivo)){

                $linha                    = fgets($arquivo);    
                $linha                    = str_replace(PHP_EOL, '', $linha);
                $dados                    = explode(';', $linha); 
                $i++;     
                                  
                /**DISPRESA HEAD DO ARQUIVO */
                if($i == 1){
                    continue;
                }    

                $objMen = new Mensalidade();

                $objMen->chapa = isset($dados['25']) ? str_pad(trim($dados['25']), 6, 0 , STR_PAD_LEFT) : null;
                $objMen->operadora = isset($args['operadora']) ? $args['operadora'] : null;
                $objMen->nota = isset($args['nota']) ? $args['nota'] : null;
                $objMen->cnpj =  preg_replace("/[^0-9]/", "",Enum::cnpjOperadora($args['operadora']));
                $objMen->titular = isset($dados[3]) ? $dados[3] : null;
                $objMen->cartao = isset($dados[1]) ? $dados[3] : null;
                $objMen->beneficiario = isset($dados[2]) ? $dados[2] : null;
                $objMen->valor = isset($dados['32']) ? ltrim($dados['32'],"0") : null;
                $objMen->credito = isset($dados['32']) ? (strpos(strval($dados['32']),"-") ? 1 : 0) : null;
                $objMen->competencia = $args['competencia'].'-01';
                $objMen->incosistencia = HUtil::validarChapa($listChapas,$objMen->chapa);
                $objMen->hashImportancao = $hashImportancao;

                array_push($objArray, $objMen);
                unset($objMen);
            }
            
            fclose($arquivo);
            unset($arquivo);
            $retorno =  $dao->salvarMensalidade($objArray);

    } catch (\Exception $e){

        throw "Error :".$e->getMessage();
    }
    
    return array('Nota'=>$objArray[0],'return'=> $retorno);
}
        
    /**
     * @method Processar Arquivo da Operadora Unimed-BH Coparticipação
     *  @param {argumentos,arquivo Operadpra
     */
        public static  function processUnimedBhCoparticipacao($args,$arquivo)
        {
            $dao        = new MImportArquivo();

            $objArray   = array();
            $listChapas = $dao->buscarChapasAtivas();
            $i          = 0;
            $hashImportancao        = rand().date('s'); 

            try {

                    $objArray = array();
                    
                    while (!feof($arquivo)){

                        $linha                    = fgets($arquivo);    
                        $linha                    = str_replace(PHP_EOL, '', $linha);
                        $dados                    = explode(';', $linha); 
                        $i++;                 
                    
                        /**DISPRESA HEAD DO ARQUIVO */
                        if($i == 1){
                            continue;
                        }    
                
                    $objCorp = new Coparticipacao();
  
                    $objCorp->operadora        = $args['operadora'];
                    $objCorp->nota             = $args['nota'];
                    $objCorp->cnpj             =  preg_replace("/[^0-9]/", "",Enum::cnpjOperadora($args['operadora']));
                    $objCorp->competencia      = $args['competencia'];
                    $objCorp->cartao           = $dados[2];
                    $objCorp->chapa             = HUtil::formataChapa($dados[18]);
                    $objCorp->titular          = $dados[17];
                    $objCorp->beneficiario     = $dados[3];
                    $objCorp->prestador        = $dados[5];
                    $objCorp->dtAtendimento    = $dados[8];
                    $objCorp->CodProcedimento  = $dados[12];
                    $objCorp->descricao        = $dados[11];
                    $objCorp->valor            = HUtil::formatDecimal($dados['16']);
                    $objCorp->incosistencia    = HUtil::validarChapa($listChapas,$objMen->chapa);
                    $objCorp->hashImportancao    =  $hashImportancao;   

                    array_push($objArray, $objCorp);
                    unset($objCorp);    
                    }
            
            fclose($arquivo); 
            unset($arquivo);  
            $return = $dao->salvarCoparticipacao($objArray);

                } catch (Exception $e){

                    throw "Error :".$e->getMessage();
            }

        return $return;
        }


        
}