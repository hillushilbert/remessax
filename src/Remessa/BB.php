<?php

/*
* @descr: Gera o arquivo de remessa para cobranca no padrao CNAB 400 vers. 7.0 ITAU
*/

class Remessax_Remessa_BB extends Remessax_Remessa {


	public function setHeader(){
		
		$config = $this->config;		
		
		$conteudo .= '0';											//01.0 001 a 001 9(001) Identificação do Registro Header: “0” (zero)
		$conteudo .= 1;												//02.0 002 a 002 9(001) Tipo de Operação: “1” (um)
		$conteudo .= 'REMESSA'; 									//03.0 003 a 009 X(007) Identificação por Extenso do Tipo de Operação #01
		$conteudo .= '01';  										//04.0 010 a 011 9(002) Identificação do Tipo de Serviço: “01”
		$conteudo .= $this->formatString('COBRANCA',8); 			//05.0 012 a 019 X(008) Identificação por Extenso do Tipo de Serviço: “COBRANCA”
		$conteudo .= $this->complementoRegistro(7,"brancos");		//06.0 020 a 026 X(007) Complemento do Registro: “Brancos”
		$conteudo .= $this->formatNumber($config->agencia,4);		//07.0 027 a 030 9(004) Prefixo da Agência: Número da Agência onde está cadastrado o convênio líder do cedente #02
		$conteudo .= $this->formatNumber($config->agencia_dv,1);	//08.0 031 a 031 X(001) Dígito Verificador - D.V. - do Prefixo da Agência. #02
		$conteudo .= $this->formatNumber($config->conta,8);			//09.0 032 a 039 9(008) Número da Conta Corrente: Número da conta onde está cadastrado o Convênio Líder do Cedente #02
		$conteudo .= $this->formatNumber($config->conta_dv,1);		//10.0 040 a 040 X(001) Dígito Verificador - D.V. – do Número da Conta Corrente do Cedente 02
		$conteudo .= $this->complementoRegistro(6,"zeros");			//11.0 041 a 046 9(006) Complemento do Registro: “000000”
		$conteudo .= $this->formatString($config->razao_social,30);	//12.0 047 a 076 X(030) Nome do Cedente
		$conteudo .= $this->formatString('001BANCODOBRASIL',18);	//13.0 077 a 094 X(018) 001BANCODOBRASIL
		$conteudo .= $this->formatData();							//14.0 095 a 100 9(006) Data da Gravação: Informe no formato “DDMMAA” #21
		$conteudo .= $this->formatNumber($config->codigo,7);		//15.0 101 a 107 9(007) Seqüencial da Remessa #03
		$conteudo .= $this->complementoRegistro(22,"brancos");		//16.0 108 a 129 X(22)  Complemento do Registro: “Brancos”
		$conteudo .= $this->complementoRegistro(7,"zeros");			//17.0 130 a 136 9(007) Número do Convênio Líder (numeração acima de 1.000.000 um milhão)" #04
		$conteudo .= $this->complementoRegistro(258,"brancos");		//18.0 137 a 394 X(258) Complemento do Registro: “Brancos”
		$conteudo .= $this->sequencial(1); 							//19.0 395 a 400 9(006) Seqüencial do Registro:”000001”
		
		$conteudo = '';

		## REGISTRO HEADER	
		$conteudo .= chr(13).chr(10); 							//essa é a quebra de linha

		$this->conteudo = $conteudo;
	}

	
	public function setMovimento(){
		
		$config = $this->config;

		### DADOS DOS CLIENTES PARA TESTE
		//var_dump($config );
		$i = 2;
		
		foreach($this->titulos as $cliente)
		{
			//var_dump($cliente);
			## REGISTRO DETALHE (OBRIGATORIO)
			$conteudo = '';	
				
			//cod_convenio
			//var_carteira
			//tp_cobranca					
			
			$conteudo .= 7;												// 01.7 001 a 001 9(001) Identificação do Registro Detalhe: 7 (sete)
			$conteudo .= '22'; 											// 02.7 002 a 003 9(002) Tipo de Inscrição do Cedente #22
			$conteudo .= $this->formatNumber($config->cpf_cnpj,14);		// 03.7 004 a 017 9(014) Número do CPF/CNPJ do Cedente
			$conteudo .= $this->formatNumber($config->agencia,4);  		// 04.7 018 a 021 9(004) Prefixo da Agência 02
			$conteudo .= $this->formatNumber($config->agencia_dv,1);	// 05.7 022 a 022 X(001) Dígito Verificador - D.V. - do Prefixo da Agência #02
			$conteudo .= $this->formatNumber($config->conta,8); 		// 06.7 023 a 030 9(008) Número da Conta Corrente do Cedente #02
			$conteudo .= $this->formatNumber($config->conta_dv,1);		// 07.7 031 a 031 X(001) Dígito Verificador - D.V. - do Número da Conta Corrente do Cedente #02
			$conteudo .= $this->formatNumber($config->cod_convenio,7);	// 08.7 032 a 038 9(007) Número do Convênio de Cobrança do Cedente #02
			$conteudo .= $this->formatNumber($cliente->id_titulo,25);	// 09.7 039 a 063 X(025) Código de Controle da Empresa #23
			$conteudo .= $this->formatNumber($cliente->id_titulo,17);	// 10.7 064 a 080 9(017) Nosso-Número #06
			$conteudo .= $this->complementoRegistro(2,"zeros");			// 11.7 081 a 082 9(002) Número da Prestação: “00” (Zeros)
			$conteudo .= $this->complementoRegistro(2,"zeros");			// 12.7 083 a 084 9(002) Grupo de Valor: “00” (Zeros)
			$conteudo .= $this->complementoRegistro(3,"brancos"); 		// 13.7 085 a 087 X(003) Complemento do Registro: “Brancos”
			$conteudo .= $this->complementoRegistro(1,"brancos");		// 14.7 088 a 088 X(001) Indicativo de Mensagem ou Sacador/Avalista #13
			$conteudo .= $this->complementoRegistro(3,"brancos"); 		// 15.7 089 a 091 X(003) Prefixo do Título: “Brancos”
			$conteudo .= $this->formatNumber($config->var_carteira,3);	// 16.7 092 a 094 9(003) Variação da Carteira #02
			$conteudo .= $this->complementoRegistro(1,"zeros");			// 17.7 095 a 095 9(001) Conta Caução: “0” (Zero)
			$conteudo .= $this->complementoRegistro(6,"zeros");			// 18.7 096 a 101 9(006) Número do Borderô: “000000” (Zeros)
			$conteudo .= $this->formatString($config->tp_cobranca,5);  // 19.7 102 a 106 X(005) Tipo de Cobrança #24
			$conteudo .= $this->formatNumber($config->carteira,3); 		// 20.7 107 a 108 9(002) Carteira de Cobrança #25
			$conteudo .= $this->formatNumber(1,2);						// 21.7 109 a 110 9(002) Comando #20
			22.7 111 a 120 X(010) Seu Número/Número do Título Atribuído pelo Cedente #05
			23.7 121 a 126 9(006) Data de Vencimento #08
			24.7 127 a 139 9(011)v99 Valor do Título #19
			25.7 140 a 142 9(003) Número do Banco: “001”
			26.7 143 a 146 9(004) Prefixo da Agência Cobradora: “0000” #26
			$conteudo .= $this->complementoRegistro(1,"brancos"); 		// 27.7 147 a 147 X(001) Dígito Verificador do Prefixo da Agência Cobradora: “Brancos”
			28.7 148 a 149 9(002) Espécie de Titulo #07
			29.7 150 a 150 X(001) Aceite do Título: #27
			30.7 151 a 156 9(006) Data de Emissão: Informe no formato “DDMMAA” #28
			31.7 157 a 158 9(002) Instrução Codificada #09
			32.7 159 a 160 9(002) Instrução Codificada #09
			33.7 161 a 173 9(011)v99 Juros de Mora por Dia de Atraso #10
			34.7 174 a 179 9(006) Data Limite para Concessão de Desconto/Data de Operação do BBVendor/Juros de Mora. #11
			35.7 180 a 192 9(011)v99 Valor do Desconto #29
			36.7 193 a 205 9(011)v99 Valor do IOF/Qtde Unidade Variável. #30
			37.7 206 a 218 9(011)v99 Valor do Abatimento #31
			38.7 219 a 220 9(002) Tipo de Inscrição do Sacado #32
			39.7 221 a 234 9(014) Número do CNPJ ou CPF do Sacado #33
			40.7 235 a 271 X(037) Nome do Sacado
			$conteudo .= $this->complementoRegistro(3,"brancos"); 		// 41.7 272 a 274 X(003) Complemento do Registro: “Brancos”
			42.7 275 a 314 X(040) Endereço do Sacado
			43.7 315 a 326 X(012) Bairro do Sacado
			44.7 327 a 334 9(008) CEP do Endereço do Sacado
			45.7 335 a 349 X(015) Cidade do Sacado
			46.7 350 a 351 X(002) UF da Cidade do Sacado
			47.7 352 a 391 X(040) Observações/Mensagem ou Sacador/Avalista #13
			48.7 392 a 393 X(002) Número de Dias Para Protesto #34
			$conteudo .= $this->complementoRegistro(1,"brancos"); 		// 49.7 394 a 394 X(001) Complemento do Registro: “Brancos”
			50.7 395 a 400 9(006) Seqüencial de Registro #35
			

	
																					#NOME DO CAMPO                #SIGNIFICADO            		#POSICAO    #PICTURE
			                                                   	// codigo inscricao            tipo inscricao empresa    	002 003        9(02)
			                 // cnpj da empresa                                        	004 017        9(14)
			                 // agencia                        mantenedora da conta    	018 021        9(04)
			$conteudo .= '00';                                                    	// zeros                        complemento registro    	022 023        9(02)
			                    // conta                        numero da conta            	024 028        9(05)
			                  // dac                            dig autoconf conta        029 029        9(01)
			                  // brancos                        complemento registro     	030 033        X(04)
			$conteudo .= $this->complementoRegistro(4,"zeros");                     // CÓD.INSTRUÇÃO/ALEGAÇÃO A SER CANC NOTA 27             	034 037        9(04)
			               // USO / IDENT. DO TÍTULO NA EMPRESA NOTA 2            		038 062        X(25)
			if(in_array($config->carteira,array('104','138','112','147'))){
				
			}else{
				$conteudo .= $this->formatNumber($cliente->id_titulo,8);            // NOSSO NUMERO / ID TITULO DO BANCO NOTA 3            		063 070        9(08)
			}
			$conteudo .= '0000000000000';                                        	// QTDE MOEDA            NOTA 4                            	071 083        9(08)V9(5)
			                 // nº da carteira        nº carteira banco                	084 086        9(03)            
			$conteudo .= $this->complementoRegistro(21,"brancos");                  // uso do banco ident. oper. no banco                    	087 107        X(21)
			$conteudo .= 'I';                                                    	// carteira codigo da carteira NOTA 5                    	108 108        X(01)
			$conteudo .= '01';                                                     	// codigo ocorrencia / ident da ocorrencia NOTA 6        	109 110        9(02)
			$conteudo .= $this->complementoRegistro(10,'brancos');                  // nº documento / nº documento de cobranca    NOTA 18       111 120        X(10)
			$conteudo .= $this->formatData($cliente->vencimento);                   // vencimento data venc. titulo NOTA 7                    	121 126        9(06)
			$conteudo .= $this->formatValor($cliente->valor,13);                    // valor titulo            valor nominal NOTA 8            	127 139        9(11)V9(2)
			$conteudo .= 341;                                                    	// codigo do banco        Nº BANCO CÂMARA COMP.            	140    142        9(03)         
			$conteudo .= $this->zeros(0,5);                                         //agencia cobradora / ONDE TÍTULO SERÁ COBRADO NOTA 9    	143 147        9(05)
			$conteudo .= 15;                                                    	// especie        especie do titulo NOTA 10                	148 149        X(02)
			$conteudo .= 'A'; 														// aceite ident de titutlo aceito (A=aceite,N=nao aceite)   150 150        X(01)
			$conteudo .= $this->formatData(date('Y-m-d'));         					// data emissao titulo     NOTA 31                    		151 156        9(06)
			$conteudo .= '88';             											// instrucao 1            NOTA 11                    		157 158        X(02)
			$conteudo .= '86';            											// instrucao 2            NOTA 11                    		159 160        X(02)
			$conteudo .= '0000000000000';											// juros de 1 dia    valor de mora NOTA 12    				161 173        9(11)V9(02)
			$conteudo .= $this->zeros(0,6);    										// desconto até            data limite p/ descont    		174 179        9(06)
			$conteudo .= '0000000000000';											// valor desconto        a ser concedido NOTA 13 			180 192        9(11)V9(02)
			$conteudo .= '0000000000000';    										// valor I.O.F RECOLHIDO P NOTAS SEGURO NOTA 14    			193 205        9(11)V9(02)
			$conteudo .= '0000000000000';    										// abatimento            a ser concedido NOTA 13    		206 218        9(11)V9(02)
			$conteudo .= $cliente->tipo; 											// codigo de inscricao tipo de insc. sacado 01=CPF 02=CNPJ  219 220        9(02)
			$conteudo .= $this->formatNumber($cliente->cpf,14);             		// numero de inscricao    cpf ou cnpj                		221 234        9(14)
			$conteudo .= $this->formatString($cliente->nome,30);    				// nome            nome do sacado NOTA 15    				235 264        X(30)
			$conteudo .= $this->complementoRegistro(10,"brancos");					//NOTA 15 complem regist        							265 274        X(10)
			$conteudo .= $this->formatString($cliente->endereco,40);    			// logradouro rua numero e compl sacado            			275 314        X(40)
			$conteudo .= $this->formatString($cliente->bairro,12);    				// bairro                bairro do sacado        			315 326        X(12)
			$conteudo .= $this->formatString($cliente->cep,8);    					// cep                    cep do sacado            			327 334        9(08)
			$conteudo .= $this->formatString($cliente->cidade,15);    				// cidade                cidade do sacado        			335 349        X(15)        
			$conteudo .= $this->formatString($cliente->estado,2);    				// estado                uf do sacado            			350 351        X(02)
			$conteudo .= $this->limit('',30);    									// sacador/avalista        sacad ou aval. NOTA 16    		352 381        X(30)
			$conteudo .= $this->complementoRegistro(4,"brancos");					// complemento de regist.        							382 385        X(04)
			$conteudo .= $this->zeros(0,6);    										// data de mora            data de mora            			386 391        9(06)        
			$conteudo .= $this->zeros(0,2);            								// prazo                qtde de dias NOTA 11(A)    			392 393        9(02)            
			$conteudo .= $this->complementoRegistro(1,"brancos");        			// brancos                complemento de registr.    		394 394     	X(01)
			$conteudo .= $this->sequencial($i++);            						// numero sequencial    do registro no arquivo    			395 400        9(06)
			$conteudo .= chr(13).chr(10); 											//essa é a quebra de linha
			$this->val_total += $cliente->valor;

			$this->tot_linhas++;		
			$this->checkTitulo($conteudo,$cliente,$i-1);
			$this->conteudo .= $conteudo;

		} // fecha loop de clientes
		  
	}

