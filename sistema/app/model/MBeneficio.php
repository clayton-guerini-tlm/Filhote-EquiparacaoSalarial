<?php

class MBeneficio extends Model {

    public function __construct() {
        set_time_limit(0);
        ini_set('memory_limit', '2048M');
        ini_set('mysql.connect_timeout', 3600);
        ini_set('default_socket_timeout', 3600);
        parent::__construct('serverdge', 'modulo_beneficios', 'modulo_beneficios');
        parent::__addConnection('serverdge', 'sigo_integrado', 'sigo_integrado');
		parent::__addConnection('serverdge', 'modulo_beneficios', 'modulo_beneficios');
        //parent::__addConnection('serverdge', 'modulo_ponto', 'modulo_ponto');
        parent::__addConnection('sigo_ponto', 'modulo_ponto', 'modulo_ponto');

    }

}
