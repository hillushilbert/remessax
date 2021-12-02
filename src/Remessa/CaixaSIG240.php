<?php

/*
* @descr: Gera o arquivo de remessa para cobranca no padrao CNAB 240 vers. 7.0 ITAU
*/

class Remessax_Remessa_CaixaSIG240 extends Remessax_Remessa {

	private $lote = '0001';
	private $remessa_id;
	
	public function setRemessaId($remessa_id){
		$this->remessa_id = $remessa_id;
	}
	
	public function setHeader(){
		
		$this->config->cpf_cnpj = str_replace('.','',$this->config->cpf_cnpj);
		$this->config->cpf_cnpj = str_replace('/','',$this->config->cpf_cnpj);
		$this->config->cpf_cnpj = str_replace('-','',$this->config->cpf_cnpj);
		
		$config = $this->config;		
		$conteudo = '';

		## REGISTRO HEADER - lote	
																	#NOME DO CAMPO      #SIGNIFICADO            	#POSICAO    #PICTURE
		$conteudo .= '104';             							//Banco				Código do Banco 			1 	3 		3 - Num ‘104’ G001 
		$conteudo .= '0000';             							//Lote				Lote de Serviço 			4 	7 		4 - Num *G002
		$conteudo .= '0';        									//Registro 			Tipo de Registro 			8 	8 		1 - Num '1' *G003
		$conteudo .= $this->complementoRegistro(9,"brancos");		//CNAB 				Uso FEBRABAN / CNAB 		9 	17 		9 - Alfa Brancos G004
		$conteudo .= '2';    										//Inscrição 		Tipo Inscrição da Empresa 	18 	18 		1 - Num *G005
		$conteudo .= $this->formatNumber($config->cpf_cnpj,14); 	//Inscrição 		Nº de Inscrição da Empresa 	19 	32 		14 - Num *G006
		$conteudo .= $this->complementoRegistro(20,"zeros");        //Uso Exclusivo 	Uso Exclusivo CAIXA 		33 	52 		20 - Num ‘0’
		$conteudo .= $this->formatNumber($config->agencia,5);		//agencia           Agência da Conta 			53 	57 		5 - Num *G008
		$conteudo .= $this->formatNumber($config->agencia_dv,1);	//agencia           Dígito da Agência 			58 	58 		1 - Alfa *G009
		$conteudo .= $this->formatNumber($config->conta_cedente,6);	//Código Cedente 	Código do Convênio no Banco 59 	64		6 - Num *G007
		$conteudo .= '00000000';        							//Uso Exclusivo 	Uso Exclusivo CAIXA 		65 	72 		8 - Num ‘0’
		$conteudo .= $this->formatString($config->razao_social,30);	//Nome				Nome da Empresa 			73 	102 	30 - Alfa G013
		$conteudo .= $this->formatString('CAIXA ECONOMICA FEDERAL',30);	//Nome			Nome do Banco 				103 132 	30 - Alfa G014
		$conteudo .= $this->complementoRegistro(10,"brancos");		//CNAB 				Uso Exclusivo FEBRABAN/CNAB 133 142 	10 - Alfa Brancos G004
		$conteudo .= '1';											//Código 			Código Remessa / Retorno 	143 143 	1 - Num G015
		$conteudo .= $this->formatData();							//Data de Geração 	Data de Geração do Arquivo 	144 151 	8 - Num G016
		$conteudo .= date('His');									//Hora de Geração 	Hora de Geração do Arquivo 	152 157 	6 - Num G017
		$conteudo .= $this->formatNumber($this->remessa_id,6);		//Seqüência (NSA) 	Número Seqüencial do Arquivo158 163 	6 - Num *G018
		$conteudo .= '050';        									//Layout do Arquivo No da Versão do Layout 		164 166 	3 - Num '050' *G019
		$conteudo .= '00000';        								//Densidade Densidade de Gravação do Arquivo 	167 171 	5 - Num ‘0’ G020
		$conteudo .= $this->complementoRegistro(20,"brancos");		//Reservado Banco 	Para Uso Reservado do Banco 172 191 	20 - Alfa G021
		
		//$conteudo .= $this->complementoRegistro(20,"brancos");		//Reservado Empresa Para Uso Reservado da Empresa 192 211 	20 - Alfa G022
		$conteudo .= $this->formatString('REMESSA-PRODUCAO',20);		//Reservado Empresa Para Uso Reservado da Empresa 192 211 	20 - Alfa G022
		
		$conteudo .= $this->complementoRegistro(4,"brancos");		//Versão Aplicativo Versão Aplicativo CAIXA 	212 215 	4 - Alfa C077
		$conteudo .= $this->complementoRegistro(25,"brancos");		//CNAB Uso Exclusivo FEBRABAN / CNAB 			216 240 	25 - Alfa Brancos G004
		$conteudo .= chr(13).chr(10); 								//essa é a quebra de linha
		$this->tot_linhas = 1;
		
		
		## REGISTRO HEADER - lote	
																	#NOME DO CAMPO      #SIGNIFICADO            	#POSICAO    #PICTURE
		$conteudo .= '104';             							//Banco				Código do Banco 			1 	3 		3 - Num ‘104’ G001 
		$conteudo .= $this->lote;             						//Lote				Lote de Serviço 			4 	7 		4 - Num *G002
		$conteudo .= '1';        									//Registro 			Tipo de Registro 			8 	8 		1 - Num '1' *G003
		$conteudo .= 'R';            								//Operação 			Tipo de Operação 			9 	9 		1 - Alfa *G028
		$conteudo .= '01';    										//Serviço 			Tipo de Serviço 			10 	11 		2 - Num *G025
		$conteudo .= '00';    										//CNAB 				Uso Exclusivo FEBRABAN/CNAB 12 	13 		2 - Num ‘00’ G004
		$conteudo .= '030';    										//Layout do Lote 	NºVersão do Layout do Lote 	14 	16 		3 - Num '030' *G030
		$conteudo .= ' ';    										//CNAB 				Uso Exclusivo FEBRABAN/CNAB 17 	17 		1 - Alfa Brancos G004
		$conteudo .= '2';    										//Inscrição 		Tipo Inscrição da Empresa 	18 	18 		1 - Num *G005
		$conteudo .= $this->formatNumber($config->cpf_cnpj,15); 	//Inscrição 		Nº de Inscrição da Empresa 	19 	33 		15 - Num *G006
		$conteudo .= $this->formatNumber($config->conta_cedente,6);	//Código Cedente 	Código do Convênio no Banco 34 	39 		6 - Num *G007
		$conteudo .= '00000000000000';    							//Uso Exclusivo 	Uso Exclusivo CAIXA 		40 	53 		14 - Num ‘0’
		$conteudo .= $this->formatNumber($config->agencia,5);		//agencia           Agência da Conta 			54 	58 		5 - Num *G008
		$conteudo .= $this->formatNumber($config->agencia_dv,1);	//agencia           Dígito da Conta 			59 	59 		1 - Alfa *G011
		$conteudo .= $this->formatNumber($config->conta_cedente,6);	//Código Cedente 	Código do Convênio no Banco 60 	65 		6 - Num *G007
		$conteudo .= '0000000';    									//Cód Mod Personalizado Código do Modelo Personalizado 66 72 7 - Num C078
		$conteudo .= '0';    										//Uso Exclusivo 	Uso Exclusivo CAIXA 		73 	73		1 - Num ‘0’
		$conteudo .= $this->formatString($config->razao_social,30);	//Nome				Nome da Empresa 			74 	103 	30 - Alfa G013
		$conteudo .= $this->complementoRegistro(80,"brancos");		//Mensagem 1 e 2								104 183 	80 - Alfa C073
		$conteudo .= $this->formatNumber($this->remessa_id,8);		//Nº Remessa 		Número Remessa/Retorno 		184 191 	8 - Num G079
		$conteudo .= $this->formatData();							//Dt. Gravação 		Data de Gravação Remessa    192 199 	8 - Num G068
		$conteudo .= '00000000';            						//Data do Crédito 	Data do Crédito 			200 207 	8 - Num C003
		$conteudo .= $this->complementoRegistro(33,"brancos");		//CNAB 				Uso Exclusivo FEBRABAN/CNAB 208 240 	33 - Alfa Brancos G004
		
		$conteudo .= chr(13).chr(10); 							//essa é a quebra de linha
		$this->tot_linhas++;
		
		$this->conteudo = $conteudo;
	}

	
	public function setMovimento(){
		
		$config = $this->config;

		### DADOS DOS CLIENTES PARA TESTE
		//var_dump($config );
		$i = 1;
		
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
			// Valor
			if(empty($cliente->valor)){
				$this->arr_mensagens[] = $cliente->matricula.' :: Valor da mensalidade faltando';
			}			
			
					
			$conteudo = '';	

			$cliente->cod_movimento = !empty($cliente->cod_movimento) ? $cliente->cod_movimento : '01';
			
			// Registro Detalhe - Segmento P (Obrigatório - Remessa)
			$conteudo .= '104';             							//Banco				Código do Banco 			1 	3 		3 - Num ‘104’ G001 
			$conteudo .= $this->formatNumber($this->lote,4);            //Lote				Lote de Serviço 			4 	7 		4 - Num *G002
			$conteudo .= '3';        									//Registro 			Tipo de Registro 			8 	8 		1 - Num '1' *G003
			$conteudo .= $this->formatNumber($i,5);       				//Nº do Registro 	Nº seq do Registro no Lote 	9 	13 		5 - Num *G038
			$conteudo .= 'P';            								//Segmento 			Cód. Segmento				14 	14 		1 - Alfa ‘Q’ *G039
			$conteudo .= ' ';											//CNAB 				Uso Exclusivo FEBRABAN/CNAB 15 	15 		1 - Alfa Brancos G004
			$conteudo .= $this->formatNumber($cliente->cod_movimento,2);// Cód. Mov. 		Código de Movimento Remessa 16 	17 		2 - Num *C004			
			$conteudo .= $this->formatNumber($config->agencia,5);		//agencia           Agência da Conta 			18 	22		5 - Num *G008
			$conteudo .= $this->formatNumber($config->agencia_dv,1);	//agencia           Dígito da Conta 			23 	23 		1 - Alfa *G009
			$conteudo .= $this->formatNumber($config->conta_cedente,6);	//Código Cedente 	Código do Convênio no Banco 24 	29 		6 - Num *G007
			$conteudo .= $this->complementoRegistro(11,"zeros");    	//Uso Exclusivo 	Uso Exclusivo CAIXA 		30 	40 		11 - Num ‘0’
			// Característica Cobrança
			$conteudo .= '14';											//Carteira			Modalidade da Carteira 		41 	42 		2 - Num *G069   
			$conteudo .= $this->formatNumber($cliente->id_titulo,15);   //Nosso Número		Id. do Título no Banco 		43 	57 		15 - Num *G069    
			$conteudo .= '1';      										//Carteira 			Código da Carteira 			58 	58 		1 - Num *C006    
			$conteudo .= '1';      										//Cadastramento 	Forma de Cadastr. do Título 59 	59 		1 - Num *C007 
			$conteudo .= '2';      										//Documento 		Tipo de Documento 			60 	60 		1 - Alfa ‘2’ C008
			$conteudo .= '2';      										//Emissão Bloqueto 	Id da Emissão do Bloqueto 	61 	61 		1 - Num *C009
			$conteudo .= '0';      										//Distribuição		Identificação da Entrega  	62 	62 		1 - Alfa C010
			// 
			$conteudo .= $this->formatNumber($cliente->id_titulo,11);   //Nº do Documento 	No. do Documento de Cobrança 63 73 		11 - Alfa *C011
			$conteudo .= $this->complementoRegistro(4,"brancos");		//Uso Exclusivo 	CAIXA 						74 	77 		4 - Alfa Brancos
			$conteudo .= $this->formatData($cliente->vencimento);       //Vencimento		Dt de Vencimento do Título 	78 	85 		8 - Num *C012
			$conteudo .= $this->formatValor($cliente->valor,15);        //Valor do Título 	Valor Nominal do Título 	86 	100 	13 2 Num *G070
			$conteudo .= $this->formatNumber(0,5);						//Ag. Cobradora 	Agência da Cobrança 		101 105 	5 - Num *C014
			$conteudo .= $this->formatNumber(0,1);						//agencia           Dígito da Conta 			106 106 	1 - Alfa *C014
			$conteudo .= '17';											//Espécie de Título Espécie do Título 			107 108 	2 - Num *C015
			$conteudo .= 'A';											//Aceite Identific. de Título Aceito/Não Aceito 109 109 	1 - Alfa C016
			$conteudo .= $this->formatData(date('Y-m-d'));         		//Data Emissão 		Data da Emissão do Título 	110 117 	8 - Num G071
			
			if(!empty($config->mora)){
				$valor_mora = round(floatval($cliente->valor * ($config->mora / 100)),2);
				$conteudo .= '1';										//Cód. Juros Mora 	Código do Juros de Mora 	118 118 	1 - Num *C018
				$conteudo .= $this->formatData($cliente->vencimento,1);	//Data Juros Mora 	Data do Juros de Mora 		119 126 	8 - Num *C019
				$conteudo .= $this->formatValor($valor_mora,15);       	//Juros Mora Juros de Mora por Dia/Taxa 		127 141 	13 2 Num C020
			}else{
				$conteudo .= '0';										//Cód. Juros Mora 	Código do Juros de Mora 	118 118 	1 - Num *C018
				$conteudo .= $this->formatNumber(0,8);					//Data Juros Mora 	Data do Juros de Mora 		119 126 	8 - Num *C019
				$conteudo .= $this->formatValor(0,15);       	        //Juros Mora Juros de Mora por Dia/Taxa 		127 141 	13 2 Num C020
			}
			
			// regras para desconto	
			if(!empty($cliente->vencimento_antecipado) && !empty($cliente->valor_antecipado)){
				$desconto  = $cliente->valor - $cliente->valor_antecipado;
				$conteudo .= '1';													//Cód. Desc. 1 		Código do Desconto 1 		142 142 	1 - Num *C021
				$conteudo .= $this->formatData($cliente->vencimento_antecipado);	//Data Desc. 1 		Data do Desconto 1 			143 150 	8 - Num C022
				$conteudo .= $this->formatValor($desconto ,15);   					//Desconto 1 		Valor a ser Concedido		151 165 	13 2 Num C023				
			}else{
				$conteudo .= '0';													//Cód. Desc. 1 		Código do Desconto 1 		142 142 	1 - Num *C021
				$conteudo .= '        ';											//Data Desc. 1 		Data do Desconto 1 			143 150 	8 - Num C022
				$conteudo .= $this->formatValor(0,15);       						//Desconto 1 		Valor a ser Concedido		151 165 	13 2 Num C023
			}
			
			$conteudo .= $this->formatValor(0,15);       				//Vlr IOF 			Valor IOF a ser Recolhido 	166 180 	13 2 Num C024
			$conteudo .= $this->formatValor(0,15);       				//Vlr Abatimento 	Valor do Abatimento 		181 195 	13 2 Num G045
			$conteudo .= $this->formatString($cliente->id_titulo,25);   //Uso Cedente 		Id do Título na Empresa 	196 220 	25 - Alfa G072
			$conteudo .= '3';											//Código p/ Protesto Código para Protesto 		221 221 	1 - Num C026
																		/*1 = Protestar / 3 = Não Protestar / 9 = Cancelamento Protesto Automático*/
			//$conteudo .= '90';											//Prazo p/ Protesto Número de Dias para Protesto222 223 	2 - Num C027
			$conteudo .= '00';											//Prazo p/ Protesto Número de Dias para Protesto222 223 	2 - Num C027
			//$conteudo .= '2';											//Código p/ Baixa Código para Baixa/Devolução 	224 224 	1 - Num C028
			$conteudo .= '1';											//Código p/ Baixa Código para Baixa/Devolução 	224 224 	1 - Num C028
			$conteudo .= '090';											//Prazo p/ Baixa Número de Dias para Baixa 		225 227 	3 - Alfa C029
			$conteudo .= '09';											//Código da Moeda 	Código da Moeda 			228 229 	2 - Num *G065
			$conteudo .= '0000000000';									//Uso Exclusivo 	Uso Exclusivo CAIXA 		230 239 	10 - Num ‘0’
			$conteudo .= $this->complementoRegistro(1,"brancos");		//CNAB Uso Exclusivo FEBRABAN/CNAB 				240 240 	1 - Alfa Brancos G004
			$conteudo .= chr(13).chr(10); 								//essa é a quebra de linha
			//$this->lote++;
			$this->tot_linhas++;
			
			$i++;
			// ---------------------------------------------------------------------------------------------------------------------------------------
			// Registro Detalhe - Segmento Q (Obrigatório - Remessa)
			$conteudo .= '104';             							//Banco				Código do Banco 			1 	3 		3 - Num ‘104’ G001 
			$conteudo .= $this->formatNumber($this->lote,4);            //Lote				Lote de Serviço 			4 	7 		4 - Num *G002
			$conteudo .= '3';        									//Registro 			Tipo de Registro 			8 	8 		1 - Num '1' *G003
			$conteudo .= $this->formatNumber($i,5);            			//Nº do Registro 	Nº seq do Registro no Lote 	9 	13 		5 - Num *G038
			$conteudo .= 'Q';            								//Segmento 			Cód. Segmento				14 	14 		1 - Alfa ‘Q’ *G039
			$conteudo .= ' ';
			//$conteudo .= '01';
			$conteudo .= $this->formatNumber($cliente->cod_movimento,2);// Cód. Mov. 		Código de Movimento Remessa 16 	17 		2 - Num *C004			
			
			// dados sacado
			$conteudo .= '1';
			$conteudo .= $this->formatNumber($cliente->cpf,15);    		// Número 			Número de Inscrição 		19 	33 		15 - Num *G006                                     
			$conteudo .= $this->formatString($cliente->nome,40);    	// Nome 			Nome 						34 	73 		40 - Alfa
			$conteudo .= $this->formatString($cliente->endereco,40);    // Endereço 		Endereço 					74 	113 	40 - Alfa G032
			$conteudo .= $this->formatString($cliente->bairro,15);    	// Bairro 			Bairro 						114 128 	15 - Alfa G032
			$conteudo .= $this->formatString($cliente->cep,8);    		// CEP 				CEP 						129 133 	5 - Num G034
			$conteudo .= $this->formatString($cliente->cidade,15);    	// Cidade 			Cidade 						137 151 	15 - Alfa G033
			$conteudo .= $this->formatString($cliente->estado,2);    	// UF 				Unidade da Federação 		152 153 	2 - Alfa G036
			// sac. /aval
			$conteudo .= '0';
			$conteudo .= $this->formatNumber('0',15);
			$conteudo .= $this->complementoRegistro(40,"brancos");
			$conteudo .= $this->complementoRegistro(3,"brancos");
			$conteudo .= $this->complementoRegistro(20,"brancos");
			$conteudo .= $this->complementoRegistro(8,"brancos");
			$conteudo .= chr(13).chr(10); 											//essa é a quebra de linha
			//$this->lote++;
			
			$this->val_total += $cliente->valor;

			$this->tot_linhas++;		
			//$this->checkTitulo($conteudo,$cliente,$i-1);
			$this->conteudo .= $conteudo;
			
			$i++;
			
			#-- ----------------------------------------------------------------------------------------------------------
			if(empty($config->juros)) continue;
			
			$conteudo  = '104'; 										//Banco Código do Banco na Compensação 1 3 3 - Num ‘104’ G001
			$conteudo .= $this->formatNumber($this->lote,4); 			//Lote Lote de Serviço 4 7 4 - Num *G002
			$conteudo .= '3'; 											//Registro Tipo de Registro 8 8 1 - Num ‘3’ *G003
			$conteudo .= $this->formatNumber($i,5); 					//Nº do Registro Nº Sequencial do Registro no Lote 9 13 5 - Num *G038
			$conteudo .= 'R'; 											//Segmento Cód. Segmento do Registro Detalhe 14 14 1 - Alfa ‘R’ *G039
			$conteudo .= ' ';											//CNAB Uso Exclusivo FEBRABAN/CNAB 15 15 1 - Alfa Brancos G004
			$conteudo .= $this->formatNumber($cliente->cod_movimento,2);//Cód. Mov. Código de Movimento Remessa 16 17 2 - Num *C004
			$conteudo .= '0';											//Cód. Desc. 2 Código do Desconto 2 18 18 1 - Num *C021
			$conteudo .= $this->formatNumber(0,8);						//Data Desc. 2 Data do Desconto 2 19 26 8 - Num C022
			$conteudo .= $this->formatNumber(0,15);						//Desconto 2 Valor/Percentual a ser Concedido 27 41 13 2 Num C023
			$conteudo .= '0';											//Cód. Desc. 3 Código do Desconto 3 42 42 1 - Num *C021
			$conteudo .= $this->formatNumber(0,8);						//Data Desc. 3 Data do Desconto 3 43 50 8 - Num C022
			$conteudo .= $this->formatNumber(0,15);						//Desconto 3 Valor/Percentual a Ser Concedido 51 65 13 2 Num C023
			$conteudo .= '2';											//Cód. Multa Código da Multa 66 66 1 - Alfa G073
			$conteudo .= $this->formatData($cliente->vencimento,1);		//Data da Multa Data da Multa 67 74 8 - Num G074
			$conteudo .= $this->formatNumber($config->juros,15);		//Multa Valor/Percentual a Ser Aplicado 75 89 13 2 Num G075
			$conteudo .= $this->complementoRegistro(10,"brancos");		//Informação ao Sacado Informação ao Sacado 90 99 10 - Alfa *C036
			$conteudo .= $this->complementoRegistro(40,"brancos");		//Informação 3 Mensagem 3 100 139 40 - Alfa *C037
			$conteudo .= $this->complementoRegistro(40,"brancos");		//Informação 4 Mensagem 4 140 179 40 - Alfa *C037
			$conteudo .= $this->complementoRegistro(50,"brancos");		//E-mail sacado E-mail sacado p/ envio de informações 180 229 50 - Alfa G032
			$conteudo .= $this->complementoRegistro(11,"brancos");		//CNAB Uso Exclusivo FEBRABAN/CNAB 230 240 11 - Alfa Brancos G004
			$conteudo .= chr(13).chr(10); 	
			$this->tot_linhas++;		
			$this->conteudo .= $conteudo;
			$i++;
			
		} // fecha loop de clientes
		  
	}

	public function setTrailler(){
		
		$conteudo = '104'; 									//01.5 Banco Código do Banco na Compensação 1 3 3 - Num ‘104’ G001
		$conteudo .= $this->formatNumber($this->lote,4);	//02.5 Lote Lote de Serviço 4 7 4 - Num *G002
		$conteudo .= '5';									//03.5 Controle Registro Tipo de Registro 8 8 1 - Num ‘5’ *G003
		$conteudo .= $this->complementoRegistro(9,"brancos"); //04.5 CNAB Uso Exclusivo FEBRABAN/CNAB 9 17 9 - Alfa Brancos G004
		$conteudo .= $this->formatNumber($this->tot_linhas,6);//05.5 Qtde de Registros Quantidade de Registros no Lote 18 23 6 - Num *G057
		$conteudo .= $this->formatNumber('0',6);				  //06.5 Quantidade de Títulos em Cobrança 24 29 6 - Num *C070
		$conteudo .= $this->formatNumber('0',17);				  //07.5 Totalização da Cobrança Simples Valor Total dosTítulos em Carteiras 30 46 15 2 Num *C071
		$conteudo .= $this->formatNumber('0',6);				  //08.5 Quantidade de Títulos em Cobrança 47 52 6 - Num *C070 
		$conteudo .= $this->formatNumber('0',17);				  //09.5 Totalização da Cobrança Caucionada Valor Total dosTítulos em Carteiras 53 69 15 2 Num *C071
		$conteudo .= $this->formatNumber('0',6);				  //10.5 Quantidade de Títulos em Cobrança 70 75 6 - Num *C070
		$conteudo .= $this->formatNumber('0',17);				  //11.5 Totalização da Cobrança Descontada Quantidade de Títulos em Carteiras 76 92 15 2 Num *C071
		$conteudo .= $this->complementoRegistro(31,"brancos"); //12.5 CNAB Uso Exclusivo FEBRABA/CNAB 93 123 31 - Alfa Brancos G004
		$conteudo .= $this->complementoRegistro(117,"brancos"); //15.5 CNAB Uso Exclusivo FEBRABAN/CNAB 124 240 117 - Alfa Brancos G004
		$conteudo .= chr(13).chr(10);
		$this->tot_linhas++;
		#-------------------------------------------------------------------------------------
		$conteudo .= '104';
		$conteudo .= '9999';
		$conteudo .= '9';
		$conteudo .= $this->complementoRegistro(9,"brancos");
		$conteudo .= $this->formatNumber(1,6);             		// numero de inscricao    cpf ou cnpj                		221 234        9(14)	
		$conteudo .= $this->formatNumber($this->tot_linhas+1,6);
		$conteudo .= $this->complementoRegistro(6,"brancos");
		$conteudo .= $this->complementoRegistro(205,"brancos");
		$this->conteudo .= $conteudo.chr(13).chr(10);
	
	}

	protected function checkTitulo($linha,$titulo,$seq){
		if(strlen(trim($linha)) != 241) echo 'Linha '.$seq.' com tamanho invalido ('.strlen(trim($linha)).')<br>';
		$arr_layout = array();
		$arr_layout['id_titulo'] = array('start'=>'38','size'=>'25','func'=>'formatNumber');
		$arr_layout['vencimento'] = array('start'=>'121','size'=>'6','func'=>'formatData');
		$arr_layout['valor'] = array('start'=>'127','size'=>'13','func'=>'formatValor');
		$arr_layout['tipo'] = array('start'=>'219','size'=>'2','func'=>'');
		$arr_layout['cpf'] = array('start'=>'221','size'=>'14','func'=>'formatString');
		$arr_layout['nome'] = array('start'=>'235','size'=>'30','func'=>'formatString');
		$arr_layout['endereco'] = array('start'=>'275','size'=>'40','func'=>'formatString');
		$arr_layout['bairro'] = array('start'=>'315','size'=>'12','func'=>'formatString');
		$arr_layout['cep'] = array('start'=>'327','size'=>'8','func'=>'formatString');
		$arr_layout['cidade'] = array('start'=>'335','size'=>'15','func'=>'formatString');
		$arr_layout['estado'] = array('start'=>'350','size'=>'2','func'=>'formatString');

		foreach($arr_layout as $campo=>$def){
			$metodo = $def['func'];
			$def['start'] = $def['start'] -1; 
			if($metodo){
				if($this->$metodo($titulo->$campo,$def['size']) != substr($linha,$def['start'],$def['size'])){
					echo "Linha ".$seq." campo: ".$campo." valor informado :".$titulo->$campo." => valor na linha ".substr($linha,$def['start'],$def['size'])."<br>";
				}
			
			}
			else{
			
			}
		}
		
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
		if($data == false){
			$data = date('Y-m-d');
		}
		// data em branco
		if($data === ''){
			return '00000000';
		}
		
		$data = substr($data,0,10);
		$aData = explode('-',$data);

		$timestamp = mktime(0, 0, 0, $aData[1], $aData[2] +$add_dias, $aData[0]);

		$return = gmdate("d",$timestamp).gmdate("m",$timestamp).gmdate("Y",$timestamp);
		return $return;	
	}	


}

