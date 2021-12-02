<?php

namespace Skynix\Remessax;

use Exception;
use ReflectionClass;

abstract class DbAdapter {

	public function FillClass($Db,$sSQL){
	
		$ds = $this->getData($Db,$sSQL);
		
		$reflect = new ReflectionClass($this);
		
		foreach($ds[0] as $field => $value)
		{
			$property = '';			
			try{
				$property = $reflect->getProperty($field);
			}
			catch(Exception $e){
					$property = false;
			}
			
			if(is_object($property)){
				$value = str_replace("'","",$value);
				$this->$field = $value;			
			}
		}		
	
	}
	
	public static function FillList($Db,$sSQL,$className){
	
		$arr_objects = array();
		$dataset = self::getData($Db,$sSQL);
		
		if(empty($dataset)) return null;
		
		$reflect = new ReflectionClass($className);
		foreach($dataset as $ds)
		{
			$obj_temp = new $className();
			foreach($ds as $field => $value)
			{
				$property = '';			
				try{
					$property = $reflect->getProperty($field);
				}
				catch(Exception $e){
						$property = false;
				}
				
				if(is_object($property)){
					$value = str_replace("'","",$value);
					$obj_temp->$field = $value;			
				}
			}
			$arr_objects[] = $obj_temp ;
		}
		return $arr_objects;
	}
	
	private function getData($Db,$sSQL){

		$Db->SetFetchMode(ADODB_FETCH_ASSOC);
		
		$rs = $Db->Execute($sSQL);
		
		if(empty($rs))
		throw new Exception("Fill Class executou um querie invalida!");
		
		$ds = $rs->getRows();
 		
		if(empty($ds))
		throw new Exception("Fill Class n�o retornou nenhum dado!");
		$Db->SetFetchMode(ADODB_FETCH_NUM);
		return $ds;
	}

}

?>