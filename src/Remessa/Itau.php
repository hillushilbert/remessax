<?php

/*
* @descr: Gera o arquivo de remessa para cobranca no padrao CNAB 400 vers. 7.0 ITAU
*/

class Remessax_Remessa_Itau extends Remessax_Remessa {

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
			$remessa_replace = $remessa_mark = 'R';
			$this->filename = REMESSAX_REMESSA_PATH.$remessa_mark.$this->DATA['DIA'].$this->DATA['MES'].$this->DATA['ANO'].".rem";
			
			$id_mark = 1;
			while(is_file($this->filename))
			{
				$remessa_replace = $remessa_mark.$id_mark;
				$id_mark++;
				$this->filename = REMESSAX_REMESSA_PATH.$remessa_replace.$this->DATA['DIA'].$this->DATA['MES'].$this->DATA['ANO'].".rem";
				//$this->filename = str_replace($remessa_mark,$remessa_replace,$this->filename);	
			}
			$this->filename_download = '/includes_sc/boletophp/remessa/'.$remessa_replace.$this->DATA['DIA'].$this->DATA['MES'].$this->DATA['ANO'].".rem";
		}
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
		$conteudo .= $this->formatString('COBRANCA',15);    	//literal cobranca    escr. extenso    			012 026        X(15)
		$conteudo .= $this->formatNumber($config->agencia,4);	//agencia                mantenedora conta      027 030        9(04)
		$conteudo .= $this->complementoRegistro(2,"zeros");		//zeros complemento d registro    				031 032        9(02)
		$conteudo .= $this->formatNumber($config->conta,5);		//conta                conta da empresa        	033 037        9(05)
		$conteudo .= $this->formatNumber($config->conta_dv,1);	//dac                    digito autoconf conta  038 038        9(01)
		$conteudo .= $this->complementoRegistro(8,"brancos");	//complemento registro     						039 046        X(08)
		$conteudo .= $this->formatString($config->razao_social,30);	//nome da empresa            					047 076        X(30)
		$conteudo .= 341;            							//codigo banco            Nº BANCO CÂMARA COMP. 077 079        9(03)
		$conteudo .= $this->formatString('BANCO ITAU SA',15);   //nome do banco por ext.    					080 094        X(15)
		$conteudo .= $this->formatData();						//data geracao arquivo    						095 100        9(06)
		$conteudo .= $this->complementoRegistro(294,"brancos");	// complemento de registr    					101 394        X(294)
		$conteudo .= $this->sequencial(1);        				// numero sequencial    registro no arquivo     395 400        9(06)
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
            			
			
			//var_dump($cliente);
			## REGISTRO DETALHE (OBRIGATORIO)
			$conteudo = '';	
			
																					#NOME DO CAMPO                #SIGNIFICADO            		#POSICAO    #PICTURE
			$conteudo .= 1;                                                        	// tipo registro                id registro transacac.    	001 001        9(01)
			$conteudo .= '02';                                                    	// codigo inscricao            tipo inscricao empresa    	002 003        9(02)
			$conteudo .= $this->formatNumber($config->cpf_cnpj,14);                 // cnpj da empresa                                        	004 017        9(14)
			$conteudo .= $this->formatNumber($config->agencia,4);                   // agencia                        mantenedora da conta    	018 021        9(04)
			$conteudo .= '00';                                                    	// zeros                        complemento registro    	022 023        9(02)
			$conteudo .= $this->formatNumber($config->conta,5);                     // conta                        numero da conta            	024 028        9(05)
			$conteudo .= $this->formatNumber($config->conta_dv,1);                  // dac                            dig autoconf conta        029 029        9(01)
			$conteudo .= $this->complementoRegistro(4,"brancos");                   // brancos                        complemento registro     	030 033        X(04)
			$conteudo .= $this->complementoRegistro(4,"zeros");                     // CÓD.INSTRUÇÃO/ALEGAÇÃO A SER CANC NOTA 27             	034 037        9(04)
			$conteudo .= $this->formatNumber($cliente->id_titulo,25);               // USO / IDENT. DO TÍTULO NA EMPRESA NOTA 2            		038 062        X(25)
			if(in_array($config->carteira,array('104','138','112','147'))){
				$conteudo .= $this->complementoRegistro(8,"zeros");
			}else{
				$conteudo .= $this->formatNumber($cliente->id_titulo,8);            // NOSSO NUMERO / ID TITULO DO BANCO NOTA 3            		063 070        9(08)
			}
			$conteudo .= '0000000000000';                                        	// QTDE MOEDA            NOTA 4                            	071 083        9(08)V9(5)
			$conteudo .= $this->formatNumber($config->carteira,3);                  // nº da carteira        nº carteira banco                	084 086        9(03)            
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
			//$conteudo .= '88';             											// instrucao 1            NOTA 11                    		157 158        X(02)
			$conteudo .= '00';             											// instrucao 1            NOTA 11                    		157 158        X(02)
			//$conteudo .= '86';            											// instrucao 2            NOTA 11                    		159 160        X(02)
			$conteudo .= '00';            											// instrucao 2            NOTA 11                    		159 160        X(02)
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
		$arr_layout['cpf'] = array('start'=>'221','size'=>'14','func'=>'formatNumber');
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
				if($metodo == 'formatData'){
					$res = $this->$metodo($titulo->$campo);
				} else {
					$res = $this->$metodo($titulo->$campo,$def['size']);
				}
				
				if($res != substr($linha,$def['start'],$def['size'])){	
					echo "Linha ".$seq." campo: ".$campo." valor informado :".$res." => valor na linha ".substr($linha,$def['start'],$def['size'])."<br>";
				}
			
			}
			else{
			
			}
		}
		
	}


}


?>