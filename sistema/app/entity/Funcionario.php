<?php

class Funcionario
{

    public $chapa;
	public $nome;
    public $filial;
  
    public function __construct() {        
        
        $this->acoes = '<span class="material-icons editar-permissoes" title="Editar Permissões" style="cursor: pointer;" data-chapa="'.$this->chapa.'" data-nome="'.$this->nome.'">edit</span>';
    }
}