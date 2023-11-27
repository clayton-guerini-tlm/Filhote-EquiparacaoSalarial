<?php

class GrupoMunicipio
{

    public $id;
    public $uf_id;
	public $nome;
    public $uf;
    public $cidades;
    public $btnEditar;
    public $btnExcluir;

    public function __construct() {        
          
        $this->btnEditar = '<span class="material-icons editar-grupo" title="Editar" style="cursor: pointer;" data-id="'.$this->id.'" data-uf_id="'.$this->uf_id.'" data-nome="'.$this->nome.'">edit</span>';  

        $this->btnExcluir = '<span class="material-icons excluir-grupo" title="Remover" style="cursor: pointer;" data-id="'.$this->id.'">delete</span>';
    }
}