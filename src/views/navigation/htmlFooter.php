</div>

<?php
$error = "";

if (isset($_SESSION['ERRORMESSAGE'])){
	$error = $_SESSION['ERRORMESSAGE'];
	$_SESSION['ERRORMESSAGE'] = "";
}

if ($error != ""){
	echo "<script type='text/javascript'>
			displayErrorMessage('" . str_replace("'",'"',$error) . "');
	</script>";
}

$success = "";

if (isset($_SESSION['SUCCESSMESSAGE'])){
	$success = $_SESSION['SUCCESSMESSAGE'];
	$_SESSION['SUCCESSMESSAGE'] = "";
}

if ($success != ""){
	echo "<script type='text/javascript'>
		displaySuccessMessage('" . str_replace("'",'"',$success) . "');
	</script>";
}

// register some variables in js to use for the language.
?>

<script type="text/javascript">
	var language = new Array();
	language["generic:error:message"] = '<?php echo langEcho("generic:error:message")?>';
</script>

<?php
	echo loadView("navigation/initOnEveryCallback");
?>
</body>
</html>