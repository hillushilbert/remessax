<?php

abstract class Remessax_Titulo {

	public $tipo;
	public $cpf;
	public $id_titulo;
	public $id_aluno;
	public $nosso_numero;
	public $vencimento;
	public $dataDesconto;
	public $valorDesconto;
	public $nome;
	public $endereco;
	public $bairro;
	public $cep;
	public $cidade;
	public $estado;
	public $valor;
	public $matricula;

	public abstract function to_string();

}

?>