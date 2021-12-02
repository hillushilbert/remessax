<?php

namespace Skynix\Remessax;

abstract class Config extends DbAdapter {

    public $carteira;
    public $agencia;
    public $agencia_dv;
    public $conta;
    public $conta_dv;
    public $tp_inscricao;
    public $cpf_cnpj;
    public $razao_social;
    public $juros = 2;
    public $mora = 0.5;
    public $aceite = 'A';
    public $dias_multa  = 0;
    public $dias_juros  = 0;
		
	public function __construct($Db,$sSQL = false){
	
		// preenche os valores do objeto a partir de uma querie
		if(strlen($sSQL) > 0){
			$this->FillClass($Db,$sSQL);
		}
	}
}

?>