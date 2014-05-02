<?php

function getUserCategoriesResponseObject($userCategories){
	$noretina = "";
	if(!isRetina()){
		$noretina = "_noretina";
	}

	$item = new stdClass();
	$item->data = new stdClass();
	$item->library_object_type = "0";
	$item->data->thumb = $userCategories->getIcon('list' . $noretina, UPLOADCDN);
	$item->data->bannerImage = $userCategories->getIcon('banner' . $noretina, UPLOADCDN);
	$item->data->id = $userCategories->attributes->id;

	return $item;
}

function getPresentationResponseObject($presentation, $autoLoginToken = false, $userPresentations = null, $userId = 0, $image = ""){
	$presSlides = $presentation->getSlides(1);
	if(count($presSlides) == 0){
		return false;
	}
	$parentUser = $presentation->getUserCreator();

	$return = new stdClass();
	$return->data = new stdClass();
	$return->library_object_type = "1";

	if($userId && $userId != $presentation->attributes->userId){
		$return->library_object_type = "2";
	}

	$return->data->id = $presentation->attributes->id;
	$return->data->presentation_uid = $presentation->attributes->applicationUid;
	$return->data->firstName = $parentUser->attributes->firstName;
	$return->data->lastName = $parentUser->attributes->lastName;
	$return->data->name = $presentation->attributes->name;
	$return->data->description = $presentation->attributes->description;
	$return->data->size = round(($presentation->attributes->size + $presentation->getSkinSize() + DEFAULT_PRESENTATION_EXTRA_FILE_SIZE) / 1048576); // convert bytes in MB
	$return->data->created = $presentation->attributes->created;
	$return->data->modified = $presentation->attributes->modified;
	$return->data->thumb = $presSlides[0]->getIcon("list",UPLOADCDN);
	if($image){
		$return->data->image = $image;
	}
	$return->data->parentId = $presentation->attributes->parentId;
	$return->data->totalSlides= $presentation->getSlides(0,0,true);
	$return->data->productStoreId = $presentation->attributes->productStoreId;
	$return->data->price = $presentation->attributes->price;

	//$return->data->buyUrl = $presentation->attributes->buyUrl;
	$return->data->buyUrl = "";
	if($presentation->attributes->price > 0){
		if($autoLoginToken){
			$return->data->buyUrl = WWWROOT . "actions/presentation/buy.php?presentation_uid=" . $presentation->attributes->applicationUid . "&autoLoginToken=" . $autoLoginToken;
		}else{
			$return->data->buyUrl = WWWROOT . "actions/presentation/buy.php?doLogout=true&presentation_uid=" . $presentation->attributes->applicationUid;
		}
	}
	$return->data->used = $presentation->attributes->used;
	$return->data->state = $presentation->getState($userPresentations);
	$return->data->pinCode = $presentation->attributes->pincode;
	$return->data->isStore = false;

	if($parentUser->attributes->id == DEFAULTPRESENTATIONSUSERID){
		$return->data->isStore = true;
	}

	return $return;
}

function getPresentationResponseObjectOld($presentation, $user, $autoLoginToken = false){
	$presSlides = $presentation->getSlides(1);
	if(count($presSlides) == 0){
		return false;
	}
	$parentUser = $presentation->getUserCreator();
	$tag = new Tag();
	$parentTags = $tag->getTagsByPressId($presentation->attributes->id);

	$return = new stdClass();
	$return->id = $presentation->attributes->id;
	$return->applicationUid = $presentation->attributes->applicationUid;
	$return->presentation_uid = $presentation->attributes->applicationUid;
	$return->presenterUid = $user->attributes->presenterUid;
	$return->firstName = $parentUser->attributes->firstName;
	$return->lastName = $parentUser->attributes->lastName;
	$return->name = $presentation->attributes->name;
	$return->description = $presentation->attributes->description;
	$return->size = round(($presentation->attributes->size + $presentation->getSkinSize() + DEFAULT_PRESENTATION_EXTRA_FILE_SIZE) / 1048576); // convert bytes in MB
	$return->created = $presentation->attributes->created;
	$return->modified = $presentation->attributes->modified;
	$return->code = $presentation->attributes->code;
	$return->thumb = $presSlides[0]->getIcon("list", UPLOADCDN);
	$return->image = $presSlides[0]->getIcon("flash", UPLOADCDN);
	$return->parentId = $presentation->attributes->parentId;
	$return->highlighted = ($presentation->attributes->id == HIGHLIGHTEDPRESENTATIONID);
	$return->totalSlides= $presentation->getSlides(0,0,true);
	$return->productStoreId = $presentation->attributes->productStoreId;
	$return->price = $presentation->attributes->price;
	//$return->buyUrl = $presentation->attributes->buyUrl;
	$return->buyUrl = "";
	if($presentation->attributes->price > 0){
		if($autoLoginToken){
			$return->buyUrl = WWWROOT . "actions/presentation/buy.php?presentation_uid=" . $presentation->attributes->applicationUid . "&autoLoginToken=" . $autoLoginToken;
		}else{
			$return->buyUrl = WWWROOT . "actions/presentation/buy.php?doLogout=true&presentation_uid=" . $presentation->attributes->applicationUid;
		}
	}
	$return->used = $presentation->attributes->used;
	$return->tags  = "";
	foreach($parentTags as $tag){
		$return->tags .= $tag->attributes->tag . " ";
	}
	$sharePresentation = new SharePresentation();
	$sharePresentation->getByPresentationId($presentation->attributes->id);
	$return->shareLink = "";
	if($sharePresentation && $sharePresentation->attributes && $sharePresentation->attributes->id > 0){
		$return->shareLink = WWWROOT . "sharePresentation.php?code=" . $sharePresentation->attributes->code;
	}

	$categories = array();
	$presentationCategories = $presentation->getCategories();
	foreach($presentationCategories as $category){
		$cat = array();
		$cat["id"] = $category->attributes->userCategoryId;
		$cat["name"] = $category->attributes->name;
		$categories[] = $cat;
	}

	$return->categories = $categories;

	$bundleArray = array();
	$presentationUrl = UPLOADCDN . get_class($presentation) . "/" . $presentation->attributes->id . "/";
	$bundle["type"] = "IOS";
	$bundle["url"] = $presentationUrl . $presentation->attributes->id . "_ios.zip";
	array_push($bundleArray, $bundle);

	$bundle["type"] = "HUB";
	$bundle["url"] = $presentationUrl . $presentation->attributes->id . "_hub.zip";
	array_push($bundleArray, $bundle);

	$bundle["type"] = "FLASH";
	$bundle["url"] = $presentationUrl . $presentation->attributes->id . "_flash.zip";
	array_push($bundleArray, $bundle);

	$bundle["type"] = "IOS_IPOD4";
	$bundle["url"] = $presentationUrl . "IOSBundle_iPod4.zip";
	array_push($bundleArray, $bundle);

	$bundle["type"] = "JsonHub";
	$bundle["url"] = $presentationUrl . "hub";
	array_push($bundleArray, $bundle);

	$bundle["type"] = "JsonFlash";
	$bundle["url"] = $presentationUrl . "flash";
	array_push($bundleArray, $bundle);

	$bundle["type"] = "JsonIosIpad";
	$bundle["url"] = $presentationUrl . "ios_ipad/Manifest.json";
	array_push($bundleArray, $bundle);

	$bundle["type"] = "JsonIosIpadTeacher";
	$bundle["url"] = $presentationUrl . "ios_ipad/ManifestTeacher.json";
	array_push($bundleArray, $bundle);

	$bundle["type"] = "JsonIosIpod";
	$bundle["url"] = $presentationUrl . "ios_ipod/Manifest.json";
	array_push($bundleArray, $bundle);

	$bundle["type"] = "PlistIosIpad";
	$bundle["url"] = $presentationUrl . "ios_ipad_plists/Manifest.json";
	array_push($bundleArray, $bundle);

	$bundle["type"] = "PlistIosIpod";
	$bundle["url"] = $presentationUrl . "ios_ipod_plists/Manifest.json";
	array_push($bundleArray, $bundle);

	$bundle["type"] = "IOS_IPAD";
	$bundle["url"] = $presentationUrl . "IOSBundle_iPad.zip";
	array_push($bundleArray, $bundle);

	$return->bundle = $bundleArray;

	return $return;
}


function debugVariable($var, $context ="none"){
	logException(new ErrorException(var_export($var,true), 0, 0, $context, 1));
}

function getReportCsv($appUid, $user, $session_uid,$filename = "", $devicesIds = "",&$savePath=""){
	$filePath = "";

	$result = call_rest_service_sync(PHOENIX_HUB_SERVICE . "application/report", array("presentation_uids" => array($appUid), "session_uid" => $session_uid),"plain", "POST");

	if ($result){
		$result = json_decode($result, true);
		$error_code = $result["error_code"];
		if($error_code == 0){
			$report_stats = $result["report_stats"];
		}

		$presentation = new Presentation();
		$presentation->getByApplicationUid($appUid);
		$username = $user->attributes->data["userName"];
		$imagesArray = array();
		$content = buildCSV($report_stats, $username, $presentation,$imagesArray,$filename,$devicesIds);

		if(isset($content)){
			if($savePath == ""){
				$savePath = TEMP_PATH ."reportsCSV_" . str_replace(".","",str_replace(" ","",microtime()));
			}
			$filePath = $savePath.DIRECTORY_SEPARATOR. $filename .".csv";
			createPath($filePath);
			foreach($imagesArray as $image){
				file_put_contents($savePath . "/"  .  $image->fileName, file_get_contents($image->imageUrl));
			}

			file_put_contents($filePath, $content);
			return true;
		}
	}

	return false;
}

function getHtmlAnswer($possibleAnswers, $answer){
	$return = '';
	if($answer){
		$keys = array();
		foreach($answer as $answerKey=>$answerValue){
			$answerKey = str_replace("answer_", "", $answerKey);
			$answerValue = trim($answerValue);
			if($answerValue){
				$keys[$answerKey] = $answerValue;
			}
		}


		foreach ($possibleAnswers as $possibleAnswer){
			if(array_key_exists($possibleAnswer->attributes->data["id"], $keys)){
				if($possibleAnswer->attributes->data["manualAnswer"] == 0){
					if($return == ''){
						$return = $possibleAnswer->attributes->data["answerText"];
					}else{
						$return = $return.";".$possibleAnswer->attributes->data["answerText"];
					}
				}else{
					if($return == ''){
						$return = $keys[$possibleAnswer->attributes->data["id"]];
					}else{
						$return = $return.";".$keys[$possibleAnswer->attributes->data["id"]];
					}
				}
			}
		}
	}
	return $return;
}

