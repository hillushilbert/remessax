<?php

class Remessax_Titulo_CaixaSIG240 extends Remessax_Titulo {
	
	public $vencimento_antecipado;
	public $valor_antecipado;
	public $cod_movimento;
	
	public function to_string(){
		echo $this->id_titulo;
	}
}

?>