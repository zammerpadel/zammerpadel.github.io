<?php echo loadView('navigation/htmlHeader', array("title" => $params['title'])); ?>
<body>
<div id="loading" style="display:none;">
	<img src='<?php echo WWWSTATIC?>images/loading.gif' alt='' />
</div>
<div id="header" class="loginHeader">
	<div class="wrapper">
		<?php echo loadView('navigation/languageSelector', array()); ?>
		<div id="message" style="display:none">
		<div id="errorMessage" class="errorMsg" style="display:none"></div>
		<div id="successMessage" class="succesMsg" style="display:none"></div>
		<!-- <a href="#" onclick="$('#message').slideUp(1000); return false;"><?php echo langEcho("popup:close")?></a> -->
		</div>
	</div>
</div>
<div id="wrapper">