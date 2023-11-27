<?php

class EquiparacaoSalarial
{

    public $filial;
	public $chapa;
    public $nomeFuncionario;
    public $funcao;
    public $nomeLider;
    public $codSecao;
    public $dtAdmissao;
    public $dtInicioFuncao;
    public $salario;
    public $validado;
    public $acoes;
    public $justificativa;
    public $idEquiparacao;

    public function __construct() {        
        
        $this->acoes = '
        <span class="material-icons acoesEquiparacao" title="Validar Desligamento" style="cursor: pointer;" data-id="'.$this->idEquiparacao.'" id="'.$this->idEquiparacao.'">bookmark_add</span>      
        ';
        $this->justificativa = '    
        <span class="material-icons acoesMotivos" title="Historico de Motivos" style="cursor: pointer;" data-chapa="'.$this->chapa.'" chapa="'.$this->chapa.'">history</span>
        ';
    }
}