<?php

function logError($message,$newLine = "<br/>"){
	GLOBAL $LOGERRORSET;
	$LOGERRORSET = true;
	if(isset($_SESSION['ERRORMESSAGE'])){
		$_SESSION['ERRORMESSAGE'] .= $message .$newLine;
	}else{
		$_SESSION['ERRORMESSAGE'] = $message . $newLine;
	}
}

function logErrorDialog($dialogInformation){
	GLOBAL $LOGERRORSET;
	$LOGERRORSET = true;
	$_SESSION['ERRORDIALOG'] = $dialogInformation;
}

function logErrorAndSendEmail($message, $subject, $toEmail, $toName, $object = array(), $logFile = LOGFILE){

// 	if(file_exists($logFile) && filesize ($logFile) > LOGFILEMAXSIZE){
// 		rename($logFile, $logFile . date("j-n-Y-H-i-s"));
// 	}

	$request = $_REQUEST;
	if(array_key_exists("password", $request)){
		$request["password"] = "*****";
	}
	if(array_key_exists("passwordRepeat", $request)){
		$request["passwordRepeat"] = "*****";
	}
	$fh = fopen($logFile, 'a');
	$error =  "date: ". date("j/n/Y  H:i:s") . "\n" .
			  "message: ".$message. "\n" .
			  "request: ".var_export($request,true). "\n" .
			  "files: " . var_export($_FILES, true) . "\n" .
			  "url: " . curPageURL() . "\n" .
			  "data: ".var_export($object, true). "\n\n";
	fwrite($fh, $error);
	fclose($fh);

// 	if(is_email($toEmail))
	sendNotificationEmail(str_replace("\n","<br/>",$error), $subject, $toEmail, $toName);
}

function logErrorToFile($message, $object = array(), $logFile = LOGFILE){

	$request = $_REQUEST;
	if(array_key_exists("password", $request)){
		$request["password"] = "*****";
	}
	if(array_key_exists("passwordRepeat", $request)){
		$request["passwordRepeat"] = "*****";
	}
	$fh = fopen($logFile, 'a');
	$error =  "date: ". date("j/n/Y  H:i:s") . "\n" .
			  "message: ".$message. "\n" .
			  "request: ".var_export($request,true). "\n" .
			  "files:" . var_export($_FILES, true) . "\n" .
			  "url:" . curPageURL() . "\n" .
			  "data: ".var_export($object, true). "\n\n";
	fwrite($fh, $error);
	fclose($fh);
}

function logSuccess($message){
	if(isset($_SESSION['SUCCESSMESSAGE'])){
		$_SESSION['SUCCESSMESSAGE'] .= $message ."<br/>";
	}else{
		$_SESSION['SUCCESSMESSAGE'] = $message ."<br/>";
	}
}

function logException($e){
	if(file_exists(LOGFILE) && filesize (LOGFILE) > LOGFILEMAXSIZE){
		rename(LOGFILE, LOGFILE . date("j-n-Y-H-i-s"));
	}

	$request = $_REQUEST;
	if(array_key_exists("password", $request)){
		$request["password"] = "*****";
	}
	if(array_key_exists("passwordRepeat", $request)){
		$request["passwordRepeat"] = "*****";
	}

	$fh = fopen(LOGFILE, 'a');
	$error =  "date: ". date("j/n/Y  H:i:s") . "\n" .
						"code: ".$e->getCode() . "\n" .
						"file: ".$e->getFile() . "\n" .
						"line: ".$e->getLine() . "\n" .
						"message: ".$e->getMessage() . "\n" .
						"request:" . var_export($request, true) . "\n" .
						"files:" . var_export($_FILES, true) . "\n" .
						"url:" . curPageURL() . "\n" .
						"trace: ".$e->getTraceAsString() . "\n\n";
	fwrite($fh, $error);
	fclose($fh);
	if(DEBUG){
		echo str_replace("\n", "<br/>", $error);
	}

}

function logExceptionAndSendEmail($e, $subject, $toEmail, $toName, $object = array(), $logFile = LOGFILE){

	if(file_exists($logFile) && filesize ($logFile) > LOGFILEMAXSIZE){
		rename($logFile, $logFile . date("j-n-Y-H-i-s"));
	}

	$request = $_REQUEST;
	if(array_key_exists("password", $request)){
		$request["password"] = "*****";
	}
	if(array_key_exists("passwordRepeat", $request)){
		$request["passwordRepeat"] = "*****";
	}

	$fh = fopen($logFile, 'a');
	$error =  "date: ". date("j/n/Y  H:i:s") . "\n" .
						"code: ".$e->getCode() . "\n" .
						"file: ".$e->getFile() . "\n" .
						"line: ".$e->getLine() . "\n" .
						"message: ".$e->getMessage() . "\n" .
						"request:" . var_export($request, true) . "\n" .
						"files:" . var_export($_FILES, true) . "\n" .
						"data: ".var_export($object, true). "\n" .
						"url:" . curPageURL() . "\n" .
						"trace: ".$e->getTraceAsString() . "\n\n";
	fwrite($fh, $error);
	fclose($fh);

	// 	if(is_email($toEmail))
	sendNotificationEmail($error, $subject, $toEmail, $toName);
}

function handleError($errno, $errstr, $errfile, $errline, array $errcontext)
{
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }

    logException(new ErrorException($errstr, 0, $errno, $errfile, $errline));
    return false;
}
set_error_handler('handleError');

?>