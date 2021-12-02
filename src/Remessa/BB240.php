<?php

/*
* @descr: Gera o arquivo de remessa para cobranca no padrao CNAB 240 vers. BB
*/

class Remessax_Remessa_BB240 extends Remessax_Remessa {

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
		$conteudo .= '001'; 										//01.0 Código do Banco na Compensação 1 3 3 - Numérico 001 G001 
																	//001 para Banco do Brasil S.A.
		$conteudo .= '0000'; 										//02.0 Lote de Serviço 4 7 4 - Numérico 0000 G002
		$conteudo .= '0'; 											//03.0 Tipo de Registro 8 8 1 - Numérico 0 G003
		$conteudo .= $this->complementoRegistro(9,"brancos"); 		//04.0 Uso Exclusivo FEBRABAN / CNAB 9 17 9 - Alfanumérico Brancos 
																	//G004
		$conteudo .= '2'; 											//05.0 Tipo de Inscrição da Empresa 18 18 1 - Numérico G005 
																	//1 – para CPF e 2 – para CNPJ.
		$conteudo .= $this->formatNumber($config->cpf_cnpj,14);		//06.0 Número de Inscrição da Empresa 19 32 14 - Numérico 
																	//G006 Informar número da inscrição (CPF ou CNPJ) da Empresa, 
																	//alinhado à direita com zeros à esquerda.
		#CAMPO NOVO
		$conteudo .= $this->formatNumber($config->cod_convenio,9); 	//07.0 BB1 Nùmero do convênio de cobrança BB 33 41 9 Numérico 
																	//Informar o número do convênio de cobrança, alinhado à direita com
																	//zeros à esquerda.
		$conteudo .= '0014';										//07.0 BB2 Cobrança Cedente BB 42 45 4 Numérico Informar 
																	//0014 para cobrança cedente
		$conteudo .= $this->formatNumber($config->carteira,2);		//07.0 BB3 Número da carteira de cobrança BB 46 47 2 
																	//Numérico Informar o número da carteira de cobrança
		#CAMPO NOVO
		$conteudo .= $this->formatNumber($config->carteira_var,3);	//07.0 BB4 Número da variação da carteira de cobrança BB 48 50 3 
																	//Numérico Informar o número da variação da carteira de cobrança
		$conteudo .= $this->complementoRegistro(2,"brancos");		//07.0 BB5 Campo reservado BB 51 52 2 Alfanumérico Informar brancos.
		$conteudo .= $this->formatNumber($config->agencia,5);		//08.0 Agência Mantenedora da Conta 53 57 5 - Numérico G008
		$conteudo .= $this->formatNumber($config->agencia_dv,1);	//09.0 Dígito Verificador da Agência 58 58 1 - Alfanumérico G009 Obs. Em caso de dígito X informar maiúsculo.
		$conteudo .= $this->formatNumber($config->conta,12); 		//10.0 Número da Conta Corrente 59 70 12 - Numérico G010
		$conteudo .= $this->formatNumber($config->conta_dv,1);		//11.0 Dígito Verificador da Conta 71 71 1 - Alfanumérico G011 Obs. Em caso de dígito X informar maiúsculo.
		$conteudo .= $this->complementoRegistro(1,"brancos");		//12.0 Dígito Verificador da Ag/Conta 72 72 1 - Alfanumérico G012 Campo não tratado pelo Banco do Brasil. Informar 'branco' (espaço) OU zero.
		$conteudo .= $this->formatString($config->razao_social,30);	//13.0 Nome da Empresa 73 102 30 - Alfanumérico G013
		$conteudo .= $this->formatString('BANCO DO BRASIL S.A.',30);//14.0 Nome do Banco 103 132 30 - Alfanumérico G014 BANCO DO BRASIL S.A.
		$conteudo .= $this->complementoRegistro(10,"brancos");		//15.0 Uso Exclusivo FEBRABAN / CNAB 133 142 10 - Alfanumérico Brancos G004 
																	//Informar 'brancos' (espaços).
		$conteudo .= '1';											//16.0 Código Remessa / Retorno 143 143 1 - Numérico G015
		$conteudo .= $this->formatData();							//17.0 Data de Geração do Arquivo 144 151 8 - Numérico G016 
																	//Informar no formato DDMMAAAA, observando que a data de geração 
																	//do arquivo não pode ser maior que a data de envio do arquivo para o banco.
		$conteudo .= date('His');									//18.0 Hora de Geração do Arquivo 152 157 6 - Numérico G017 Zeros 
																	//OU informar no formato HHMMSS, HH horas, MM minutos e SS segundos.
		$conteudo .= $this->formatNumber($this->remessa_id,6);		//19.0 Número Seqüencial do Arquivo 158 163 6 - Numérico 
																	//G018 Informação a cargo da empresa. O campo não é criticado pelo 
																	//sistema do Banco do Brasil. Informar zeros OU um número sequencial, 
																	//incrementando a cada novo arquivo.
		$conteudo .= '000';											//20.0 Nº da Versão do Layout do Arquivo 164 166 3 - Numérico 083 G019
																	//Campo não criticado pelo sistema. Informar zeros ou número
																	//da versão do leiaute do arquivo que foi usado para formatação
																	//dos campos. Versões disponíveis: 084, 083, 082, 080, 050,
																	//040, ou 030.
		$conteudo .= '     ';										//21.0 Densidade de Gravação do Arquivo 167 171 5 - Numérico G020
																	//Campo não criticado pelo sistema do Banco do Brasil. Informar
																	//zeros, 'brancos', 01600 ou 06250.
		$conteudo .= $this->complementoRegistro(20,"brancos");		//22.0 Para Uso Reservado do Banco 172 191 20 - Alfanumérico G021
		$conteudo .= $this->complementoRegistro(20,"brancos");		//23.0 Para Uso Reservado da Empresa 192 211 20 - Alfanumérico G022
																	//Campo não tratado pelo Banco do Brasil. Informar 'brancos'
																	//(espaços) OU zeros.
		$conteudo .= $this->complementoRegistro(29,"brancos");		//24.0 Uso Exclusivo FEBRABAN / CNAB 212 240 29 - Alfanumérico Brancos G004
																	//Informar 'brancos', porém se o arquivo foi formatado com a
																	//versão do layout 030, pode ser informado 'CSP' nas posições
																	//223 a 225, e 'zeros' nas posições 226 a 228.
		$conteudo .= chr(13).chr(10); 								//essa é a quebra de linha
		
		
		## REGISTRO HEADER - lote	
		//01.1 Código do Banco na Compensação 1 3 3 - Numérico 001 G001 001 para Banco do Brasil S.A.
		$conteudo .= '001'; 
		//02.1 Lote de Serviço 4 7 4 - Numérico G002
		//     Começar com '0001'. Essa informação deve ser igual em todos os
		//	   registros desse lote, até o seu trailer. Se o arquivo possui mais de um
		//	   lote, incrementar em 1 cada lote, exemplo o 2º lote do arquivo é o
		//     '0002', e assim sucessivamente.
		$conteudo .= $this->lote; 
		//03.1 Tipo de Registro 8 8 1 - Numérico '1' G003
		$conteudo .= '1';
		//04.1 Tipo de Operação 9 9 1 - Alfanumérico G028 R – para arquivo remessa, T – quando arquivo retorno.
		$conteudo .= 'R';
		//05.1 Tipo de Serviço 10 11 2 - Numérico '01' G025
		$conteudo .= '01';  
		//06.1 Uso Exclusivo FEBRABAN/CNAB 12 13 2 - Alfanumérico Brancos G004 Informar 'brancos' (espaços).
		$conteudo .= '  '; 
		//07.1 Nº da Versão do Layout do Lote 14 16 3 - Numérico '042' G030
		//     Campo não criticado pelo sistema. Informar Zeros OU se preferir,
		//     informar número da versão do leiaute do Lote que foi utilizado como
		//     base para formatação dos campos. Versões disponíveis: 043, 042, 041,
		//     040, 030 e 020. A versão do Lote quando informada deve estar
		//     condizente com a versão do Arquivo (posições 164 a 166 do Header de
		//     Arquivo). Ou seja, para utilizar 043 no lote o Header do arquivo deve
		//     conter 084, para 042 no lote o Header do arquivo deve conter 083, para
		//     041 no lote o Header do arquivo deve conter 082, para 040 no lote o
		//     Header do arquivo deve conter 080, para 030 no lote o Header do
		//     arquivo deve conter 040, para 020 no lote o Header do arquivo deve conter 030.
		$conteudo .= '000'; 
		//08.1 Uso Exclusivo FEBRABAN/CNAB 17 17 1 - Alfanumérico Brancos G004
		$conteudo .= ' ';
		//09.1 Tipo de Inscrição da Empresa 18 18 1 - Numérico G005 1 – para CPF e 2 – para CNPJ.
		$conteudo .= '2'; 
		//10.1 Nº de Inscrição da Empresa 19 33 15 - Numérico G006
		//     Informar número da inscrição (CPF ou CNPJ) da Empresa, alinhado à
		//	   direita com zeros à esquerda.
		$conteudo .= $this->formatNumber($config->cpf_cnpj,15); 
		//11.1
		// numero convenio
		//11.1 BB1 Nùmero do convênio de cobrança BB 34 42 9 Numérico Informar o número do convênio de cobrança, alinhado à direita com zeros à esquerda.
		$conteudo .= $this->formatNumber($config->cod_convenio,9); 
		//11.1 BB2 Cobrança Cedente BB 43 46 4 Numérico Informar 0014 para cobrança cedente
		$conteudo .= '0014';
		//11.1 BB3 Número da carteira de cobrança BB 47 48 2 Numérico Informar o número da carteira de cobrança
		$conteudo .= $this->formatNumber($config->carteira,2);
		//11.1 BB4 Número da variação da carteira de cobrança BB 49 51 3 Numérico 
		//     Informar o número da variação da carteira de cobrança
		$conteudo .= $this->formatNumber($config->carteira_var,3);
		//11.1 BB5 Campo que identifica remessa de testes 52 53 2 Alfanumérico informar brancos; ou
		//     para tratamento de arquivo teste: cliente, antes de realizar os procedimentos abaixo,entre em contato
		/*
		com sua agência, pois a situação de seu intercâmbio eletrônico de
		dados deverá ser alterado de ATIVO para TESTE.
		Importante que nesse caso não deverá ser enviado arquivos para a
		produção, pois sua condição foi alterada para TESTE.
		Obs.: Caso a empresa queira efetuar TESTE pelo sistema, com
		geração
		de arquivo retorno TESTE pelo Gerenciador Financeiro, basta
		substituir os espaços em branco (posições 52 e 53) por "TS".
		Caso não queira realizar os testes, informe brancos.
		*/
		$conteudo .= '  ';
		//12.1 Agência Mantenedora da Conta 54 58 5 - Numérico G008
		$conteudo .= $this->formatNumber($config->agencia,5);
		//13.1 Dígito Verificador da Conta 59 59 1 - Alfanumérico G009 Obs. Em caso de dígito X informar maiúsculo.
		$conteudo .= $this->formatNumber($config->agencia_dv,1);
		//14.1 Número da Conta Corrente 60 71 12 - Numérico G010
		$conteudo .= $this->formatNumber($config->conta,12);
		//15.1 Dígito Verificador da Conta 72 72 1 - Alfanumérico G011 Obs. Em caso de dígito X informar maiúsculo.
		$conteudo .= $this->formatNumber($config->conta_dv,1);
		//16.1 Dígito Verificador da Ag/Conta 73 73 1 - Alfanumérico G012
		//     Campo não tratado pelo Banco do Brasil. Informar 'branco' (espaço) OU zero.
		$conteudo .= ' ';
		//17.1 Nome da Empresa 74 103 30 - Alfanumérico G013
		$conteudo .= $this->formatString($config->razao_social,30);
		//18.1 Mensagem 1 104 143 40 - Alfanumérico C073
		/*
		Para utilizar a Mensagem 1 é necessário não ter informado
		sacador/avalista. Esse campo só é tratado quando a Mensagem 3, do
		segmento R, não estiver sendo utilizada ('brancos'). Além disso,
		quando acatada essa mensagem será impressa em todos os bloquetos
		do lote. Caso não queira utilizar o campo, informar 'brancos' (espaços). */
		$conteudo .= $this->complementoRegistro(40,"brancos");
		//19.1 Mensagem 2 144 183 40 - Alfanumérico C073 Campo não tratado pelo Banco do Brasil. Informar 'brancos' (espaços).
		$conteudo .= $this->complementoRegistro(40,"brancos");
		//20.1 Número Remessa/Retorno 184 191 8 - Numérico G079
		//     Informação a cargo da empresa. Sugerimos informar número
		//     sequencial para controle. Campo não é criticado pelo Banco do Brasil.
		$conteudo .= $this->formatNumber($this->remessa_id,8);
		//21.1 Data de Gravação Remessa/Retorno 192 199 8 - Numérico G068
		//     Informar a data de Gravação da Remessa, lembrando que a data de
		//     gravação não deve ser maior que a data de envio do arquivo para o banco, OU informar Zeros.
		$conteudo .= $this->formatData();
		//22.1 Data do Crédito 200 207 8 - Numérico C003
		//     Campo não tratado pelo Banco do Brasil. Informar 'brancos' (espaços) OU Zeros.
		$conteudo .= $this->complementoRegistro(8,"brancos");
		//23.1 Uso Exclusivo FEBRABAN/CNAB 208 240 33 - Alfanumérico Brancos G004 Informar 'brancos' (espaços).
		$conteudo .= $this->complementoRegistro(33,"brancos");
		$conteudo .= chr(13).chr(10); 								//essa é a quebra de linha
		
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
            			
			
			$cliente->cod_movimento = !empty($cliente->cod_movimento) ? $cliente->cod_movimento : '01';
			
