<?php

class Remessax_Config_Sicredi extends Remessax_Config {
    
    public $posto;
	public $byte;
	public $multa;
	public $num_remessa;
	
	public function getArrayConfig(){
		$arr_config = $this->_getArrayConfig();
	}

}

?>