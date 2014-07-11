<?php
echo loadView('navigation/header');
echo loadView('navigation/tab');
	
$clubPadel = array();
$clubPadel['name'] = "CLUB ZAMMER PADEL";
$clubPadel['image'] = "club_zammer_padel.jpg";
$clubPadel['neighbour'] = "Florida";
$clubPadel['phone'] = "4797-9083";
$clubPadel['location'] = "Fray J. Sarmiento 1200";
$clubPadel['email'] = "zammer.padel@gmail.com";
echo loadView('navigation/banner', array("club" => $clubPadel));

$productManager = new Product();
$products = $productManager->getProductsForHome();
echo loadView('proshop/index', array("products" => $products));

$clubTenis = array();
$clubTenis['name'] = "CLUB ZAMMER TENIS";
$clubTenis['image'] = "club_zammer_padel.jpg";
$clubTenis['neighbour'] = "Villa Martelli";
$clubTenis['phone'] = "4797-9083";
$clubTenis['location'] = "Fray J. Sarmiento 1200";
$clubTenis['email'] = "zammer.padel@gmail.com";
echo loadView('navigation/banner', array("club" => $clubTenis));


echo loadView('navigation/footer');