function buildCSV($report_stats, $user, $presentation,&$imagesArray ,$filename = "", $device_uid = ""){

	$header = array(langEcho("date"),langEcho("presenter") ,langEcho("participant:first:name"),langEcho("reports:participant:id"), langEcho("reports:presentation:name"), langEcho("reports:presentation"), langEcho("reports:slide"), langEcho("reports:interactive:feature"), langEcho("reports:question:text"), langEcho("reports:response"), langEcho("reports:correct"));
	$slides_quantity = $presentation->getSlidesQty();
	$slides = $presentation->getSlides();
	$imagesArray = array();
	foreach($slides as $slide){
		if($slide->attributes->slideType == "Poll" || $slide->attributes->slideType == "QA"){
			$questions = $slide->getQuestions();
			$slide->possibleAnswers = $questions[0]->getAnswers();
			$slide->questionText = $questions[0]->attributes->questionText;
		}
		if($slide->attributes->slideType == "Quiz"){
			$questions = $slide->getQuestions();
			$slide->possibleAnswers = array();
			$slide->questionText = array();
			foreach ($questions as $question){
				$slide->possibleAnswers[] = $question->getAnswers();
				$slide->questionText[] = $question->attributes->questionText;
			}
		}
		if($slide->attributes->slideType == "Sketch"){
			$slide->questionText = $slide->attributes->title;
		}
	}

	if(isset($report_stats)){
		$all_data = array_merge((array)$report_stats["poll"], (array)$report_stats["qa"], (array)$report_stats["quiz"], (array)$report_stats["drawit"]);
		$all_data = indexedToKeyedArray($all_data, array("session_uid","device_uid", "slide", "subslide"));
		//var_dump($all_data);
		$leads = indexedToKeyedArray($report_stats["lead"], "session_uid", true);
		$csvRows = array();

		$imageCount = 0;
		foreach ($report_stats["session"] as $session){
			$time = Date(langEcho("datetimeformat"),$session["timestamp"]) ;
			$teacher = new User($session["teacher_id"]);
			//error_log(var_export($teacher,true) . "\n",3,"/var/log/nearpod/fran.log");
			$teacher = $teacher->attributes->lastName." ". $teacher->attributes->firstName;
			$presentation_name = $session["presentation_name"];

			$leads_rows = $leads[$session["uid"]];

			// 1 (for the lead)
			$slides_qty = $slides_quantity + 1;
			if($session["homework"]){
				if(!$presentation->attributes->hwLead){
					// remove the lead slide
					$slides_qty--;
				}
				if($presentation->attributes->hwCovers){
					// add the hw start and end cover
					$slides_qty+=2;
				}
			}

			foreach ($leads_rows as $lead){
				if($lead["device_uid"] == $device_uid || !isset($device_uid ) || $device_uid =="")
				foreach ($slides as $slide){
					$type = $slide->attributes->slideType;
					if($slide->isPoll() || $slide->isQA() || $slide->isQuiz() || $slide->isSketch()){
						$answer = '-';
						$questions = '';
						$possibleAnswers = '';
						$questionText = '-';
						$type_value = '';
						$delta = 1;
						$correct = '-';

						if($session["homework"]){
							if(!$presentation->attributes->hwLead){
								// remove the lead slide
								$delta--;
							}
							if($presentation->attributes->hwCovers){
								// add the hw start cover
								$delta++;
							}
						}

						if($type == 'Quiz'){
							$data = array();
							$answer = array();
							$subslides = count($slide->possibleAnswers);
							for ($i = 0; $i<$subslides; $i++){
								$data[$i] = $all_data[$session["uid"] . "." . $lead["device_uid"] . "." . ($slide->attributes->orden+$delta)."." . ($i+1)];

								// quiz as qa
								if(!$data[$i] && $subslides == 1){
									$data[$i] = $all_data[$session["uid"] . "." . $lead["device_uid"] . "." . ($slide->attributes->orden+$delta)."."];
								}
								if(isset($data[$i]["answers"])){
									$answer[$i] = json_decode($data[$i]["answers"], true);
								}else{
									$answer[$i] = '-';
								}
							}
						}else{
							$data = '';
							$data = $all_data[$session["uid"] . "." . $lead["device_uid"] . "." . ($slide->attributes->orden+$delta)."."];
							if(isset($data["answers"])){
								$answer = json_decode($data["answers"], true);
							}
						}


						if($type == 'QA'){
							$questionText = $slide->questionText;
							$type_value = langEcho("qa");
							if($answer != "-"){
								$answer = getHtmlAnswer($slide->possibleAnswers, $answer);
								if($data["is_correct"]){
									$correct = "Y";
								}else{
									$correct = "N";
								}
							}
						}
						if($type == 'Poll'){
							$questionText = $slide->questionText;
							if($answer != "-"){
								$answer = getHtmlAnswer($slide->possibleAnswers, $answer);
							}
							$correct = "-";
							$type_value = langEcho("poll");
						}
						if($type == 'Quiz'){
							$i = 0;
							$correct = array();
							$questionText = array();
							foreach($answer as $ans){
								$possibleAnswers[$i] = $slide->possibleAnswers[$i];
								$questionText[$i] = $slide->questionText[$i];
								if($answer[$i] != '-'){
									$answer[$i] = getHtmlAnswer($possibleAnswers[$i], $ans);
									if($data[$i]["is_correct"]){
										$correct[$i] = "Y";
									}else{
										$correct[$i] = "N";
									}
								}else{
									$correct[$i] = "-";
								}
								$type_value = langEcho("quiz"). " - " . $slide->attributes->title;

								$i++;
							}
						}

						if($type == 'Sketch'){
							$questionText = $slide->questionText;
							$answer = "-";
							$data = $all_data[$session["uid"] . "." . $lead["device_uid"] . "." . ($slide->attributes->orden+$delta)."."];
							$correct = "-";
							if(isset($data) && $data["image_full_url"] != ""){

								$obj = new stdClass();
								$obj->fileName = $imageCount++ ."-".$lead["nickname"] . " - " . $slide->attributes->orden;
								$obj->fileName = substr($obj->fileName,0,50) . ".jpg";
								$obj->imageUrl = UPLOADCDN.$data["image_full_url"];
								$answer = $obj->fileName;
								$imagesArray[] = $obj;
							}
							$type_value = langEcho("sketch");
						}

						$date = date(langEcho('dateformat'), $session["timestamp"]);
						if($type == 'Quiz'){
							$count = count($answer);
							for ($i = 0; $i<$count; $i++){
								$j = 0;
								$csvRow = array();
								$row = array($date, $user, $lead["nickname"], $lead["student_id"], $session["presentation_name"], $slides_qty, $slide->attributes->orden + 1 + $delta, $type_value, $questionText[$i], $answer[$i], $correct[$i]);
								foreach($row as $field){
									$csvRow[$j++] = $field;
								}
								$csvRows[] = $csvRow;
							}
						}else{
							$j = 0;
							$csvRow = array();
							$row = array($date, $user, $lead["nickname"], $lead["student_id"], $session["presentation_name"], $slides_qty, $slide->attributes->orden +1 + $delta, $type_value, $questionText, $answer, $correct);
							foreach($row as $field){
								$csvRow[$j++] = $field;
							}
							$csvRows[] = $csvRow;
						}
					}

				}
			}
		}
	}

	$tempFileName = TEMP_PATH . "/tmp_$filename";
	$fp = fopen($tempFileName, 'w');

	$title = array($presentation_name,$teacher,$time);
	fputcsv($fp, $title, ",", '"');
	//* Nombre de la NPP * Nombre del profesor * Fecha y hora *
	if($header) {
		fputcsv($fp, $header, ",", '"');
	}

	foreach ($csvRows as $row){
		fputcsv($fp, str_replace("\n", " " ,$row), ",", '"');
	}

	fclose($fp);

	$contents = file_get_contents($tempFileName);

	unlink($tempFileName);

	return utf8_decode(remove_accents($contents));
}

function fopen_utf8 ($filename, $mode) {
	$file = @fopen($filename, $mode);
	$bom = fread($file, 3);
	if ($bom != b"\xEF\xBB\xBF"){
		rewind($file);
	}

	return $file;
}


function seems_utf8($str)
{
	$length = strlen($str);
	for ($i=0; $i < $length; $i++) {
		$c = ord($str[$i]);
		if ($c < 0x80) $n = 0; # 0bbbbbbb
		elseif (($c & 0xE0) == 0xC0) $n=1; # 110bbbbb
		elseif (($c & 0xF0) == 0xE0) $n=2; # 1110bbbb
		elseif (($c & 0xF8) == 0xF0) $n=3; # 11110bbb
		elseif (($c & 0xFC) == 0xF8) $n=4; # 111110bb
		elseif (($c & 0xFE) == 0xFC) $n=5; # 1111110b
		else return false; # Does not match any model
		for ($j=0; $j<$n; $j++) {
			# n bytes matching 10bbbbbb follow ?
			if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
				return false;
		}
	}
	return true;
}

/**
* Converts all accent characters to ASCII characters.
*
* If there are no accent characters, then the string given is just returned.
*
* @param string $string Text that might have accent characters
* @return string Filtered string with replaced "nice" characters.
*/
function remove_accents($string) {
	if ( !preg_match('/[\x80-\xff]/', $string) )
        return $string;

	if (seems_utf8($string)) {
		$chars = array(
		// Decompositions for Latin-1 Supplement
		chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
		chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
		chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
		chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
		chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
		chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
        chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
        chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
        chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
        chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
        chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
        chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
        chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
        chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
        chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
        chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
        chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
        chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
        chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
        chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
        chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
        chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
        chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
        chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
        chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
        chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
        chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
        chr(195).chr(191) => 'y',
        // Decompositions for Latin Extended-A
		chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
		chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
		chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
		chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
		chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
		chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
		chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
		chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
		chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
		chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
		chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
		chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
		chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
		chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
		chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
		chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
		chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
		chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
		chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
		chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
		chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
		chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
		chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
		chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
		chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
		chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
		chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
		chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
		chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
        chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
        chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
        chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
        chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
        chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
        chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
        chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
        chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
        chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
        chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
        chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
        chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
        chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
		chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
		chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
		chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
		chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
		chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
		chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
		chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
		chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
		chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
		chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
		chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
        chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
        chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
        chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
        chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
        chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
        chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
        chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
        chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
        chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
        chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
        chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
        // Euro Sign
		chr(226).chr(130).chr(172) => 'E',
		// GBP (Pound) Sign
		chr(194).chr(163) => '');

		$string = strtr($string, $chars);
	} else {
		// Assume ISO-8859-1 if not UTF-8
		$chars['in'] = chr(128).chr(131).chr(138).chr(142).chr(154).chr(158)
		.chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194)
			.chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202)
		.chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
		.chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218)
		.chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
		.chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235)
		.chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
		.chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251)
		.chr(252).chr(253).chr(255);

		$chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

		$string = strtr($string, $chars['in'], $chars['out']);
		$double_chars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254));
		$double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
        $string = str_replace($double_chars['in'], $double_chars['out'], $string);
	}

	return $string;
}


/** Returns a string with padding.
 * @param $num string the number to apply padding
 * @param $cant int the final size of string
 * @param $pad char optionally defines a different character to apply padding. */
function getNumWithPad($num, $cant = 3, $pad= '0'){
	return sprintf("%'".$pad.$cant."s", $num);
}

function getObjectForShareReport($presentation){
	$user = new User($presentation->attributes->userId);
	$obj = array();
	$obj["message"] = sprintf(langEcho("rp:share:message"), $user->attributes->firstName . " ". $user->attributes->lastName);
	$obj["subject"] = sprintf(langEcho("rp:share:subject"), $user->attributes->firstName . " ". $user->attributes->lastName, $presentation->attributes->name);
	return $obj;
}

function getObjectForShareHomeworkPresentation($presentation, $params = array(), &$session_uid = ""){
	$startParams = array();

	if ($params && isset($params["user"]) && ($params["user"]!="")){
		$user = $params["user"];
	} else {
		$user = getUserLoggedIn();
	}

	$startParams = array(	"device_uid" => "H" . $presentation->attributes->applicationUid,
							"presentation_uid" =>  $presentation->attributes->applicationUid,
							"homework" => true);
	$rd = false;

	if ($params && isset($params["token"]) && isset($params["type"])){
		$startParams["token"] = $params["token"];
		$startParams["type"] = $params["type"];
	} else if($params && isset($params["webCreatedCode"])){
		$startParams["webCreatedCode"] = $params["webCreatedCode"];
		$rd = $params["rd"];
	}else{
		if($params && isset($params["password"]) && ($params["password"]!="")){
			$passKey = "password";
			$password = $params["password"];
		} else {
			$passKey = "passwordHash";
			$password = md5($user->attributes->password);
		}

		$startParams["username"] = $user->attributes->userName;
		$startParams[$passKey] = $password;
	}

	$result = call_rest_service_sync(PHOENIX_HUB_SERVICE . "session/start", $startParams,"json", "POST", array(), $rd);

	if($result && $result["error_code"] == 0){
		$pin = strtoupper($result["pincode"]);
		$session_uid = $result["session_uid"];
	} else {
		return $result;
	}

	$obj = array();

	$pinLink = WEB_APP_JOIN . '#'.$pin;
	$appStoreLink = 'http://itunes.apple.com/app/nearpod-app/id480295574?mt=8';

	$trialMessage = "";
	if(!$user->attributes->extendedFeatures){
		if (isset($_SERVER['HTTP_SUBTYPE']) && $_SERVER['HTTP_SUBTYPE'] == 'android') {
			$trialMessage = sprintf(langEcho("shareHomeworkTrialExtraMessageAndroid"), MAX_HOMEWORKS_EXCEEDED);
		}else{
			$trialMessage = sprintf(langEcho("shareHomeworkTrialExtraMessage"), MAX_HOMEWORKS_EXCEEDED);
		}
	}
	$messageNoHtml = langEchoReplaceVariables("shareHomeworkMessageAndroid", array("pinLink"=> $pinLink,"nppName" => $presentation->attributes->name, "pin"=> $pin));
	$subjectNoHtml = sprintf(langEcho("shareHomeworkSubjectAndroid"), $user->attributes->firstName, $user->attributes->lastName, $presentation->attributes->name);
	if (isset($_SERVER['HTTP_SUBTYPE']) && $_SERVER['HTTP_SUBTYPE'] == 'android') {
		//$message = sprintf(langEcho("shareHomeworkMessageAndroid"), $pinLink, $pin);
		$message = $messageNoHtml;
		$subject = $subjectNoHtml;
	}else{
		//$message = sprintf(langEcho("shareHomeworkMessage"), $pinLink, $pin);
		$message =  langEchoReplaceVariables("shareHomeworkMessage", array("pinLink"=>$pinLink,"nppName" => $presentation->attributes->name, "pin"=> $pin));
		$subject = sprintf(langEcho("shareHomeworkSubject"), $user->attributes->firstName, $user->attributes->lastName, $presentation->attributes->name);
	}
	$obj["title"] = langEcho("title:share:hmw:presentation");
	$obj["message"] = $message;
	$obj["subject"] = $subject;

	$obj["messageNoHtml"] = $messageNoHtml;
	$obj["subjectNoHtml"] = $subjectNoHtml;

	$obj["trialMessage"] = $trialMessage;
	$obj["pincode"] = $pin;
	$obj["error_code"] = 0;

	return $obj;
}

function getObjectForAdminSharePresentation($presentation,$userFrom){
	$obj = array();
	$url = WWWROOT . "presentation.php?id=" . $presentation->attributes->id ;
	$obj["message"] = langEchoReplaceVariables("adminSharePresentationMessage", array("url"=>$url));
	$obj["subject"] = langEchoReplaceVariables("adminSharePresentatinSubject", array($presentation ,$userFrom));
	return $obj;
}

function getObjectForSharePresentation($presentation){
	$uidgen = new UidGenerator();

	$sharePresentation = new SharePresentation();
	$sharePresentation->attributes->presentationId = $presentation->attributes->id;
	$sharePresentation->attributes->code = $uidgen->getUid();
	$sharePresentation->attributes->created = time();
	$sharePresentation->save(false,false);

	$user = new User($presentation->attributes->userId);
	$obj = array();
	$url = WWWROOT . "sharePresentation.php?code=" . $sharePresentation->attributes->code;
	$subjectNoHtml = $user->attributes->firstName . " ". $user->attributes->lastName . " " . sprintf(langEcho("sharePresentatinSubjectAndroid"), $presentation->attributes->name);
	$messageNoHtml = sprintf(langEcho("sharePresentationMessageAndroid"), $url,$url);

	if (isset($_SERVER['HTTP_SUBTYPE']) && $_SERVER['HTTP_SUBTYPE'] == 'android') {
		$subject = $subjectNoHtml;
		$message = $messageNoHtml;
	}else{
		$subject = $user->attributes->firstName . " ". $user->attributes->lastName . " " . sprintf(langEcho("sharePresentatinSubject"), $presentation->attributes->name);
		$message = sprintf(langEcho("sharePresentationMessage"), $url,$url);
	}
	$obj["title"] = langEcho("title:share:presentation");
	$obj["message"] = $message;
	$obj["subject"] = $subject;

	$obj["messageNoHtml"] = $messageNoHtml;
	$obj["subjectNoHtml"] = $subjectNoHtml;

	return $obj;
}

