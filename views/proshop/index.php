<?php
$products = getInput("products");
$i = 0;
?>
<div class="proshopContainer">
<?php foreach ($products as $product){?>
	<div class = "product">
		<img title="<?php echo $product["name"]?>" src="<?php echo $product["image"]?>"></img>
		<div class = "productDescription">
			<a><?php echo $product["name"]?></a>
			<p><?php echo $product["price"]?></p>
		</div>
	</div>
<?php }?>
</div>