<?php
$text = getInputArray("text", $params, "");
?>
<div class="restrictedAreaContainer">
<?php 
	if($text == "")
		echo langEcho("restrictedarea");
	else
		echo $text;	
?>
</div>