			$conteudo = '';	
	
			//01.3P Código do Banco na Compensação 1 3 3 - Numérico G001 001 para Banco do Brasil S.A.
			$conteudo .= '001';	
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
			//07.3P Código de Movimento Remessa 16 17 2 - Numérico C004
			/*
			Códigos de Movimento para Remessa tratados pelo Banco do Brasil: 01 – Entrada de títulos, 02 – Pedido de
			baixa, 04 – Concessão de Abatimento, 05 – Cancelamento de Abatimento, 06 – Alteração de Vencimento, 07
			– Concessão de Desconto, 08 – Cancelamento de Desconto, 09 – Protestar, 10 – Cancela/Sustação da
			Instrução de protesto, 30 – Recusa da Alegação do Sacado, 31 – Alteração de Outros Dados, 40 – Alteração
			de Modalidade.
			*/
			$conteudo .= '01';
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
			//13.3P Identificação do Título no Banco 38 57 20 - Alfanumérico G069
			/*
			1. Caso seja o Banco do Brasil quem gera o "Nosso Número", informar 'brancos' (espaços) ou Zeros.
			2. Caso seja a empresa quem gera o "Nosso Número", informar de acordo com o número do convênio de
			cobrança conforme as seguintes regras:
			a) Para número de convênio de 4 posições (carteira 17 até 9.999) informar o nosso número com
			11 posições mais o DV (dígito verificador), sendo as 4 primeiras posições o número do convênio,
			as 7 posições seguintes um número sequencial para controle e mais o DV.
			Exemplo: CONVÊNIOS DE 0001 ATÉ 9.999
			123412345671
			CCCCSSSSSSSD
			Onde: C = Convênio S = Sequencial D = dígito verificador
			b) Para número de convênio de 6 posições (de 10.000 a 999.999), informar o nosso número com
			11 posições mais o DV, sendo as 6 primeiras posições o número do convênio, as 5 posições
			seguintes um número sequencial para controle e mais o DV.
			Exemplo: CONVÊNIOS DE 010.000 ATÉ 999.999
			123456123451
			CCCCCCSSSSSD
			Onde: C = Convênio S = Sequencial D = dígito verificador
			c) Para número de convênio de 7 posições (acima de 1.000.000) informar o nosso número com
			17 posições sem DV, sendo as 7 primeiras posições o número do convênio e as 10 posições seguintes
			um número sequencial para controle. Esse é o padrão mais utilizado atualmente.
			Exemplo: CONVÊNIOS DE 1.000.000 ATÉ 9.999.999
			12345671234567890
			CCCCCCCSSSSSSSSSS
			Onde: C = Convênio S = Sequencial
			Importante:
			todos os "nosso número" devem ser alinhados à esquerda com brancos à direita.
			*/
			//$conteudo .= '0014'.$this->formatNumber($cliente->id_titulo,7).'0';
			$conteudo.= $this->formatNumber($config->cod_convenio,7).$this->formatNumber($cliente->id_titulo,10).'   '; 
			//14.3P Código da Carteira 58 58 1 - Numérico C006
			/*
			Informar 1 – para carteira 11/12 na modalidade Simples; 2 ou 3 – para carteira 11/17 modalidade
			Vinculada/Caucionada e carteira 31; 4 – para carteira 11/17 modalidade Descontada e carteira 51; e 7 – para
			carteira 17 modalidade Simples.
			*/
			//CAMPO NOVO
			$conteudo.= '7';
			//15.3P Forma de Cadastr. do Título no Banco 59 59 1 - Numérico C007
			/*
			Campo não tratado pelo sistema do Banco do Brasil. Pode ser informado 'branco', Zero, 1 – com
			cadastramento (Cobrança registrada) ou 2 – sem cadastramento (Cobrança sem registro).
			*/
			$conteudo .= ' ';
			
