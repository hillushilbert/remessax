<?php

namespace Skynix\Remessax\Remessa;

use DateInterval;
use DateTime;
use Skynix\Remessax\Remessa;

//config::prefixo_cooperativa

/*
* @descr: Gera o arquivo de remessa para cobranca no padrao CNAB 240 vers. BB
*/

class 	Sicoob240 extends Remessa {

	private $lote = '0001';
	private $remessa_id;
	private $lote_registros = array();
	public function setRemessaId($remessa_id){
		$this->remessa_id = $remessa_id;
	}
	
	public function setHeader(){
		
		$this->config->cpf_cnpj = str_replace('.','',$this->config->cpf_cnpj);
		$this->config->cpf_cnpj = str_replace('/','',$this->config->cpf_cnpj);
		$this->config->cpf_cnpj = str_replace('-','',$this->config->cpf_cnpj);
		
		$config = $this->config;		
		$conteudo = '';
		$conteudo .= '756'; 										//01.0 Código do Banco na Compensação 1 3 3 - Numérico 001 G001 
																	//001 para Banco do Brasil S.A.
		$conteudo .= '0000'; 										//02.0 Lote de Serviço 4 7 4 - Numérico 0000 G002
		$conteudo .= '0'; 											//03.0 Tipo de Registro 8 8 1 - Numérico 0 G003
		$conteudo .= $this->complementoRegistro(9,"brancos"); 		//04.0 Uso Exclusivo FEBRABAN / CNAB 9 17 9 - Alfanumérico Brancos 
																	//G004
		$conteudo .= '2'; 											//05.0 Tipo de Inscrição da Empresa 18 18 1 - Numérico G005 
																	//1 – para CPF e 2 – para CNPJ.
		$conteudo .= $this->formatNumber($config->cpf_cnpj,14);		// 019	032	014	-	Num - Número de Inscrição da Empresa	
		
		#CAMPO NOVO
		$conteudo .= $this->complementoRegistro(20,"brancos");		// 033	052	020	-	Alfa - Código do Convênio no Sicoob: Preencher com espaços em branco


		$conteudo .= $this->formatNumber($config->agencia,5); 		// 053	057	005	-	Num - Prefixo da Cooperativa: vide planilha "Contracapa" deste arquivo
		$conteudo .= $this->formatNumber($config->agencia_dv,1);	// 058	058	001	-	Alfa - Dígito Verificador do Prefixo: vide planilha "Contracapa" deste arquivo
		$conteudo .= $this->formatNumber($config->conta,12); 		// 059	070	012	-	Num - Conta Corrente: vide planilha "Contracapa" deste arquivo
		$conteudo .= $this->formatNumber($config->conta_dv,1);		// 071	071	001	-	Num - Dígito Verificador da Conta: vide planilha "Contracapa" deste arquivo
		$conteudo .= '0';											// 072	072	001	-	Alfa - Dígito Verificador da Ag/Conta: Preencher com zeros
		$conteudo .= $this->formatString($config->razao_social,30);	// 073	102	030	-	Alfa - Nome da Empresa
		$conteudo .= $this->formatString('SICOOB',30); 				// 103	132	030	-	Alfa - Nome do Banco: SICOOB
		$conteudo .= $this->complementoRegistro(10,"brancos"); 		// 133	142	010	-	Alfa - Uso Exclusivo FEBRABAN / CNAB: Preencher com espaços em branco
		$conteudo .= '1';											// 143	143	001	-	Num	Arquivo	Código - Código Remessa / Retorno: "1"
		$conteudo .= $this->formatData();							// 144	151	008	-	Num - Data de Geração do Arquivo
		$conteudo .= date('His');									// 152	157	006	-	Num - Hora de Geração do Arquivo
		$conteudo .= $this->formatNumber($this->remessa_id,6);		// 158	163	006	-	Num - Seqüência (NSA)		
		$conteudo .= '081';											// 164	166	003	-	Num - No da Versão do Layout do Arquivo: "081"
		$conteudo .= '00000';										// 167	171	005	-	Num - Densidade de Gravação do Arquivo: "00000"
		$conteudo .= $this->complementoRegistro(20,"brancos");		// 172	191	020	-	Alfa - brancos
		$conteudo .= $this->complementoRegistro(20,"brancos");		// 192	211	020	-	Alfa
		$conteudo .= $this->complementoRegistro(29,"brancos");		// 212	240	029	-	Alfa
		$conteudo .= chr(13).chr(10); 
	

		// =======================================================================================
		
		## REGISTRO HEADER - lote	
		//001	003	003	-	Num - Código do Banco na Compensação: "756"
		$conteudo .= '756'; 
		//004	007	004	-	Num - Lote
		$conteudo .= $this->lote; 
		//008	008	001	-	Num - Tipo de Registro: "1"
		$conteudo .= '1';
		//009	009	001	-	Alfa - Tipo de Operação: "R"
		$conteudo .= 'R';
		//010	011	002	-	Num - Tipo de Serviço: "01"
		$conteudo .= '01';  
		//012	013	002	-	Alfa - Uso Exclusivo FEBRABAN/CNAB: Preencher com espaços em branco
		$conteudo .= '  '; 
		//014	016	003	-	Num - Nº da Versão do Layout do Lote: "040"
		$conteudo .= '040'; 
		//017	017	001	-	Alfa - Uso Exclusivo FEBRABAN/CNAB: Preencher com espaços em branco
		$conteudo .= ' ';
		//018	018	001	-	Num - "Tipo de Inscrição da Empresa: '1'  =  CPF '2'  =  CGC / CNPJ"
		$conteudo .= '2'; 
		//019	033	015	-	Num - Nº de Inscrição da Empresa
		$conteudo .= $this->formatNumber($config->cpf_cnpj,15); 
		//034	053	020	-	Alfa - Código do Convênio no Banco: Preencher com espaços em branco
		$conteudo .= $this->complementoRegistro(20,"brancos");
		//054	058	005	-	Num - Prefixo da Cooperativa: vide planilha "Contracapa" deste arquivo
		$conteudo .= $this->formatNumber($config->agencia,5);
		//059	059	001	-	Alfa - Dígito Verificador do Prefixo: vide planilha "Contracapa" deste arquivo
		$conteudo .= $this->formatNumber($config->agencia_dv,1);
		//060	071	012	-	Num - Conta Corrente: vide planilha "Contracapa" deste arquivo
		$conteudo .= $this->formatNumber($config->conta,12);
		//072	072	001	-	Num - Dígito Verificador da Conta: vide planilha "Contracapa" deste arquivo
		$conteudo .= $this->formatNumber($config->conta_dv,1);
		//073	073	001	-	Alfa
		$conteudo .= ' ';
		//074	103	030	-	Alfa - Nome da Empresa
		$conteudo .= $this->formatString($config->razao_social,30);
		//104	143	040	-	Alfa
		$conteudo .= $this->complementoRegistro(40,"brancos");
		//144	183	040	-	Alfa
		$conteudo .= $this->complementoRegistro(40,"brancos");
		//184	191	008	-	Num - Nº Rem./Ret.	
		$conteudo .= $this->formatNumber($this->remessa_id,8);
		// 192	199	008	-	Num - Data de Gravação Remessa/Retorno
		$conteudo .= $this->formatData();
		//200	207	008	-	Num - Data do Crédito: "00000000"
		$conteudo .= '00000000';
		// 208	240	033	-	Alfa - Uso Exclusivo FEBRABAN/CNAB: Preencher com espaços em branco
		$conteudo .= $this->complementoRegistro(33,"brancos");
		$conteudo .= chr(13).chr(10); 								//essa é a quebra de linha
		// -------------------------------------------------------------------------
			
		$this->tot_linhas++;
		
		$this->conteudo = $conteudo;
	}

	
	public function setMovimento(){
		
		$config = $this->config;

		### DADOS DOS CLIENTES PARA TESTE
		//var_dump($config );
		$i = 1;
		
		// total de registros do lote
		$this->lote_registros[$this->lote] = (count($this->titulos) * 2) + 2;
		
		foreach($this->titulos as $cliente)
		{
			
			$label = '<a href="#" onclick="openLinkOnTab(\''.$cliente->id_aluno.'\');">'.$cliente->matricula.'</a>';
			// cpf
			if(empty($cliente->cpf)){
				$this->arr_mensagens[] = $label.' :: CNPJ ou CPF do Aluno faltando';
			}
			
			// endereco
			if(empty($cliente->endereco)){
				$this->arr_mensagens[] = $label.' :: Endereco do Aluno faltando';
			}
			
			// bairro
			if(empty($cliente->bairro)){
				$this->arr_mensagens[] = $label.' :: Bairro do Aluno faltando';
			}
			
			// CEP
			if(empty($cliente->cep)){
				$this->arr_mensagens[] = $label.' :: CEP do Aluno faltando';
			}

			// Cidade
			if(empty($cliente->cidade)){
				$this->arr_mensagens[] = $label.' :: Cidade do Aluno faltando';
			}
			// Estado
			if(empty($cliente->estado)){
				$this->arr_mensagens[] = $label.' :: Estado do Aluno faltando';
			}
			// Valor
			if(empty($cliente->valor)){
				$this->arr_mensagens[] = $label.' :: Valor da mensalidade faltando';
			}
            			
			$cliente->cod_movimento = !empty($cliente->cod_movimento) ? $cliente->cod_movimento : '01';
			
			$conteudo = '';	
	
			//01.3P Código do Banco na Compensação 1 3 3 - Numérico G001 001 para Banco do Brasil S.A.
			$conteudo .= '756';	
			//02.3P Lote Lote de Serviço 4 7 4 - Numérico G002 Informar o número do lote ao qual pertence o registro. Deve ser igual ao número informado no Header do lote.
			$conteudo .= $this->formatNumber($this->lote,4);        
			//03.3P Tipo de Registro 8 8 1 - Numérico '3' G003
			$conteudo .= '3';	
			//04.3P Nº Sequencial do Registro no Lote 9 13 5 - Numérico G038 Começar com 00001 e ir incrementando em 1 a cada nova linha de registro detalhe.
			$conteudo .= $this->formatNumber($i,5); 
			//05.3P Cód. Segmento do Registro Detalhe 14 14 1 - Alfanumérico 'P' G039
			$conteudo .= 'P';
			//06.3P Uso Exclusivo FEBRABAN/CNAB 15 15 1 - Alfanumérico Brancos G004
			$conteudo .= ' ';
			//016	017	002	-	Num - Cód. Mov.		
			$conteudo .= '01'; // $cliente->cod_movimento
			//08.3P Agência Mantenedora da Conta 18 22 5 - Numérico G008
			$conteudo .= $this->formatNumber($config->agencia,5);	
			//09.3P Dígito Verificador da Agência 23 23 1 - Alfanumérico G009 Obs. Em caso de dígito X informar maiúsculo.
			$conteudo .= $this->formatNumber($config->agencia_dv,1);
			//10.3P Número da Conta Corrente 24 35 12 - Numérico G010
			$conteudo .= $this->formatNumber($config->conta,12);
			//11.3P Dígito Verificador da Conta 36 36 1 - Alfanumérico G011 Obs. Em caso de dígito X informar maiúsculo.
			$conteudo .= $this->formatNumber($config->conta_dv,1);
			//12.3P Dígito Verificador da Ag/Conta 37 37 1 - Alfanumérico G012 Campo não tratado pelo Banco do Brasil. Informar 'branco' (espaço) OU zero.
			$conteudo .= ' ';
			
			//038	057	020	-	Alfa - Nosso Número:
			/*
			Nosso Número:
			- Se emissão a cargo do Sicoob (vide planilha ""Contracapa"" deste arquivo):
				  NumTitulo - 10 posições (1 a 10) = Preencher com zeros
				  Parcela - 02 posições (11 a 12) - ""01"" se parcela única
				  Modalidade - 02 posições (13 a 14) - vide planilha ""Contracapa"" deste arquivo
				  Tipo Formulário - 01 posição  (15 a 15):
					  ""1"" -auto-copiativo
					  ""3""-auto-envelopável
					  ""4""-A4 sem envelopamento
					  ""6""-A4 sem envelopamento 3 vias
				 Em branco - 05 posições (16 a 20)
			- Se emissão a cargo do Beneficiário (vide planilha ""Contracapa"" deste arquivo):
				 NumTitulo - 10 posições (1 a 10): Vide planilha ""02.Especificações do Boleto"" deste arquivo item 3.13
				 Parcela - 02 posições (11 a 12) - ""01"" se parcela única
				 Modalidade - 02 posições (13 a 14) - vide planilha ""Contracapa"" deste arquivo
				 Tipo Formulário - 01 posição  (15 a 15):
					  ""1"" -auto-copiativo
					  ""3""-auto-envelopável
					  ""4""-A4 sem envelopamento
					  ""6""-A4 sem envelopamento 3 vias
				 Em branco - 05 posições (16 a 20)

			*/
			$dig = $this->nossoNumero($config->agencia,$config->convenio,$cliente->id_titulo);
			//var_dump($dig);
			$nosso_numero = str_pad($cliente->id_titulo.$dig,10,'0',STR_PAD_LEFT).
							'01'.
							str_pad($config->modalidade_cobranca,2,'0',STR_PAD_LEFT).
							'4'.
							'     ';
			//die($nosso_numero);
			//var_dump(str_pad($cliente->id_titulo.$dig,10,'0',STR_PAD_LEFT),$config->agencia,$config->convenio,$cliente->id_titulo);				
							
			$conteudo.= $nosso_numero; 

			//058	058	001	-	Num - Carteira		
			$conteudo.= intval($config->carteira);
			
			//059	059	001	-	Num - Forma de Cadastr. do Título no Banco: "0"
			$conteudo .= '0';
			
			//060	060	001	-	Alfa - Tipo de Documento: Preencher com espaços em branco
			$conteudo .= ' ';
			
			//061	061	001	-	Num - "Identificação da Emissão do Boleto: (vide planilha ""Contracapa"" deste arquivo) '1'  =  Sicoob Emite '2'  =  Beneficiário Emite"
			$conteudo .= '1';
			
			//062	062	001	-	Alfa - Distrib. Boleto		
			$conteudo .= '2'; 
			
			//063	077	015	-	Alfa - Nº do Documento			
			$conteudo .= $this->formatNumber($cliente->id_titulo,15);
			
			//078	085	008	-	Num - vencimento
			$conteudo .= $this->formatData($cliente->vencimento);  
			
			//086	100	013	002	Num	Valor do Título		
			$conteudo .= $this->formatValor($cliente->valor,15);  
			
			//101	105	005	-	Num	Ag. Cobradora			
			$conteudo .= $this->formatNumber(0,5);
			
			//106	106	001	-	Alfa	DV- Dígito Verificador da Agência: Preencher com espaços em branco		
			$conteudo .= ' ';
			
			//107	108	002	-	Num	Espécie de Título			
			/*
			"Espécie do Título:
				'01'  =  CH Cheque
				'02'  =  DM Duplicata Mercantil
				'03'  =  DMI Duplicata Mercantil p/ Indicação
				'04'  =  DS Duplicata de Serviço
				'05'  =  DSI Duplicata de Serviço p/ Indicação
				'06'  =  DR Duplicata Rural
				'07'  =  LC Letra de Câmbio
				'08'  =  NCC Nota de Crédito Comercial
				'09'  =  NCE Nota de Crédito a Exportação
				'10'  =  NCI Nota de Crédito Industrial
				'11'  =  NCR Nota de Crédito Rural
				'12'  =  NP Nota Promissória
				'13'  =  NPR Nota Promissória Rural
				'14'  =  TM Triplicata Mercantil
				'15'  =  TS Triplicata de Serviço
				'16'  =  NS Nota de Seguro
				'17'  =  RC Recibo
				'18'  =  FAT Fatura
				'19'  =  ND Nota de Débito
				'20'  =  AP Apólice de Seguro
				'21'  =  ME Mensalidade Escolar
				'22'  =  PC Parcela de Consórcio
				'23'  =  NF Nota Fiscal
				'24'  =  DD Documento de Dívida
				‘25’ = Cédula de Produto Rural
				'31' = Cartão de Crédito
				'32' = BDP Boleto de Proposta
				'99'  =  Outros"
			*/
			$conteudo .= '04';
			
			//109	109	001	-	Alfa	Aceite			
			$conteudo .= $this->formatString($config->aceite,1);
			
			//110	117	008	-	Num	Data Emissão do Título			
			$conteudo .= $this->formatData(date('Y-m-d'));  
			
			//118	118	001	-	Num - Cód. Juros Mora		
			$conteudo .= '2';
			
			//119	126	008	-	Num - Data do Juros de Mora: preencher com a Data de Vencimento do Título formato DDMMAAAA
			$conteudo .= $this->addDays($cliente->vencimento,1); 
			
			//127	141	013	002	Num - Juros Mora		
			$conteudo .= $this->formatValor(round($cliente->valor * ($config->mora / 100),2),15); 

			
			// regras para desconto	
			if(!empty($cliente->vencimento_antecipado) && !empty($cliente->valor_antecipado)){
				$desconto  = $cliente->valor - $cliente->valor_antecipado;
				$conteudo .= '1';													//Cód. Desc. 1 		Código do Desconto 1 		142 142 	1 - Num *C021
				$conteudo .= $this->formatData($cliente->vencimento_antecipado);	//Data Desc. 1 		Data do Desconto 1 			143 150 	8 - Num C022
				$conteudo .= $this->formatValor($desconto ,15);   					//Desconto 1 		Valor a ser Concedido		151 165 	13 2 Num C023				
			}else{
				$conteudo .= '0';													//Cód. Desc. 1 		Código do Desconto 1 		142 142 	1 - Num *C021
				///$conteudo .= '        ';											//Data Desc. 1 		Data do Desconto 1 			143 150 	8 - Num C022
				$conteudo .= $this->formatValor(0,8);											//Data Desc. 1 		Data do Desconto 1 			143 150 	8 - Num C022
				$conteudo .= $this->formatValor(0,15);       						//Desconto 1 		Valor a ser Concedido		151 165 	13 2 Num C023
			}			
			
			//33.3P Valor do IOF a ser Recolhido 166 180 13 2 Numérico C024 Zeros, quando não houver IOF a ser cobrando.
			$conteudo .= $this->formatValor(0,15);

			//34.3P Valor do Abatimento 181 195 13 2 Numérico G045
			/*
			O valor do abatimento, sempre que informado, é deduzido do valor original do título, não importa quando o
			sacado efetue o pagamento.			
			*/
			$conteudo .= $this->formatValor(0,15);

            //Identificação do Título na Empresa 196 220 25 - Alfanumérico G072
            $conteudo .= $this->formatNumber($cliente->id_titulo,25);
            
            
			//221	221	001	-	Num	Código p/ Protesto			
			$conteudo .= '3';

			//222	223	002	-	Num	Prazo p/ Protesto			
			$conteudo .= '00';

			//224	224	001	-	Num	Código p/ Baixa/Devolução			
			$conteudo .= '0';

			//225	227	003	-	Alfa	Prazo p/ Baixa/Devolução			
			$conteudo .= '   ';

			//40.3P Código da Moeda 228 229 2 - Numérico G065
			$conteudo .= '09';

			//230	239	010	-	Num	Número do Contrato - 			
			$conteudo .= $this->complementoRegistro(10,"zeros");

			//42.3P Uso Exclusivo FEBRABAN/CNAB 240 240 1 - Alfanumérico Brancos G004 Informar 'brancos' (espaços).
			$conteudo .= $this->complementoRegistro(1,"brancos");
			
			$conteudo .= chr(13).chr(10); 								//essa é a quebra de linha
			//$this->lote++;
			$this->tot_linhas++;
			
			$i++;
			
			// ---------------------------------------------------------------------------------------------------------------------------------------
			// Registro Detalhe - Segmento Q (Obrigatório - Remessa)
			//1	3	3	- 	Num - Código do Banco na Compensação: "756"
			$conteudo .= '756';             							
			//02.3Q Lote de Serviço 4 7 4 - Numérico G002 Informar o número do lote ao qual pertence o registro. Deve ser igual ao número informado no Header do lote.
			$conteudo .= $this->formatNumber($this->lote,4);
			//03.3Q Tipo de Registro 8 8 1 - Numérico ‘3’ G003
			$conteudo .= '3';
			//04.3Q Nº Sequencial do Registro no Lote 9 13 5 - Numérico G038 Começar com 00002 e ir incrementando em 1 a cada nova linha do registro detalhe, esse sequencial é continuação do segmento 'P' anterior.
			$conteudo .= $this->formatNumber($i,5); 
			//05.3Q Cód. Segmento do Registro Detalhe 14 14 1 - Alfanumérico ‘Q’ G039
			$conteudo .= 'Q';
			//06.3Q Uso Exclusivo FEBRABAN/CNAB 15 15 1 - Alfanumérico Brancos G004
			$conteudo .= ' ';
			//07.3Q Código de Movimento Remessa 16 17 2 - Numérico C004 Repetir código informado no segmento P.
			$conteudo .= '01';
			//08.3Q Tipo de Inscrição 18 18 1 - Numérico G005 1 – para CPF e 2 – para CNPJ. Pode ser informado '0' (zero), nesse caso, o sistema entende que trata-se de CNPJ.
			$conteudo .= '1';
			//09.3Q Número de Inscrição 19 33 15 - Numérico G006 Informar número da inscrição (CPF ou CNPJ) da Empresa, alinhado à direita com zeros à esquerda.
			$conteudo .= $this->formatNumber($cliente->cpf,15);
			//10.3Q Nome 34 73 40 - Alfanumérico G013 São tratadas somente 37 posições, da posição 34 a 70.
			$conteudo .= $this->formatString($cliente->nome,40); 
			//11.3Q Endereço 74 113 40 - Alfanumérico G032 Informar endereço completo, principalmente naqueles casos onde o Banco quem faz a distribuição dos bloquetos.
			$conteudo .= $this->formatString($cliente->endereco,40);
			//12.3Q Bairro 114 128 15 - Alfanumérico G032 São tratadas somente 12 posições, da posição 114 a 125.
			$conteudo .= $this->formatString($cliente->bairro,15); 
			//13.3Q CEP 129 133 5 - Numérico G034 Preencher com CEP válido, caso contrário, o registro será recusado.
			//14.3Q Sufixo do CEP 134 136 3 - Numérico G035 Preencher com sufixo de CEP válido, caso contrário, o registro será recusado.
			$conteudo .= $this->formatString($cliente->cep,8);
			//15.3Q Cidade 137 151 15 - Alfanumérico G033
			$conteudo .= $this->formatString($cliente->cidade,15); 
			//16.3Q Unidade da Federação 152 153 2 - Alfanumérico G036
			$conteudo .= $this->formatString($cliente->estado,2);
			//17.3Q Tipo de Inscrição 154 154 1 - Numérico G005 Este campo deve estar preenchido somente quando o Cedente original do título for outro. Caso não haja sacador/avalista preencher com '0' (zero) ou 'branco' (espaço)
			$conteudo .= '0';
			//18.3Q Número de Inscrição 155 169 15 - Numérico G006 Este campo deve estar preenchido somente quando o Cedente original do título for outro. Caso não haja sacador/avalista preencher com Zeros ou 'brancos' (espaços).
			$conteudo .= $this->formatNumber('0',15);
			//19.3Q Nome do Sacador/Avalista 170 209 40 - Alfanumérico G013 Este campo deve estar preenchido somente quando o Cedente original do título for outro. São tratadas somente 21 posições, da posição 170 a 190. Caso não haja
			//											  sacador/avalista, preencher com 'brancos'. Se informado sacador/avalista não será possível a utilização da Mensagem 1 ou 3.
			$conteudo .= $this->complementoRegistro(40,"brancos");
			//20.3Q Cód. Bco. Corresp. na Compensação 210 212 3 - Numérico C031 Campo não tratado. Preencher com 'zeros'.
			$conteudo .= $this->complementoRegistro(3,"zeros");
			//21.3Q Nosso Nº no Banco Correspondente 213 232 20 - Alfanumérico C032 Campo não tratado. Preencher com 'brancos'.
			$conteudo .= $this->complementoRegistro(20,"brancos");
			//22.3Q Uso Exclusivo FEBRABAN/CNAB 233 240 8 - Alfanumérico Brancos G004 Informar 'brancos' (espaços).
			$conteudo .= $this->complementoRegistro(8,"brancos");
			
			//essa é a quebra de linha
			$conteudo .= chr(13).chr(10); 											
			$this->tot_linhas++;
			$i++;
			
			#-- ----------------------------------------------------------------------------------------------------------
			$acumulado = 1;
			/*
			if(!empty($config->juros)) 
			{
				$conteudo .= '756'; 										//1 3 3 - Numérico 001 G001 001 para Banco do Brasil S.A.
				$conteudo .= $this->formatNumber($this->lote,4); 			//4 7 4 - Numérico G002 Informar o número do lote ao qual pertence o registro. Deve
																			 #ser igual ao número informado no Header do lote
				$conteudo .= '3'; 											//8 8 1 - Numérico ‘3’ G003 3 – para registro Detalhe.
				$conteudo .= $this->formatNumber($i,5); 					//9 13 5 - Numérico G038
																			#	Ir incrementando em 1 a cada nova linha do registro detalhe,
																			#	esse sequencial é continuação dos segmentos 'P' e 'Q'
																			#	anterior.
				$conteudo .= 'R'; 											//14 14 1 - Alfanumérico ‘R’ G039
				$conteudo .= ' ';											//15 15 1 - Alfanumérico Brancos G004
				$conteudo .= '01'; //$this->formatNumber($cliente->cod_movimento,2);//16 17 2 - Numérico C004 Repetir código informado no segmento P.
				$conteudo .= '0';											//18 18 1 - Numérico C021 Campo não tratado. Informar Zeros ou 'brancos'
				$conteudo .= $this->formatNumber(0,8);						//19 26 8 - Numérico C022 Campo não tratado. Informar Zeros ou 'brancos'.
				$conteudo .= $this->formatNumber(0,15);						//27 41 13 2 Numérico C023 Campo não tratado. Informar Zeros ou 'brancos'.
				$conteudo .= '0';											//42 42 1 - Numérico C021 Campo não tratado. Informar Zeros ou 'brancos'.
				$conteudo .= $this->formatNumber(0,8);						//43 50 8 - Numérico C022 Campo não tratado. Informar Zeros ou 'brancos'.
				$conteudo .= $this->formatNumber(0,15);						//51 65 13 2 Numérico C023 Campo não tratado. Informar Zeros ou 'brancos'.
				$conteudo .= '2';											//66 66 1 - Alfanumérico G073
																			#	Informamos que em caso de cobrança de multa, esta
																			#	percentual/valor é fixo, sendo cobrado apenas uma única vez.
																			#	Caso não tenha multa, informar '0' (zero).
				$conteudo .= $this->formatData($cliente->vencimento,1);		//67 74 8 - Numérico G074 Caso não tenha multa, informar 'zeros'. Sistema aceita a
																			#	mesma data do vencimento, ou dia seguinte.
				$conteudo .= $this->formatNumber($config->juros,15);		//75 89 13 2 Numérico G075 Caso não tenha multa, informar 'zeros'.
				$conteudo .= $this->complementoRegistro(10,"brancos");		//90 99 10 - Alfanumérico C036 Campo não tratado. Informar Zeros ou 'brancos'
				$conteudo .= $this->complementoRegistro(40,"brancos");		//100 139 40 - Alfanumérico C037
																			#	No caso em que a impressão do bloqueto é feita pelo banco,
																			#	essa mensagem sobrescreve a mensagem 1 do header de
																			#	lote e o campo Sacador/Avalista, ou seja, se o cliente utilizar
																			#	os campos Mensagem 1, Mensagem 3 e Sacador/Avalista,
																			#	somente a Mensagem 3 será impressa no boleto. A
																			#	mensagem 3 é impressa no campo instruções somente da 1ª
																			#	via.
				$conteudo .= $this->complementoRegistro(40,"brancos");		//140 179 40 - Alfanumérico C037 Campo não tratado. Informar Zeros ou 'brancos'.
				$conteudo .= $this->complementoRegistro(20,"brancos");		//180 199 20 - Alfanumérico Brancos G004 Campo não tratado. Informar 'brancos'.
				$conteudo .= $this->formatNumber(0,8);						//200 207 8 - Numérico C038 Campo não tratado. Informar Zeros.
				$conteudo .= $this->formatNumber(0,3);						//208 210 3 - Numérico G001 Campo não tratado. Informar Zeros.
				$conteudo .= $this->formatNumber(0,5);						//211 215 5 - Numérico G008 Campo não tratado. Informar Zeros.
				$conteudo .= $this->complementoRegistro(1,"brancos");		//216 216 1 - Alfanumérico G009 Campo não tratado. Informar Zeros ou 'brancos'
				$conteudo .= $this->formatNumber(0,12);						//217 228 12 - Numérico G010 Campo não tratado. Informar Zeros.
				$conteudo .= $this->formatNumber(0,1);						//229 229 1 - Alfanumérico G011 Campo não tratado. Informar Zeros ou 'brancos'.
				$conteudo .= $this->formatNumber(0,1);						//230 230 1 - Alfanumérico G012 Campo não tratado. Informar Zeros ou 'brancos'.
				$conteudo .= $this->formatNumber(0,1);						//231 231 1 - Numérico C039 Campo não tratado. Informar Zeros.
				$conteudo .= $this->complementoRegistro(9,"brancos");		//232 240 9 - Alfanumérico Brancos G004 Campo não tratado. Informar 'brancos'.
				$conteudo .= chr(13).chr(10); 	
				$this->tot_linhas++;		
				$i++;
				$acumulado++;
			}
			*/
						
			$this->val_total += $cliente->valor;

            // Verifica se o cpf não foi informado!
            if(empty($cliente->cpf)){
                $this->arr_mensagens[] = utf8_decode("CPF não informado no titulo :".$cliente->id_titulo);
            }
            
            // Verifica se foi informado um titulo vencido
            if($cliente->vencimento < date('Y-m-d')){
                $this->arr_mensagens[] = utf8_decode("Data de vencimento do titulo não pode ser inferior a hoje. [".$cliente->id_titulo."]");
            }
					
			$this->conteudo .= $conteudo;
			$this->checkTitulo($conteudo,$cliente,$i-$acumulado);
			
			
		} // fecha loop de clientes
		  
	}

