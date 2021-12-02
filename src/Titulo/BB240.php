<?php

class Remessax_Titulo_BB240 extends Remessax_Titulo {

	public $cod_movimento;
	public $vencimento_antecipado;
	public $valor_antecipado;
	
	public function to_string(){
		echo $this->id_titulo;
	}
}

?>