			//16.3P Tipo de Documento 60 60 1 - Alfanumérico C008 Campo não tratado pelo sistema do Banco do Brasil. Pode ser informado 'branco', Zero, 1 – Tradicional, ou 2 – Escritural.
			$conteudo .= ' ';
			
			//17.3P Identificação da Emissão do Bloqueto 61 61 1 - Numérico C009
			/*
			Campo não tratado pelo sistema do Banco do Brasil. Pode ser informado 'branco', Zero, ou de acordo com a
			carteira e quem fará a emissão dos bloquetos. No caso de carteira 11/12/31/51, utilizar código 1 – Banco
			emite, OU códigos 4 – Banco reemite e 5 – Banco não reemite, porém nestes dois últimos casos, o código de
			Movimento Remessa (posições 16 a 17) deve ser código '31' Alteração de outros dados (para títulos que já
			estão registrados no Banco do Brasil). No caso de carteira 17, podem ser usados os códigos: 1 – Banco emite,
			2 – Cliente emite, 3 – Banco pre-emite e cliente complementa, 6 – Cobrança sem papel. Permite ainda,
			códigos 4 – Banco reemite e 5 – Banco não reemite, porém o código de Movimento Remessa (posições 16 a
			17) deve ser código '31' Alteração de outros dados (para títulos que já estão registrados no Banco do Brasil).
			Obs.: Quando utilizar código, informar de acordo com o que foi cadastrado para a carteira junto ao Banco do
			Brasil, consulte seu gerente de relacionamento.			
			*/
			$conteudo .= ' ';
			