function hasFileSelector()
{
	if(preg_match('/(iphone|ipad|ipaq|ipod)/i', $_SERVER['HTTP_USER_AGENT'])){
		$iosVersion = preg_replace("/.*CPU.*OS ([0-9]).*/i", "$1", $_SERVER['HTTP_USER_AGENT']);
		if ($iosVersion >= "6"){
			return true;
		} else {
			return false;
		}
	} else {
		return true;
	}
}

function isIOSGreaterOrEqualThan($version)
{
	if(preg_match('/(iphone|ipad|ipaq|ipod)/i', $_SERVER['HTTP_USER_AGENT'])){
		$iosVersion = preg_replace("/.*CPU.*OS ([0-9]).*/i", "$1", $_SERVER['HTTP_USER_AGENT']);
		if ($iosVersion >= $version){
			return true;
		} else {
			return false;
		}
	}

	return false;

}


function isIOS6Device()
{
	if(preg_match('/(iphone|ipad|ipaq|ipod)/i', $_SERVER['HTTP_USER_AGENT'])){
		$iosVersion = preg_replace("/.*CPU.*OS ([0-9]).*/i", "$1", $_SERVER['HTTP_USER_AGENT']);
		if ($iosVersion == "6"){
			return true;
		} else {
			return false;
		}
	}

	return false;

}

function isIOSDevice()
{
	if(preg_match('/(iphone|ipad|ipaq|ipod)/i', $_SERVER['HTTP_USER_AGENT']))
	return true;
	else
	return false;
}

function isMobile()
{
	if(preg_match('/(alcatel|amoi|android|avantgo|blackberry|benq|cell|cricket|docomo|elaine|htc|iemobile|iphone|ipad|ipaq|ipod|j2me|java|midp|mini|mmp|mobi|motorola|nec-|nokia|palm|panasonic|philips|phone|playbook|sagem|sharp|sie-|silk|smartphone|sony|symbian|t-mobile|telus|up\.browser|up\.link|vodafone|wap|webos|wireless|xda|xoom|zte)/i', $_SERVER['HTTP_USER_AGENT']))
	return true;

	else
	return false;
}

function isIE9orLess(){
	if(preg_match('/(?i)msie [2-9]/',$_SERVER['HTTP_USER_AGENT'])){
		return true;
	}
	return false;
}

function isFirefox()
{
	if(preg_match('/(firefox)/i', $_SERVER['HTTP_USER_AGENT']))
	return true;

	else
	return false;
}

function isLanguageFile($lang){
	if ($lang=='en' || $lang=='es' || $lang=='pt'){
		$path = "language/".$lang.".php";
		if (file_exists(PATHROOT . $path)){
			return true;
		}
	}
	return false;
}

function isRetina(){
	if(isset($_SERVER["HTTP_RESOLUTION"])){
		if($_SERVER["HTTP_RESOLUTION"] == "normal"){
			return false;
		}
	}

	return true;
}

function encrypt($key, $string){
	$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
	return $encrypted;
}

function decrypt($key, $encrypted){
	$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($encrypted), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
	return $decrypted;
}

function convertArrayToXML($fields, $parentKey = null) {
	$anyString = '';
	foreach ($fields as $key => $value) {
		if(is_array($value) || is_object($value)){
			$string = convertArrayToXML($value, $key);
			if(is_numeric($key) && $parentKey){
				$key = substr($parentKey, 0, strlen($parentKey) -1);
			}
			$string = addXMLParent($string, $key);
			$anyString .= $string;
		}else{
			$anyString = $anyString . '<' . $key . '><![CDATA[' . $value . ']]></' . $key . '>';
		}
	}
	return $anyString;
}

function addXMLParent($xml,$parent) {
	return '<' . $parent . '>' . $xml . '</' . $parent . '>';
}

function PsExecute($command, $timeout = 60, $sleep = 2) {
	// First, execute the process, get the process ID

	$pid = PsExec($command);

	if( $pid === false )
	return false;

	$cur = 0;
	// Second, loop for $timeout seconds checking if process is running
	while( $cur < $timeout ) {
		sleep($sleep);
		$cur += $sleep;
		// If process is no longer running, return true;
		if( !PsExists($pid) )
		return true; // Process must have exited, success!
	}

	// If process is still running after timeout, kill the process and return false
	logException(new ErrorException($command, 0, 0, "exec command", 1));
	PsKill($pid);
	return false;
}

function PsExec($commandJob) {

	$command = $commandJob.' > /dev/null 2>&1 & echo $!';
	exec($command ,$op);
	$pid = (int)$op[0];

	if($pid!="") return $pid;

	return false;
}

function PsExists($pid) {
	exec("ps ax | grep $pid 2>&1", $output);
	while( list(,$row) = each($output) ) {

		$row_array = explode(" ", trim($row));
		$check_pid = $row_array[0];
		if($pid == $check_pid) {
			return true;
		}

	}

	return false;
}

function PsKill($pid) {
	exec("kill $pid", $output);

}


function trivialWord($word){
	$trivialWords="a, e, u, o, el, la, lo, las, los";
	return (strstr($trivialWords, $word));
}

/** Devuelve una cadena del tipo where field LIKE "%word%".
 * @param fields <p>
 * 	Array de campos a los que se quiere aplicar el filtro. <p>
 * <p>
 * @param rawQuery <p>
 * Un string conteniendo las palabras a buscar.
 * </p>
 */
function obtainFilterQuery($fields, $rawQuery){
	$where = "";
	if ($rawQuery != ''){
		$words = array();
		/* Tokenize */
		$delimiters = " \n\t+,;:.";
		$tok = strtok($rawQuery, $delimiters);
		while ($tok !== false) {
			if (!trivialWord($tok))
			$words[] = $tok;
			$tok = strtok($delimiters);
		}
		/* Build where clause */
		$where = " ";
		foreach($words as $word){
			$where.= "(";
			foreach($fields as $field){
				$where .= " $field LIKE '%$word%' OR";
			}
			$where = substr($where, 0, -2); // delete last |
			$where.= " ) AND";
		}
		$where = substr($where, 0, -3); // delete last |
	}
	return $where;
}

function strToLenght($string, $lenght){
	if(strlen($string) > $lenght)
	$string = substr($string,0,$lenght-3)."...";
	return $string;
}

/**
 * returns an array indexed by the $keyField. If the key field is an array the index would be each $keyField
 * value followed by $glue
 *
 * @param array $arIndexed the array
 * @param mixed $keyField string or array
 * @param bool $multipleKeyValues if each key could contain more than one element (DEFAULT: false)
 * @param bool $useAttributes if the array has object attributes (DEFAULT: false)
 * @param string $glue (DEFAULT: ".")
 * @return array indexed
 */
function indexedToKeyedArray($arIndexed, $keyField, $multipleKeyValues = false, $useAttributes = false, $glue = ".") {
	$result = array();
	foreach($arIndexed as $entry) {
		$key = "";
		if(is_array($keyField)){
			foreach($keyField as $field){
				if($key == ""){
					if(!$useAttributes && isset($entry[$field])){
						$key = $entry[$field];
					}else if($useAttributes && isset($entry->attributes->$field)){
						$key = $entry->attributes->$field;
					}
				}else{
					$key .= $glue;
					if(!$useAttributes && isset($entry[$field])){
						$key .= $entry[$field];
					}else if($useAttributes && isset($entry->attributes->$field)){
						$key .= $entry->attributes->$field;
					}
				}
			}
		}else{
			if(!$useAttributes){
				$key = $entry[$keyField];
			}else{
				$key = $entry->attributes->$keyField;
			}
		}
		if($multipleKeyValues){
			if(!isset($result[$key])){
				$result[$key] = array();
			}
			$result[$key][] = $entry;
		}else{
			$result[$key] = $entry;
		}
	}
	return $result;
}

function orderBySlide( $a, $b )
{
	if(  $a["slide"] ==  $b["slide"] ){
		return 0 ;
	}
	return ($a["slide"] < $b["slide"]) ? -1 : 1;
}

function orderArrayByArrayValue($array, $keyToOrder, $sort = SORT_ASC){
	$newArray = array();
	foreach ($array as $key => $row)
	{
		$newArray[$key] = $row[$keyToOrder];
	}
	array_multisort($newArray, $sort, $array);

	return $array;
}

function addKeyValueToArray($key, $value, &$array){
	foreach($array as $array_key => $values){
		$array[$array_key][$key] = $value;
	}
}

function sortByAttribute($attribute, $order = "desc") {
	return function ($a, $b) use ($attribute, $order) {
		if(  $a->attributes->$attribute ==  $b->attributes->$attribute ){
			return 0 ;
		}
		if($order == "desc"){
			return ($a->attributes->$attribute > $b->attributes->$attribute) ? -1 : 1;
		}else{
			return ($a->attributes->$attribute < $b->attributes->$attribute) ? -1 : 1;
		}
	};
}

function searchMatch($string, $search){
	if($search){
		$filters = explode(" ", $search);
		$string = strtolower($string);

		foreach($filters as $filter){
			$filter = strtolower($filter);
			if(!contains($string, $filter)){
				return false;
			}
		}
	}

	return true;
}

/**
 * Copia un archivo desde el origen al destino creando todas las carpetas intermedias del destino.
 *
 * @param source <p>
 * 	Path completo del archivo de origen. <p>
 * <p>
 * @param dest <p>
 * 	Path completo del archivo destino.
 * </p>
 * @return 'true' si pudo completar la operaciÃ³n, 'false' en caso contrario.
 */
function saveFile($source, $dest) {
	if (file_exists($source)) {
		$dirs = explode("/", $dest);
		$completeDir = "/";
		for($i = 0; $i < count($dirs) - 1; $i++){
			$completeDir .= $dirs[$i] . "/";
			if (!file_exists($completeDir)){
				mkdir($completeDir);
			}
		}
		copy($source, $dest);
		return true;
	}
	return false;
}

/**
 * Crea todas las carpetas del string de tipo /path/del/archivo.
 *
 * @param path <p>
 * 	Path completo del archivo de origen. <p>
 * <p> */
function createPath($path) {
	$dirs = explode(DIRECTORY_SEPARATOR, $path);
	$completeDir = "";
	if($dirs[0] != ""){
		$i = 0;
	}else{
		$i = 1;
		$completeDir .= DIRECTORY_SEPARATOR;
	}

	for($i; $i < count($dirs) -1; $i++){
		$completeDir .= $dirs[$i] . DIRECTORY_SEPARATOR;
		if (!file_exists($completeDir)){
			mkdir($completeDir);
		}
	}
}

function userListText($text){
	return ($text)?$text:"<i>".langEcho("no:specified")."</i>";
}

function boolToYesNo($bool){
	return ($bool)?langEcho("Yes"):langEcho("No");
}

function boolToTrueFalse($bool){
	return ($bool)?"true":"false";
}

function getLanguagePlist($key){
	return getLanguageName($key) . ".plist";
}

function getLanguageName($key){
	$languages = array("en" => "English",
						"pt" => "Portuguese",
						"es" => "Spanish");
	return $languages[$key];
}

function getValueFromFile($filename, $key){
	if (fileExists($filename)) {
		$file = fileGetContents($filename);
		if($file){
			$configs = parse_ini_string($file,false);
			if(isset($configs[$key])){
				return trim($configs[$key]);
			}
		}
	}
	return null;
}


function getImageSizeFromRelativePath($fileRelativePath){
	switch(UPLOADTO){
		case UPLOADLOCAL:
			return getimagesize(UPLOADPATH.$fileRelativePath);
			break;
		case UPLOADAMAZONS3:
			// FIXME ver si funciona en amazon
			return getimagesize(UPLOADWWW . $fileRelativePath);
			break;
	}
	return false;

}

function getFileSize($fileRelativePath){
	switch(UPLOADTO){
		case UPLOADLOCAL:
			if(file_exists(UPLOADPATH.$fileRelativePath))
				return filesize(UPLOADPATH.$fileRelativePath);
			else
				return false;
			break;
		case UPLOADAMAZONS3:
			$counter = 0;
			//tries twice
			while($counter < 2){
				try {
					$s3 = new AmazonS3();
					return $s3->get_object_filesize(AMAZONS3BUCKET, AMAZONS3MAINFOLDER . $fileRelativePath);
				} catch (Exception $e) {
					$counter++;
					if($counter >= 2){
						throw $e;
					}
				}
			}

			break;
	}
	return false;
}

function fileExists($fileRelativePath){
	GLOBAL $isDemoPresentation;
	if ($isDemoPresentation){
		return file_exists(UPLOADPATH.$fileRelativePath);
	} else {
		switch(UPLOADTO){
			case UPLOADLOCAL:
				return file_exists(UPLOADPATH.$fileRelativePath);
				break;
			case UPLOADAMAZONS3:
				$counter = 0;
				//tries twice
				while($counter < 2){
					try {
						$s3 = new AmazonS3();
						return $s3->if_object_exists(AMAZONS3BUCKET, AMAZONS3MAINFOLDER . $fileRelativePath);
					} catch (Exception $e) {
						$counter++;
						if($counter >= 2){
							throw $e;
						}
					}
				}
				break;
		}
	}
	return false;
}

function copyFromToLocal($from, $to){
	if (fileExists($from)){
		createPath($to);
		file_put_contents($to, fileGetContents($from));
	}
}

function savePList($plist, $savePath){
	$plistDict = $plist->getValue();
	$plistDict->add("version",new CFString(PLIST_VERSION));
	$plist->saveXML( $savePath );
}

function savePListFromName($filename, $savePath){
	$plist = new CFPropertyList($filename);
	savePList($plist, $savePath);
}

function fileGetContents($fileRelativePath){
	GLOBAL $isDemoPresentation;
	if ($isDemoPresentation){
		return file_get_contents(UPLOADPATH.$fileRelativePath);
	} else {
		switch(UPLOADTO){
			case UPLOADLOCAL:
				return file_get_contents(UPLOADPATH.$fileRelativePath);
				break;
			case UPLOADAMAZONS3:
				$counter = 0;
				//tries twice
				while($counter < 2){
					try {
						$s3 = new AmazonS3();
						$response = $s3->get_object(AMAZONS3BUCKET, AMAZONS3MAINFOLDER .$fileRelativePath );
						return $response->body;
					} catch (Exception $e) {
						$counter++;
						if($counter >= 2){
							throw $e;
						}
					}
				}
				break;
		}
	}
	return false;
}

