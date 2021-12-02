<?php
namespace Skynix\Remessax;

use DateInterval;
use DateTime;

define('REMESSAX_REMESSA_PATH',__DIR__);
/*
* @descr: Gera o arquivo de remessa para cobranca no padrao CNAB 400 vers. 7.0 ITAU
*/

abstract class Remessa {

	protected $seq;
	protected $config;
	protected $titulos = [];
	protected $DATAHORA;
	protected $DATA;
	protected $conteudo;

	protected $val_total;
	protected $tot_linhas;
	
	protected $filename;
	protected $filename_download;	
	protected $arr_mensagens = array();	
	
	public function __construct(Config $config,TituloList $titulos){
		
		$this->config = $config;
		$this->titulos = $titulos;
		
		$this->val_total = 0.0;
		$this->tot_linhas = 2;

		$timestamp = mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"));

		$this->DATAHORA['PT'] = gmdate("d/m/Y H:i:s", $timestamp);
		$this->DATAHORA['EN'] = gmdate("Y-m-d H:i:s", $timestamp);
		$this->DATA['PT'] = gmdate("d/m/Y", $timestamp);
		$this->DATA['EN'] = gmdate("Y-m-d", $timestamp);
		$this->DATA['DIA'] = gmdate("d",$timestamp);
		$this->DATA['MES'] = gmdate("m",$timestamp);
		$this->DATA['ANO'] = gmdate("y",$timestamp);
		$this->HORA = gmdate("H:i:s", $timestamp);	
		
		$this->setFileName();
		
		foreach($this->titulos as $idx=>$oTitulo){
			if($oTitulo->vencimento < date('Y-m-d')){
				$this->arr_mensagens[] = "O titulo ".$oTitulo->id_titulo." est� vencido.";
				$this->titulos->checkItemInvalido($idx);
				continue;
			} 

			if($oTitulo->cpf == '' || empty($oTitulo->cpf)){
				$this->arr_mensagens[] = 'O titulo <a href="#" onclick="openLinkOnTab(\''.$oTitulo->id_aluno.'\');">'.$oTitulo->id_titulo.'</a> est� sem o CPF do Sacado.';
				$this->titulos->checkItemInvalido($idx);
				continue;
			}
			
			if($oTitulo->nome == '' || empty($oTitulo->nome)){
				$this->arr_mensagens[] = "O titulo ".$oTitulo->id_titulo." est� sem o Nome do Sacado.";
				$this->titulos->checkItemInvalido($idx);
				continue;
			}			
		}
		$this->titulos->clean();
			
		$this->conteudo = '';
	}
	
	public function setSeq($seq){
		$this->seq = $seq;
	}
	
	public function getSeq(){
		return $this->seq;
	}	
	
	/**
	 * setFileName
	 *
	 * Define o nome do arquivo de remessa que será gerado
	 *
	 * @return void
	 */
	protected function setFileName($seq=false){
		
		if($this->filename_download === null || $this->filename === null)
		{
			$this->filename = REMESSAX_REMESSA_PATH.'Remessa_'.$this->DATA['DIA'].$this->DATA['MES'].$this->DATA['ANO'].".rem";
			
			$remessa_mark = 'Remessa_';
			$id_mark = 1;
			while(is_file($this->filename)){
				$remessa_replace = substr($remessa_mark,0,-1).$id_mark;
				$id_mark++;
				$this->filename = str_replace($remessa_mark,$remessa_replace,$this->filename);
				$remessa_mark = $remessa_replace;		
			}
			$this->filename_download = '/includes_sc/boletophp/remessa/'.$remessa_mark.$this->DATA['DIA'].$this->DATA['MES'].$this->DATA['ANO'].".rem";
		}
	}
	
	/**
	 * getFilename
	 * 
	 * Retorna arquivo de remessa gerado
	 *
	 * @return String
	 */
	public function getFilename(){
	
		return $this->filename_download;
	}