			//18.3P Identificação da Distribuição 62 62 1 - Alfanumérico C010
			/*
			Campo não tratado pelo Banco do Brasil. Informar 'branco' (espaço) OU zero ou de acordo com a carteira e
			quem fará a distribuição dos bloquetos. Para carteira 11/12/31/51 utilizar código 1– Banco distribui. Para
			carteira 17, pode ser utilizado código 1 – Banco distribui, 2 – Cliente distribui ou 3 – Banco envia e-mail (nesse
			caso complementar com registro S), de acordo com o que foi cadastrado para a carteira junto ao Banco do
			Brasil, consulte seu gerente de relacionamento.			
			*/
			$conteudo .= ' ';
			
			//19.3P Número do Documento de Cobrança 63 77 15 - Alfanumérico C011
			/*
			No caso de carteira 17, na modalidade onde a impressão dos bloquetos é feita pela empresa, atentar para que
			o número informado nesse campo seja exatemente igual ao campo Número do Documento do bloqueto
			impresso (considerando inclusive zeros à esquerda, espaços, barras, etc).			
			*/
			$conteudo .= $this->formatNumber($cliente->id_titulo,15);
			
			//20.3P Data de Vencimento do Título 78 85 8 - Numérico C012
			/*
			Não deve ser menor que a data de emissão do bloqueto. Para carteira 11, 12, 15, 17 e 31, admite o registro
			de títulos com prazo de vencimento até 2500 dias. Para carteira 11,17 modalidade Descontada e carteira 51,
			admite o registro de títulos com prazo de vencimento até 360 dias. Para vencimento “A vista” preencher com
			'11111111', e “Contra-apresentação” preencher com '99999999'. Obs.: O prazo legal para vencimento “a vista”
			ou “contra-apresentação” é de 15 dias da data do registro do bloqueto no banco.			
			*/
			$conteudo .= $this->formatData($cliente->vencimento);  
			