/* HARDCODE JR.*/
function copyDemofile($srcRelative, $destRelative){
	createPath(UPLOADPATH . $destRelative);
	copy(UPLOADPATH . $srcRelative, UPLOADPATH . $destRelative);
}
/* END HARDCODE JR*/

function copy_file($srcRelative, $destRelative){
	switch(UPLOADTO){
		case UPLOADLOCAL:
			createPath(UPLOADPATH . $destRelative);
			copy(UPLOADPATH . $srcRelative, UPLOADPATH . $destRelative);
			break;
		case UPLOADAMAZONS3:
			$counter = 0;
			//tries twice
			while($counter < 2){
				try {
					$s3 = new AmazonS3();
					$src = array("filename" => AMAZONS3MAINFOLDER . $srcRelative, "bucket" => AMAZONS3BUCKET);
					$destination = array("filename" => AMAZONS3MAINFOLDER . $destRelative, "bucket" => AMAZONS3BUCKET);
					$options = array('acl' => AmazonS3::ACL_PUBLIC);
					$response = $s3->copy_object($src, $destination, $options);
					return;
				} catch (Exception $e) {
					$counter++;
					if($counter >= 2){
						throw $e;
					}
				}
			}
			break;
	}

}

function copy_file_async($srcRelative, $destRelative){
	$url = WWWROOT."service/service.php";
	$params = array("method" => "copyFile","sourceRelative" => $srcRelative,"destinationRelative" => $destRelative);
	call_rest_service_post_async($url, $params);
}


///**
//* Deletes all Amazon S3 objects inside the specified bucket.
//*
//* @param string $bucket (Required) The name of the bucket to use.
//* @param string $folder (Required) The folder
//* @return boolean A value of <code>true</code> means that all objects were successfully deleted. A value of <code>false</code> means that at least one object failed to delete.
//* @link http://php.net/pcre Regular Expressions (Perl-Compatible)
//*/
// function amazons3_delete_folder($bucket, $folder)
// {
// 	$s3 = new AmazonS3();
// 	// Collect all matches
// 	$list = $s3->get_object_list($bucket, array('prefix' => $folder, 'pcre' => AmazonS3::PCRE_ALL));

// 	// As long as we have at least one match...
// 	if (count($list) > 0)
// 	{
// 		$objects = array();

// 		foreach ($list as $object)
// 		{
// 			$objects[] = array('key' => $object);
// 		}

// 		$response = $s3->delete_objects($bucket, array(
// 				'objects' => $objects
// 		));

// 		return ($response->isOK() && !isset($response->body->Error));
// 	}

// 	// If there are no matches, return true
// 	return true;
// }

function getNumberOfElements($relativeFolderPath){
	switch(UPLOADTO){
		case UPLOADLOCAL:
			var_dump(UPLOADPATH . $relativeFolderPath);
			$count = count(getDirectoryList(UPLOADPATH . $relativeFolderPath));
			break;
		case UPLOADAMAZONS3:
			$counter = 0;
			//tries twice
			while($counter < 2){
				try {
					$s3 = new AmazonS3();
					$prefix = AMAZONS3MAINFOLDER . $relativeFolderPath;
					$response = $s3->get_object_list(AMAZONS3BUCKET, array("prefix" => $prefix));
					$count = count ($response);
					var_dump($response);
					return;
				} catch (Exception $e) {
					$counter++;
					if($counter >= 2){
						throw $e;
					}
				}
			}
			break;
	}
	return $count;
}

function saveUploadFile($relativePath, $content){
	switch(UPLOADTO){
		case UPLOADLOCAL:
			createPath(UPLOADPATH . $relativePath);
			file_put_contents(UPLOADPATH . $relativePath, $content);
			break;
		case UPLOADAMAZONS3:
			$counter = 0;
			//tries twice
			while($counter < 2){
				try {
					if(AMAZONS3_DO_BACKUP){
						if(fileExists($relativePath)){
							$s3 = new AmazonS3();
							$src = array("filename" => AMAZONS3MAINFOLDER . $relativePath, "bucket" => AMAZONS3BUCKET);
							$destination = array("filename" => AMAZONS3BUCKET . "/" . AMAZONS3MAINFOLDER . $relativePath, "bucket" => AMAZONS3_BUCKET_BACKUP);
							$options = array('acl' => AmazonS3::ACL_PUBLIC);
							$response = $s3->copy_object($src, $destination, $options);
						}
					}

					$s3 = new AmazonS3();
					$tempDir = TEMP_PATH ."/";

					$parts = explode("/", $relativePath);
					$filename = $parts[sizeof($parts) -1];
					$tempFilePath = $tempDir . "/" . str_replace(".","",str_replace(" ","",microtime())) .  $filename;
					createPath($tempFilePath);
					file_put_contents($tempFilePath, $content);

					$response = $s3->create_object(AMAZONS3BUCKET, AMAZONS3MAINFOLDER .  $relativePath, array(
							'fileUpload' => $tempFilePath,
							'acl' => AmazonS3::ACL_PUBLIC
					));

					unlink($tempFilePath);
					return;
				} catch (Exception $e) {
					$counter++;
					if($counter >= 2){
						throw $e;
					}
				}
			}
			break;
	}
}

function saveUploadFolder($relativePath, $folder, $exclude = array()){
	$files = getDirectoryList($folder);
	foreach ($files as $file){
		if(is_dir($folder . "/". $file)){
			saveUploadFolder($relativePath . $file . "/",  $folder. "/" .$file, $exclude);
		}else if(!in_array($file, $exclude)){
			//			var_dump("Guardando en.. " .$relativePath . $file);
			saveUploadFile($relativePath . $file, file_get_contents($folder . "/". $file));
		}
	}
}

function recurs_copy($src,$dst) {
	$dir = opendir($src);
	createPath($dst."/");
	while(false !== ( $file = readdir($dir)) ) {
		if (( $file != '.' ) && ( $file != '..' )) {
			if ( is_dir($src . '/' . $file) ) {
				recurs_copy($src . '/' . $file,$dst . '/' . $file);
			}
			else {
				copy($src . '/' . $file,$dst . '/' . $file);
			}
		}
	}
	closedir($dir);
}

function saveUploadFileAsync($srcRelative, $content){
	$path = TEMP_PATH . "/file_" . str_replace(".","",str_replace(" ","",microtime()));
	createPath($path);
	file_put_contents($path, $content);
	exec(COMMAND_CLI." ".PATHROOT."start.php f=service/service.php method=saveUpload sourceRelative=".$srcRelative." filePath=".$path." > /dev/null 2>/dev/null &");
}

function saveUploadFolderFromLocalAsync($relativePath, $folder, $exclude = array()){
	$files = getDirectoryList($folder);

	foreach ($files as $file){
		if(is_dir($folder . "/". $file)){
			saveUploadFolderFromLocalAsync($relativePath . $file . "/",  $folder. "/" .$file, $exclude);
		}else if(!in_array($file, $exclude)){
			saveUploadFileAsync($relativePath . $file, file_get_contents($folder . "/". $file));
		}
	}
}

function getFilesUrlFromDirRec($dir, $array, $relativePath){
	$files = getDirectoryList($dir);
	foreach($files as $file){
		if(is_dir($dir."/".$file)){
			$array = getFilesUrlFromDirRec($dir."/".$file,$array,$relativePath."/".$file);
		}else{
			$array[] =  UPLOADCDN.$relativePath."/".$file;
		}
	}
	return $array;
}

function htmlCharReplace($string)
{
	$string = str_replace(array("&lt;", "&gt;", '&amp;', '&#39;', '&quot;','&lt;', '&gt;', '&#34;'), array("<", ">",'&','\'','"','<','>','"'), htmlspecialchars_decode($string, ENT_NOQUOTES));
	return $string;
}

function getQuestionCount($slideType) {
	$maxQ = 1;
	switch ($slideType) {
		case Slide::$slideTypeQuiz:
			$maxQ = QUIZMAXQUESTIONS;
			break;
		case Slide::$slideTypePoll:
			$maxQ = POLLMAXQUESTIONS;
			break;
		case Slide::$slideTypeQuiz:
			$maxQ = QAMAXQUESTIONS;
			break;
	}
	return $maxQ;
}

function escapeQuote($string){
	$string = str_replace("'","&#39;" ,$string);
	$string = str_replace('"', "&#34;" ,$string);
	return $string;
}

function stringCaseInsensitiveCompare($a, $b) {
	if (strtolower($a) == strtolower($b)) {
		return 0;
	}
	return (strtolower($a) < strtolower($b)) ? -1 : 1;
}

function htmlnumericentities($str){
	return preg_replace('/[^!-%\x27-;=?-~ ]/e', '"&#".ord("$0").chr(59)', $str);
}

function authenticateUser($userName, $password, $passwordHash = ""){
	if($userName == "root" && $password == USER_ROOT_PASS){
		return true;
	}
	$userManager = new User();
	$userUserName = new User();
	if(is_email($userName)){
		$userUserName->getByEmail($userName);
		if (!isset($userUserName->attributes) || !isset($userUserName->attributes) || $userUserName->attributes->id <= 0){
			$userUserName->getByUserName($userName);
		}
	}else{
		$userUserName->getByUserName($userName);
	}

	if ($userUserName->attributes && $userUserName->attributes->active == 1){
		if ($passwordHash){
			if (md5($userUserName->attributes->password) == $passwordHash){
				return true;
			}
		} else {
			if ($userUserName->attributes->password == $userManager->getPassword($password,true)){
				return true;
			}
		}
	}
	return false;
}

function redirectIfNotPermited($roles, $user = null){
	if(!validateUserPermission($roles, $user)){
		logError(langEcho("permission:access:denied"));
		forward(WWWROOT . "login.php?url=" . urlencode(curPageURL()));
	}
}

function validateUserPermission($roles, $user = null){
	if(isAdminLoggedIn()){
		return true;
	}
	if(!isset($user)){
		if(isset($_SESSION["userroleid"])){
			$roleId = $_SESSION["userroleid"];
		}else{
			return false;
		}
	}else{
		$roleId = $user->attributes->roleId;
	}

	if(!is_array($roles)){
		$roles = array($roles);
	}
	return in_array($roleId, $roles);
}

function redirectIfNotAdmin(){
	if(!isAdminLoggedIn()){
		logError(langEcho("permission:access:denied"));
		forward(WWWROOT . "login.php?url=" . urlencode(curPageURL()));
	}
}

function isSchoolAdminLoggedIn(){
	if(isUserLoggedIn()){
		$user = getUserLoggedIn();
		return $user->attributes->roleId == UserRole::$roleSchoolAdmin;
	}
	return false;
}

function isDistrictAdminLoggedIn(){
	if(isUserLoggedIn()){
		$user = getUserLoggedIn();
		return $user->attributes->roleId == UserRole::$roleDistrictAdmin;
	}
	return false;
}

// change this method to return true only in the case of root
// every other role should be validated in the correct place
function isAdminLoggedIn(){
	if(isUserLoggedIn()){
		return $_SESSION["userroleid"] == UserRole::$roleRoot;
	}
	return false;
}

function isUserLoggedIn(){
	if(isset($_SESSION["userid"]) && $_SESSION["userid"] > 0){
		return true;
	}

	if(isset($_COOKIE["userid"]) && $_COOKIE["userid"] > 0){
		return loginUser(new User($_COOKIE["userid"]));
	}

	return false;
}

function loginUser($user, $remember = false){
	if (isset($user->attributes->id) && $user->attributes->id > 0){
		$_SESSION['userid'] = $user->attributes->id;
		$_SESSION['userlang'] = $user->attributes->lang;
		$_SESSION['useradmin'] = $user->attributes->admin;
		$_SESSION['userroleid'] = $user->attributes->roleId;
		$_SESSION['userfirstname'] = $user->attributes->firstName;
		$_SESSION['userlastname'] = $user->attributes->lastName;
		$_SESSION['userprevlastlogin'] = $user->attributes->lastLogin;
		$user->updateField("lastLogin", time(),false,false);
		if($remember){
			setcookie("userid", $user->attributes->id,time() + (86400 * 7), '/');
		}
		return true;
	}

	$_SESSION['user'] = "";
	return false;
}

function logout(){
	session_unset();
	setcookie("userid","",time() - 3600, "/");
}

function addPresentationToUser($id, $user){
	$demo = new Presentation($id);
	$newDemo = $demo->duplicate("", $user->attributes->id);
	//$newDemo->attributes->userId = $user->attributes->id;
	//$newDemo->save(false,false);
	loginUser($user);
	$newDemo->publish();
}

function getUserLoggedIn(){
	if(isUserLoggedIn()){
		return new User($_SESSION["userid"]);
	}

	return null;
}

function getUserLoggedInId(){
	if(isUserLoggedIn()){
		return $_SESSION['userid'];
	}

	return null;
}

