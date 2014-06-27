<?php

function echoJsonResponse($success, $url = "", $message = "", $javascript = "", $errors = array(), $extraData = array()){

	$ajaxRequest = getInput("ajaxRequest",false);

	//var_dump($_REQUEST);
	if($ajaxRequest){
		$json = new stdClass();
		$json->success = $success;
		$json->url = $url;
		$json->javascript = $javascript;
		if(!$success){
			$_SESSION['SUCCESSMESSAGE'] = "";
			if(isset($_SESSION['ERRORMESSAGE'])){
				$message = $_SESSION['ERRORMESSAGE'] .$message ;
				$_SESSION['ERRORMESSAGE'] = "";
			}

		}

		if (isset($_SESSION['ERRORDIALOG'])){
			$dialog = $_SESSION['ERRORDIALOG'];
			unset($_SESSION['ERRORDIALOG']);
		}

		if (isset($dialog) && !$json->javascript){
			$strParams = "";

			if (isset($dialog["params"]) && count($dialog["params"]) > 0){
				$strParams .= "'" . array_shift($dialog["params"]) . "'";
				foreach ($dialog["params"] as $param){
					$strParams .= " , '" . $param . "'";
				}

			}

			$json->javascript = $dialog["jsFunction"] . "(" . $strParams . ")";
		}

		// this is bacuse the javascript that gets this response breaks with the chars < >
		$json->message = str_replace("<br/>", "\n",$message);
		$json->message = str_replace("<", "{_less_}",$json->message);
		$json->message = str_replace(">", "{_greater_}",$json->message);

		$json->javascript = str_replace("<br/>", "\n",$json->javascript);
		$json->javascript = str_replace("<", "{_less_}",$json->javascript);
		$json->javascript = str_replace(">", "{_greater_}",$json->javascript);

		$json->errors = $errors;

		if(!empty($extraData)){
			foreach ($extraData as $key => $value){
				$json->$key = $value;
			}
		}

		echo json_encode($json);
		exit;
	}else{
		if($message || (isset($extraData["showPrelogMessages"]) && $extraData["showPrelogMessages"])){
			if(!$success){
				logError($message);
				if(isset($extraData["showInAlert"]) && $extraData["showInAlert"]){
					$_SESSION['ERRORMESSAGESHOWINALERT'] = true;
				}else{
					$_SESSION['ERRORMESSAGESHOWINALERT'] = false;
				}
			}else{
				logSuccess($message);
			}
		}
		if($url != ""){
			forward($url);
		} else {
			forward($_SERVER["HTTP_REFERER"]);
		}
	}
}
