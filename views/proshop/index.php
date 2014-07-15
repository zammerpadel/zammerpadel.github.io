<?php
$products = getInput("products");
?>
<div class="proshopContainer">
	<div>
		<ul id="carousel"> 
		    <li><img src="images/carousel/logo_zammer.png" /></li>
		    <li><img src="images/carousel/logo_head.png" /></li>    
		    <li><img src="images/carousel/logo_vairo.jpg" /></li>     
		    <li><img src="images/carousel/logo_zammer.png" /></li> 
		    <li><img src="images/carousel/logo_head.png" /></li>                                                          
		</ul>
	</div>
	
	<div>
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
</div>
<script type="text/javascript">
	$("#carousel").flexisel({
        visibleItems: 5,
        animationSpeed: 1000,
        autoPlay: true,
        autoPlaySpeed: 3000,            
        pauseOnHover: true,
        enableResponsiveBreakpoints: true,
        responsiveBreakpoints: { 
            portrait: { 
                changePoint:480,
                visibleItems: 1
            }, 
            landscape: { 
                changePoint:640,
                visibleItems: 2
            },
            tablet: { 
                changePoint:768,
                visibleItems: 3
            }
        }
    });    
});
</script>