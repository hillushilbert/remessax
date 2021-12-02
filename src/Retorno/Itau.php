<?php

class Remessax_Retorno_Itau extends Remessax_Retorno {


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
		$titulo = $this->loadTag('Titulo',$line);
		//$titulo->id_parcela   = (int)substr($titulo->id_parcela,0,-1);
		$titulo->id_parcela   = (int)($titulo->id_parcela);
		$titulo->valor_pago   = $this->formatNumber($titulo->valor_pago);
		$titulo->vl_titulo    = $this->formatNumber($titulo->vl_titulo);
		$titulo->desconto     = $this->formatNumber($titulo->desconto);
		$titulo->juros_mora   = $this->formatNumber($titulo->juros_mora);
		$titulo->data_credito = $this->formatDate($titulo->data_credito);
		$titulo->dt_ocorrencia = $this->formatDate($titulo->dt_ocorrencia);
		return $titulo;
	}
	
	protected function readTrailer($line){
		// to do print_r($line);
	}

}