<?php

class DClass {
    
    public function __get($p) {
        if(property_exists(get_class($this), $p)) {
            return $this->$p;
        }
    }
    
    public function __set($p, $v) {
        if(property_exists(get_class($this), $p)) {
            $this->$p = $v;
        }
    }
    
}