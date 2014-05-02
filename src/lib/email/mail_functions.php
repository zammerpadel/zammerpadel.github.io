<?php
/** Sends a notification by email
 * @param string $message Message to be sent as notification.
 * @param string $subject Subject of notification.
 * @param string $toemail E-mail address of the destination. This field also support a string with several e-mails separated by ';'.
 * @param string $toname Name of the destination.
 * @param array $extraParams Array (key => value) with some of the following extra parameters:<p>
 * - string fromEmail: alternative e-mail source <p>
 * - string fromName: alternative e-mail name <p>
 * - string attachment: path of file to attach <p>
 * - string array cc: array of addresses to send as CC.  <p>
 * - string array cco: array of addresses to send as CCO.  <p>
 * @return message on error, true on success.
 */
function sendNotificationEmail($message, $subject, $toemail, $toname, $extraParams = array()){
	static $phpmailer;

	$phpmailer = new PHPMailer();

    $phpmailer->ClearAllRecipients();
    $phpmailer->ClearAttachments();
    $phpmailer->ClearCustomHeaders();
    if(!isset($extraParams["headers"])){
    	$extraParams["headers"] = array();
    }
    array_push($extraParams["headers"], "X-MC-Track:opens,clicks_all");

    if(array_key_exists("fromEmail", $extraParams))
    	$phpmailer->From = $extraParams["fromEmail"];
    else
    	$phpmailer->From = MAIL_FROM;

	if(array_key_exists("fromName", $extraParams))
		$phpmailer->FromName = $extraParams["fromName"];
	else
		$phpmailer->FromName = MAIL_FROM_NAME;

    if(array_key_exists("attachment", $extraParams)){
    	if(is_array($extraParams["attachment"])){
    		foreach ($extraParams["attachment"] as $attach) {
    			$phpmailer->AddAttachment($attach);
    		}
    	}else{
    		$phpmailer->AddAttachment($extraParams["attachment"]);
    	}
    }

    if(array_key_exists("headers", $extraParams)){
    	foreach($extraParams["headers"] as $header){
    		$phpmailer->addCustomHeader($header);
    	}
    }

    if(array_key_exists("cc", $extraParams)){
    	foreach($extraParams["cc"] as $address){
    		if(is_array($address)){
    			$phpmailer->AddCC($address["email"],$address["name"]);
    		}else{
				$phpmailer->AddCC($address,$address);
    		}
    	}
    }

    if(array_key_exists("cco", $extraParams)){
    	foreach($extraParams["cco"] as $address){
    		if(is_array($address)){
    			$phpmailer->AddBCC($address["email"],$address["name"]);
    		}else{
    			$phpmailer->AddBCC($address,$address);
    		}
    	}
    }

    if($toemail != ""){
	    $emailArray = explode(",", $toemail);
	    foreach ($emailArray as $email) {
	 	   $phpmailer->AddAddress($email, $toname);
	    }
    }

    $phpmailer->Subject = $subject;

	$phpmailer->IsHTML(true);
	$phpmailer->Body = $message;
	$phpmailer->IsSMTP();
	$phpmailer->Host = SMTP_HOST;
	$phpmailer->SMTPAuth = true;
	$phpmailer->Username = SMTP_USER;
	$phpmailer->Password = SMTP_PASSWORD;
	$phpmailer->Port = SMTP_PORT;
	$phpmailer->CharSet = "UTF-8";
    $return = $phpmailer->Send();

    if (!$return )
    {
        return $phpmailer->ErrorInfo;
	}
    return TRUE;

}
/*
function sendNotificationEmail($message, $subject, $toemail, $toname, $fromemail = "", $fromname = "", $images = array(), $attachment = null, $support = false){
	if(isset($attachment)){
		$phpmailer->AddAttachment($attachment);
	}

	$phpmailer->AddAddress($toemail, $toname);
	$phpmailer->Subject = utf8_decode($subject);

	$phpmailer->IsHTML(true);
	$phpmailer->Body = utf8_decode($message);
	$phpmailer->IsSMTP();
	$phpmailer->Host = SMTP_HOST;
	$phpmailer->SMTPAuth = true;
	// $phpmailer->Username = SMTP_USER;
	// $phpmailer->Password = SMTP_PASSWORD;
	if ($support){
		$phpmailer->Username = SMTP_USER_SUPPORT;
		$phpmailer->Password = SMTP_PASSWORD_SUPPORT;
	} else {
		$phpmailer->Username = SMTP_USER_NOREPLY;
		$phpmailer->Password = SMTP_PASSWORD_NOREPLY;
	}
	$phpmailer->Port = SMTP_PORT;

	//Images must be an array( name => path )
	foreach ($images as $key => $value){
		$phpmailer->AddEmbeddedImage($value, $key, $key);
	}

	$return = $phpmailer->Send();

	if (!$return )
	{
		return 'PHPMailer error: ' . $phpmailer->ErrorInfo;
	}
	return TRUE;

}*/


?>