			//21.3P Valor Nominal do Título 86 100 13 2 Numérico G070
			$conteudo .= $this->formatValor($cliente->valor,15);  
			
			//22.3P Agência Encarregada da Cobrança 101 105 5 - Numérico C014 Informar Zeros. A agência encarregada da Cobrança é definida de acordo com o CEP do sacado.
			$conteudo .= $this->formatNumber(0,5);
			
			//23.3P Dígito Verificador da Agência 106 106 1 - Alfanumérico G009 Informar 'branco' (espaço).
			//$conteudo .= $this->formatNumber($config->agencia_dv,1);
			$conteudo .= ' ';
			
			//24.3P Espécie do Título 107 108 2 - Numérico C015
			/*
			Para carteira 11 e 17 modalidade Simples, pode ser usado: 01 – Cheque, 02 – Duplicata Mercantil, 04 –
			Duplicata de Serviço, 06 – Duplicata Rural, 07 – Letra de Câmbio, 12 – Nota Promissória, 17 - Recibo, 19 –
			Nota de Debito, 26 – Warrant, 27 – Dívida Ativa de Estado, 28 – Divida Ativa de Município e 29 – Dívida Ativa
			União. Para carteira 12 (moeda variável) pode ser usado: 02 – Duplicata Mercantil, 04 – Duplicata de Serviço,
			07 – Letra de Câmbio, 12 – Nota Promissória, 17 – Recibo e 19 – Nota de Débito. Para carteira 15 (prêmio de
			seguro) pode ser usado: 16 – Nota de Seguro e 20 – Apólice de Seguro. Para carteira 11/17 modalidade
			Vinculada e carteira 31, pode ser usado: 02 – Duplicata Mercantil e 04 – Duplicata de Serviço. Para carteira
			11/17 modalidade Descontada e carteira 51, pode ser usado: 02 – Duplicata Mercantil, 04 – Duplicata de
			Serviço, e 07 – Letra de Câmbio. Obs.: O Banco do Brasil encaminha para protesto os seguintes títulos:
			Duplicata Mercantil, Rural e de Serviço, Letra de Câmbio, e Certidão de Dívida Ativa da União, dos Estados e
			do Município.			
			*/
			$conteudo .= '04';
			
