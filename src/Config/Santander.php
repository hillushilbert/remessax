<?php

class Remessax_Config_Santander extends Remessax_Config {
	
	public $tp_inscricao;
	public $cod_transmissao;
	public $cod_cobranca;
	public $cod_agencia;
	public $codigo_cliente;
	/*
	public $cod_empresa;
	public $cod_emissao;
	*/
	
	public function getArrayConfig(){
		$arr_config = $this->_getArrayConfig();
	}

}

?>