<?php

namespace Skynix\Remessax\Remessa;

use Skynix\Remessax\Remessa;


/*
* @descr: Gera o arquivo de remessa para cobranca no padrao CNAB 400 Santander 2017
*/

class Santander extends Remessa {

	private function calcNossoNumero($id_pagamento){
	
		$id_pagamento = (string)$id_pagamento;
		$tot = 0;
		$mult = 2;
		for($i=strlen($id_pagamento);$i>0;$i--)
		{
			$tot += ($mult) * $id_pagamento[$i-1];
			
			$mult++;
			if($mult > 9)
			{
				$mult = 2;
			}
		}
		$resto = $tot % 11;
		
		if($resto == 10)
		{
			$digito = '1';
		}
		else if($resto == 0 || $resto == 1)
		{
			$digito = '0';
		}
		else
		{
			$digito = 11 - $resto;		
		}
		$return = $id_pagamento.$digito;
		$return = str_pad($return,8,'0',STR_PAD_LEFT);	
		return $return;
	}


	
	public function setHeader(){
		
		$config = $this->config;		
		$conteudo = '';

		## REGISTRO HEADER
																#NOME DO CAMPO        #SIGNIFICADO            	#POSICAO    #PICTURE
		$conteudo .= '0';             							//tipo de registro    id registro header        001 001        9(01) 
		$conteudo .= 1;             							//operacao            tipo operacao remessa    	002 002        9(01)
		$conteudo .= 'REMESSA';        							//literal remessa        escr. extenso          003 009        X(07)
		$conteudo .= '01';            							//codigo servico        id tipo servico         010 011        9(02)
		$conteudo .= $this->limit('COBRANCA',15);    			//literal cobranca    escr. extenso    			012 026        X(15)
		$conteudo .= $this->limit($config->cod_transmissao,20);	// C�digo de Transmiss�o (nota 1)			    027 030        9(04)
		$conteudo .= $this->limit($config->razao_social,30);	//nome da empresa            					047 076        X(30)
		$conteudo .= '033';            							//codigo banco            N� BANCO C�MARA COMP. 077 079        9(03)
		$conteudo .= $this->limit('SANTANDER',15);         		//nome do banco por ext.    					080 094        X(15)
		$conteudo .= $this->formatData();						//data geracao arquivo    						095 100        9(06)
		$conteudo .= $this->complementoRegistro(16,"zeros");	//zeros complemento d registro    				031 032        9(02)
		$conteudo .= $this->complementoRegistro(275,"brancos");	//complemento registro     						039 046        X(08)
		$conteudo .= '000';        								//conta                conta da empresa        	033 037        9(05)
		$conteudo .= '000001';                					//dac                    digito autoconf conta  038 038        9(01)
		$conteudo .= chr(13).chr(10); 							//essa � a quebra de linha

		$this->conteudo = $conteudo;
	}

	
	
