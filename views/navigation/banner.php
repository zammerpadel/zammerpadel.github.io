<?php
$club = getInput("club");
?>
<div class="banner">
	<div class="bannerContainer right">
		<p class ="bannerTitle"><?php echo $club['name']?></p>
		<p class ="bannerSubtitle"><?php echo $club['neighbour']?></p>
	</div>
	<img src='images/<?php echo $club['image']?>'></img>
	<div class="bannerContainer left">
		<p><?php echo "Email: ".$club['email']?></p>
		<p><?php echo "Ubicaci&oacuten: ".$club['location']?></p>
		<p><?php echo "Tel: ".$club['phone']?></p>
	</div>
</div>