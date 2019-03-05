<?php 

use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\Category;

$app->get('/admin/categories', function(){

	$categories = Category::listAll();

	//header e footer = false, por causa do layout do login possui um header e um footer próprio, ou seja, diferente das demais páginas
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("categories", [
		"categories"=>$categories
	]);
});

$app->get('/admin/categories/create', function(){

	//header e footer = false, por causa do layout do login possui um header e um footer próprio, ou seja, diferente das demais páginas
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("categories-create");
});

$app->post('/admin/categories/create', function(){

	$category = new Category();

	$category->setData($_POST);

	$category->save();

	header("Location: /admin/categories");
	exit;
});

$app->post('/admin/categories/:idcategory/delete', function($idcategory){

	$category = new Category();

	$category->get((int)$idcategory);

	$category->delete();

	header("Location: /admin/categories");
	exit;
});

$app->get('/categories/:idcategory', function($idcategory){
	$category = new Category();

	$category->get((int)$idcategory);

	$page = new Page();

	$page->setTpl("category", [
		'category'=>$category->getValues(),
		'products'=>[]
	]);
});

 ?>