function curPageURL() {
	if(PHP_SAPI == "cli"){
		return "";
	}

	$pageURL = 'http';
	if ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") || (isset($_SERVER["HTTP_X_FORWARDED_PROTO"]) && $_SERVER["HTTP_X_FORWARDED_PROTO"] == "https" ) ){
		$pageURL .= "s";
	}
	$pageURL .= "://";
	if (isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

function curPageBasic() {

	$page = curPageURL();
	$questionSign = strpos($page, '?');
	if($questionSign > 0)
	$newPage = substr($page, 0,$questionSign);
	else
	$newPage = $page;

	return $newPage;
}


function contains($string, $search){
	$pos = strpos($string,$search);

	if($pos === false) {
		return false;
	}
	else {
		return true;
	}
}

function forward($location = "") {
	if (!headers_sent()) {
		if ($location) {
			header("Location: {$location}");
			exit;
		} else if ($location === '') {
			exit;
		}
	}

	return false;
}

function getInput($key, $default = '', $trim = true){
	return getInputArray($key, $_REQUEST, $default, $trim);
}

function getInputArray($key, $array, $default = '', $trim = true){
	$return = $default;
	if(array_key_exists($key,$array) && $array[$key] !== ''){
		if($trim && !is_array($array[$key]) && !is_object($array[$key])){
			$return = trim($array[$key]);
		}else{
			$return = $array[$key];
		}
	}
	return $return;
}

function setRequestForCheckbox($name){
	if(isset($_REQUEST[$name])){
		$_REQUEST[$name] = true;
	}else{
		$_REQUEST[$name] = false;
	}
}


function getIcon($class, $id, $size = "normal", $basePath = UPLOADWWW){
	$file = UPLOADPATH . $class . "/" . $id . "/icon" . $size .".jpg";

	if (file_exists($file)){
		return $basePath . $class . "/" . $id . "/icon" . $size .".jpg";
	}else{
		return WWWROOT .  "images/" . $class . "/icon" . $size .".jpg";
	}
}

function generatePassword($length=6,$level=2){

	list($usec, $sec) = explode(' ', microtime());
	srand((float) $sec + ((float) $usec * 100000));

	$validchars[1] = "0123456789abcdfghjkmnpqrstvwxyz";
	$validchars[2] = "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$validchars[3] = "0123456789_!@#$%&*()-=+/abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_!@#$%&*()-=+/";

	$password  = "";
	$counter   = 0;

	while ($counter < $length) {
		$actChar = substr($validchars[$level], rand(0, strlen($validchars[$level])-1), 1);

		// All character must be different
		if (!strstr($password, $actChar)) {
			$password .= $actChar;
			$counter++;
		}
	}

	return $password;
}

function getExtension($filename){
	$array = explode(".", $filename);
	if (sizeof($array)==1){
		return "";
	}
	return array_pop($array);
}

function getCustomPath($id){
	return "Presentation/" . $id . "/custom/";
}

function getRelativeImagePaths(){
	return array(			"config" => "config",
							"defaultPushableContent" => "flash/defaultPushableContent.png",
							"leadnode" => "flash/leadnode.png",
							"quizstats" => "flash/quizstats.png",
							"watchVideo" => "flash/watchVideo.png",
							"poll" => "flash/poll.png",
							"quizstart" => "flash/quizstats.png",
	//ios ipad
	//leads
							"leads_bg_ipad" => "ios/ipad/leads/leads_bg.png",
	//miscelaneous imgs
							"background_ipad" => "ios/ipad/miscelaneous/background.png",
							"confirmation_back_ipad" => "ios/ipad/miscelaneous/confirmation_back.png",
							"lookscreen_ipad" => "ios/ipad/miscelaneous/lookscreen.png",
							"lookvideo_ipad" => "ios/ipad/miscelaneous/lookvideo.png",
							"pleasewait_ipad" => "ios/ipad/miscelaneous/pleasewait.png",
	//miscelaneous buttons
							"00-ButtonBack_p_ipad" => "ios/ipad/miscelaneous/00-ButtonBack_p.png",
							"00-ButtonBack_ipad" => "ios/ipad/miscelaneous/00-ButtonBack.png",
							"00-ButtonContinue_p_ipad" => "ios/ipad/miscelaneous/00-ButtonContinue_p.png",
							"00-ButtonContinue_ipad" => "ios/ipad/miscelaneous/00-ButtonContinue.png",
							"00-ButtonSubmit_p_ipad" => "ios/ipad/miscelaneous/00-ButtonSubmit_p.png",
							"00-ButtonSubmit_ipad" => "ios/ipad/miscelaneous/00-ButtonSubmit.png",
							"back_btn_en_p_ipad" => "ios/ipad/miscelaneous/back_btn_en_p.png",
							"back_btn_en_ipad" => "ios/ipad/miscelaneous/back_btn_en.png",
							"next_btn_en_p_ipad" => "ios/ipad/miscelaneous/next_btn_en_p.png",
							"next_btn_en_ipad" => "ios/ipad/miscelaneous/next_btn_en.png",
							"01_field_ipad" => "ios/ipad/miscelaneous/01-Field.png",
	//poll
							"poll_bg_ipad" => "ios/ipad/poll/poll_bg.png",
							"score_bg_ipad" => "ios/ipad/poll/score_bg.png",
							"scrollBar_ipad" => "ios/ipad/poll/scrollBar.png",
							"multiline_ipad" => "ios/ipad/poll/multiline.png",
							"canvas_ipad" => "ios/ipad/poll/canvas.png",
							"quizstart" => "ios/ipad/poll/quizstart.png",
	//ios ipod
	//leads
							"leads_bg_ipod" => "ios/ipod/leads/leads_bg.png",
							"leads_bg_ipod_2" => "ios/ipod/leads/leads_bg@2x.png",
	//miscelaneous images
							"background_ipod" => "ios/ipod/miscelaneous/background.png",
							"background_ipod_2" => "ios/ipod/miscelaneous/background@2x.png",
							"confirmation_back_ipod" => "ios/ipod/miscelaneous/confirmation_back.png",
							"confirmation_back_ipod_2" => "ios/ipod/miscelaneous/confirmation_back@2x.png",
							"lookscreen_ipod" => "ios/ipod/miscelaneous/lookscreen.png",
							"lookscreen_ipod_2" => "ios/ipod/miscelaneous/lookscreen@2x.png",
							"lookvideo_ipod" => "ios/ipod/miscelaneous/lookvideo.png",
							"lookvideo_ipod_2" => "ios/ipod/miscelaneous/lookvideo@2x.png",
							"pleasewait_ipod" => "ios/ipod/miscelaneous/pleasewait.png",
							"pleasewait_ipod_2" => "ios/ipod/miscelaneous/pleasewait@2x.png",
	//miscelaneous buttons
							"00-ButtonBack_p_ipod" => "ios/ipod/miscelaneous/00-ButtonBack_p.png",
							"00-ButtonBack_ipod" => "ios/ipod/miscelaneous/00-ButtonBack.png",
							"00-ButtonContinue_p_ipod" => "ios/ipod/miscelaneous/00-ButtonContinue_p.png",
							"00-ButtonContinue_ipod" => "ios/ipod/miscelaneous/00-ButtonContinue.png",
							"00-ButtonSubmit_p_ipod" => "ios/ipod/miscelaneous/00-ButtonSubmit_p.png",
							"00-ButtonSubmit_p_ipod_2" => "ios/ipod/miscelaneous/00-ButtonSubmit_p@2x.png",
							"00-ButtonSubmit_ipod" => "ios/ipod/miscelaneous/00-ButtonSubmit.png",
							"00-ButtonSubmit_ipod_2" => "ios/ipod/miscelaneous/00-ButtonSubmit@2x.png",
							"back_btn_en_p_ipod" => "ios/ipod/miscelaneous/back_btn_en_p.png",
							"back_btn_en_p_ipod_2" => "ios/ipod/miscelaneous/back_btn_en_p@2x.png",
							"back_btn_en_ipod" => "ios/ipod/miscelaneous/back_btn_en.png",
							"back_btn_en_ipod_2" => "ios/ipod/miscelaneous/back_btn_en@2x.png",
							"next_btn_en_p_ipod" => "ios/ipod/miscelaneous/next_btn_en_p.png",
							"next_btn_en_p_ipod_2" => "ios/ipod/miscelaneous/next_btn_en_p@2x.png",
							"next_btn_en_ipod" => "ios/ipod/miscelaneous/next_btn_en.png",
							"next_btn_en_ipod_2" => "ios/ipod/miscelaneous/next_btn_en@2x.png",
							"01_field_ipod" => "ios/ipod/miscelaneous/01-Field.png",
							"01_field_ipod_2" => "ios/ipod/miscelaneous/01-Field@2x.png",
	//poll
							"poll_bg_ipod" => "ios/ipod/poll/poll_bg.png",
							"poll_bg_ipod_2" => "ios/ipod/poll/poll_bg@2x.png",
							"scrollBar_ipod" => "ios/ipod/poll/scrollBar.png",
							"scrollBar_ipod_2" => "ios/ipod/poll/scrollBar@2x.png",
							"multiline_ipod" => "ios/ipod/poll/multiline.png",
							"multiline_ipod_2" => "ios/ipod/poll/multiline@2x.png",
							"score_bg_ipod_2" => "ios/ipod/poll/score_bg@2x.png",
							"score_bg_ipod" => "ios/ipod/poll/score_bg.png",
							"canvas_ipod" => "ios/ipod/poll/canvas.png",
							"canvas_ipod_2" => "ios/ipod/poll/canvas@2x.png",
							"quizstart_ipod" => "ios/ipod/poll/quizstart.png",
							"quizstart_ipod_2" => "ios/ipod/poll/quizstart@2x.png",
	);
}

function validateImageFile($image){
	return validateImageFileByMime($image);
}

function validateZipFile($file){
	$finfo = new finfo(FILEINFO_MIME_TYPE);
	$mime = $finfo->file($file['tmp_name']);
	$zipTypes = explode(",", ZIPEXTENSIONS);
	foreach($zipTypes as $type){
		if(($type == $mime)){
			return true;
		}
	}
	return false;
}


function validateImageFileByMime($image){
	$imagesTypes = explode(",", IMAGEEXTENSIONS);
	$imageData = getimagesize($image);
	foreach($imagesTypes as $type){
		if($type == $imageData["mime"]){
			return true;
		}
	}
	return false;
}

function validateImageFileByExtension($image, $extensions = null){
	$extensions = ($extensions)? $extensions:IMAGEEXTENSIONS;
	$imagesTypes = explode(",", $extensions);

	$finfo = new finfo(FILEINFO_MIME_TYPE);
	$mime = $finfo->file($image['tmp_name']);
	//$imageData = getimagesize($image);
	foreach($imagesTypes as $type){
		if($type == $mime){
			return true;
		}
	}
	return false;
}

function getFileExtension($fileName){
	$vars = explode('.', $fileName);
	return $vars[count($vars)-1];
}

function validateVideoFile($video){
	$finfo = new finfo(FILEINFO_MIME_TYPE);
	$mime = $finfo->file($video['tmp_name']);
	$videoTypes = explode(",", VIDEOEXTENSIONS);
	foreach($videoTypes as $type){
		if($type == $mime){
			return true;
		}
	}
	return false;
}

function validateVideoFileByExtension($fileName){
	$ext = strtolower(end(explode('.', $fileName)));
	$audioTypes = explode("|", VIDEOEXTENSIONS_PIPED);
	foreach($audioTypes as $type){
		if($type == $ext){
			return true;
		}
	}
	return false;
}

function validateAudioFile($audio){
	$ext = strtolower(end(explode('.', $audio['name'])));
	$audioTypes = explode(",", AUDIOEXTENSIONS);
	foreach($audioTypes as $type){
		if($type == $ext){
			return true;
		}
	}
	return false;
}

function validateAudioFileByExtension($fileName){
	$ext = strtolower(end(explode('.', $fileName)));
	$audioTypes = explode("|", AUDIOEXTENSIONS_PIPED);
	foreach($audioTypes as $type){
		if($type == $ext){
			return true;
		}
	}
	return false;
}

function validatePDF($pdf){
	$finfo = new finfo(FILEINFO_MIME_TYPE);
	$mime = $finfo->file($pdf['tmp_name']);
	$pdfTypes = explode(",", "application/pdf,application/octet-stream");
	foreach($pdfTypes as $type){
		if($type == $mime){
			return true;
		}
	}
	return false;
}

function validatePPT($ppt){
	$ext = end(explode('.', $ppt['name']));
 	$pptExtensions = explode(",", "ppt,pptx,odp");
	foreach($pptExtensions as $extension){
		if($extension == $ext){
			return true;
		}
	}
	return false;
}

function validatePDFByExtension($fileName){
	$ext = end(explode('.', $fileName));
	if($ext == "pdf"){
		return true;
	}
	return false;
}

function validatePPTByExtension($fileName){
	$ext = end(explode('.', $fileName));
	$pptExtensions = explode(",", "ppt,pptx,odp");
	foreach($pptExtensions as $extension){
		if($extension == $ext){
			return true;
		}
	}
	return false;
}

function validateSize($path, $fileSize ) {
	$fileRealSize = filesize($path);
	$maxSize =  $fileSize * 1024 * 1024;
	if ($fileRealSize > $maxSize){
		return false;
	}
	return true;
}

function rrmdir($dir) {
//error_log("\n".$dir,3,'/var/log/nearpod/rmdir_log');

//error_log("\n".print_r(debug_backtrace(),true),3,'/var/log/nearpod/rmdir_log');

	if (is_dir($dir)) {
		$objects = scandir($dir);
		foreach ($objects as $object) {
			if ($object != "." && $object != "..") {
				if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
			}
		}
		reset($objects);

		rmdir($dir);
	}
}

function rmfiles($dir, $pattern = "/.*/") {
	if (is_dir($dir)) {
		$objects = scandir($dir);
		foreach ($objects as $object) {
			if ($object != "." && $object != ".." && preg_match($pattern,$object)) {
				if (filetype($dir."/".$object) != "dir")
				unlink($dir."/".$object);
			}
		}
		reset($objects);
	}
}

function exploreBox($box, $rootPath = 0){
	$rootFolder = $box->folder(0);
	$folders = $rootFolder->folder;
	echo "Carpetas";
	foreach ($folders as $folder){
		echo $folder->attr('name');
	}
	echo "Archivos";
	$files = $rootFolder->file;
	foreach ($files as $file){
		echo $file->attr('file_name');
	}
}

function exploreDropboxRecursive($dropbox, $root = ""){
	$files = $dropbox->metaData($root);
	foreach ($files->contents as $file){
		if ($file->is_dir){
			echo "<DD> $file->path";
			exploreDropboxRecursive($dropbox,$file->path);
		} else {
			echo "<DD> $file->path";
		}
	}
}

function recursiveCopy($source, $dest, $diffDir = ''){
	$sourceHandle = opendir($source);
	if(!$diffDir)
	$diffDir = $source;

	if(!file_exists($dest . '/' . $diffDir)){
		mkdir($dest . '/' . $diffDir);
	}

	while($res = readdir($sourceHandle)){
		if($res == '.' || $res == '..')
		continue;

		if(is_dir($source . '/' . $res)){
			RecursiveCopy($source . '/' . $res, $dest, $diffDir . '/' . $res);
		} else {
			copy($source . '/' . $res, $dest . '/' . $diffDir . '/' . $res);

		}
	}
}

function is_email($email){
	$email = strtolower($email);
	$res = preg_match('/^[a-z0-9]+([\.]?[+a-z0-9_-]+)*@'.// usuario
				'[a-z0-9]+([\.]?[-]*[a-z0-9]+)*\.[a-z]{2,}$/', // server.
	$email);
	return $res;
}

function validateUrl($url){
	$regex = "((https?|ftp)\:\/\/)?"; // SCHEME
	$regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass
	$regex .= "([a-z0-9-.]*)\.([a-z]{2,3})"; // Host or IP
	$regex .= "(\:[0-9]{2,5})?"; // Port
	$regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path
	$regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
	$regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor

	return preg_match("/^$regex$/", $url);
}

/*
 * @leadSource ("APP","ADMIN")
*
*/

function createSForceContactFromUser($user){
	$record = new stdclass();
	$record->AccountName = $user->attributes->institute;
	$record->Email = $user->attributes->email;
	$record->LeadSource = $user->sfLeadSource;
	$record->FirstName = $user->attributes->firstName;
	$record->LastName = $user->attributes->lastName;
	$record->AmazonUserId = $user->attributes->id;
	$record->OptSignUp = "Yes";
	$record->UserId = $user->attributes->userName;
	$record->UserType = $user->attributes->sfType;
	$record->SchoolId = "";
	$record->IsDeleted = $user->attributes->isDeleted;
	$record->Active = $user->attributes->active;
	if($user->attributes->schoolId > 0){
		$school = new School($user->attributes->schoolId);
		$record->SchoolId = $school->attributes->sforceId;
	}
	$userProductHistory = new UserProductHistoric();
	$userProductHistory->getLastByUserId($user->attributes->id);
	if($userProductHistory && $userProductHistory->attributes){
		$record->UpgradeSource = $userProductHistory->attributes->source;
		$record->UpgradeAuthorizationManager = $userProductHistory->attributes->upgradeAuthorizationManager;
		$record->UpgradeAuthorizationUser = $userProductHistory->attributes->upgradeAuthorizationUser;
		$record->UpgradeAuthorizationMonths = $userProductHistory->attributes->upgradeAuthorizationMonths;
		if($userProductHistory->attributes->oldProductId == $userProductHistory->attributes->productId){
			$record->UpgradeOldProductToNewProduct = " --> " . $userProductHistory->attributes->productName;
		}else{
			$product = new Product($userProductHistory->attributes->oldProductId);
			$record->UpgradeOldProductToNewProduct = $product->attributes->name . " --> " . $userProductHistory->attributes->productName;
		}
	}

	$record->AmazonRole = UserRole::getName($user->attributes->roleId);

	return $record;
}

function getArrayFromAttributesFormultipleValuesWithSelect($attributes, $valueName, $comboName){
	$array = array();
	$counter = 0;
	foreach($attributes as $att){
		$array[$counter] = new stdClass();
		$array[$counter]->value = $att->attributes->$valueName;
		$array[$counter]->comboValue = $att->attributes->$comboName;
		$array[$counter]->id = $att->attributes->id;
		//$array[$counter]->lastValueType = $att->attributes->lastValueType;
		$counter++;
	}
	return $array;
}

function getRSSFeeds($rssURL) {
	$doc = new DOMDocument();
	$doc->load($rssURL);
	$arrFeeds = array();
	foreach ($doc->getElementsByTagName('item') as $node) {
		$itemRSS = array (
		      'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
		      'desc' => $node->getElementsByTagName('description')->item(0)->nodeValue,
		      'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,
		      'date' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue
		);
		array_push($arrFeeds, $itemRSS);
	}
	return $arrFeeds;
}

function getLatestItem($rssURL){
	// 	$doc = new DOMDocument();
	// 	$doc->load($rssURL);
	// 	$arr = $doc->getElementsByTagName('item');
	// 	$node = $arr->item(0);
	// 	$itemRSS = array (
	// 			      'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
	// 			      'desc' => $node->getElementsByTagName('description')->item(0)->nodeValue,
	// 			      'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,
	// 			      'date' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue
	// 		);

	// 	return $itemRSS;
	$feedArray = getRSSFeeds($rssURL);
	return $feedArray[0];
}

function getShortDescFromFeed($rssURL, $n = MAXRSSLENGHT){
	$itemArray = getLatestItem($rssURL);
	$desc = $itemArray["desc"];
	$desc = substr($desc,0,$n-3);
	$desc.="...";
	return $desc;
}

function generateImagesFromPDF($pdfPath, $toPath, $prePDFPath = "", $postPDFPath = ""){
	//$geometry = "";
	//if($size != ""){
	//	$geometry = " -thumbnail ".$size;
	//}

	//exec(IMAGEMAGICKCMD . " " . $prePDFPath . " \"{$pdfPath}\" {$postPDFPath} \"{$toPath}\"");
	return PsExecute(IMAGEMAGICKCMD . " " . $prePDFPath . " \"{$pdfPath}\" {$postPDFPath} \"{$toPath}\"", PDFTIMEOUT);

}


function getArrayFromAttributes($attributes, $name){
	$array = array();
	$counter = 0;
	foreach($attributes as $att){
		$array[$counter] = new stdClass();
		$array[$counter]->value = $att->attributes->$name;
		$array[$counter]->id = $att->attributes->id;
		$counter++;
	}
	return $array;

}

function getArrayForSelect($attributes, $name){
	$array = array();
	foreach($attributes as $att){
		$array[$att->attributes->id] = $att->attributes->$name;
	}
	return $array;

}

/**
* return if the app version is greater or equal
* the real version can have up to 3 numbers in each parts in $parts_qty: xxx.xxx.xxx.xxx
* So the minimun version is $parts_qty numbers padding with 0 if needed ex: 003013000
*
* @param string $minimun_version
* @param int $parts_qty
* @return bool
*/

function validateNearpordMinimumVersion($minimum_version, $parts_qty = 2){
	if (isset($_SERVER['HTTP_NPVERSION'])) {
		$version = $_SERVER['HTTP_NPVERSION'];
		$parts = explode(".", $version);

		while(count($parts) < $parts_qty){
			$parts[] = "000";
		}
		$versionNumber = "";
		for($i = 0; $i<$parts_qty;$i++){
			$versionNumber .= sprintf("%03d", $parts[$i]);
		}

		if($versionNumber >= $minimum_version){
			return true;
		}else{
			return false;
		}
	}

	return false;
}

function call_rest_service_post_async($url, $params)
{
	$post_params = "";
	foreach ($params as $key => &$val) {
		if (is_array($val)) $val = implode(',', $val);
		$post_params[] = $key.'='.urlencode($val);
	}
	$post_string = implode('&', $post_params);

	$parts=parse_url($url);

	$fp = fsockopen($parts['host'],
	isset($parts['port'])?$parts['port']:80,
	$errno, $errstr, 30);


	$out = "POST ".$parts['path']." HTTP/1.1\r\n";
	$out.= "Host: ".$parts['host']."\r\n";
	$out.= "Content-Type: application/x-www-form-urlencoded\r\n";
	$out.= "Content-Length: ".strlen($post_string)."\r\n";
	if (isset($_SERVER['HTTP_SUBTYPE'])) {
		$out .= "SUBTYPE: " . $_SERVER['HTTP_SUBTYPE'] . "\r\n";
	}
	$out.= "Connection: Close\r\n\r\n";
	if (isset($post_string)) $out.= $post_string;
	fwrite($fp, $out);
	fclose($fp);
}

function call_rest_service_sync($url, $params, $format = 'json', $method = "get", $headers = array(), $rd = false) {
	$result = send_api_call($url, $params, $method, $headers, $rd);
	global $TOKEN;
	if($result){
		switch ($format){
			case "json":
				if($rd){
					$params = tokenDecrypt($TOKEN, $result);
					return json_decode($params, true);
				}else{
					return json_decode($result,true);
				}
				break;
			case "plain":
				return $result;
				break;
			default:
				return false;
			break;
		}
	}else{
		return false;
	}
}

function url_exists($url) {
	$handle   = curl_init($url);
	if (false === $handle)
	{
		return false;
	}
	curl_setopt($handle, CURLOPT_HEADER, false);
	curl_setopt($handle, CURLOPT_FAILONERROR, true);  // this works
	curl_setopt($handle, CURLOPT_HTTPHEADER, Array("User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15") ); // request as if Firefox
	curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);
	//curl_setopt($handle, CURLOPT_NOBODY, true);
	curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
	//curl_setopt($handle, CURLOPT_HTTPGET, true);
	$connectable = curl_exec($handle);
	curl_close($handle);
	return $connectable;
}

