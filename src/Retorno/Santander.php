<?php

namespace Skynix\Remessax\Retorno;

use Skynix\Remessax\Retorno;

class Santander extends Retorno {


	protected function readHeader($line){
		$header = $this->loadTag('Header',$line);
		
		/*	
		001 a 001 Identifica��o do Registro 001 0 X
		002 a 002 Identifica��o do Arquivo Retorno 001 2 X
		003 a 009 Literal Retorno 007 Retorno X
		010 a 011 C�digo do Servi�o 002 01 X
		012 a 026 Literal Servi�o 015 Cobran�a X
		027 a 046 C�digo da Empresa 020 N� Empresa X
		047 a 076 Nome da Empresa por Extenso 030 Raz�o Social X
		077 a 079 N� do Bradesco na C�mara Compensa��o 003 237 X
		080 a 094 Nome do Banco por Extenso 015 Bradesco X
		095 a 100 Data da Grava��o do Arquivo 006 DDMMAA X
		101 a 108 Densidade de Grava��o 008 01600000 X
		109 a 113 N� Aviso Banc�rio 005 N� aviso X
		114 a 379 Branco 266 Branco
		380 a 385 Data do Cr�dito 006 DDMMAA X
		386 a 394 Branco 009 Branco
		395 a 400 N� Seq�encial de registro 006 000001 X
		*/
		
		$header->dta_arquivo = $this->formatDate($header->dta_credito);
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
		$titulo->mt_ocorrencia = $this->getDescricaoOcorrencia($titulo->cod_movimento);
		return $titulo;
	}
	
	protected function readTrailer($line){
		// to do print_r($line);
	}
	
	public function getDescricaoOcorrencia($codigo){
		$dataset = array(
			'01' => 'T�TULO N�O EXISTE',
			'02' => 'ENTRADA T�T. CONFIRMADA',
			'03' => 'ENTRADA T�T. REJEITADA',
			'06' => 'LIQUIDA��O',
			'07' => 'LIQUIDA��O POR CONTA',
			'08' => 'LIQUIDA��O POR SALDO',
			'09' => 'BAIXA AUTOM�TICA',
			'10' => 'T�T. BAIX. CONF. INSTRU��O',
			'11' => 'EM SER',
			'12' => 'ABATIMENTO CONCEDIDO',
			'13' => 'ABATIMENTO CANCELADO',
			'14' => 'ALTERA��O DE VENCIMENTO',
			'15' => 'CONFIRMA��O DE PROTESTO',
			'16' => 'T�T. J� BAIXADO/LIQUIDADO',
			'17' => 'LIQUIDADO EM CART�RIO',
			'21' => 'T�T. ENVIADO A CART�RIO',
			'22' => 'T�T. RETIRADO DE CART�RIO',
			'24' => 'CUSTAS DE CART�RIO',
			'25' => 'PROTESTAR T�TULO',
			'26' => 'SUSTAR PROTESTO',
			'35' => 'T�TULO DDA RECONHECIDO PELO PAGADOR',
			'36' => 'T�TULO DDA N�O RECONHECIDO PELO PAGADOR',
			'37' => 'T�TULO DDA RECUSADO PELA CIP',
			'38' => 'RECEBIMENTO DA INSTRU��O N�O PROTESTAR',
			'39' => 'ESP�CIE DE T�TULO N�O PERMITE A INSTRU��O',
		);
		
		return $dataset[$codigo];
		
	}

}