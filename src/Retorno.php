<?php

class Remessax_Retorno_Exception extends Exception { }

/*
* @descr: Gera o arquivo de remessa para cobranca no padrao CNAB 400 vers. 7.0 ITAU
*/

abstract class Remessax_Retorno {

	protected $xmlLayout;
	protected $handle;
	
	protected $_header;
	protected $_titulos;
	protected $_trailer;
	protected $_total_linhas;
	
	public function __construct($fileName){
	
		
		$parts = explode('_',get_class($this));
		
		$xmlPath = dirname(__FILE__).'/'.$parts[1].'/'.$parts[2].'.xml';

		
		if(!is_file($xmlPath))
		throw new Remessax_Retorno_Exception("Arquivo de Laytout nao encontrado! {$xmlPath}");
		
		// Layout do arquivo de remessas
		$this->xmlLayout = simplexml_load_file($xmlPath);	
		
		if(!is_file($fileName))
		throw new Remessax_Retorno_Exception("Arquivo de remessa invalido! {$fileName}");
	
	
		// Handle do arquivo de retorno
		$this->handle = fopen($fileName,'r');
		
		$this->_total_linhas = count(file($fileName));
	}
	
	public function __destroy(){
		fclose($this->handle);
	}
	
	abstract protected function readHeader($line);
	abstract protected function readTitulo($line);
	abstract protected function readTrailer($line);
	
	/**
	 * loadTag
	 *
	 * Realiza leitura de layout de arquivo de retorno e 
	 * faz parser da linha em formato de objeto
	 *
	 * @param String $tagName
	 * @param String $linha
	 */
	protected function loadTag($tagName,$linha){
		
		$record = new stdClass;
		foreach($this->xmlLayout->$tagName->field as $field){
			$name = (string)$field->attributes()->name;
			$start = (string)$field->attributes()->start;
			$size = (string)$field->attributes()->size;
			$default = (string)$field->attributes()->default;
			$type = (string)$field->attributes()->type;
			$value = substr($linha,($start-1),$size);
			$record->$name = $value;
		}
		//var_dump($record);
		return $record;
	}
	
	/**
	 * titulos
	 *
	 * Retorna lista de titulos baixados no arquivo de remessa
	 *
	 * @return Array
	 */
	public function titulos(){
		return $this->_titulos;
	}
	
	/**
	 *
	 *
	 *
	 *
	 */
	public function header(){
		return $this->_header;
	}
	public function validate(){
		$linha = 1;
		while(!feof($this->handle))
		{
			$buffer = fgets($this->handle,4096);
			if($linha == 1){
				$this->_header = $this->readHeader($buffer);
			}
			elseif($linha > 1 && $linha < $this->_total_linhas){
				$this->_titulos[] = $this->readTitulo($buffer);
			}
			else{	
				$this->_trailer = $this->readTrailer($buffer);
			}
			$linha++;
		}
	}
	
	protected function formatNumber($numero){
		$novo = floatval(substr($numero,0,-2).'.'.substr($numero,-2));
		return $novo;
	}
	
	protected function formatDate($data){
		$novo = '20'.substr($data,4,2).'-'.substr($data,2,2).'-'.substr($data,0,2);
		return $novo;
	}

}
?>