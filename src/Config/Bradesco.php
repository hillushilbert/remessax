<?php

class Remessax_Config_Bradesco extends Remessax_Config {

	public $mensagem;
	public $cod_empresa;
	public $cod_emissao;
	public $instrucao01;
	public $instrucao02;
	
	public function getArrayConfig(){
		$arr_config = $this->_getArrayConfig();
	}

}

?>