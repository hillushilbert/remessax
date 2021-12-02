<?php

namespace Skynix\Remessax\Remessa;

use Skynix\Remessax\Remessa;


/*
* @descr: Gera o arquivo de remessa para cobranca no padrao CNAB 400 vers. 7.0 ITAU
*/

class Bradesco extends Remessa {

	private $idEmpresa;
	public  $teste = false;
	private $seq_remessa;
	
	public function setContador($seq_remessa){
		$this->seq_remessa = $seq_remessa;
	}
	
	public function setHeader(){
		
		$config = $this->config;		
		$conteudo = '';
		
		## REGISTRO HEADER	
																		#NOME DO CAMPO        #SIGNIFICADO            	#POSICAO    #PICTURE
		$conteudo .= '0';             									//001 a 001		Identifica��o do Registro			001			0						X	
		$conteudo .= 1;             									//002 a 002		Identifica��o do Arquivo Remessa	001			1				X
		$conteudo .= 'REMESSA';        									//003 a 009		Literal Remessa						007			REMESSA			X
		$conteudo .= '01';            									//010 a 011		C�digo de Servi�o					002			01			X
		$conteudo .= $this->limit('COBRANCA',15);    					//012 a 026		Literal Servi�o						015			COBRANCA			X
		$conteudo .= $this->formatNumber($config->cod_empresa,20);		//027 a 046		C�digo da Empresa					020			Ser� fornecido pelo Bradesco, quando do Cadastramento Vide Obs. P�g. 16			X
		$conteudo .= $this->formatString($config->razao_social,30);		//047 a 076		Nome da Empresa						030			Raz�o Social			X
		$conteudo .= '237';												//077 a 079		N. Bradesco C�mara de Compensa��o	003			237			X
		$conteudo .= $this->limit('BRADESCO',15); 						//080 a 094		Nome do Banco por Extenso			015			Bradesco			X
		$conteudo .= $this->formatData();								//095 a 100		Data da Grava��o do Arquivo			006			DDMMAA			Vide Obs. P�g. 16 X
		$conteudo .= $this->complementoRegistro(8,"brancos");			//101 a 108		Branco								008			Branco			X
		$conteudo .= 'MX';       										//109 a 110		Identifica��o do sistema			002			MX			Vide Obs. P�g. 16 X
		// preciso recuperar essa informa��o antes de chegar aqui
		// TODO = 25/03/2019
		$conteudo .= $this->sequencial($this->seq_remessa,7);      		//111 a 117		N� Seq�encial de Remessa			007			Seq�encial			Vide Obs. P�g. 16 X
		$conteudo .= $this->complementoRegistro(277,"brancos");			//118 a 394		Branco								277			Branco			X
		$conteudo .= $this->sequencial(1);        						//395 a 400		N� Seq�encial do Registro 			006			000001			X
		$conteudo .= chr(13).chr(10); 									//essa � a quebra de linha

		$this->conteudo = $conteudo;
	}

	
	/**
	 * setFileName
	 *
	 * Define o nome do arquivo de remessa que ser� gerado
	 *
	 * @return void
	 */
	protected function setFileName($seq=false){
		
		
		if($this->filename_download === null || $this->filename === null)
		{
			
			$this->filename = REMESSAX_REMESSA_PATH.'CB'.
							  $this->DATA['DIA'].
							  $this->DATA['MES'].
							  "01.rem";
			
			$remessa_mark = '01';
			$id_mark = 2;
			while(is_file($this->filename)){
				$remessa_replace = str_pad($id_mark,2,'0',STR_PAD_LEFT);
				$id_mark++;
				
				$this->filename = REMESSAX_REMESSA_PATH.'CB'.
							  $this->DATA['DIA'].
							  $this->DATA['MES'].
							  $remessa_replace.
							  ".rem";
				
			}
			$this->filename_download = basename($this->filename);
	
		}
	}	
	
		
	private function getIdEmpresa(){

		if($this->idEmpresa === null){
			
			//str_pad($input, 10, "-=", STR_PAD_LEFT);
			$carteira 	= str_pad(substr($this->config->carteira,0,3),3,"0",STR_PAD_LEFT);
			$agencia 	= str_pad(substr($this->config->agencia,0,5),5,"0",STR_PAD_LEFT);
			$conta 		= str_pad(substr($this->config->conta,0,7),7,"0",STR_PAD_LEFT);
			$dv_conta 	= str_pad(substr($this->config->conta_dv,0,1),1,"0",STR_PAD_LEFT);
			$this->idEmpresa = '0'.$carteira.$agencia.$conta.$dv_conta;
		}

		return $this->idEmpresa;
	}
	
