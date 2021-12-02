<?php

namespace Skynix\Remessax\Factory;

use Skynix\Remessax\Factory;
use Skynix\Remessax\Remessa\Sicoob240 as RemessaSicoob240;

class Sicoob240 extends Factory {

	protected function createRemessa(){
		
		$config = $this->getConfig();

		$titulos = $this->getTitulos();
		
		$remessa = new RemessaSicoob240($config,$titulos);
		$remessa->setRemessaId($this->getNextId());
        /*
        if(!empty($remessa->getMensagens())){
            throw new Exception("Remessa contém titulos com erros!");    
        }
		*/
		return $remessa;	
	}
	
	protected function getCodBanco(){
		return '756';
	}
	
	protected function InsertRemessaTitulos(){
		//echo '<pre>';
		foreach($this->aTitulos as $oTitulo){
			//var_dump( $oTitulo);
            $sSQL = "INSERT INTO tb_fin_remessa_titulos 
                      (id_remessa,id_titulo,nosso_numero,vencimento,valor,cpf_cnpj,cod_movimento) 
                     VALUES ('".$this->id_remessa."',
                             '".$oTitulo->id_titulo."',
                             '".$oTitulo->nosso_numero."',
                             '".$oTitulo->vencimento."',
                             '".$oTitulo->valor."',
                             '".$oTitulo->cpf."',						 
                             '".$oTitulo->cod_movimento."'						 
                             )";
            $ret = $this->Execute($sSQL);		

            if($ret === false ) return false;
			
		}
		//echo '</pre>';
	}
	
}

?>