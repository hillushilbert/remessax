<?php

namespace Skynix\Remessax\Config;

use Skynix\Remessax\Config;

class Sicredi extends Config {
    
    public $posto;
	public $byte;
	public $multa;
	public $num_remessa;
	
	public function getArrayConfig(){
		$arr_config = $this->_getArrayConfig();
	}

}

?>