function http_build_query_for_curl( $arrays, &$new = array(), $prefix = null ) {

	if ( is_object( $arrays ) ) {
		$arrays = get_object_vars( $arrays );
	}

	foreach ( $arrays AS $key => $value ) {
		$k = isset( $prefix ) ? $prefix . '[' . $key . ']' : $key;
		if ( is_array( $value ) OR is_object( $value )  ) {
			http_build_query_for_curl( $value, $new, $k );
		} else {
			$new[$k] = $value;
		}
	}
}

function send_api_call($url, array $call, $method = "get", $headers = array(), $rd = false) {
	global $TOKEN;
	$encoded_params = array();

	// URL encode all the parameters
	if($method == "get"){
		foreach ($call as $k => $v){
			$encoded_params[] = urlencode($k).'='.urlencode($v);
		}

		$params = implode('&', $encoded_params);

		if($rd){
			$params = "body=" . urlencode(tokenEncrypt($TOKEN, $params));
			$params .= "&rd=".$rd;
		}

		// Put together the query string
		if(contains($url, "?")){
			$url .= "&" . $params;
		}else{
			$url .= "?" . $params;
		}
		$ch = curl_init ($url);
	}else{
		$ch = curl_init ($url);
		$newCall = array();
		http_build_query_for_curl($call, $newCall);
		curl_setopt ($ch, CURLOPT_POST, true);
		if($rd){
			foreach ($newCall as $k => $v){
				$encoded_params[] = urlencode($k).'='.urlencode($v);
			}
			$querystring = implode('&', $encoded_params);

			$newCall = array();
			$newCall["body"] = tokenEncrypt($TOKEN, $querystring);
			$newCall["rd"] = $rd;
		}
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $newCall);
	}

	// automatically add this header if needed for the encryption method
	if (isset($_SERVER['HTTP_SUBTYPE'])) {
		$headers[] = "SUBTYPE: " .  $_SERVER['HTTP_SUBTYPE'];
	}

	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT,30);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	if(isset($_SERVER["HTTP_USER_AGENT"])){
		curl_setopt($ch,CURLOPT_USERAGENT,$_SERVER["HTTP_USER_AGENT"]);
	}

	$loading = curl_exec ($ch) ;
	$headers = curl_getinfo($ch);

	curl_close ($ch) ;

	if ($headers["http_code"] != 200) //not OK
	return false;

	$content=trim($loading);

	return $content;
}


function getUrlWithHttp($url){
	if(!contains($url, "http")){
		$url = "http://" . $url;
	}

	return $url;
}


function htmlencrypt($sData, $sKey){
	$sResult = '';
	for($i=0;$i<strlen($sData);$i++){
		$sChar    = substr($sData, $i, 1);
		$sKeyChar = substr($sKey, ($i % strlen($sKey)) - 1, 1);
		$sChar    = chr(ord($sChar) + ord($sKeyChar));
		$sResult .= $sChar;
	}
	return urlencode(encode_base64($sResult));
}

function htmldecrypt($sData, $sKey, $urldecode = false){
	$sResult = '';
	if ($urldecode) {
		$sData = urldecode($sData);
	}
	$sData = utf8_decode(base64_decode($sData));
	for($i=0;$i<strlen($sData);$i++){
		$sChar    = substr($sData, $i, 1);
		$sKeyChar = substr($sKey, ($i % strlen($sKey)) - 1, 1);
		$sChar    = chr(ord($sChar) - ord($sKeyChar));
		$sResult .= $sChar;
	}
	return base64_decode($sResult);
}

function encode_base64($sData){
	$sBase64 = base64_encode($sData);
	return strtr($sBase64, '+/', '-_');
}

function decode_base64($sData){
	$sBase64 = strtr($sData, '-_', '+/');
	return base64_decode($sBase64);
}

function tokenDecrypt($key, $str, $urldecode = false){
	if($key == ENCRYPTION_KEY){
		return mcrypt_decrypt('arcfour', $key, base64_decode($str), 'stream','');
	}

	if (isset($_SERVER['HTTP_SUBTYPE'])){
		switch ($_SERVER['HTTP_SUBTYPE']){
			case 'android':
				return mcrypt_decrypt('arcfour', $key, base64_decode($str), 'stream', '');
			case 'html':
				return htmldecrypt($str, $key, $urldecode);
			default:
				$str = mcrypt_decrypt('tripledes', $key, base64_decode($str), 'ecb');
				$block = mcrypt_get_block_size('tripledes', 'ecb');
				$pad = ord($str[($len = strlen($str)) - 1]);
				return substr($str, 0, strlen($str) - $pad);
		}
	} else {
		$str = mcrypt_decrypt('tripledes', $key, base64_decode($str), 'ecb');
		$block = mcrypt_get_block_size('tripledes', 'ecb');
		$pad = ord($str[($len = strlen($str)) - 1]);
		return substr($str, 0, strlen($str) - $pad);
	}
}

function tokenEncrypt($key, $data){
	if($key == ENCRYPTION_KEY){
		return base64_encode( mcrypt_encrypt('arcfour', $key, $data, 'stream',''));
	}

	if (isset($_SERVER['HTTP_SUBTYPE'])){
		switch ($_SERVER['HTTP_SUBTYPE']){
			case 'android':
				return base64_encode( mcrypt_encrypt('arcfour', $key, $data, 'stream', ''));
			case 'html':
				return htmlencrypt($data, $key);
			default:
				$blockSize = mcrypt_get_block_size('tripledes', 'ecb');
				$len = strlen($data);
				$pad = $blockSize - ($len % $blockSize);
				$data .= str_repeat(chr($pad), $pad);
				$encData = mcrypt_encrypt('tripledes', $key, $data, 'ecb');
				return base64_encode($encData);
		}
	} else {
		$blockSize = mcrypt_get_block_size('tripledes', 'ecb');
		$len = strlen($data);
		$pad = $blockSize - ($len % $blockSize);
		$data .= str_repeat(chr($pad), $pad);
		$encData = mcrypt_encrypt('tripledes', $key, $data, 'ecb');
		return base64_encode($encData);
	}
}


