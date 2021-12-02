<?php

namespace Skynix\Remessax\Config;

use Skynix\Remessax\Config;


class Bradesco extends Config {

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