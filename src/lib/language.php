<?php

function languageEnabled($lang){
	if(in_array($lang, explode(",", LANGUAGES))){
		return true;
	}
	return false;
}

function getEnabledLanguages(){
	$languages = explode(",", LANGUAGES);
	$languagesEnable = array();
	foreach($languages as $language){
		$languagesEnable[$language] = $language;
	}
	return $languagesEnable;
}

function includeLanguage($language){
	global $LANGUAGES;
	if(!isset($LANGUAGES[$language])){
		$LANGUAGES[$language] = include(PATHROOT . "language/" . $language . ".php");
 	}
 	return $LANGUAGES[$language];
}

function setLanguage($lang){
	global $currentLang;
	global $LANG;
	if(languageEnabled($lang)){
		$currentLang = $lang;
		$LANG = includeLanguage($lang);
	}else{
		$LANG = includeLanguage(DEFAULTLANG);
	}
}

/**
* search the $string in the language selected
*
* @param string $string the key to search in the language
* @param string $language the language to use (DEFAULT: getLanguage())
* @param string $lower if the key should be lowered before the search in the language array (DEFAULT: false)
* @param string $replace the array of object values to replace variables (DEFAULT: array())
* @param bool	$deleteEnters delete enters from final string.
* @return string language string replaced
*/
function langEcho($string, $language = '', $lower = false, $replace = array(), $deleteEnters = false){
	GLOBAL $LANG;
	GLOBAL $DEFAULTLANG;
	$langToUse = $LANG;
	if ($language && ($language != getLanguage()) && languageEnabled($language)) {
		$langToUse = includeLanguage($language);
	}
	$key = $string;
	if($lower){
		$key = strtolower($string);
	}
	if($langToUse && array_key_exists($key, $langToUse)){
		$string = $langToUse[$key];
 	}
	else if (array_key_exists($key, $DEFAULTLANG)){
 		$string = $DEFAULTLANG[$key];
 	}

 	if($replace){
 		$matches = array();
 		preg_match_all("/{_([^}]*)_}/", $string, $matches);

 		$valuesToSearch = array();
 		foreach($matches[1] as $match){
 			$parts = explode(".", $match);
 			$entity = "";
 			$attribute = "";

 			if(count($parts) > 1){
 				$entity = $parts[0];
 				$attribute = $parts[1];
 			}else{
 				$entity = "custom_variables_without_entity";
 				$attribute = $parts[0];
 			}

 			if(!array_key_exists($entity, $valuesToSearch)){
 				$valuesToSearch[$entity] = array();
 			}
 			$valuesToSearch[$entity][] = $attribute;
 		}

 		foreach($valuesToSearch as $entity => $attributes){

 			$replaceEntity = null;
 			if($entity == "custom_variables_without_entity"){
 				foreach($attributes as $attribute){
	 				if(array_key_exists($attribute, $replace)){
	 					$string = str_replace("{_" . $attribute . "_}", $replace[$attribute], $string);
	 				}
 				}
 			}else if(array_key_exists($entity, $replace)){
 				$replaceEntity = $replace[$entity];
 			}else{
	 			foreach($replace as $object){
	 				if(strtolower(get_class($object)) == strtolower($entity)){
	 					$replaceEntity = $object;
	 					break;
	 				}
	 			}
 			}

 			if($replaceEntity){
 				if(is_array($replaceEntity)){
 					foreach($attributes as $attribute){
 						if(array_key_exists($attribute, $replaceEntity)){
 							$string = str_replace("{_" . $entity . "." . $attribute . "_}", $replaceEntity[$attribute], $string);
 						}
 					}
 				}else{
 					foreach($attributes as $attribute){
 						if(isset($replaceEntity->$attribute)){
 							$string = str_replace("{_" . $entity . "." . $attribute . "_}", $replaceEntity->$attribute, $string);
 						}else{
 							if($replaceEntity->attributes && isset($replaceEntity->attributes->$attribute)){
 								$string = str_replace("{_" . $entity . "." . $attribute . "_}", $replaceEntity->attributes->$attribute, $string);
 							}
 						}
 					}
 				}
 			}
 		}
 	}

 	if ($deleteEnters){
 		$string = str_replace("\n", "", $string);
 	}
	return $string;
}
function getLanguagesEnabled(){
	return explode(",", LANGUAGES);
}

function getFullLanguajeName($lang){
	switch ($lang) {
		case "en":
			return "English";
		break;
		case "pt":
			return "Português";
		break;
		case "es":
			return "Español";
		break;
		case "fr":
			return "Français";
		break;
		default:
			return "English";
		break;
	}
}

/**
* will call langEcho with the right params.
*
* @param string $string the key to search in the language
* @param string $replace the array of object values to replace variables (DEFAULT: array())
* @param string $language the language to use (DEFAULT: getLanguage())
* @param string $lower if the key should be lowered before the search in the language array (DEFAULT: false)
* @param bool	$deleteEnters delete enters from final string.
* @return string language string replaced
*/

function langEchoReplaceVariables($string, $replace = array(), $language = '', $lower = '', $deleteEnters = false){
	return langEcho($string, $language, $lower, $replace, $deleteEnters);
}

function getLanguage(){
	global $currentLang;

	if(isset($currentLang) && languageEnabled($currentLang)){
		return $currentLang;
	}

	if(isUserLoggedIn()){
		return $_SESSION["userlang"];
	}

	if(isset($_COOKIE['lang'])){
		return $_COOKIE['lang'];
	}

	return DEFAULTLANG;
}

function ParseDateTimeToEn($date){
	$size = strlen($date);
	$day = "";
	$month = "";
	$year = "";
	if (getLanguage() == "en"){
		return $date;
	}else if (getLanguage() == "pt" || getLanguage() == "es"){
		for($i = 0; $i<2; $i++){
			$day.= $date[$i];
		}
		for($i = 3; $i<5; $i++){
			$month.= $date[$i];
		}
		for($i = 6; $i<$size; $i++){
			$year.= $date[$i];
		}
	}

	return $month . "/" . $day . "/" . $year;
}

function ParseDateTimeToEs($date){
	$size = strlen($date);
	$day = "";
	$month = "";
	$year = "";

	for($i = 0; $i<2; $i++){
		$month.= $date[$i];
	}
	for($i = 3; $i<5; $i++){
		$day.= $date[$i];
	}
	for($i = 6; $i<$size; $i++){
		$year.= $date[$i];
	}


	return $day . "/" . $month . "/" . $year;
}
function getLanguageFromNamespace($namespace) {
    GLOBAL $LANG;
    $return = array();
    $pattern = '/^' . $namespace.'/';
    foreach( $LANG as $key => $word) {
        if(preg_match($pattern, $key)) {
            $key = str_replace($namespace, "", $key);
            $return [$key] = $word;
        }
    }
    return $return;
}