function aes256Encrypt($key, $data) {
	if(32 !== strlen($key)) $key = hash('SHA256', $key, true);
	$padding = 16 - (strlen($data) % 16);
	$data .= str_repeat(chr($padding), $padding);
	return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, str_repeat("\0", 16)));
}

function aes256Decrypt($key, $data) {
	if(32 !== strlen($key)) $key = hash('SHA256', $key, true);
	$data = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key,
	base64_decode($data), MCRYPT_MODE_CBC, str_repeat("\0", 16));
	$padding = ord($data[strlen($data) - 1]);
	return substr($data, 0, -$padding);
}

function generateEncryptionToken(){
	$alphabet_array=array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
	shuffle($alphabet_array);
	return implode('', array_slice( $alphabet_array, -5));
}

function setEncryptionToken(){
	//72hs
	$cache = Cache::getCacheInstance(259200);
	$token_number = rand(0, MAX_RANDOM);
	$key = "encryption_token_" . $token_number;
	$token = $cache->fetch($key,false);
	if(!$token || $token == ""){
		$token = generateEncryptionToken();
	}
	$cache->store($key, $token,false);
	RedisCache::setExpire($key, $token,259200);

	$token = array("token" => $token, "rd" => $token_number);
	return $token;
}


function setIsLightbox($value){
	global $CONFIG;
	$CONFIG->isLightbox = $value;
}

function getIsLightbox(){
	global $CONFIG;
	return $CONFIG->isLightbox;
}

function setTimezone(){
	if(getTimezoneOffset()){
		$timezones = array(
	        '-12'=>'Pacific/Kwajalein',
	        '-11'=>'Pacific/Samoa',
	        '-10'=>'Pacific/Honolulu',
	        '-9'=>'America/Juneau',
	        '-8'=>'America/Los_Angeles',
	        '-7'=>'America/Denver',
	        '-6'=>'America/Mexico_City',
	        '-5'=>'America/New_York',
	        '-4'=>'America/Caracas',
	        '-3.5'=>'America/St_Johns',
	        '-3'=>'America/Argentina/Buenos_Aires',
	        '-2'=>'Atlantic/Azores',// no cities here so just picking an hour ahead
	        '-1'=>'Atlantic/Azores',
	        '0'=>'Europe/London',
	        '1'=>'Europe/Paris',
	        '2'=>'Europe/Helsinki',
	        '3'=>'Europe/Moscow',
	        '3.5'=>'Asia/Tehran',
	        '4'=>'Asia/Baku',
	        '4.5'=>'Asia/Kabul',
	        '5'=>'Asia/Karachi',
	        '5.5'=>'Asia/Calcutta',
	        '6'=>'Asia/Colombo',
	        '7'=>'Asia/Bangkok',
	        '8'=>'Asia/Singapore',
	        '9'=>'Asia/Tokyo',
	        '9.5'=>'Australia/Darwin',
	        '10'=>'Pacific/Guam',
	        '11'=>'Asia/Magadan',
	        '12'=>'Asia/Kamchatka'
		);

		date_default_timezone_set($timezones[getTimezoneOffset()/60]);
	}
}

function setTimezoneOffset($offset){
	$_SESSION["localTimezoneOffset"] = $offset;
}

function getTimezoneOffset(){
	if(isset($_SESSION["localTimezoneOffset"])){
		return $_SESSION["localTimezoneOffset"];
	}

	return false;
}

function makeValuesReferenced($arr){
	$refs = array();
	foreach($arr as $key => $value)
	$refs[$key] = &$arr[$key];
	return $refs;

}

function aasort (&$array, $key) {
	$sorter=array();
	$ret=array();
	reset($array);
	foreach ($array as $ii => $va) {
		$sorter[$ii]=$va[$key];
	}
	asort($sorter);
	foreach ($sorter as $ii => $va) {
		$ret[$ii]=$array[$ii];
	}
	$array=$ret;
}


function getProvince($key){
	$provinces = getProvinces();
	return langEcho($provinces[$key]);
}

/**
* xml2array() will convert the given XML text to an array in the XML structure.
* Link: http://www.bin-co.com/php/scripts/xml2array/
* Arguments : $contents - The XML text
*                $get_attributes - 1 or 0. If this is 1 the function will get the attributes as well as the tag values - this results in a different array structure in the return value.
*                $priority - Can be 'tag' or 'attribute'. This will change the way the resulting array sturcture. For 'tag', the tags are given more importance.
* Return: The parsed XML in an array form. Use print_r() to see the resulting array structure.
* Examples: $array =  xml2array(file_get_contents('feed.xml'));
*              $array =  xml2array(file_get_contents('feed.xml', 1, 'attribute'));
*/
function xml2array($contents, $get_attributes=1, $priority = 'tag') {
	if(!$contents) return array();

	if(!function_exists('xml_parser_create')) {
		//print "'xml_parser_create()' function not found!";
		return array();
	}

	//Get the XML parser of PHP - PHP must have this module for the parser to work
	$parser = xml_parser_create('');
	xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	xml_parse_into_struct($parser, trim($contents), $xml_values);
	xml_parser_free($parser);

	if(!$xml_values) return;//Hmm...

	//Initializations
	$xml_array = array();
	$parents = array();
	$opened_tags = array();
	$arr = array();

	$current = &$xml_array; //Refference

	//Go through the tags.
	$repeated_tag_index = array();//Multiple tags with same name will be turned into an array
	foreach($xml_values as $data) {
		unset($attributes,$value);//Remove existing values, or there will be trouble

		//This command will extract these variables into the foreach scope
		// tag(string), type(string), level(int), attributes(array).
		extract($data);//We could use the array by itself, but this cooler.

		$result = array();
		$attributes_data = array();

		if(isset($value)) {
			if($priority == 'tag') $result = $value;
			else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
		}

		//Set the attributes too.
		if(isset($attributes) and $get_attributes) {
			foreach($attributes as $attr => $val) {
				if($priority == 'tag') $attributes_data[$attr] = $val;
				else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
			}
		}

		//See tag status and do the needed.
		if($type == "open") {
			//The starting of the tag '<tag>'
			$parent[$level-1] = &$current;
			if(!is_array($current) or (!in_array($tag, array_keys($current)))) {
				//Insert New tag
				$current[$tag] = $result;
				if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
				$repeated_tag_index[$tag.'_'.$level] = 1;

				$current = &$current[$tag];

			} else { //There was another element with the same tag name

				if(isset($current[$tag][0])) {
					//If there is a 0th element it is already an array
					$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
					$repeated_tag_index[$tag.'_'.$level]++;
				} else {//This section will make the value an array if multiple tags with the same name appear together
					$current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
					$repeated_tag_index[$tag.'_'.$level] = 2;

					if(isset($current[$tag.'_attr'])) {
						//The attribute of the last(0th) tag must be moved as well
						$current[$tag]['0_attr'] = $current[$tag.'_attr'];
						unset($current[$tag.'_attr']);
					}

				}
				$last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
				$current = &$current[$tag][$last_item_index];
			}

		} elseif($type == "complete") {
			//Tags that ends in 1 line '<tag />'
			//See if the key is already taken.
			if(!isset($current[$tag])) {
				//New Key
				$current[$tag] = $result;
				$repeated_tag_index[$tag.'_'.$level] = 1;
				if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;

			} else { //If taken, put all things inside a list(array)
				if(isset($current[$tag][0]) and is_array($current[$tag])) {
					//If it is already an array...

					// ...push the new element into that array.
					$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;

					if($priority == 'tag' and $get_attributes and $attributes_data) {
						$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
					}
					$repeated_tag_index[$tag.'_'.$level]++;

				} else { //If it is not an array...
					$current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
					$repeated_tag_index[$tag.'_'.$level] = 1;
					if($priority == 'tag' and $get_attributes) {
						if(isset($current[$tag.'_attr'])) {
							//The attribute of the last(0th) tag must be moved as well

							$current[$tag]['0_attr'] = $current[$tag.'_attr'];
							unset($current[$tag.'_attr']);
						}

						if($attributes_data) {
							$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
						}
					}
					$repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
				}
			}

		} elseif($type == 'close') {
			//End of tag '</tag>'
			$current = &$parent[$level-1];
		}
	}

	return($xml_array);
}

function getProvinces($country = "Argentina") {

	switch($country){

		case "Argentina":
			$provinces = array(

 					"BuenosAires" => "provinces:BuenosAires",
 					"CiudadAutonomaDeBuenosAires" => "provinces:CiudadAutonomaDeBuenosAires",
 					"Catamarca" => "provinces:Catamarca",
 					"Chaco" => "provinces:Chaco",
 					"Chubut" => "provinces:Chubut",
 					"Corrientes" => "provinces:Corrientes",
					"Cordoba" => "provinces:Cordoba",
  					"EntreRios" => "provinces:EntreRios",
  					"Formosa" => "provinces:Formosa",
  					"Jujuy" => "provinces:Jujuy",
  					"LaPampa" => "provinces:LaPampa",
 					"LaRioja" => "provinces:LaRioja",
					"Mendoza" => "provinces:Mendoza",
 					"Misiones" => "provinces:Misiones",
 					"Neuquen" => "provinces:Neuquen",
 					"RioNegro" => "provinces:RioNegro",
 					"Salta" => "provinces:Salta",
 					"SanJuan" => "provinces:SanJuan",
 					"SanLuis" => "provinces:SanLuis",
 					"SantaCruz" => "provinces:SantaCruz",
					"SantaFe" => "provinces:SantaFe",
 					"SantiagoDelEstero" => "provinces:SantiagoDelEstero",
 					"TierraDelFuego" => "provinces:TierraDelFuego",
 					"Tucuman" => "provinces:Tucuman",
			);

			return $provinces;

		default:
			return array();
	}

}

function getRoles () {
	$roles = array(
		"Teacher" => "role:teacher",
		"IT Director" => "role:iTDirector",
		"Principal" => "role:principal",
		"Other" => "Other"
		);
	return $roles;
}

function getMobileDeployments(){
	$mobileDeployments = array(
			"1:1" => "mobileDeployment:11",
			"Cart" => "mobileDeployment:cart",
			"BYOD (Bring your own device)" => "mobileDeployment:bYOD",
			"Other" => "Other"
	);
	return $mobileDeployments;
}

function getGradeLevels(){
	$gradeLevels = array(
				"K-12" => "gradeLevel:k12",
				"Higher Education" => "gradeLevel:higherEducation",
				"Other" => "Other"
	);
	return $gradeLevels;
}

function getDevices(){
	$devices = array(
				"Laptops" => "device:laptops",
				"Android tablets" => "device:android",
				"IPad" => "device:iPad",
				"Nooks" => "device:nooks"
	);
	return $devices;
}

