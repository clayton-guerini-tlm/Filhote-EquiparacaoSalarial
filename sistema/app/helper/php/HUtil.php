<?php 
 
class HUtil{


 
    /**
    * @method Formata as chapas conforme Padrão
    */
    public static function formataChapa($chapa)
    {

        $chapaFormatada = ltrim($chapa,"0");
        return str_pad($chapaFormatada, 6, 0 , STR_PAD_LEFT);
     } 
     
     /**
      * @method Validar Chapa existente na Base
      */

      public static function validarChapa($chapas,$chapa){
            
        if(in_array($chapa,$chapas)){

            return false;
        }
              
        return true;
     }

     /**
      * @method Retorna valor com casa decimais separada por ponto
      */
      public static function formatDecimal($value){

        $valor  = ltrim($value,"0");
        return floatval(substr_replace($valor, '.', -2, 0));
      }

}