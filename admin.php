<?php 

use \Hcode\PageAdmin;
use \Hcode\Model\User;

//Rota para página inicial do admin
$app->get('/admin', function() {
	
	//Método que verifica se o usuario está logado e se ele é um admin
	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("index");

});

//Rota para a página/rota de login do admin
$app->get('/admin/login', function(){
	//header e footer = false, por causa do layout do login possui um header e um footer próprio, ou seja, diferente das demais páginas
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
]);

	$page->setTpl("login");
});

//Rota
$app->post('/admin/login', function(){
	//Método que autentica o usuário
	User::login($_POST["login"], $_POST["password"]);

	//E redireciona para a pagina/rota do admin
	header("Location: /admin");
	exit;
});

//Rota para deslogar usuário
$app->get('/admin/logout', function(){
	User::logout();

	header("Location: /admin/login");
	exit;
});

//Rota do 'esqueci a senha'
$app->get('/admin/forgot', function(){
	//header e footer = false, por causa do layout do login possui um header e um footer próprio, ou seja, diferente das demais páginas
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot");
});

$app->post('/admin/forgot', function(){
	$user = User::getForgot($_POST["email"]);

	header("Location: /admin/forgot/sent");
	exit;
});

$app->get('/admin/forgot/sent', function(){
	//header e footer = false, por causa do layout do login possui um header e um footer próprio, ou seja, diferente das demais páginas
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-sent");
});

$app->get('/admin/forgot/reset', function(){
	$user = User::validForgotDecrypt($_GET["code"]);

	//header e footer = false, por causa do layout do login possui um header e um footer próprio, ou seja, diferente das demais páginas
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));
});

$app->post('/admin/forgot/reset', function(){
	$forgot = User::validForgotDecrypt($_POST["code"]);

	User::setForgotUsed($forgot["idrecovey"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, ["cost"=>12]);

	$user->setPassword($password);

	//header e footer = false, por causa do layout do login possui um header e um footer próprio, ou seja, diferente das demais páginas
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-reset-success");
});

 ?>