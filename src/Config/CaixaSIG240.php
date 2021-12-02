<?php

namespace Skynix\Remessax\Config;

use Skynix\Remessax\Config;

class CaixaSIG240 extends Config {

	public $mensagem;
	public $conta_cedente;
	public $inicio_nosso_numero;
	
	public function getArrayConfig(){
		$arr_config = $this->_getArrayConfig();
	}

}

?>