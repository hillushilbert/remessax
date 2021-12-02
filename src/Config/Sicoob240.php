<?php

namespace Skynix\Remessax\Config;

use Skynix\Remessax\Config;

class Sicoob240 extends Config {

	public $modalidade_cobranca;
	public $numero_parcela;
	public $convenio;
	
	public function getArrayConfig(){
		$arr_config = $this->_getArrayConfig();
	}

}

?>