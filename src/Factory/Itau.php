<?php

namespace Skynix\Remessax\Factory;

use Skynix\Remessax\Factory;
use Skynix\Remessax\Remessa\Itau as RemessaItau;

class Itau extends Factory {

	protected function createRemessa(){
		
		$config = $this->getConfig();

		$titulos = $this->getTitulos();
		
		$remessa = new RemessaItau($config,$titulos);
		return $remessa;	
	}
	
	protected function getCodBanco(){
		return '341';
	}
	
	protected function InsertRemessaTitulos(){
		//echo '<pre>';
		foreach($this->aTitulos as $oTitulo){
			
			$sSQL = "INSERT INTO tb_fin_remessa_titulos (id_remessa,id_titulo,nosso_numero,vencimento,valor,cpf_cnpj) 
					 VALUES ('".$this->id_remessa."',
							 '".$oTitulo->id_titulo."',
							 '".$oTitulo->nosso_numero."',
							 '".$oTitulo->vencimento."',
							 '".$oTitulo->valor."',
							 '".$oTitulo->cpf."'						 
							 )";
			$ret = $this->Execute($sSQL);		

			if($ret === false ) return false;
			
		}
		//echo '</pre>';
	}	
	
}

?>