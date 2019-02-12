<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;

class User extends Model {

	//Constante com nome da sessão
	const SESSION = "User";

	public static function login($login,$password)
	{
		$sql = new Sql();

		//Listando usuário desejado caso exista
		$results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
			":LOGIN"=>$login
		));

		//Se este login não existe, então é 'estourada' uma exceção
		if(count($results) === 0)
		{
			throw new \Exception("Usuário inexistente ou senha inválida");
			
		}

		//Recebendo array com os dados do usuario encontrado
		$data = $results[0];

		//password_verify compara a senha(string) com hash que foi gerado para a senha no banco e retorna true ou false
		if(password_verify($password, $data["despassword"]) === true)
		{
			$user = new User();

			//Método que faz os setters do array que foi recebido pela consulta no banco
			$user->setData($data);

			//A variável de sessão recebe os dados dos gets gerados pelo metodo getValues
			$_SESSION[User::SESSION] = $user->getValues();

			//$retorna o objeto
			return $user;

		//Se não é 'estourada' uma exceção
		} else {
			throw new \Exception("Usuário inexistente ou senha inválida");
			
		}
	}

	//Método estático que verifica se o usuario está logado e se é um admin
	public static function verifyLogin($inadmin = true)
	{
		//Se não tem a variável de sessão ou se a variáve de sessão está vazia ou se o iduser da sessão não é maior que 0 ou se o campo inadmin for diferente de true
		if(!isset($_SESSION[User::SESSION]) || !$_SESSION[User::SESSION] || !(int)$_SESSION[User::SESSION]["iduser"] > 0 || (bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin)
		{
			//Redireciona para a rota de login
			header("Location: /admin/login");
			exit;
		}
	}

	//Método para remover o usuário da sessão
	public static function logout()
	{
		$_SESSION[User::SESSION] = NULL;
	}

	public static function listAll()
	{
		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");

		return $results;
	}

	public function save()
	{
		$sql = new Sql();

		$results = $sql->select("CALL sp_users_save(:desperson,:deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
			":desperson"=>$this->getdesperson(),
			":deslogin"=>$this->getdeslogin(),
			":despassword"=>$this->getdespassword(),
			":desemail"=>$this->getdesemail(),
			":nrphone"=>$this->getnrphone(),
			":inadmin"=>$this->getinadmin()
		));

		$this->setData($results[0]);

	}

	public function get($iduser)
	{
		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", array(
			":iduser"=>$iduser
		));

		$this->setData($results[0]);
	}

	public function update()
	{
		$sql = new Sql();

		$results = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson,:deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
			":iduser"=>$this->getiduser(),
			":desperson"=>$this->getdesperson(),
			":deslogin"=>$this->getdeslogin(),
			":despassword"=>$this->getdespassword(),
			":desemail"=>$this->getdesemail(),
			":nrphone"=>$this->getnrphone(),
			":inadmin"=>$this->getinadmin()
		));

		$this->setData($results[0]);
	}

	public function delete()
	{
		$sql = new Sql();

		$sql->query("CALL sp_users_delete(:iduser)", array(
			":iduser"=>$this->getiduser()
		));
	}
}

 ?>