	/**
     * formatData
     *
     * Transforma a data no padrão yyyy-mm-dd para o formato de banco 
     *
     * @param String $data
     * @param int $add_dias (opcional)
     * @return $string
     */
	protected function formatData($data = false,$add_dias = 0) {
		$return = array();
		// data do sistema
		if($data == false)
		{
			$data = date('Y-m-d');
		}
		// data em branco
		if($data === '')
		{
			return '000000';
		}
		
		$data = substr($data,0,10);
		$aData = explode('-',$data);

		$timestamp = mktime(0, 0, 0, $aData[1], $aData[2] + $add_dias, $aData[0]);

		$return = gmdate("d",$timestamp).gmdate("m",$timestamp).gmdate("y",$timestamp);
		return $return;	
	}
	
	
	protected function limit($palavra,$limite)
	{
	
		$palavra = (string)$palavra;
		if(strlen($palavra) >= $limite)
		{
			$var = substr($palavra, 0,$limite);
		}
		else
		{
			$max = (int)($limite-strlen($palavra));
			$var = $palavra.$this->complementoRegistro($max,"brancos");
		}
		return $var;
	}



	protected function sequencial($i,$size=6)
	{
		$return = str_pad($i,$size,'0',STR_PAD_LEFT);
		return $return;
	}
	
	protected function formatNumber($valor,$size=1)
	{
		$valor = preg_replace('/\D/', '', $valor);
		$valor = str_pad($valor,$size,'0',STR_PAD_LEFT);
		if(strlen($valor) > $size){
			throw new Exception('formatNumber :: '.$valor.':'.$size);
		}
		return $valor;	
	}

	protected function formatValor($valor,$size)
	{
		$tmp_valor = $valor * 100;
		$tmp_valor = (int)$tmp_valor;
		$return = str_pad($tmp_valor,$size,'0',STR_PAD_LEFT);
		return $return; 
	}
	
	protected function formatString($string,$size,$complento=' '){
		$return = trim($string);
		$return = substr($return,0,$size);
		$return = str_pad($return,$size,$complento);
		$return = strtoupper($return);
		//$return = preg_replace( '/[`^~\'"]/', null, iconv( 'UTF-8', 'ASCII//TRANSLIT', $return ) );
		
		$from = "�������������������������Ǻ";
		$to =   "AAAAEEIOOOUUCAAAAEEIOOOUUC.";
		$return = strtr($return, $from, $to); // corrompe os caracteres acentuados
		
		return $return ;
	}
	

	protected function zeros($min,$max)
	{
		$zeros = '';
		$x = ($max - strlen($min));
		for($i = 0; $i < $x; $i++)
		{
			$zeros .= '0';
		}
		return $zeros.$min;
	}
	
	/**
	 * modulo11
	 *
	 * Calcula digito para modulo11
	 *
	 * @param int $valor
	 * @param int $base
	 * @param String $resto_maior
	 * @return string
	 */
	protected function modulo11($valor,$base=9,$resto_maior='0'){
	
		$valor = (string)$valor;
		$tot = 0;
		$mult = 2;
		
		for($i=strlen($valor);$i>0;$i--)
		{
			$tot += ($mult) * $valor[$i-1];
			$mult++;
			if($mult > $base) $mult = 2;
		}
		
		$resto = $tot % 11;
		
		if($resto == 10) 	$digito = '1';
		elseif($resto == 0) $digito = '0';
		elseif($resto == 1)	$digito = $resto_maior;
		else				$digito = 11 - $resto;		

		return $digito;
	}	
	

	protected function complementoRegistro($int,$tipo)
	{
		if($tipo == "zeros")
		{
			$space = '';
			for($i = 1; $i <= $int; $i++)
			{
				$space .= '0';
			}
		}
		else if($tipo == "brancos")
		{
			$space = '';
			for($i = 1; $i <= $int; $i++)
			{
				$space .= ' ';
			}
		}
		return $space;
	}
	
	public function doSave($dump=false){
		$this->setHeader();
		$this->setMovimento();
		$this->setTrailler();
			
		if (!$handle = @fopen($this->filename, 'w+')) 
		throw new Exception("N�o foi poss�vel abrir o arquivo ($this->filename)");
		

		// Escreve $conteudo no nosso arquivo aberto.
		if (fputs($handle, $this->conteudo) === FALSE) 
		throw new Exception("N�o foi poss�vel escrever no arquivo ($filename)");
		
		fclose($handle);
		
		if($dump === true)
		{
			echo '<h3>'.$this->filename.'</h3>';
			echo '<pre>';
			print_r($this->conteudo);
			echo '</pre>';
		}

		return true;
	}	

    abstract public function setHeader();
    abstract public function setMovimento();	
    abstract public function setTrailler();

	public function getMensagens(){
		return $this->arr_mensagens;
	}
	//abstract protected function checkTitulo($linha,$titulo,$seq);

}
?>