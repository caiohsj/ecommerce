<?php 

use \Hcode\PageAdmin;
use \Hcode\Model\User;

//Rota para listagem de usuáios
$app->get('/admin/users', function(){
	//Método que verifica se o usuario está logado e se ele é um admin
	User::verifyLogin();

	$users = User::listAll();

	$page = new PageAdmin();

	$page->setTpl("users", array(
		"users"=>$users
	));
});

//Rota para criação de um usuário
$app->get('/admin/users/create', function(){
	//Método que verifica se o usuario está logado e se ele é um admin
	User::verifyLogin();
	
	$page = new PageAdmin();

	$page->setTpl("users-create");
});

$app->get('/admin/users/:iduser/delete', function($iduser){
	//Método que verifica se o usuario está logado e se ele é um admin
	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$user->delete();

	//header("Location: /admin/users");
	//exit;

});

//Rota para editar um usuário
$app->get('/admin/users/:iduser', function($iduser){
	//Método que verifica se o usuario está logado e se ele é um admin
	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);
	
	$page = new PageAdmin();

	$page->setTpl("users-update", array(
		"user"=>$user->getValues()
	));
});

$app->post('/admin/users/create', function(){
	//Método que verifica se o usuario está logado e se ele é um admin
	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$_POST["despassword"] = password_hash($_POST["despassword"], PASSWORD_DEFAULT, ["cost"=>21]);

	$user->setData($_POST);

	$user->save();

	header("Location: /admin/users");
	exit;
});

$app->post('/admin/users/:iduser', function($iduser){
	//Método que verifica se o usuario está logado e se ele é um admin
	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->get((int)$iduser);

	$user->setData($_POST);

	$user->update();

	header("Location: /admin/users");
	exit;
});

 ?>