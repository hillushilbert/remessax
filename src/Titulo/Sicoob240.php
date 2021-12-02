<?php

namespace Skynix\Remessax\Titulo;

use Skynix\Remessax\Titulo;

class Sicoob240 extends Titulo {

	public $cod_movimento;
	public $vencimento_antecipado;
	public $valor_antecipado;
	
	public function to_string(){
		echo $this->id_titulo;
	}
}

?>