	public function setMovimento(){
		
		$config = $this->config;
		### DADOS DOS CLIENTES PARA TESTE

		$i = 2;
		$conteudo = '';	
		foreach($this->titulos as $cliente)
		{
			// cpf
			if(empty($cliente->cpf)){
				$this->arr_mensagens[] = $cliente->matricula.' :: CNPJ ou CPF do Aluno faltando';
			}
			
			// endereco
			if(empty($cliente->endereco)){
				$this->arr_mensagens[] = $cliente->matricula.' :: Endereco do Aluno faltando';
			}
			
			// bairro
			if(empty($cliente->bairro)){
				$this->arr_mensagens[] = $cliente->matricula.' :: Bairro do Aluno faltando';
			}
			
			// CEP
			if(empty($cliente->cep)){
				$this->arr_mensagens[] = $cliente->matricula.' :: CEP do Aluno faltando';
			}

			// Cidade
			if(empty($cliente->cidade)){
				$this->arr_mensagens[] = $cliente->matricula.' :: Cidade do Aluno faltando';
			}
			// Estado
			if(empty($cliente->estado)){
				$this->arr_mensagens[] = $cliente->matricula.' :: Estado do Aluno faltando';
			}
			// Estado
			if(empty($cliente->valor)){
				$this->arr_mensagens[] = $cliente->matricula.' :: Valor da mensalidade faltando';
			}
			/*
			echo '<pre>';
			print_r($config);
			echo '</pre>';
			*/
			$cod_cobranca = str_pad($config->cod_cobranca,8,'0',STR_PAD_LEFT);
			$codigo_cliente = str_pad($config->codigo_cliente,10,'0',STR_PAD_LEFT);
			$multa = '0';
			if($config->mora > 0.0){
				$multa = '4';
			}
			## REGISTRO DETALHE (OBRIGATORIO)
			##																		#NOME DO CAMPO                #SIGNIFICADO            		#POSICAO    #PICTURE
			$conteudo .= 1;                                                        	// 001 001 9(001) C�digo do registro = 1
			$conteudo .= $this->limit($config->tp_inscricao,2);    		            // 002 003 9(002) Tipo de inscri��o do cedente: 01 = CPF 02 = CGC
			$conteudo .= $this->limit($config->cpf_cnpj,14);                        // 004 017 9(014) CGC ou CPF do cedente                                        	004 017        9(14)
			//$conteudo .= $this->limit($config->cod_transmissao,20);               // 018 037 9(020) C�digo de Transmiss�o (nota 2)
			$conteudo .= $this->formatNumber($config->agencia,4);               	// 018 021 9(004) C�digo da ag�ncia Benefici�rio (nota 2)  
            $conteudo .= $this->formatNumber(substr($cod_cobranca,0,8),8);  		// 022 029 9(008) Conta movimento Benefici�rio (nota 2)
            $conteudo .= $this->formatNumber(substr($codigo_cliente,0,8),8);		// 030 037 9(008) Conta cobran�a Benefici�rio (nota 2)
            $conteudo .= $this->limit($cliente->id_titulo,25);                     	// 038 062 X(025) N�mero de controle do participante, para controle por parte do cedente
			$conteudo .= $this->calcNossoNumero($cliente->id_titulo);              	// 063 070 9(008) Nosso n�mero (nota 3)
			$conteudo .= '000000';                                                 	// 071 076 9(006) Data do segundo desconto
			$conteudo .= $this->complementoRegistro(1,"brancos");                   // 077 077 X(001) Branco
			$conteudo .= $multa;							                        // 078 078 9(001) Informa��o de multa = 4, sen�o houver informar zero Verificar p�gina 16
			$conteudo .= $this->formatValor($config->juros,4);			            // 079 082 9(004)v99 Percentual multa por atraso %
			$conteudo .= '00';							                        	// 083 084 9(002) Unidade de valor moeda corrente = 00
			$conteudo .= '0000000000000';                                        	// 085 097 9(013)v99 Valor do t�tulo em outra unidade (consultar banco)
			$conteudo .= $this->complementoRegistro(4,"brancos");                   // 098 101 X(004) Brancos
			$conteudo .= $this->complementoRegistro(6,"zeros");                   	// 102 107 9(006) Data para cobran�a de multa. (Nota 4)
			//$conteudo .= $this->limit($config->carteira,1);							// 108 108 9(001) C�digo da carteira (1,3,4,5,6,7)
			$conteudo .= '5';														// 108 108 9(001) C�digo da carteira (1,3,4,5,6,7)
			$conteudo .= '01';		                                             	// 109 110 9(002) C�digo da ocorr�ncia: entrada de titulo
			$conteudo .= $this->limit($cliente->id_titulo,10);                     	// 111 120 X(010) Seu n�mero
			$conteudo .= $this->formatData($cliente->vencimento);                 	// 121 126 9(006) Data de vencimento do t�tulo
			$conteudo .= $this->formatValor($cliente->valor,13);                    // 127 139 9(013)v99 Valor do t�tulo - moeda corrente
			$conteudo .= '033';                                             		// 140 142 9(003) N�mero do Banco cobrador = 033
			if($config->carteira == '5')
				$conteudo .= $this->formatNumber($config->agencia,5);           // 143 147 9(005) C�digo da ag�ncia cobradora do Banco Santander, opcional informar somente se carteira for igual a 5, caso contr�rio, informar zeros.
			else
				$conteudo .= '00000';                                               // 143 147 9(005) C�digo da ag�ncia cobradora do Banco Santander, opcional informar somente se carteira for igual a 5, caso contr�rio, informar zeros.
			$conteudo .= '06'; 														// 148 149 9(002) Esp�cie de documento:
			$conteudo .= 'N';         												// 150 150 X(001) Tipo de aceite = N
			$conteudo .= $this->formatData();             							// 151 156 9(006) Data da emiss�o do t�tulo
			$conteudo .= '00';            											// 157 158 9(002) Primeira instru��o cobran�a
			$conteudo .= '00';														// 159 160 9(002) Segunda instru��o cobran�a
			$conteudo .= $this->formatValor($config->mora,13);						// 161 173 9(013)v99 Valor de mora a ser cobrado por dia de atraso
			$conteudo .= $this->formatData($cliente->dataDesconto);					// 174 179 9(006) Data limite para concess�o de desconto
			$conteudo .= $this->formatValor($cliente->valorDesconto,13);    		// 180 192 9(013)v99 Valor de desconto a ser concedido
			$conteudo .= $this->complementoRegistro(13,"zeros");					// 193 205 9(013)v99 Valor do IOF a ser recolhido pelo Banco para nota de seguro
			$conteudo .= $this->complementoRegistro(13,"zeros");   					// 206 218 9(013)v99 Valor do abatimento a ser concedido ou valor do segundo desconto. Vide posi��o 71.
			$conteudo .= '01'; 														// 219 220 9(002) Tipo de inscri��o do sacado: 01 = CPF 02 = CGC
			$conteudo .= $this->formatNumber($cliente->cpf,14);             		// 221 234 9(014) CGC ou CPF do sacado
			$conteudo .= $this->limit($cliente->nome,40);    						// 235 274 X(040) Nome do sacado
			$conteudo .= $this->limit($cliente->endereco,40); 						// 275 314 X(040) Endere�o do sacado
			$conteudo .= $this->limit($cliente->bairro,12);    						// 315 326 X(012) Bairro do sacado
			$conteudo .= $this->limit(substr($cliente->cep,0,5),5);					// 327 331 9(005) CEP do sacado
			$conteudo .= $this->limit(substr($cliente->cep,5,3),3);					// 332 334 9(003) Complemento do CEP
			$conteudo .= $this->limit($cliente->cidade,15);    						// 335 349 X015) Munic�pio do sacado
			$conteudo .= $this->limit($cliente->estado,2);    						// 350 351 X(002) UF Estado do sacado
			$conteudo .= $this->complementoRegistro(30,"brancos");	   				// 352 381 X(030) Nome do sacador ou coobrigado
			$conteudo .= ' ';    													// 382 382 X(001) Brancos
			$conteudo .= 'I';    													// 383 383 X(001) Identificador do Complemento (i mai�sculo � vide nota 2)
			/*
			Complemento Conta Cobran�a (posi��es 384-385): preencher com a �ltima posi��o
			da conta cobran�a e com o d�gito (CCCCCCCCC-D)
			*/
			//$conteudo .= $this->limit(substr($config->cod_cobranca,8,2),2);		// 384 385 9(002) Complemento (nota 2)
			$conteudo .= $this->limit(substr($codigo_cliente,8,2),2);				// 384 385 9(002) Complemento (nota 2)
			$conteudo .= $this->complementoRegistro(6,"brancos");					// 386 391 X(006) Brancos
			$conteudo .= '00';														// 392 393 9(002) N�mero de dias para protesto.
			$conteudo .= $this->complementoRegistro(1,"brancos");					// 394 394 X(001) Branco
			$conteudo .= $this->sequencial($this->tot_linhas);						// 395 400 9(006) N�mero seq�encial do registro no arquivo
			$conteudo .= chr(13).chr(10); 											//essa � a quebra de linha
			$this->val_total += $cliente->valor;

			$this->tot_linhas++;		
		} // fecha loop de clientes
		  
		$this->conteudo .= $conteudo;

	}

	public function setTrailler(){
		
		$conteudo = '';
		$conteudo .= 9;
		$conteudo .= $this->sequencial($this->tot_linhas);
		$conteudo .= $this->formatValor($this->val_total,13);
		$conteudo .= $this->complementoRegistro(374,"zeros");
		$conteudo .= $this->sequencial($this->tot_linhas);
		$this->conteudo .= $conteudo;
	}


}
?>