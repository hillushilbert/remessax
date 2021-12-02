<?php

class Remessax_Titulo_Sicredi extends Remessax_Titulo {
	
	public $cod_movimento;
	public $vencimento_antecipado;
	public $valor_antecipado;		
	public $id_cliente;		
		
	public function to_string(){
		echo $this->id_titulo;
	}
}

?>