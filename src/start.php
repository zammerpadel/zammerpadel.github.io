<?php
/* Register autolaod functions */
spl_autoload_register('loader_others');
spl_autoload_register('loader_class');

if(PHP_SAPI == "cli"){
	if ($argc > 0)
	{
		for ($i=1;$i < $argc;$i++)
		{
			parse_str($argv[$i],$tmp);
			$_REQUEST = array_merge($_REQUEST, $tmp);
		}
	}
}

include_once(dirname(__FILE__) . "/config.php");

ini_set('session.gc_maxlifetime', "604800");

set_include_path(
get_include_path()
. PATH_SEPARATOR . realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'business')
. PATH_SEPARATOR . realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'data_access')
);

if (CACHE_SESSION_ENABLED) {
	include_once(dirname(__FILE__) . "/lib/cache.php");
	include_once(dirname(__FILE__) . "/lib/SessionHandler.php");
	$handler = new NPSessionHandler();
	session_set_save_handler(
		array(&$handler, 'open'),
		array(&$handler, 'close'),
		array(&$handler, 'read'),
		array(&$handler, 'write'),
		array(&$handler, 'destroy'),
		array(&$handler, 'gc')
	);

	// the following prevents unexpected effects when using objects as save handlers
	register_shutdown_function('session_write_close');
}

GLOBAL $CONFIG;
$CONFIG = new stdClass();
$CONFIG->isLightbox = false;

GLOBAL $isDemoPresentation;
$isDemoPresentation = false;

GLOBAL $LOGERRORSET;
$LOGERRORSET = false;

GLOBAL $classPathMap;
$classPathMap = array(
	"PHPMailer" => "/lib/email/class.phpmailer.php",
	"SMTP" => "/lib/email/class.smtp.php",
	"Cache" => "/lib/cache.php",
	"RedisCache" => "/lib/RedisCache.php",
	"SessionHandler" => "/lib/SessionHandler.php",
	"UidGenerator" => "/lib/UidGenerator.php",
	"zipfile" => "/lib/zipfile.php",
	"MailChimp" => "/helpers/mailchimp.php",
	"Transloadit" => "/helpers/Transloadit/Transloadit.php",
);

/* Include specific files manually */
$files = array(
"helpers/AmazonS3/sdk.class.php",
"lib/email/mail_functions.php",
"lib/image.php",
"lib/jsonResponse.php",
"lib/language.php",
"lib/logguer.php",
"lib/utilities.php",
"lib/views.php",
);

foreach ($files as $file){
	include_once(PATHROOT. "/" .$file);
}

$publicPages = array(
"login",
//"signup",
"actions/user/login",
"actions/user/googleLogin",
"actions/user/webGoogleLogin",
"actions/user/save",
"actions/user/logout",
"actions/user/setTimezone",
"actions/email/send",
"actions/language/set",
"index",
"service",
"/service",
"changePassword",
"actions/user/changePassword",
"actions/user/sendEmailPassRecovery",
"test",
"actions/presentation/resizeImages",
"actions/slide/transloadVideoNotify",
"actions/slide/transloadAudioNotify",
"admin/encodeVideosNotify",
"admin/republishPresentations",
"resizePresentationsAsync",
"zoho",
"cronSyncStatsToZoho",
"cronSyncStatsToDataBase",
"passwordRecovery.php",
"ForgotPassword",
"importantMessage",
"ieMessage",
"mobileMessage",
"paypalNotify",
"actions/presentation/buyNotify",
"signInForm1",
);

if(!isset($_REQUEST["f"]) || !contains($_REQUEST["f"] ,"service.php")){
	session_start();
}

global $LANGUAGES;
if (!isset($LANGUAGES)) {
	$LANGUAGES = array();
}

global $DEFAULTLANG;
$DEFAULTLANG = includeLanguage(DEFAULTLANG);

setLanguage(getLanguage());

setTimezone();

if((isset($_REQUEST["secondLogin"]) && $_REQUEST["secondLogin"] == "allowAccess") || PHP_SAPI == "cli"){
	@session_start();
	$_SESSION["secondLoginAccess"] = true;
}

// login root user if is a command executed by console.
if(PHP_SAPI == "cli"){
	$user = new User();
	$user->getByUserName("root");
	loginUser($user);
}

if(PARSE_INI_ERROR ||
	(MAINTENANCE && PHP_SAPI != "cli" &&
		((!isset($_REQUEST["f"]) || $_REQUEST["f"] != "service/service.php") &&
		(!isset($_SESSION["secondLoginAccess"]) || !$_SESSION["secondLoginAccess"]))
	)){
	$_REQUEST["f"] = "down.php";
}

