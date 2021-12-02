<?php

namespace Skynix\Remessax;

use Exception;

abstract class Factory {

	private $Db;
	protected $aTitulos;
	protected $config;
	protected $periodo_ini;
	protected $periodo_fim;
	protected $id_remessa;
	
	public function __construct($adodb,Config $config,TituloList $aTitulos){
		$this->Db = $adodb;
		$this->config = $config;
		$this->aTitulos = $aTitulos;
	}
	
	/**
	 * Lookup
	 *
	 * Execute query no banco de dados e retorna dataset 
	 *
	 * @param String $sSQL
	 * @return Array/Bool
	 */	
	protected function Lookup($sSQL){
		$ds = false;
		$rs = $this->Db->Execute($sSQL);
		if($rs)
		$ds = $rs->getRows();
		return $ds;
	}

	/**
	 * Execute
	 *
	 * Alias para metodo do adodb Execute
	 *
	 * @param String $sSQL
	 * @return mixed
	 */
	protected function Execute($sSQL){
		return $this->Db->Execute($sSQL);
	}
	
	public function setConfig($config){
		$this->config = $config;
	}
	
	/**
	 * getConfig
	 *
	 * Retorna objeto stdclass com as informa��es de configura��o do banco
	 * para emissao do arquivo de remessa
	 *
	 */
	protected function getConfig(){
		return $this->config;
	}
	
	protected function InsertRemessa($nome_arquivo){

		$sSQL = "SELECT 
					count(*) 
				 from 
					tb_fin_remessa 
				 WHERE 
					periodo_ini = '".$this->periodo_ini."' AND 
					periodo_fim = '".$this->periodo_fim."' AND 
					codigo_banco = '".$this->getCodBanco()."'";
		$ds = $this->Lookup($sSQL);
	
		//if(!empty($ds[0][0]) && $ds[0][0] > 0)
		//throw new Exception("Remessa j� foi criada!");
	
		$this->id_remessa = $this->getNextId();
		
		$sSQL = "INSERT INTO tb_fin_remessa (codigo,nome_arquivo,data_criacao,codigo_banco,periodo_ini,periodo_fim) 
				 VALUES (".$this->id_remessa.",'".$nome_arquivo."',NOW(),'".$this->getCodBanco()."','".$this->periodo_ini."','".$this->periodo_fim."')";
		return $this->Execute($sSQL);
	}
	
	protected function getNextId(){
		$sSQL = "SELECT MAX(codigo) from tb_fin_remessa";
		$ds = $this->Lookup($sSQL);
		$seq = !empty($ds[0][0])?$ds[0][0]+1:1;
		return $seq;
	}
	
	public function makeRemessa($data_ini,$data_fim){

		if(empty($data_ini) || empty($data_fim))
		throw new Exception('N�o foi informado a data de gera��o do arquivo de remssa!');
		
		$this->periodo_ini = $data_ini;
		$this->periodo_fim = $data_fim;
		
		if($this->config === null)
		throw new Exception('Configura��o n�o informada!');
		
		if($this->aTitulos === null)
		throw new Exception('Titulos n�o informados!');
		
		try{
			//$this->Db->debug = true;
			//$this->Db->BeginTrans();
		
			$remessa = $this->createRemessa($data_ini,$data_fim);
			$remessa->setSeq($this->getNextId());
			// grava registro de arquuivo de remessa
			$ret = $this->InsertRemessa(basename($remessa->getFilename()));
			if($ret === false) throw new Exception("erro no header");
			// grava registros da amarra��o dos arquivos de remessa com os titulos
			$ret = $this->InsertRemessaTitulos();
			if($ret === false) throw new Exception("erro nos itens");
			
			//$this->Db->CommitTrans();
		}catch(Exception $e){
			//$this->Db->RollbackTrans();
			throw new Exception('Gerando Remessa :: '.$e->getMessage());
		}
		
		
		
		$remessa->doSave(false);
		return $remessa;
	}
	
	
	abstract protected function InsertRemessaTitulos();

	abstract protected function createRemessa();
	
	abstract protected function getCodBanco();
	
	public function getTitulos(){
		return $this->aTitulos;
	}
	
	public function setTitulos($titulos){
		$this->aTitulos = $titulos;
	}
}


?>