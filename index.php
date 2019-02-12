<?php 
session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;

$app = new Slim();

$app->config('debug', true);

//Rota da página principal do site
$app->get('/', function() {
	
	$page = new Page();

	$page->setTpl("index");

});

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

	header("Location: /admin/users");
	exit;

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

$app->run();

 ?>