	public function setTrailler(){
		
		
		#Trailer de Lote
		//01.5 Código do Banco na Compensação 1 3 3 - Numérico 001 G001 001 para Banco do Brasil S.A.
		$conteudo = '756';
		//02.5 Lote de Serviço 4 7 4 - Numérico G002 Informar mesmo número do header de lote.
		$conteudo .= $this->formatNumber($this->lote,4);
		//03.5 Tipo de Registro 8 8 1 - Numérico '5' G003
		$conteudo .= '5';
		//04.5 Uso Exclusivo FEBRABAN/CNAB 9 17 9 - Alfanumérico G004 Informar 'brancos'.
		$conteudo .= $this->complementoRegistro(9,"brancos");
		//05.5 Quantidade de Registros do Lote 18 23 6 - Numérico G057 Total de linhas do lote (inclui Header de lote, Registros e Trailer de lote).
		$conteudo .= $this->formatNumber(($this->tot_linhas-1),6);
		//06.5	024	029	006	-	Num - Totalização da Cobrança Simples - Quantidade de Títulos em Cobrança		
		$conteudo .= $this->formatNumber(count($this->titulos),6);
		//07.5	030	046	015	002	Num - Valor Total dosTítulos em Carteiras
		$conteudo .= $this->formatNumber($this->val_total,17);
		
		//06.5 Uso Exclusivo FEBRABAN/CNAB 24 240 217 - Alfanumérico Brancos G004 Informar Zeros e 'brancos'.
		$conteudo .= $this->complementoRegistro(69,"zeros");
		$conteudo .= $this->complementoRegistro(125,"brancos");
		$this->conteudo .= $conteudo.chr(13).chr(10);
		$this->tot_linhas++;		
		
		
		#Trailer de Arquivo
		//01.9 Código do Banco na Compensação 1 3 3 - Numérico 001 G001 001 para Banco do Brasil S.A.
		$conteudo = '756';
		//02.9 Lote de Serviço 4 7 4 - Numérico 9999 G002
		$conteudo .= '9999';
		//03.9 Tipo de Registro 8 8 1 - Numérico 9 G003
		$conteudo .= '9';
		//04.9 Uso Exclusivo FEBRABAN/CNAB 9 17 9 - Alfanumérico Brancos G004
		$conteudo .= $this->complementoRegistro(9,"brancos");
		//05.9 Quantidade de Lotes do Arquivo 18 23 6 - Numérico G049 Informar quantos lotes o arquivo possui.
		$conteudo .= $this->formatNumber(1,6); 
		//06.9 Quantidade de Registros do Arquivo 24 29 6 - Numérico G056 Quantidade igual ao número total de registros (linhas) do arquivo.
		$conteudo .= $this->formatNumber($this->tot_linhas,6);
		//07.9 Qtde de Contas p/ Conc. (Lotes) 30 35 6 - Numérico G037 Campo não criticado pelo sistema, informar Zeros ou 'brancos'.
		$conteudo .= $this->complementoRegistro(6,"zeros");
		//08.9 Uso Exclusivo FEBRABAN/CNAB 36 240 205 - Alfanumérico Brancos G004
		$conteudo .= $this->complementoRegistro(205,"brancos");
		
		$this->conteudo .= $conteudo.chr(13).chr(10);
	
	}

