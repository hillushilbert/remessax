<?php

class Remessax_Config_CaixaSIG240 extends Remessax_Config {

	public $mensagem;
	public $conta_cedente;
	public $inicio_nosso_numero;
	
	public function getArrayConfig(){
		$arr_config = $this->_getArrayConfig();
	}

}

?>