			//25.3P Identific. de Título Aceito/Não Aceito 109 109 1 - Alfanumérico C016
			/*
			Informar 'A' – para sim, ou 'N' – para não. Os bloquetos registrados com aceite 'A' (sim) somente podem ser
			encaminhados a cartório com o título original assinado pelo sacado e endossadas ao Banco do Brasil S.A.			
			*/
			$conteudo .= $this->formatString($config->aceite,1);
			
			//26.3P Data da Emissão do Título 110 117 8 - Numérico G071
			/*
			A data de emissão não pode ser maior que a data do vencimento, nem maior que a data de envio de arquivo.
			No caso, da carteira 11, além das observações anteriores, não pode ser igual a data de vencimento.			
			*/
			$conteudo .= $this->formatData(date('Y-m-d'));  
			
			//27.3P Código do Juros de Mora 118 118 1 - Numérico C018
			/*
			O código '3' – isento, não é tratado automaticamente pelo Banco. Para que haja isenção de juros esta
			informação deve ser cadastrada na sua carteira de cobrança, junto a sua agência de relacionamento.
			*/
			$conteudo .= '2';
			
			//28.3P Data do Juros de Mora 119 126 8 - Numérico C019 Não há carência de juros.
			$dateJuros = date_create_from_format('Y-m-d', $cliente->vencimento);
			$dateJuros->add(new DateInterval('P1D'));
			$conteudo .= $this->formatData($dateJuros->format('Y-m-d'));	
			
