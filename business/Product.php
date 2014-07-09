<?php

class Product extends BaseEntity{
	protected $db_table = 'products';
	
	function __construct($id = 0){
		$this->logicDelete = true;
		parent::__construct($id);
	}

	function id() {
		return $this->attributes->id;
	}
	
	function getProductsForHome(){
		$count = $this->getAll('',0,0,true);
		$maxProducts = 9;
		
		$products = array();
		
		while((count($products) <= $count) && (count($products) < $maxProducts)){
			$product = new Product(rand(1,$count-1));
			if($product && !in_array($product, $products)){
				array_push($products, $product);
			}
		}
		return $products;
	}
}
?>
