<?php 

use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\Category;
use \Hcode\Model\Product;
use \Hcode\Model\User;

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

$app->get('/admin/categories/:idcategory/products', function($idcategory){
	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$page = new PageAdmin();

	$page->setTpl("categories-products", [
		'category'=>$category->getValues(),
		'productsRelated'=>$category->getProducts(),
		'productsNotRelated'=>$category->getProducts(false)
	]);
});

$app->get('/admin/categories/:idcategory/products/:idproduct/add', function($idcategory, $idproduct){
	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$product = new Product();

	$product->get((int)$idproduct);

	$category->addProduct($product);

	header("Location: /admin/categories/".$idcategory."/products");
	exit;
});

$app->get('/admin/categories/:idcategory/products/:idproduct/remove', function($idcategory, $idproduct){
	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$product = new Product();

	$product->get((int)$idproduct);

	$category->removeProduct($product);

	header("Location: /admin/categories/".$idcategory."/products");
	exit;
});


 ?>