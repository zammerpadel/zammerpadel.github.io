<?php
$products = getInput("products");
?>
<div class="proshopContainer">
<?php foreach ($products as $product){?>
	<div class = "product">
		<img title="<?php echo $product->attributes->name?>" src="<?php echo "images/".$product->attributes->image?>"></img>
		<div class = "productDescription">
			<a><?php echo $product->attributes->name?></a>
			<p><?php echo "$".$product->attributes->price?></p>
		</div>
	</div>
<?php }?>
</div>