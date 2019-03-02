<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;

class User extends Model {

	const SECRET = "HcodePhp7_Secret";

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

	public static function getForgot($email)
	{
		$sql = new Sql();

		$results = $sql->select("
			SELECT * FROM tb_persons a 
			INNER JOIN tb_users b USING(idperson) 
			WHERE a.desemail = :email;
		", array(
			":email"=>$email
		));

		if(count($results) === 0)
		{
			throw new \Exception("Não foi possível recuperar a senha.");
		}
		else
		{
			$data = $results[0];

			$results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
				":iduser"=>$data["iduser"],
				":desip"=>$_SERVER["REMOTE_ADDR"]
			));

			if(count($results2) === 0)
			{
				throw new \Exception("Não foi possível recuperar a senha.");
			}
			else
			{
				$dataRecovery = $results2[0];

				$code = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, User::SECRET, $dataRecovery["idrecovery"], MCRYPT_MODE_ECB));

				$link = "http://hcodecommerce.com.br/admin/forgot/reset?code=$code";

				$mailer = new Mailer($data["desemail"], $data["desperson"], "Redefenir senha", "forgot", array(
					"name"=>$data["desperson"],
					"link"=>$link
				));

				$mailer->send();

				return $data;
			}
		}
	}

	public function validForgotDecrypt($code)
	{
		mcrypt_decrypt(MCRYPT_RIJNDAEL_128 ,User::SECRET, base64_encode($code), MCRYPT_MODE_ECB);

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_userspasswordsrecoveries a INNER JOIN tb_users b USING(iduser) INNER JOIN tb_persons c USING(idperson) WHERE a.idrecovery = :idrecovery AND a.dtrecovey IS NULL AND DATE_ADD(a.dtregister, INTERVAL  HOUR) => NOW()", array(
			":idrecovery"=>$idrecovery
		));

		if(count($results) === 0)
		{
			throw new \Exception("Não foi possível recuperar a senha");
		}
		else
		{
			return $results[0];
		}
	}

	public static function setForgotUsed($idrecovery)
	{
		$sql = new Sql();

		$sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovey = NOW() WHERE idrecovery = :idrecovery", array(
			":idrecovery"=>$idrecovery
		));
	}

	public function setPassword($password)
	{
		$sql = new Sql();

		$sql->query("UPDATE tb_users SET despassword = :password WHERE iduser = :iduser", array(
			":password"=>$password,
			":iduser"=>$this->getiduser()
		));
	}
}

 ?>