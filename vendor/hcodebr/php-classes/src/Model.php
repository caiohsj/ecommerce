<?php 

namespace Hcode;

//Classe que faz os getters e setter automaticamente
class Model {
	private $values = [];

	//Método mágico que é 'acionado' quando qualquer método é chamado, recebe por parâmetro o nome do método e os argumentos desse método que foi chamado
	public function __call($name, $args)
	{
		//Pega os 3 primeiros caracteres do nome do método chamado, ou seja, 'get' ou 'set'
		$method = substr($name, 0, 3);
		//Pega os caracteres restantes do nome do método chamado, exemplo: setnome -> 'nome'
		$fieldName = substr($name, 3, strlen($name));


		switch ($method) {
			//Caso seja 'get', retorna o valor
			case "get":
				return $this->values[$fieldName];
			break;
			//Caso seja 'set', atribui o valor
			case "set":
				$this->values[$fieldName] = $args[0];
			break;
		}
	}


	//Método que faz os setters dos campos e valores que serão passados pelo array que é retornado da consulta no banco
	public function setData($data = array())
	{

		foreach ($data as $key => $value) {
			//Concatena o nome do método 'set' com o valor da chave 'key', exemplo: 'setiduser(com o valor da chave)''
			$this->{"set".$key}($value);
		}
	}

	//Método que faz os getters, retorna o array com os valores
	public function getValues()
	{
		return $this->values;
	}
}

 ?>