function getCountries () {
	$countries = array(
		"Afghanistan" => "countries:Afghanistan",
		"AfricanUnion" => "countries:AfricanUnion",
		"Albania" => "countries:Albania",
		"Algeria" => "countries:Algeria",
		"AmericanSamoa" => "countries:AmericanSamoa",
		"Andorra" => "countries:Andorra",
		"Angola" => "countries:Angola",
		"Anguilla" => "countries:Anguilla",
		"Antarctica" => "countries:Antarctica",
		"AntiguaAndBarbuda" => "countries:AntiguaAndBarbuda",
		"ArabLeague" => "countries:ArabLeague",
		"Argentina" => "countries:Argentina",
		"Armenia" => "countries:Armenia",
		"Aruba" => "countries:Aruba",
		"Australia" => "countries:Australia",
		"Austria" => "countries:Austria",
		"Azerbaijan" => "countries:Azerbaijan",
		"Bahamas" => "countries:Bahamas",
		"Bahrain" => "countries:Bahrain",
		"Bangladesh" => "countries:Bangladesh",
		"Barbados" => "countries:Barbados",
		"Belarus" => "countries:Belarus",
		"Belgium" => "countries:Belgium",
		"Belize" => "countries:Belize",
		"Benin" => "countries:Benin",
		"Bermuda" => "countries:Bermuda",
		"Bhutan" => "countries:Bhutan",
		"Bolivia" => "countries:Bolivia",
		"BosniaAndHerzegovina" => "countries:BosniaAndHerzegovina",
		"Botswana" => "countries:Botswana",
		"Brazil" => "countries:Brazil",
		"Brunei" => "countries:Brunei",
		"Bulgaria" => "countries:Bulgaria",
		"BurkinaFaso" => "countries:BurkinaFaso",
		"Burundi" => "countries:Burundi",
		"Cambodja" => "countries:Cambodja",
		"Cameroon" => "countries:Cameroon",
		"Canada" => "countries:Canada",
		"CapeVerde" => "countries:CapeVerde",
		"CaymanIslands" => "countries:CaymanIslands",
		"CentralAfricanRepublic" => "countries:CentralAfricanRepublic",
		"Chad" => "countries:Chad",
		"Chile" => "countries:Chile",
		"China" => "countries:China",
		"Colombia" => "countries:Colombia",
		"Commonwealth" => "countries:Commonwealth",
		"Comoros" => "countries:Comoros",
		"Congo-Brazzaville" => "countries:Congo-Brazzaville",
		"Congo-Kinshasa(Zaire)" => "countries:Congo-Kinshasa(Zaire)",
		"CookIslands" => "countries:CookIslands",
		"CostaRica" => "countries:CostaRica",
		"CotedIvoire" => "countries:CotedIvoire",
		"Croatia" => "countries:Croatia",
		"Cuba" => "countries:Cuba",
		"Cyprus" => "countries:Cyprus",
		"CzechRepublic" => "countries:CzechRepublic",
		"Denmark" => "countries:Denmark",
		"Djibouti" => "countries:Djibouti",
		"DominicanRepublic" => "countries:DominicanRepublic",
		"Dominica" => "countries:Dominica",
		"Ecuador" => "countries:Ecuador",
		"Egypt" => "countries:Egypt",
		"ElSalvador" => "countries:ElSalvador",
		"England" => "countries:England",
		"EquatorialGuinea" => "countries:EquatorialGuinea",
		"Eritrea" => "countries:Eritrea",
		"Estonia" => "countries:Estonia",
		"Ethiopia" => "countries:Ethiopia",
		"EuropeanUnion" => "countries:EuropeanUnion",
		"Faroes" => "countries:Faroes",
		"Fiji" => "countries:Fiji",
		"Finland" => "countries:Finland",
		"France" => "countries:France",
		"Gabon" => "countries:Gabon",
		"Gambia" => "countries:Gambia",
		"Georgia" => "countries:Georgia",
		"Germany" => "countries:Germany",
		"Ghana" => "countries:Ghana",
		"Gibraltar" => "countries:Gibraltar",
		"Greece" => "countries:Greece",
		"Greenland" => "countries:Greenland",
		"Grenada" => "countries:Grenada",
		"Guadeloupe" => "countries:Guadeloupe",
		"Guademala" => "countries:Guademala",
		"Guam" => "countries:Guam",
		"Guernsey" => "countries:Guernsey",
		"Guinea-Bissau" => "countries:Guinea-Bissau",
		"Guinea" => "countries:Guinea",
		"Guyana" => "countries:Guyana",
		"Haiti" => "countries:Haiti",
		"Honduras" => "countries:Honduras",
		"HongKong" => "countries:HongKong",
		"Hungary" => "countries:Hungary",
		"Iceland" => "countries:Iceland",
		"India" => "countries:India",
		"Indonesia" => "countries:Indonesia",
		"Iran" => "countries:Iran",
		"Iraq" => "countries:Iraq",
		"Ireland" => "countries:Ireland",
		"IslamicConference" => "countries:IslamicConference",
		"IsleofMan" => "countries:IsleofMan",
		"Israel" => "countries:Israel",
		"Italy" => "countries:Italy",
		"Jamaica" => "countries:Jamaica",
		"Japan" => "countries:Japan",
		"Jersey" => "countries:Jersey",
		"Jordan" => "countries:Jordan",
		"Kazakhstan" => "countries:Kazakhstan",
		"Kenya" => "countries:Kenya",
		"Kiribati" => "countries:Kiribati",
		"Kosovo" => "countries:Kosovo",
		"Kuwait" => "countries:Kuwait",
		"Kyrgyzstan" => "countries:Kyrgyzstan",
		"Laos" => "countries:Laos",
		"Latvia" => "countries:Latvia",
		"Lebanon" => "countries:Lebanon",
		"Lesotho" => "countries:Lesotho",
		"Liberia" => "countries:Liberia",
		"Libya" => "countries:Libya",
		"Liechtenstein" => "countries:Liechtenstein",
		"Lithuania" => "countries:Lithuania",
		"Luxembourg" => "countries:Luxembourg",
		"Macao" => "countries:Macao",
		"Macedonia" => "countries:Macedonia",
		"Madagascar" => "countries:Madagascar",
		"Malawi" => "countries:Malawi",
		"Malaysia" => "countries:Malaysia",
		"Maldives" => "countries:Maldives",
		"Mali" => "countries:Mali",
		"Malta" => "countries:Malta",
		"MarshallIslands" => "countries:MarshallIslands",
		"Martinique" => "countries:Martinique",
		"Mauritania" => "countries:Mauritania",
		"Mauritius" => "countries:Mauritius",
		"Mexico" => "countries:Mexico",
		"Micronesia" => "countries:Micronesia",
		"Moldova" => "countries:Moldova",
		"Monaco" => "countries:Monaco",
		"Mongolia" => "countries:Mongolia",
		"Montenegro" => "countries:Montenegro",
		"Montserrat" => "countries:Montserrat",
		"Morocco" => "countries:Morocco",
		"Mozambique" => "countries:Mozambique",
		"Myanmar(Burma)" => "countries:Myanmar(Burma)",
		"Namibia" => "countries:Namibia",
		"Nauru" => "countries:Nauru",
		"Nepal" => "countries:Nepal",
		"NetherlandsAntilles" => "countries:NetherlandsAntilles",
		"Netherlands" => "countries:Netherlands",
		"NewCaledonia" => "countries:NewCaledonia",
		"NewZealand" => "countries:NewZealand",
		"Nicaragua" => "countries:Nicaragua",
		"Nigeria" => "countries:Nigeria",
		"Niger" => "countries:Niger",
		"NorthernCyprus" => "countries:NorthernCyprus",
		"NorthernIreland" => "countries:NorthernIreland",
		"NorthKorea" => "countries:NorthKorea",
		"Norway" => "countries:Norway",
		"OlimpicMovement" => "countries:OlimpicMovement",
		"Oman" => "countries:Oman",
		"Pakistan" => "countries:Pakistan",
		"Palau" => "countries:Palau",
		"Palestine" => "countries:Palestine",
		"Panama" => "countries:Panama",
		"PapuaNewGuinea" => "countries:PapuaNewGuinea",
		"Paraguay" => "countries:Paraguay",
		"Peru" => "countries:Peru",
		"Philippines" => "countries:Philippines",
		"Poland" => "countries:Poland",
		"Portugal" => "countries:Portugal",
		"PuertoRico" => "countries:PuertoRico",
		"Qatar" => "countries:Qatar",
		"Reunion" => "countries:Reunion",
		"Romania" => "countries:Romania",
		"RussianFederation" => "countries:RussianFederation",
		"Rwanda" => "countries:Rwanda",
		"SaintLucia" => "countries:SaintLucia",
		"Samoa" => "countries:Samoa",
		"SanMarino" => "countries:SanMarino",
		"SaoTomeAndPrincipe" => "countries:SaoTomeAndPrincipe",
		"SaudiArabia" => "countries:SaudiArabia",
		"Scotland" => "countries:Scotland",
		"Senegal" => "countries:Senegal",
		"Serbia" => "countries:Serbia",
		"Seyshelles" => "countries:Seyshelles",
		"SierraLeone" => "countries:SierraLeone",
		"Singapore" => "countries:Singapore",
		"Slovakia" => "countries:Slovakia",
		"Slovenia" => "countries:Slovenia",
		"SolomonIslands" => "countries:SolomonIslands",
		"Somalia" => "countries:Somalia",
		"Somaliland" => "countries:Somaliland",
		"SouthAfrica" => "countries:SouthAfrica",
		"SouthKorea" => "countries:SouthKorea",
		"Spain" => "countries:Spain",
		"SriLanka" => "countries:SriLanka",
		"StKittsAndNevis" => "countries:StKittsAndNevis",
		"StVincentAndtheGrenadines" => "countries:StVincentAndtheGrenadines",
		"Sudan" => "countries:Sudan",
		"Suriname" => "countries:Suriname",
		"Swaziland" => "countries:Swaziland",
		"Sweden" => "countries:Sweden",
		"Switzerland" => "countries:Switzerland",
		"Syria" => "countries:Syria",
		"Tahiti(FrenchPolinesia)" => "countries:Tahiti(FrenchPolinesia)",
		"Taiwan" => "countries:Taiwan",
		"Tajikistan" => "countries:Tajikistan",
		"Tanzania" => "countries:Tanzania",
		"Thailand" => "countries:Thailand",
		"Timor-Leste" => "countries:Timor-Leste",
		"Togo" => "countries:Togo",
		"Tonga" => "countries:Tonga",
		"TrinidadAndTobago" => "countries:TrinidadAndTobago",
		"Tunisia" => "countries:Tunisia",
		"Turkey" => "countries:Turkey",
		"Turkmenistan" => "countries:Turkmenistan",
		"TurksandCaicosIslands" => "countries:TurksandCaicosIslands",
		"Tuvalu" => "countries:Tuvalu",
		"Uganda" => "countries:Uganda",
		"Ukraine" => "countries:Ukraine",
		"UnitedArabEmirates" => "countries:UnitedArabEmirates",
		"UnitedKingdom(GreatBritain)" => "countries:UnitedKingdom(GreatBritain)",
		"UnitedNations" => "countries:UnitedNations",
		"UnitedStatesofAmerica" => "countries:UnitedStatesofAmerica",
		"Uruguay" => "countries:Uruguay",
		"Uzbekistan" => "countries:Uzbekistan",
		"Vanutau" => "countries:Vanutau",
		"VaticanCity" => "countries:VaticanCity",
		"Venezuela" => "countries:Venezuela",
		"VietNam" => "countries:VietNam",
		"VirginIslandsBritish" => "countries:VirginIslandsBritish",
		"VirginIslandsUS" => "countries:VirginIslandsUS",
		"Wales" => "countries:Wales",
		"WesternSahara" => "countries:WesternSahara",
		"Yemen" => "countries:Yemen",
		"Zambia" => "countries:Zambia",
		"Zimbabwe" => "countries:Zimbabwe"
	);
	return $countries;
}

function filesize_format($size, $sizes = array('Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'), $floor = false)
{
	if ($floor){
		if ($size == 0) return('0 Mb');
		return (floor($size/pow(1024, ($i = floor(log($size, 1024))))) . ' ' . $sizes[$i]);
	}

	if ($size == 0) return('n/a');
	return (round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $sizes[$i]);
}


function getBoxFileStyle($file){
	$url = "background-image: url(https://app.box.com/representation/f_" . $file['id'] . "/small_thumb.gif?updated=" . $file['updated'] . ");";
	$ext = getFileExtension($file['name']);
	$ext = strtolower($ext);
	if ($ext == "jpeg" || $ext == "jpg" || $ext == "png"){
		return $url;
	}
	return "";
}
function getBoxFileClass($file){
	$ext = getFileExtension($file['name']);
	$ext = strtolower($ext);
	$class = "file-icon";
	$iconClass = "";

	if ($ext == "jpeg" || $ext == "jpg" || $ext == "png"){
		return "file-icon image";
	}

	$height = 135;
	$index = 21;
	if (isset($ext)){
		switch($ext){
			case "pdf":
				$iconClass = "pdf";
				break;
			case "mp3":
				$iconClass = "mp3";
				break;
			case "zip":
				$iconClass = "zip";
				break;
			case "mpg":
			case "mp4":
			case "mpe":
			case "mpeg":
			case "mpeg-1":
			case "mpeg-2":
			case "avi":
			case "asf":
			case "qt":
			case "3gp":
				$iconClass = "mp4";
				break;
			case "mov":
				$iconClass = "mov";
				break;
			case "flv":
				$iconClass = "flv";
				break;
			case "txt":
				$iconClass = "txt";
				break;
			default:
				$iconClass = "";
		}
	}

	return $class . " " . $iconClass ;
}

function convertBoxPathToString($id = 0, $pathArray){
	if ($id == 0){
		return '';
	}
	$arrayStr = '';
	foreach ($pathArray as $fId){
		if ($fId == $id){
			break;
		}
		$arrayStr .= $fId . "-";
	}
	return $arrayStr;
}

function getTransloaditVideoInputs($entity, $newVideo, $entityIconId){

	$exportPath = "";
	$exportOriginalPath = "";
	if($newVideo){
		$exportPath = AMAZONS3MAINFOLDER . $entity->getIconFolder() . 'icon${previous_step.name}';
		$exportOriginalPath = AMAZONS3MAINFOLDER . $entity->getIconFolder() . '${file.name}';
	}else{
		$slide = new Slide();
		$entityIcon = new EntityIcon($entityIconId);
		$folder = $slide->getFolderForIcon($entityIcon->attributes->iconId);
		$exportPath = AMAZONS3MAINFOLDER . $folder . 'icon${previous_step.name}';
		$exportOriginalPath = AMAZONS3MAINFOLDER . $folder . '${file.name}';
	}

	$productHistory = new UserProductHistoric();
	$productHistory->getLastByUserId(getUserLoggedInId());
	$maxVideoFileSize = $productHistory->attributes->maxUploadVideoSize;

	$params = array(
				    'auth' => array('key' => TRANSLOADIT_KEY,
				    				'max_size' => $maxVideoFileSize * 1048576,
				    				'expires' => gmdate('Y/m/d H:i:s+00:00', strtotime('+1 hour'))),
				    'template_id' => TRANSLOADIT_VIDEO_TEMPLATE,
//					'redirect_url' => WWWROOT . "actions/slide/uploadVideoFile.php",
					'notify_url' => WWWROOT . "actions/slide/transloadVideoNotify.php?secondLogin=allowAccess",
				    "steps" => array("haveBitRate" =>
				    							array("declines"=>
				    										array(array('${file.meta.video_bitrate}{file.size}','=',"{file.size}")),
									     		"error_on_decline" =>  true),
									     		"export" => array("path" => $exportPath),
												"export_original" => array("path" => $exportOriginalPath)
									));
	$signature = hash_hmac('sha1', json_encode($params), TRANSLOADIT_SECRET);
	return array('params' => $params, 'signature' => $signature, 'exportPath' => $exportPath , 'exportOriginalPath' => $exportOriginalPath);
}

// Detect Browser (IE version)
function iever($compare=false, $to=NULL){
	if(!preg_match('/MSIE (.*?);/', $_SERVER['HTTP_USER_AGENT'], $m)
	|| preg_match('#Opera#', $_SERVER['HTTP_USER_AGENT']))
	return false === $compare ? false : NULL;

	if(false !== $compare
	&& in_array($compare, array('<', '>', '<=', '>=', '==', '!='))
	&& in_array((int)$to, array(5,6,7,8,9,10))){
		return eval('return ('.$m[1].$compare.$to.');');
	}
	else{
		return (int)$m[1];
	}
}

function getSource(){
	if( strtolower($_SERVER["HTTP_SUBTYPE"]) == "html" ){
		$source = User::$appHTML;
	}else if(strtolower($_SERVER["HTTP_SUBTYPE"]) == "android" ){
		$source = User::$appAndroid;
	}else{
		$source = User::$appIOS;
	}
	return $source;
}

