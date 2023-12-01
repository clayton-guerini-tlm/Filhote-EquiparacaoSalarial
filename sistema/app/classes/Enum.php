<?php 

/**
 * @Class Enum
 */
class Enum{

    /**
     *@Enum Operadora 
     */

     public static function operadora($args = null){

        $data = array();

        $data[1]  = "UNIMED-BH";
        $data[2]  = "CNU";
        
        if($args != null);
            return $data[$args];
        
        return $data; 

     }
     public static function layout($args = null){

        $data = array();

        $data[1]  = "MENSALIDADE";
        $data[2]  = "COPARTICIPAÇÃO";
        
        if($args != null);
            return $data[$args];
        
        return $data; 

     }

     public static function cnpjOperadora($args = null){

        $data = array();

        $data[1]  = "16.513.178/0001-76";
        $data[2]  = "02.812.468/0001-06";

        if($args != null);
            return $data[$args];
        
        return $data;

     }

     public static function incosistencia($args = null){

        $data = array();

        $data[0]  = "Não";
        $data[1]  = "Sim";

        if($args != null);
            return $data[$args];
        
        return $data;

     }


}