			//29.3P Juros de Mora por Dia/Taxa 127 141 13 2 Numérico C020
			/*
			A taxa de juros impostada no arquivo sobrepõe a taxa cadastrada no banco. Obs.: no caso de carteira
			Descontada, o banco cobra juros específicos para essa modalidade, desconsiderando a informação colocada
			no arquivo e/ou cadastrada na carteira de cobrança.			
			*/
			$conteudo .= $this->formatValor($config->mora,15); 

			//30.3P Código do Desconto 1 142 142 1 - Numérico C021
			/*
			O Banco do Brasil só trata os códigos '1', '2' e '3'. No caso em que não há desconto, informar '0' (zero). Para
			os códigos '1' e '2' é obrigatório a informação dos demais campos referentes ao desconto, caso contrário, o
			registro do título será recusado.			
			*/
			//31.3P Data do Desconto 1 143 150 8 - Numérico C022 Zeros, quando não houver desconto a ser concedido.
			//32.3P Desconto 1 Valor/Percentual a ser Concedido 151 165 13 2 Numérico C023 Zeros, quando não houver desconto a ser concedido.
			
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
                        
			//36.3P Código para Protesto 221 221 1 - Numérico C026
			/*
			O Banco do Brasil trata somente os códigos '1' – Protestar dias corridos, '2' – Protestar dias úteis, e '3' – Não
			protestar. No caso de carteira 31 ou carteira 11/17 modalidade Vinculada, se não informado nenhum código, o
			sistema assume automaticamente Protesto em 3 dias úteis.			
			*/
			$conteudo .= '3';

			//Número de Dias para Protesto 222 223 2 - Numérico C027
			/*
			Preencher de acordo com o código informado na posição 221. Para código '1' – é possível, de 6 a 29 dias,
			35º, 40º, dia corrido. Para código '2' – é possível, 3º, 4º ou 5º dia útil. Para código '3' preencher com Zeros.			
			*/
			$conteudo .= '00';

			//38.3P Código para Baixa/Devolução 224 224 1 - Numérico C028
			/*
			Campo não tratado pelo sistema. Informar 'zeros'. O sistema considera a informação que foi cadastrada na
			sua carteira junto ao Banco do Brasil.			
			*/
			$conteudo .= '0';