	private function getDvNossoNumero($nosso_numero){
		$calc_num = $this->config->carteira.str_pad($nosso_numero,11,'0',STR_PAD_LEFT);
		$dv = $this->modulo11($calc_num,7,'P');		
		return $dv;
	}
	
	public function setMovimento(){
		
		$config = $this->config;

		//### DADOS DOS CLIENTES PARA TESTE

		$i = 2;
		$conteudo = '';	
		foreach($this->titulos as $cliente)
		{
			//## REGISTRO DETALHE (OBRIGATORIO)
			$cliente->cod_movimento = !empty($cliente->cod_movimento) ? $cliente->cod_movimento : '01';
			
																	//#NOM            #SIGNIFICADO            						#POSICAO    #PICTURE
			$conteudo .= 1;                                    		// 001 a 001	Identifica��o do Registro						001			1			X
			$conteudo .= $this->complementoRegistro(5,"brancos");	// 002 a 006	Ag�ncia de D�bito (opcional)					005			C�digo da Ag�ncia do Pagador Exclusivo para D�bito em Conta			Vide Obs. P�g. 16 X
			$conteudo .= $this->complementoRegistro(1,"brancos");	// 007 a 007	D�gito da Ag�ncia de D�bito (opcional)			001	        9(14)
			$conteudo .= $this->complementoRegistro(5,"brancos");   // 008 a 012	Raz�o da Conta Corrente (opcional)				005        9(04)
			$conteudo .= $this->complementoRegistro(7,"brancos");	// 013 a 019	Conta Corrente (opcional)						007	
			$conteudo .= $this->complementoRegistro(1,"brancos");	// 020 a 020	D�gito da Conta Corrente (opcional)				001
			$conteudo .= $this->getIdEmpresa();                     // 021 a 037	Identifica��o da Empresa Benefici�ria no Banco	017	
			$conteudo .= $this->formatString($cliente->id_titulo,25,'0');	 // 038 a 062	N� Controle do Participante						025	
			$conteudo .= '237';       								// 063 a 065		C�digo do Banco									003
			if($config->mora > 0)
				$conteudo .= '2';              						// 066 a 066		Campo de Multa									001		
			else
				$conteudo .= '0';              						// 066 a 066		Campo de Multa	
			$conteudo .= $this->formatValor($config->mora,4);     	// 067 a 070		Percentual de multa								004	
			$conteudo .= $this->formatNumber($cliente->nosso_numero,11);   // 071 a 081		Identifica��o do T�tulo no Banco				011
			$conteudo .= $this->getDvNossoNumero($cliente->nosso_numero);                 // ???            
			$conteudo .= $this->complementoRegistro(10,"zeros");     // 083 a 092		Desconto Bonifica��o por dia					010	
			$conteudo .= $this->limit($config->cod_emissao,1);       // 093 a 093		Condi��o para Emiss�o da Papeleta de Cobran�a	001			
			$conteudo .= 'N';                                 		 // 094 a 094		Ident. se emite Boleto para D�bito Autom�tico	001			N= N�o registra na cobran�a. Diferente de N registra e emite Boleto.
			$conteudo .= $this->limit('',10);                        // 095 a 104		Identifica��o da Opera��o do Banco				010			Brancos	
			$conteudo .= ' ';                             			 // 105 a 105		Indicador Rateio Cr�dito (opcional)				001			"R"			Vide Obs. P�g. 19 X
			$conteudo .= '2';                      					 // 106 a 106		Endere�amento p/ Aviso D�bito Autom�tico		001			Vide Obs. P�g. 19
			$conteudo .= $this->complementoRegistro(2,"brancos"); 	 // 107 a 108		Branco											002			Branco			X        
			$conteudo .= $this->formatNumber($cliente->cod_movimento,2); // 109 a 110		Identifica��o da ocorr�ncia						002
			$conteudo .= $this->limit($cliente->id_titulo,10);		 // 111 a 120		N� do Documento									010
			$conteudo .= $this->formatData($cliente->vencimento); 	 // 121 a 126		Data do Vencimento do T�tulo					006
			$conteudo .= $this->formatValor($cliente->valor,13);	 // 127 a 139		Valor do T�tulo									013			
			$conteudo .= $this->zeros(0,3);             			 // 140 a 142		Banco Encarregado da Cobran�a					003
			$conteudo .= $this->zeros(0,5);							 // 143 a 147		Ag�ncia Deposit�ria								005			Preencher com zeros			X
			$conteudo .= '01';										 // 148 a 149		Esp�cie de T�tulo								002			01-Duplicata			02-Nota Promiss�ria
			$conteudo .= 'N';    									 // 150 a 150		Identifica��o									001			Sempre = N			X
			$conteudo .= $this->formatData();						 // 151 a 156		Data da emiss�o do T�tulo						006			DDMMAA			X
			$conteudo .= $this->formatNumber($config->instrucao01,2);// 157 a 158		1� instru��o									002			Vide Obs. P�g. 20			X
			$conteudo .= $this->formatNumber($config->instrucao02,2);// 159 a 160		2� instru��o									002			Vide Obs. P�g. 20			X
			//$conteudo .= $this->formatValor($config->mora,13);	 // 161 a 173		Valor a ser cobrado por Dia de Atraso			013			Mora por Dia de Atraso Vide obs. P�g. 21
			$valorJurosDia = ($config->juros / 100) * $cliente->valor;
			$conteudo .= $this->formatValor($valorJurosDia,13);		 // 161 a 173		Valor a ser cobrado por Dia de Atraso			013			Mora por Dia de Atraso Vide obs. P�g. 21
			$conteudo .= $this->formatData($cliente->dataDesconto);  // 174 a 179		Data Limite P/Concess�o de Desconto				006			DDMMAA			X
			$conteudo .= $this->formatValor($cliente->valorDesconto,13);// 180 a 192	Valor do Desconto								013			Valor Desconto			X
			$conteudo .= $this->zeros(0,13);						 // 193 a 205		Valor do IOF									013			Valor do IOF � Vide Obs. P�g. 21		
			$conteudo .= $this->zeros(0,13);    					 // 206 a 218		Valor do Abatimento a conceder ou cancelar		013			Valor Abatimento			X
			$conteudo .= '01';    									 // 219 a 220		Identifica��o do Tipo de Inscri��o do Pagador	002			01-CPF			02-CNPJ
			$conteudo .= $this->zeros($cliente->cpf,14);    		 // 221 a 234		N� Inscri��o do Pagador							014			CNPJ/ CPF - Vide Obs. P�g. 21			X
			$conteudo .= $this->formatString($cliente->nome,40);			// 235 a 274		Nome do Pagador									040			Nome do Pagador			X       
			$conteudo .= $this->formatString($cliente->endereco,40);    	// 275 a 314		Endere�o Completo								040			Endere�o do Pagador			X
			$conteudo .= $this->formatString(substr($config->mensagem,0,12),12);    	// 315 a 326		1� Mensagem										012			Vide Obs. P�g. 22			X
			$conteudo .= $this->formatString(substr($cliente->cep,0,5),5); // 327 a 331		CEP												005			CEP Pagador			X
			$conteudo .= $this->formatString(substr($cliente->cep,5,3),3); // 332 a 334		Sufixo do CEP									003			Sufixo			X        
			$conteudo .= $this->formatString(substr($config->mensagem,12),60);  // 335 a 394		Sacador/Avalista ou	2� Mensagem					060			Decomposi��o Vide Obs. P�g. 22 X          
			$conteudo .= $this->sequencial($i++);            		// 395 a 400		N� Seq�encial do Registro						006			N� Seq�encial do Registro			X
			$conteudo .= chr(13).chr(10); 							//essa � a quebra de linha
			$this->val_total += $cliente->valor;

			$this->tot_linhas++;		
		} // fecha loop de clientes
		  

		$this->conteudo .= $conteudo;

	}

	public function setTrailler(){
		
		$conteudo = '';
		$conteudo .= 9;
		$conteudo .= $this->complementoRegistro(393,"brancos");
		$conteudo .= $this->sequencial($this->tot_linhas);
		$this->conteudo .= $conteudo;
	}



}


?>