	protected function checkTitulo($registro,$titulo,$seq){
		//if(strlen(trim($linha)) != 241) echo 'Linha '.$seq.' com tamanho invalido ('.strlen(trim($linha)).')<br>';
		$arr_layout = array();
		$arr_layout['id_titulo'] = array('start'=>'63','size'=>'15','func'=>'formatNumber','registro'=>0);
		$arr_layout['vencimento'] = array('start'=>'78','size'=>'8','func'=>'formatData','registro'=>0);
		$arr_layout['valor'] = array('start'=>'86','size'=>'15','func'=>'formatValor','registro'=>0);
		//$arr_layout['tipo'] = array('start'=>'219','size'=>'2','func'=>'');
		$arr_layout['cpf'] = array('start'=>'19','size'=>'15','func'=>'formatNumber','registro'=>1);
		$arr_layout['nome'] = array('start'=>'34','size'=>'40','func'=>'formatString','registro'=>1);
		$arr_layout['endereco'] = array('start'=>'74','size'=>'40','func'=>'formatString','registro'=>1);
		$arr_layout['bairro'] = array('start'=>'114','size'=>'15','func'=>'formatString','registro'=>1);
		$arr_layout['cep'] = array('start'=>'129','size'=>'8','func'=>'formatString','registro'=>1);
		$arr_layout['cidade'] = array('start'=>'137','size'=>'15','func'=>'formatString','registro'=>1);
		$arr_layout['estado'] = array('start'=>'152','size'=>'2','func'=>'formatString','registro'=>1);
        
        $linhas = explode(chr(13).chr(10),$registro);
        //var_dump($linhas);
		//die();
		foreach($linhas as $idx => $linha)
		{
			foreach($arr_layout as $campo=>$def)
			{
				
				if($def['registro'] != $idx) continue;
				
				$metodo = $def['func'];
				$def['start'] = $def['start'] -1; 
				if($metodo){
					$valor_formatado = $this->$metodo($titulo->$campo,$def['size']); 
					if($valor_formatado != substr($linha,$def['start'],$def['size'])){	
						$this->arr_mensagens[] = "Titulo : ".$titulo->id_titulo." Linha ".$seq." campo: ".$campo." valor informado :".$valor_formatado." => valor na linha ".substr($linha,$def['start'],$def['size'])."<br>";
					}
				}
				else{
				
				}
			}
		}
	}

