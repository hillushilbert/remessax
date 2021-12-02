<?php

namespace Skynix\Remessax\Remessa;

use Skynix\Remessax\Remessa;


/*
* @descr: Gera o arquivo de remessa para cobranca no padrao CNAB 400 Santander 2017
*/

class Sicredi extends Remessa {
    
    
    private function calcNossoNumero($id_parcela){
        
        /*
        Cooperativa de crédito/agência beneficiária: 0165
        Posto: 02
        Beneficiário: 00623
        Ano: 07
        Byte da geração: 2 (nosso número gerado pelo beneficiário)
        Número seqüencial: 00003 
        */
        $config = $this->config;
        $agencia = $this->formatNumber($config->agencia,4);
        $posto = $this->formatNumber($config->posto,2);
        $conta = $this->formatNumber($config->conta,5);
        $ano = date('y');
        $byte = 2;
        $id_parcela = $this->formatNumber($id_parcela,5);
        
        $id_pagamento = (string)($agencia.$posto.$conta.$ano.$byte.$id_parcela);
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
        
        /*
        AA/BXXXXX-D
        AA = Ano atual
        B = Byte (2 a 9). 1 só poderá ser utilizado pela cooperativa.
        XXXXX – Número livre de 00000 a 99999.
        D = Digito Verificador pelo módulo 11.
        EX: 13/200004-1
        */
        $return = $ano.$byte.$id_parcela.$digito;
		//$return = $id_pagamento.$digito;
		$return = str_pad($return,9,'0',STR_PAD_LEFT);	
		return $return;       
        
    
    }

	
	public function setHeader(){
        ## REGISTRO HEADER
		$config = $this->config;		
		$conteudo = '';

        $conteudo .= '0';                                           //001 a 001 001 Identificação do registro header A identicação do header deve ser “0”(zero)
        $conteudo .= 1;                                             //002 a 002 001 Identificação do arquivo remessa A identificação do arquivo de remessa deve ser “1”.
        $conteudo .= 'REMESSA';                                     //003 a 009 007 Literal remessa “REMESSA”
        $conteudo .= '01';                                          //010 a 011 002 Código do serviço de cobrança O código de serviço de cobrança é “01”
        $conteudo .= $this->limit('COBRANCA',15);                   //012 a 026 015 Literal cobrança “COBRANCA”
        $conteudo .= $this->limit($config->conta,5);                //027 a 031 005 Código do beneficiário Código do beneficiário
        $conteudo .= $this->limit($config->cpf_cnpj,14);            //032 a 045 014 CPF/CGC do beneficiário Informar CPF/CNPJ do beneficiário. Alinhado à direita e zeros à esquerda;
        $conteudo .= $this->complementoRegistro(31,"brancos");      //046 a 076 031 Filler Deixar em Brancos (sem preenchimento)
        $conteudo .= '748';                                         //077 a 079 003 Número do Sicredi “748”
        $conteudo .= $this->limit('SICREDI',15);                     //080 a 094 015 Literal Sicredi “SICREDI”
        $conteudo .= date('Ymd');                                   //095 a 102 008 Data de gravação do arquivo O Formato da data de geração do arquivo deve estar no padrão: AAAAMMDD
        $conteudo .= $this->complementoRegistro(8,"brancos");       //103 a 110 008 Filler Deixar em Branco (sem preenchimento)
        $conteudo .= $this->formatNumber($config->num_remessa,7);   //111 a 117 007 Número da remessa Deve ser maior que zero: número do último arquivo remessa + 1;
        $conteudo .= $this->complementoRegistro(273,"brancos");     //118 a 390 273 Filler Deixar em Branco (sem preenchimento)
        $conteudo .= $this->formatString('2.00',4);                 //391 a 394 004 Versão do sistema 2.00 (o ponto deve ser colocado)
        $conteudo .= '000001';                                      //395 a 400 006 Número seqüencial do registro Alinhado à direita e zeros à esquerda;
        $conteudo .= chr(13).chr(10); 
        

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
			// Valor
			if(empty($cliente->valor)){
				$this->arr_mensagens[] = $cliente->matricula.' :: Valor da mensalidade faltando';
			}
            
            //----------------------------------------------------------------------------
            // Verificar se a data de vencimento é 7 dias maior que a data de hoje;
            //----------------------------------------------------------------------------
            
            
			
            ## ---------------------------------------------------------------------------------------
            /* 001 a 001 001 Identificação do registro detalhe Identificação do registro detalhe de estar “1” */
            $conteudo .= '1';
            
            /* 002 a 002 001 Tipo de cobrança “A” - Sicredi Com Registro */
            $conteudo .= 'A';
            
            /* 003 a 003 001 Tipo de carteira “A” – Simples */
            $conteudo .= 'A';
            
            /* 004 a 004 001 Tipo de Impressão “A” – Normal “B” – Carnê */
            $conteudo .= 'A';
            
            /* 005 a 016 012 Filler Deixar em Branco (sem preenchimento) */
            $conteudo .= $this->complementoRegistro(12,"brancos");
            
            /* 017 a 017 001 Tipo de moeda “A” – Real */
            $conteudo .= 'A';
            
            /* 018 a 018 001 Tipo de desconto “A” – Valor “B” – Percentual */
            $conteudo .= 'A';
            
            /* 019 a 019 001 Tipo de juros “A” – Valor “B” – Percentual */
            $conteudo .= 'B';
            
            /* 020 a 047 028 Filler Deixar em Branco (sem preenchimento) */
            $conteudo .= $this->complementoRegistro(28,"brancos");
            
            /* 048 a 056 009 Nosso número Sicredi */
            $conteudo .= $this->calcNossoNumero($cliente->id_titulo);
            
            /* 057 a 062 006 Filler Deixar em Branco (sem preenchimento) */
            $conteudo .= $this->complementoRegistro(6,"brancos");
            
            /* 063 a 070 008 Data da Instrução O Formato da data de instrução do arquivo deve estar no padrão: AAAAMMDD  */
            $conteudo .= date('Ymd');
            
            /* 071 a 071 001 Campo alterado, quando instrução “31” Campo deve estar vazio (sem preenchimento), só utilizar quando 109-110 for = 31. Usar as 
                seguintes opções:
                A – Desconto;
                B - Juros por dia;
                C - Desconto por dia de antecipação;
                D - Data limite para concessão de desconto;
                E - Cancelamento de protesto automático; 
                F - Carteira de cobrança - não disponível. */
            $conteudo .= ' ';
            
            /* 072 a 072 001 Postagem do título “S” - Para postar o título diretamente ao pagador  
                “N” - Não postar e remeter o título para o beneficiário */
            $conteudo .= 'N';
            
            /* 073 a 073 001 Filler Deixar em Branco (sem preenchimento) */
            $conteudo .= $this->complementoRegistro(1,"brancos");
            
            /* 074 a 074 001 Emissão do boleto “A” – Impressão é feita pelo Sicredi “B” – Impressão é feita pelo Beneficiário */
            $conteudo .= 'B';
            
            /* 075 a 076 002 Número da parcela do carnê Quando o tipo de impressão for “B – Carnê” (posição 004). */
            $conteudo .= $this->formatNumber(0,2);
            
            /* 077 a 078 002 Número total de parcelas do carnê Quando o tipo de impressão for “B – Carnê” (posição 004). */
            $conteudo .= $this->formatNumber(0,2);
            
            /* 079 a 082 004 Filler Deixar em Branco (sem preenchimento) */
            $conteudo .= $this->complementoRegistro(4,"brancos");
            
            /* 083 a 092 010 Valor de desconto por dia de antecipação Informar valor de desconto (alinhado à direita e zeros à esquerda) ou senão preencher com zeros */
            $conteudo .= $this->formatValor($cliente->valorDesconto,10);
            
            /* 093 a 096 004 % multa por pagamento em atraso Alinhado à direita com zeros à esquerda, sem separador decimal ou preencher com zeros. */
            $conteudo .= $this->formatValor(round($config->multa,2),4);
            
            /* 097 a 108 012 Filler Brancos (sem preenchimento)  */
            $conteudo .= $this->complementoRegistro(12,"brancos");
            
            /* 109 a 110 002 Instrução Este campo só permite usar os seguintes códigos: 
                01 - Cadastro de título;
                02 - Pedido de baixa;
                04 - Concessão de abatimento;
                05 - Cancelamento de abatimento concedido;
                06 - Alteração de vencimento;
                09 - Pedido de protesto;
                18 - Sustar protesto e baixar título;
                19 - Sustar protesto e manter em carteira; */
            $conteudo .= '01';
            
            /* 111 a 120 010 Seu número Este campo nunca pode se repetir (Diferente de branco) - normalmente usado neste campo o número da nota fiscal gerada para o pagador. */
            $conteudo .= $this->formatNumber($cliente->id_titulo,10);
            
            /* 121 a 126 006 Data de vencimento A data de vencimento deve ser sete dias MAIOR que o campo 151 a 156 “Data de emissão”. Formato: DDMMAA */
            $conteudo .= $this->formatData($cliente->vencimento); 
            
            /* 127 a 139 013 Valor do título Alinhado à direita e zeros à esquerda; */
            $conteudo .= $this->formatValor($cliente->valor,13);
            
            /* 140 a 148 009 Filler Deixar em Branco (sem preenchimento) */
            $conteudo .= $this->complementoRegistro(9,"brancos");
            
            /* 149 a 149 001 Espécie de documento Este campo só permite usar os seguintes códigos: 
                A - Duplicata Mercantil por Indicação;
                B - Duplicata Rural;
                C - Nota Promissória;
                D - Nota Promissória Rural;
                E - Nota de Seguros;
                G – Recibo;
                H - Letra de Câmbio;
                I - Nota de Débito;
                J - Duplicata de Serviço por Indicação; 
                K – Outros.
                O – Boleto Proposta
                Obs.: Se título possuir protesto automático, favor utilizar o código A, pois esta é uma espécie de
                documento que permite utilizar o protesto automático sem a utilização de um Sacador Avalista.
                Obs.: O Boleto Proposta da liberdade ao pagador de aceitar, ou não, o produto ou serviço vinculado
                ao boleto em questão. Não sendo prejudicado pelo não pagamento do mesmo. */
            $conteudo .= 'K';
            
            /* 150 a 150 001 Aceite do título “S” – sim “N” – não */
            $conteudo .= 'N';
            
            /* 151 a 156 006 Data de emissão A data de emissão deve ser sete dias MENOR que o campo 121 a 126 “Data de vencimento”. Formato: DDMMAA */
            $conteudo .= $this->formatData();
            
            /* 157 a 158 002 Instrução de protesto automático “00” - Não protestar automaticamente “06” - Protestar automaticamente */
            $conteudo .= '00';
            
            /* 159 a 160 002 Número de dias p/protesto automático Campo numérico - mínimo 03 (três) dias 
                Quando preenchido com 3 ou 4 dias o sistema comandará protesto em dias úteis após o
                vencimento. Quando preenchido acima de 4 dias,o sistema comandará protesto em dias corridos após o vencimento. */
            $conteudo .= '00';
             
            /* 161 a 173 013 Valor/% de juros por dia de atraso Preencher com valor (alinhados à direita com zeros à esquerda) ou preencher com zeros. */
            $conteudo .= $this->formatValor(0,13);
            
            /* 174 a 179 006 Data limite p/concessão de desconto Informar data no padrão: DDMMAA ou preencher com zeros. */
            $conteudo .= $this->formatData($cliente->dataDesconto);
            
            /* 180 a 192 013 Valor/% do desconto Informar valor do desconto (alinhado à direita e zeros à esquerda) ou preencher com zeros. */
            $conteudo .= $this->formatValor($cliente->valorDesconto,13); 
            
            /* 193 a 205 013 Filler Sempre preencher com zeros neste campo. */
            $conteudo .= $this->formatValor('0',13);
            
            /* 206 a 218 013 Valor do abatimento Informar valor do abatimento (alinhado à direita e zeros à esquerda) ou preencher com zeros. */
            $conteudo .= $this->formatValor('0',13);
            
            /* 219 a 219 001 Tipo de pessoa do pagador: PF ou PJ “1” - Pessoa Física “2” - Pessoa Jurídica */
            $conteudo .= '1';
            
            /* 220 a 220 001 Filler Sempre preencher com zeros neste campo.  */
            $conteudo .= '0';
            
            /* 221 a 234 014 CPF/CNPJ do Pagador Alinhado à direita e zeros à esquerda; Obs: No momento dos testes para homologação 
                estes dados devem ser enviados com informações válidas. */
            $conteudo .= $this->formatNumber($cliente->cpf,14);
            
            /* 235 a 274 040 Nome do pagador Neste campo informar o nome do pagador sem acentuação ou caracteres especiais. */
            $conteudo .= $this->limit($cliente->nome,40); 
            
            /* 275 a 314 040 Endereço do pagador Neste campo informar o endereço do pagador sem acentuação ou caracteres especiais.  */
            $conteudo .= $this->limit($cliente->endereco,40); 
            
            /* 315 a 319 005 Código do pagador na cooperativa beneficiário Se pagador novo, o campo deve conter zeros. 
                Para pagador já cadastrado, enviar o código enviado no primeiro arquivo de retorno ou
                sempre zeros quando o sistema do beneficiário não utiliza esse campo – campo alfanumérico; */
            $conteudo .= '00000';
            
            /* 320 a 325 006 Filler Sempre preencher com zeros neste campo. */
            $conteudo .= '000000';
            
            /* 326 a 326 001 Filler Deixar em Branco (sem preenchimento) */
            $conteudo .= ' ';
            
            /* 327 a 334 008 CEP do pagador Obrigatório ser um CEP Válido */
            $conteudo .= $this->formatString($cliente->cep,8);
            
            /* 335 a 339 005 Código do Pagador junto ao cliente Campo numérico (zeros quando inexistente) 
                Obs.: Para validações de arquivos deixar este campo com zeros, após a homologação pode ser
                usado o código do cliente, conforme informação do campo. */
            $conteudo .= $this->formatNumber($cliente->id_cliente,5);
            
            /* 340 a 353 014 CPF/CNPJ do Sacador Avalista Alinhado à direita e zeros à esquerda. Deixar em 
                branco caso não exista Sacador Avalista. O Sacador Avalista deve ser diferente do beneficiário e pagador. */
            $conteudo .= $this->formatNumber(0,14);
            
            /* 354 a 394 041 Nome do Sacador Avalista Deixar em brancos quando inexistente. Caso utilize usar sem acentuação ou caracteres especiais. */
            $conteudo .= $this->complementoRegistro(41,"zeros");
            /* 395 a 400 006 Número sequencial do registro Neste campo sempre informar "000002" para 
                primeiro registro de cobrança. Alinhado à direita e zeros à esquerda; */
            $conteudo .= $this->sequencial($this->tot_linhas);
            
            ## ---------------------------------------------------------------------------------------
            $conteudo .= chr(13).chr(10); 											//essa é a quebra de linha
			$this->val_total += $cliente->valor;

			$this->tot_linhas++;		
		} // fecha loop de clientes
		  
		$this->conteudo .= $conteudo;

	}

	public function setTrailler(){
		$config = $this->config;

		$conteudo = '';
        
        /* 001 a 001 001 Identificação do registro trailer “9” */
		$conteudo .= 9;
		
        /* 002 a 002 001 Identificação do arquivo remessa “1” */
        $conteudo .= 1;
		
        /* 003 a 005 003 Número do Sicredi “748” */
        $conteudo .= '748';
        
        /* 006 a 010 005 Código do beneficiário Conta Corrente sem o DV ou conta beneficiário. */
        $conteudo .= $this->formatNumber($config->conta,5);
		
        /* 011 a 394 384 Filler Deixar em Branco (sem preenchimento) */
        $conteudo .= $this->complementoRegistro(384,"zeros");
		
        /* 395 a 400 006 Número seqüencial do registro Alinhado à direita e zeros à esquerda;  */
        $conteudo .= $this->sequencial($this->tot_linhas);
		
        $this->conteudo .= $conteudo;
	}


}
?>