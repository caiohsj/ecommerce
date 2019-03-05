<?php 

use \Hcode\Page;

//Rota da página principal do site
$app->get('/', function() {
	
	$page = new Page();

	$page->setTpl("index");

});

 ?>