<?php
echo loadView('navigation/header');
echo loadView('navigation/tab');
	
$productManager = new Product();
$products = $productManager->getProductsForHome();
echo loadView('proshop/index', array("products" => $products));
