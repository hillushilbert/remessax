<?php

namespace Skynix\Remessax\Titulo;

use Skynix\Remessax\Titulo;

class CaixaSIG240 extends Titulo {
	
	public $vencimento_antecipado;
	public $valor_antecipado;
	public $cod_movimento;
	
	public function to_string(){
		echo $this->id_titulo;
	}
}

?>