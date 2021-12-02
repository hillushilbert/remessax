<?php

namespace Skynix\Remessax\Titulo;

use Skynix\Remessax\Titulo;

class Sicredi extends Titulo {
	
	public $cod_movimento;
	public $vencimento_antecipado;
	public $valor_antecipado;		
	public $id_cliente;		
		
	public function to_string(){
		echo $this->id_titulo;
	}
}

?>