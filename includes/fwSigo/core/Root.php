<?php

final class Root {
    
    private static $root = null;
    
    public static function getRoot() {
        
        if(is_null(self::$root)) {
            
            $r = str_replace('/', '\\', $_SERVER['DOCUMENT_ROOT']);
            $r = str_replace($r, '', __DIR__);
            $eRoot = explode('\\', $r);
            $r = str_repeat('../', sizeof($eRoot));
            
            self::$root = $_SERVER['DOCUMENT_ROOT'] . './includes/fwsigo/core/';
            
        }
        
        return self::$root;
        
    }
    
}
