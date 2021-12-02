<?php

class Remessax_Retorno_CaixaSIG240 extends Remessax_Retorno {

	protected function readHeader($line){
		$header = $this->loadTag('Header',$line);
		/*	
		001 a 001 Identificação do Registro 001 0 X
		002 a 002 Identificação do Arquivo Retorno 001 2 X
		003 a 009 Literal Retorno 007 Retorno X
		010 a 011 Código do Serviço 002 01 X
		012 a 026 Literal Serviço 015 Cobrança X
		027 a 046 Código da Empresa 020 Nº Empresa X
		047 a 076 Nome da Empresa por Extenso 030 Razão Social X
		077 a 079 Nº do Bradesco na Câmara Compensação 003 237 X
		080 a 094 Nome do Banco por Extenso 015 Bradesco X
		095 a 100 Data da Gravação do Arquivo 006 DDMMAA X
		101 a 108 Densidade de Gravação 008 01600000 X
		109 a 113 Nº Aviso Bancário 005 Nº aviso X
		114 a 379 Branco 266 Branco
		380 a 385 Data do Crédito 006 DDMMAA X
		386 a 394 Branco 009 Branco
		395 a 400 Nº Seqüencial de registro 006 000001 X
		*/
		
		$header->dta_arquivo = $this->formatDate($header->dta_arquivo);
		$header->dta_credito = $this->formatDate($header->dta_credito);
		return $header;
	
	}
	
	protected function readTitulo($line){
		$titulo = $this->loadTag('SegmentoT',$line);
		$segmento = $this->getSegmento($line);
		
		if(method_exists ( $this , $segmento )){
			$titulo = $this->$segmento($line);
		}else{
			return false;
		}
		 

		
		return $titulo;
	}
	
	protected function readTrailer($line){
		// to do print_r($line);
	}
	
	public function validate(){
		
		$msg = array();
		
		$linha = 1;
		//var_dump($this->handle);
		while(!feof($this->handle))
		{
			$buffer = fgets($this->handle,4096);
			if($linha == 1){
				$this->_header = $this->readHeader($buffer);
			}
			elseif($linha > 2 && $linha < $this->_total_linhas){
				//$this->_titulos[] = $this->readTitulo($buffer);
				$segmento = $this->getSegmento($buffer);
				//var_dump($segmento);
				if($segmento == 'SegmentoT'){
					$titulo = null;
					$this->SegmentoT($buffer,$titulo);
					$buffer = fgets($this->handle,4096);
					$this->SegmentoU($buffer,$titulo);
					//echo '<pre>';
					//print_r($titulo);
					//echo '</pre>';	
					if($titulo->cod_movimento != '28') 
					$this->_titulos[]= $titulo;	
				}elseif($segmento == 'SegmentoW'){
					$this->SegmentoW($buffer,$msg);
				}
			}
			else{	
				$this->_trailer = $this->readTrailer($buffer);
			}
			$linha++;
		}
		
		return $msg;
	}	
	
	private function SegmentoT($line,&$titulo){
		$titulo = $this->loadTag('SegmentoT',$line);
		$titulo->id_parcela    = (int)$titulo->id_parcela;
		$titulo->nosso_numero  = (int)$titulo->nosso_numero;
		$titulo->vl_titulo     = $this->formatNumber($titulo->vl_titulo);
		$titulo->dt_vencimento = $this->formatDate($titulo->dt_vencimento);
	}
	
	private function SegmentoU($line,&$titulo){
		$titulo_xml = $this->loadTag('SegmentoU',$line);
		$titulo->valor_pago    = $this->formatNumber($titulo_xml->valor_pago);
		$titulo->desconto      = $this->formatNumber($titulo_xml->desconto);
		$titulo->juros_mora    = $this->formatNumber($titulo_xml->juros_mora);
		$titulo->data_credito  = $this->formatDate($titulo_xml->data_credito);
		$titulo->dt_ocorrencia = $this->formatDate($titulo_xml->dt_ocorrencia);
	}

	private function SegmentoW($linha,&$msg){
		$rec = new stdClass;
		$rec->banco = substr($linha,0,3);
		$rec->lote = substr($linha,3,4);
		$rec->tipo = substr($linha,7,1);
		$rec->num_registro = substr($linha,8,5);
		$rec->segmento = substr($linha,13,1);
		$rec->cnab = substr($linha,14,1);
		$rec->cod_movimento = substr($linha,15,2);
		$rec->num_reg_pos_arqui = substr($linha,17,6);
		$rec->cod_campo = substr($linha,24,4);
		$rec->cod_erro = substr($linha,28,129);
		//echo '<pre>';
		//var_dump($rec);
		//echo '</pre>';
		$msg[] = $rec->cod_erro; 
	}
	
	protected function formatDate($data){
		$novo = substr($data,4,4).'-'.substr($data,2,2).'-'.substr($data,0,2);
		return $novo;
	}	
	
	private function getSegmento($line){
		return 'Segmento'.substr($line,13,1);
	}

}