//check if we need to logout the user
$doLogout = getInput("doLogout", false);
if($doLogout){
	logout();
	$url = curPageURL();
	$url = str_replace("doLogout=$doLogout", "", $url);
	forward($url);
}

//check if we need to login the user automatically
$token = getInput("autoLoginToken", false);
if($token){
	$cache = Cache::getCacheInstance();
	if($cache){
		$id = $cache->fetch("userToken/" . $token);
		$user = new User($id);
		if($user->attributes && $user->attributes->id > 0){
			$url = curPageURL();
			$url = str_replace("autoLoginToken=$token", "", $url);
			// this is done because if we get the autoLoginToken for a service we should have the user logged in
			if (contains($url,"service/service.php")){
				session_start();
			}
			loginUser($user);
			// this is done to remove the autoLoginToken from the url
			// if we are in cli mode it is not necessary
			$doNotRedirect = getInput("doNotRedirect", false);
			if (!$doNotRedirect && PHP_SAPI != "cli" && !contains($url,"service/service.php")){
				forward($url);
			}
		}
	}
}


$file="";
$public = false;
$staticPage = false;

if(isset($_REQUEST["f"])){
	$file = $_REQUEST["f"];

	if(file_exists(PATHROOT . $file)){
		if(is_dir(PATHROOT . $file)){
			if ($file[strlen($file) -1] != "/"){
				$file .= "/";
			}
			$file .= "index.php";
		}
	}else if($file == ""){
		$file = "index.php";
	}else{
		$page = new Page();
		$page->getByUrlAndLanguage($file, getLanguage());
		if($page->attributes && $page->attributes->id > 0){
			$staticPage = true;
			$public = !$page->attributes->requiresUser;
		}else{
			$file = "index.php";
		}
	}
}else{
	$file = "index.php";
}

if(PHP_SAPI == "cli"){
	foreach ($publicPages as $publicPage){
		if (contains($file, $publicPage)){
			$public = true;
			break;
		}
	}
}else{

	if(curPageURL() == WWWROOT){
		$public = true;
	}
	if (!$public){
		foreach ($publicPages as $publicPage){
			if (contains(curPageURL(), WWWROOT.$publicPage)){
				$public = true;
				break;
			}
		}
	}

	if (!isUserLoggedIn()){
		if (!$public){
			forward(WWWROOT . "login.php?url=" . urlencode(curPageURL()));
		}
	}

	//save user actions
	$silentSubmit = getInput("silentSubmit");
	if(isUserLoggedIn()){
		if(!contains(curPageURL(), "presentation/progress.php") && empty($silentSubmit)){
			$log = new UserLog();
			$log->attributes->userId = getUserLoggedInId();
			$log->attributes->page = curPageURL();
			$log->attributes->date = time();
			$log->attributes->request = var_export($_REQUEST, true);
			$log->save();
		}
	}
}

if($staticPage){
	echo loadTemplate($page->attributes->template, $page->attributes->title, array("main" => $page->getBody()));
}else{
	if(file_exists(PATHROOT . $file)){
		unset($_REQUEST["f"]);
		try{
			include_once(PATHROOT . $file);
		}catch(Exception $ex){
			if(isset($_SERVER["HTTP_REFERER"])){
				$url = $_SERVER["HTTP_REFERER"];
			}else{
				$url = WWWROOT;
			}
			logException($ex);
			echoJsonResponse(false,$url,langEcho("error:please:retry"));
		}
	}
}

function getDirectoryList ($directory)
  {
    $results = array();
    $handler = opendir($directory);
    while ($file = readdir($handler)) {
      if ($file != "." && $file != ".." && $file != ".svn") {
        $results[] = $file;
      }
    }
    closedir($handler);
    return $results;
  }


function includeFolder($folder){
	$files = getDirectoryList(PATHROOT .$folder);
	foreach ($files as $file){
		if(is_dir(PATHROOT . $folder . "/". $file)){
			includeFolder($folder. "/" .$file);
		}else{
//			var_dump($folder . "/". $file);
			include_once(PATHROOT . $folder . "/". $file);
		}
	}
}

function loader_class($className){
	//echo 'Intentando cargar ', $className, ' via ', __METHOD__, "() </br> ";
	@include_once $className . '.php';
}

function loader_others($className){
	//echo 'Intentando cargar ', $className, ' via ', __METHOD__, "() </br> ";
	GLOBAL $classPathMap;
	if(isset($classPathMap[$className])){
		@include_once dirname(__FILE__) . $classPathMap[$className];
	}
}