	public function addDays($data = false,$days=1)
	{
		$date = DateTime::createFromFormat('Y-m-d',$data);
		$date->add(new DateInterval('P'.$days.'D'));
		$formatada = $this->formatData($date->format('Y-m-d'));
		return $formatada;
	}
	
	protected function formatData($data = false,$addDays = 0) {
		$return = array();
		// data do sistema
		if($data == false)
		{
			return date('dmY');
		}
		// data em branco
		if($data === '')
		{
			return '00000000';
		}
		
		$data = substr($data,0,10);
		$aData = explode('-',$data);

		//$timestamp = mktime(0, 0, 0, $aData[1], $aData[2], $aData[0]);
		$return = '';

        $return .= str_pad($aData[2], 2, "0", STR_PAD_LEFT); 
        $return .= str_pad($aData[1], 2, "0", STR_PAD_LEFT); 
        $return .= str_pad($aData[0], 4, "0", STR_PAD_LEFT);         
		return $return;	
	}

	public function nossoNumero($cooperativa,$cliente,$titulo){
		
		//var_dump($cooperativa,$cliente,$titulo);
		/*
		3.13. Nosso número: Código de controle que permite ao Sicoob e à empresa identificar os dados da cobrança que deu origem ao boleto.

		Para o cálculo do dígito verificador do nosso número, deverá ser utilizada a fórmula abaixo:
		Número da Cooperativa    9(4) – vide planilha ""Capa"" deste arquivo 
		Código do Cliente   9(10) – vide planilha ""Capa"" deste arquivo
		Nosso Número   9(7) – Iniciado em 1

		Constante para cálculo  = 3197

		a) Concatenar na seqüência completando com zero à esquerda. 
			 Ex.:Número da Cooperativa  = 0001
				   Número do Cliente  = 1-9
				   Nosso Número  = 21
				   000100000000190000021

		b) Alinhar a constante com a seqüência repetindo de traz para frente.
			 Ex.: 000100000000190000021
				  319731973197319731973

		c) Multiplicar cada componente da seqüência com o seu correspondente da constante e somar os resultados.
			 Ex.: 1*7 + 1*3 + 9*1 + 2*7 + 1*3 = 36

		d) Calcular o Resto através do Módulo 11.
			 Ex.: 36/11 = 3, resto = 3

		e) O resto da divisão deverá ser subtraído de 11 achando assim o DV (Se o Resto for igual a 0 ou 1 então o DV é igual a 0).
			 Ex.: 11 – 3 = 8, então Nosso Número + DV = 21-8
	
		*/
		$const = '319731973197319731973';
		//000100000000190000021
		$nn =  str_pad($cooperativa,4,'0',STR_PAD_LEFT)
			  .str_pad($cliente,10,'0',STR_PAD_LEFT)
			  .str_pad($titulo,7,'0',STR_PAD_LEFT);
			
		$mod = 0;	  
		for($i=0;$i<21;$i++){
			if($nn[$i] == '0') continue;
			$mod += $nn[$i] * $const[$i]; 
		}
		$res = 	$mod % 11;
		
		if($res == 0 || $res == 1) return 0;
		
		$dig = 11 - $res;
		//var_dump(($dig == 0 || $dig == 1) ? 0 : $dig);
		//return ($dig == 0 || $dig == 1) ? 0 : $dig;
		//return ($dig == 0 || $dig == 1 || $dig == 10 ) ? 0 : $dig;
		return ($dig == 0 || $dig == 10 ) ? 0 : $dig;
	}

	
/*
    public function getMensagens(){
        return $this->arr_mensagens;
    }
*/
}

