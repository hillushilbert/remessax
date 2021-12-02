 <?php

class Remessax_Factory_CaixaSIG240 extends Remessax_Factory {

	protected function createRemessa(){
		
		$config = $this->getConfig();

		$titulos = $this->getTitulos();
		
		$remessa = new Remessax_Remessa_CaixaSIG240($config,$titulos);
		$remessa->setRemessaId($this->getNextId());
		return $remessa;	
	}
	
	protected function getCodBanco(){
		return '104';
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