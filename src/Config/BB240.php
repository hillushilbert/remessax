<?php

namespace Skynix\Remessax\Config;

use Skynix\Remessax\Config;

class BB240 extends Config {

	//public $mensagem;
	public $cod_convenio;
	public $carteira_var;
	public $aceite;
	
	public function getArrayConfig(){
		$arr_config = $this->_getArrayConfig();
	}

}

?>