	public function setTrailler(){
		/*01.5 001 a 001 9(001) Identificação do Registro Transação: “5”
02.5 002 a 003 9(002) Tipo de Serviço: “03”
03.5 004 a 018 X(015) Identificação do título do cedente 36
04.5 019 a 394 X(376) Complemento do Registro: “Brancos”
05.5 395 a 400 9(006) Número Seqüencial do Registro no Arquivo
X
*/
		$conteudo = '';
		$conteudo .= 9;
		$conteudo .= $this->complementoRegistro(393,"zeros");
		$conteudo .= $this->sequencial($this->tot_linhas);
		$this->conteudo .= $conteudo.chr(13).chr(10);
	
	}

	protected function checkTitulo($linha,$titulo,$seq){
		
		if(strlen(trim($linha)) != 400) echo 'Linha '.$seq.' com tamanho invalido ('.strlen(trim($linha)).')<br>';
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


}

/*
Nota 02
PREFIXO DA AGÊNCIA, DÍGITO VERIFICADOR – D.V. - DO PREFIXO DA AGÊNCIA,
NÚMERO DA CONTA CORRENTE, DÍGITO VERIFICADOR – DV – DO NÚMERO DA
CONTA CORRENTE DO CEDENTE, CARTEIRA, VARIAÇÃO DA CARTEIRA: Os dados
necessários para preenchimento desses campos serão fornecidos pelo Banco do Brasil.

?>