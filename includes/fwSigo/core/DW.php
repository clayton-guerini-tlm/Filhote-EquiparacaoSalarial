<?php


class DW extends SqlServer {

    public function __construct($database) {

        $this->host = "Driver={SQL Server};Server=192.168.0.54\DW;Database=$database";
        // $this->host = "Driver={SQL Server};Server=192.168.0.54\DW;Database=DW_STG";S
        $this->username = 'dge';
        $this->password = '@dge2011%_';

        parent::__construct();
    }

}