			//39.3P Número de Dias para Baixa/Devolução 225 227 3 - Alfanumérico C029
			/*
			Campo não tratado pelo sistema. Informar 'zeros'. O sistema acata a informação que foi cadastrada na sua
			carteira junto ao Banco do Brasil.			
			*/
			$conteudo .= '000';

			//40.3P Código da Moeda 228 229 2 - Numérico G065
			$conteudo .= '09';

			//41.3P Nº do Contrato da Operação de Créd. 230 239 10 - Numérico C030 Campo não tratado pelo sistema. Pode ser informado 'zeros' ou o número do contrato de cobrança.
			$conteudo .= $this->complementoRegistro(10,"zeros");

			//42.3P Uso Exclusivo FEBRABAN/CNAB 240 240 1 - Alfanumérico Brancos G004 Informar 'brancos' (espaços).
			$conteudo .= $this->complementoRegistro(1,"brancos");
			
			$conteudo .= chr(13).chr(10); 								//essa é a quebra de linha
			//$this->lote++;
			$this->tot_linhas++;
			
			$i++;
			// ---------------------------------------------------------------------------------------------------------------------------------------
			// Registro Detalhe - Segmento Q (Obrigatório - Remessa)
			//01.3Q Código do Banco na Compensação 1 3 3 - Numérico '001' G001 001 para Banco do Brasil S.A.
			$conteudo .= '001';             							
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
			if(!empty($config->juros)) 
			{
				$conteudo .= '001'; 										//1 3 3 - Numérico 001 G001 001 para Banco do Brasil S.A.
				$conteudo .= $this->formatNumber($this->lote,4); 			//4 7 4 - Numérico G002 Informar o número do lote ao qual pertence o registro. Deve
																			 #ser igual ao número informado no Header do lote
				$conteudo .= '3'; 											//8 8 1 - Numérico ‘3’ G003 3 – para registro Detalhe.
				$conteudo .= $this->formatNumber($i,5); 					//9 13 5 - Numérico G038
																			#	Ir incrementando em 1 a cada nova linha do registro detalhe,
																			#	esse sequencial é continuação dos segmentos 'P' e 'Q'
																			#	anterior.
				$conteudo .= 'R'; 											//14 14 1 - Alfanumérico ‘R’ G039
				$conteudo .= ' ';											//15 15 1 - Alfanumérico Brancos G004
				$conteudo .= $this->formatNumber($cliente->cod_movimento,2);//16 17 2 - Numérico C004 Repetir código informado no segmento P.
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
		$conteudo = '001';
		//02.5 Lote de Serviço 4 7 4 - Numérico G002 Informar mesmo número do header de lote.
		$conteudo .= $this->formatNumber($this->lote,4);
		//03.5 Tipo de Registro 8 8 1 - Numérico '5' G003
		$conteudo .= '5';
		//04.5 Uso Exclusivo FEBRABAN/CNAB 9 17 9 - Alfanumérico G004 Informar 'brancos'.
		$conteudo .= $this->complementoRegistro(9,"brancos");
		//05.5 Quantidade de Registros do Lote 18 23 6 - Numérico G057 Total de linhas do lote (inclui Header de lote, Registros e Trailer de lote).
		$conteudo .= $this->formatNumber(($this->tot_linhas-1),6);
		//06.5 Uso Exclusivo FEBRABAN/CNAB 24 240 217 - Alfanumérico Brancos G004 Informar Zeros e 'brancos'.
		$conteudo .= $this->complementoRegistro(217,"brancos");
		$this->conteudo .= $conteudo.chr(13).chr(10);
		$this->tot_linhas++;		
		
		
		#Trailer de Arquivo
		//01.9 Código do Banco na Compensação 1 3 3 - Numérico 001 G001 001 para Banco do Brasil S.A.
		$conteudo = '001';
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
		$conteudo .= $this->complementoRegistro(6,"brancos");
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
	
	protected function formatData($data = false) {
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
/*
    public function getMensagens(){
        return $this->arr_mensagens;
    }
*/
}

