<?php

function cutStringImagick($imagickImage, $draw, $string, $cellWidth, $excess = '', $step = 100){
	// check it by big step because of max nested function levels.
	$lenght = strlen($string);
	// this is because the GetStringWidht is very slow for big strings
	$metrics = $imagickImage->queryFontMetrics($draw, $string);
	if($metrics["textWidth"] >= $cellWidth-1){
		if($lenght > $step){
			$subString = substr($string, 0, $lenght-$step);
			$metrics = $imagickImage->queryFontMetrics($draw, $subString);
			if($metrics["textWidth"] >= $cellWidth-1){
				$excess = substr($string, $lenght-$step, $lenght).$excess;
				return cutStringImagick($imagickImage, $draw, $subString, $cellWidth, $excess, $step);
			}else{
				$subString = substr($string, 0, $lenght-1);
				$excess = substr($string, $lenght-1, $lenght).$excess;
				return cutStringImagick($imagickImage, $draw, $subString, $cellWidth, $excess, round($step / 2));
			}
		}
		$subString = substr($string, 0, $lenght-1);
		$excess = substr($string, $lenght-1, $lenght).$excess;
		return cutStringImagick($imagickImage, $draw, $subString, $cellWidth, $excess, round($step / 2));
	}else{
		return array("mainCharacters" => $string, "exceededCharacters" =>$excess);
	}
}

function getTextRowsImagick($imagickImage, $draw, $text, $maxWidth)
{
	$firstWords = explode(" ", $text);
	$words = array();
	foreach($firstWords as $word){
		$secondWords = explode("\r\n", $word);
		$addEnter = false;
		foreach($secondWords as $secondWord){
			if($addEnter){
				$words[] = PHP_EOL;
			}
			if($secondWord != ""){
				$result = cutStringImagick($imagickImage,$draw, $secondWord, $maxWidth);
				$words[] = $result["mainCharacters"];
				while($result["exceededCharacters"] != ""){
					$result = cutStringImagick($imagickImage,$draw, $result["exceededCharacters"], $maxWidth);
					$words[] = $result["mainCharacters"];
				}
			}
			$addEnter = true;

		}
	}

	$lines = array();
	$i=0;
	while ($i < count($words))
	{
		//as long as there are words

		$line = "";
		do
		{//append words to line until the fit in size
			if($line != "" && $words[$i] != PHP_EOL) {
				$line .= " ";
			}
			elseif($words[$i] == PHP_EOL){
				$i++;
				break;
			}
			$line .= $words[$i];
			$i++;


			if(($i) == count($words)){
				break;//last word -> break
			}

			//messure size of line + next word
			$linePreview = $line." ".$words[$i];
			$metrics = $imagickImage->queryFontMetrics($draw, $linePreview);
			if($metrics["textWidth"] > $maxWidth && $words[$i] == PHP_EOL){
				$i++;
			}
			//echo $line."($i)".$metrics["textWidth"].":".$maxWidth."<br>";

		}while($metrics["textWidth"] <= $maxWidth);

		//echo "<hr>".$line."<br>";
		$lines[] = $line;
	}

	//var_export($lines);
	return $lines;
}

function merge_image_Imagick($image, $imageToMerge){
	$img1 = new Imagick( $image );
	$img2 = new Imagick( $imageToMerge );

	$img1->compositeImage( $img2, Imagick::COMPOSITE_DEFAULT, 0, 0 );

	return $img1;
}

function write_image_text_Imagick($image, $text, $font = 'Helvetica-Bold', $fontSize = 26, $gravity = Imagick::GRAVITY_SOUTH, $x = 0, $y = 30, $xRight = 0, $color = "black", $textBackground = null){
	$img1 = new Imagick( $image );

	$draw = new ImagickDraw();

	//should be HelveticaLTStd-BlkCond
	$draw->setFont($font);
	$draw->setFontSize( $fontSize );
	$draw->setfillcolor(new ImagickPixel($color));
	$draw->setGravity($gravity);

	$lines = getTextRowsImagick($img1,$draw, $text, $img1->getimagewidth() - $x - $xRight);

	$string = implode(PHP_EOL, $lines);

	if($textBackground){
		$backDraw = new ImagickDraw();
		$backDraw->setfillcolor(new ImagickPixel($textBackground));
		$metrics = $img1->queryFontMetrics($draw, "H");
		$textHeight = $metrics["textHeight"] * 0.92;
		$fullWidth = $img1->getimagewidth();
		$finalY = $y;
		foreach($lines as $line){
			$finalY+=$textHeight;
		}
		$backDraw->rectangle(0, $y - 20, $fullWidth, $finalY);
		$img1->drawimage($backDraw);
	}

	$img1->annotateImage($draw, $x, $y, 0, $string);

	return $img1;
}

function get_resized_image_from_existing_file_Imagick($input_name, $maxwidth, $maxheight, $square = FALSE, $x1 = 0, $y1 = 0, $x2 = 0, $y2 = 0, $upscale = true, $fixedSize = false, $background = 'black', $extension = DEFAULTIMAGEEXTENSION) {
	try{

		$image = new Imagick($input_name);

		// get the parameters for resizing the image
		$options = array(
				'maxwidth' => $maxwidth,
				'maxheight' => $maxheight,
				'square' => $square,
				'upscale' => $upscale,
				'x1' => $x1,
				'y1' => $y1,
				'x2' => $x2,
				'y2' => $y2,
		);
		$params = get_image_resize_parameters($image->getimagewidth(), $image->getimageheight(), $options);
		if ($params == FALSE) {
			return FALSE;
		}

		if($extension == ".png"){

			$image->setImageFormat("png");
			$image->setImageCompression(Imagick::COMPRESSION_ZIP);

		}else{
			if($extension == ".jpg"){
				$image->setImageFormat("jpg");
				$image->setImageCompression(Imagick::COMPRESSION_JPEG);
			}
		}

		$image->setImageCompressionQuality(IMAGECOMPRESIONQUALITY);
		$image->stripImage();
		$image->resizeImage($params['newwidth'],$params['newheight'],Imagick::FILTER_LANCZOS,0);

		/*$image->setImageBackgroundColor("#000000");
		$centerx = (($maxwidth - $params['newwidth']) / 2) * -1;
		$centery = (($maxheight - $params['newheight']) / 2) * -1;
		$image->extentimage($maxwidth,$maxheight, $centerx,$centery);
*/

		if($fixedSize){
			$fixedImage = new Imagick();
			$fixedImage->newImage($maxwidth, $maxheight, new ImagickPixel($background), $image->getimageformat());
			$x = 0;
			$y = 0;
			if($params['newwidth'] < $maxwidth){
				$x = ($maxwidth - $params['newwidth']) / 2;
			}else if($params['newheight'] < $maxheight){
				$y = ($maxheight - $params['newheight']) / 2;
			}
			$fixedImage->compositeImage($image, Imagick::COMPOSITE_DEFAULT, $x, $y);
			return $fixedImage;
		}

		return $image;
	}catch(Exception $ex){
		logException($ex);
		return false;
	}
}

function merge_image($image, $imageToMerge){
	if (IMAGECONVERSOR == IMAGECONVERSOR_IMAGICK) {

		$result = merge_image_Imagick($image, $imageToMerge);

	} else if (IMAGECONVERSOR == IMAGECONVERSOR_GD) {

		return false;
	} else {
		return FALSE;
	}

	return $result;
}

function write_image_text($image, $text, $font = 'Helvetica-Bold', $fontSize = 26, $gravity = Imagick::GRAVITY_SOUTH, $x = 0, $y = 30, $xRight = 0, $color = "black", $textBackground = null){
	if (IMAGECONVERSOR == IMAGECONVERSOR_IMAGICK) {
		$result = write_image_text_Imagick($image, $text,$font,$fontSize,$gravity, $x, $y, $xRight, $color, $textBackground);
	} else if (IMAGECONVERSOR == IMAGECONVERSOR_GD) {
		return false;
	} else {
		return FALSE;
	}

	return $result;
}

function get_resized_image_from_existing_file($input_name, $maxwidth, $maxheight, $square = FALSE, $x1 = 0, $y1 = 0, $x2 = 0, $y2 = 0, $upscale = true, $fixedSize = false, $background = 'black', $extension = DEFAULTIMAGEEXTENSION) {

	if (IMAGECONVERSOR == IMAGECONVERSOR_IMAGICK) {

		$result = get_resized_image_from_existing_file_Imagick($input_name, $maxwidth, $maxheight, $square, $x1, $y1, $x2, $y2, $upscale,$fixedSize, $background, $extension);

	} else if (IMAGECONVERSOR == IMAGECONVERSOR_GD) {

		$result = get_resized_image_from_existing_file_GD($input_name, $maxwidth, $maxheight, $square, $x1, $y1, $x2, $y2, $upscale);
	} else {
		return FALSE;
	}

	return $result;
}

function get_resized_image_from_existing_file_GD($input_name, $maxwidth, $maxheight, $square = FALSE, $x1 = 0, $y1 = 0, $x2 = 0, $y2 = 0, $upscale = true) {
	// Get the size information from the image
	$imgsizearray = getimagesize($input_name);
	if ($imgsizearray == FALSE) {
		return FALSE;
	}

	$width = $imgsizearray[0];
	$height = $imgsizearray[1];

	$accepted_formats = array(
		'image/jpeg' => 'jpeg',
		'image/pjpeg' => 'jpeg',
		'image/png' => 'png',
		'image/x-png' => 'png',
		'image/gif' => 'gif'
	);

	// make sure the function is available
	$load_function = "imagecreatefrom" . $accepted_formats[$imgsizearray['mime']];

	if (!is_callable($load_function)) {
		return FALSE;
	}

	// get the parameters for resizing the image
	$options = array(
		'maxwidth' => $maxwidth,
		'maxheight' => $maxheight,
		'square' => $square,
		'upscale' => $upscale,
		'x1' => $x1,
		'y1' => $y1,
		'x2' => $x2,
		'y2' => $y2,
	);
	$params = get_image_resize_parameters($width, $height, $options);
	if ($params == FALSE) {
		return FALSE;
	}

	// load original image
	$original_image = $load_function($input_name);
	if (!$original_image) {
		return FALSE;
	}

	// allocate the new image
	$new_image = imagecreatetruecolor($params['newwidth'], $params['newheight']);
	if (!$new_image) {
		return FALSE;
	}
	imagesavealpha($new_image, true);
	$trans_colour = imagecolorallocatealpha($new_image,255,255,255,127);
	//$trans_colour = imagecolorallocate($new_image, 255, 255, 255);
	imagefill($new_image, 0, 0, $trans_colour);

	$rtn_code = imagecopyresampled(	$new_image,
									$original_image,
									0,
									0,
									$params['xoffset'],
									$params['yoffset'],
									$params['newwidth'],
									$params['newheight'],
									$params['selectionwidth'],
									$params['selectionheight']);
	if (!$rtn_code) {
		return FALSE;
	}

	// grab a compressed jpeg version of the image
	ob_start();
	if(DEFAULTIMAGEEXTENSION == ".png"){
		imagepng($new_image, NULL, 9, PNG_ALL_FILTERS);
	}else{
		if(DEFAULTIMAGEEXTENSION == ".jpg"){
			imagejpeg($new_image, NULL, IMAGECOMPRESIONQUALITY);
		}
	}
	$jpeg = ob_get_clean();

	imagedestroy($new_image);
	imagedestroy($original_image);

	return $jpeg;
}

function get_image_resize_parameters($width, $height, $options) {

	$defaults = array(
		'maxwidth' => 100,
		'maxheight' => 100,

		'square' => FALSE,
		'upscale' => FALSE,

		'x1' => 0,
		'y1' => 0,
		'x2' => 0,
		'y2' => 0,
	);

	$options = array_merge($defaults, $options);

	extract($options);

	// crop image first?
	$crop = TRUE;
	if ($x1 == 0 && $y1 == 0 && $x2 == 0 && $y2 == 0) {
		$crop = FALSE;
	}

	// how large a section of the image has been selected
	if ($crop) {
		$selection_width = $x2 - $x1;
		$selection_height = $y2 - $y1;
	} else {
		// everything selected if no crop parameters
		$selection_width = $width;
		$selection_height = $height;
	}

	// determine cropping offsets
	if ($square) {
		// asking for a square image back

		// detect case where someone is passing crop parameters that are not for a square
		if ($crop == TRUE && $selection_width != $selection_height) {
			return FALSE;
		}

		// size of the new square image
		$new_width = $new_height = min($maxwidth, $maxheight);

		// find largest square that fits within the selected region
		$selection_width = $selection_height = min($selection_width, $selection_height);

		// set offsets for crop
		if ($crop) {
			$widthoffset = $x1;
			$heightoffset = $y1;
			$width = $x2 - $x1;
			$height = $width;
		} else {
			// place square region in the center
			$widthoffset = floor(($width - $selection_width) / 2);
			$heightoffset = floor(($height - $selection_height) / 2);
		}
	} else {
		// non-square new image
		$new_width = $maxwidth;
		$new_height = $maxheight;

		// maintain aspect ratio of original image/crop
		if (($selection_height / (float)$new_height) > ($selection_width / (float)$new_width)) {
			$new_width = floor($new_height * $selection_width / (float)$selection_height);
		} else {
			$new_height = floor($new_width * $selection_height / (float)$selection_width);
		}

		// by default, use entire image
		$widthoffset = 0;
		$heightoffset = 0;

		if ($crop) {
			$widthoffset = $x1;
			$heightoffset = $y1;
		}
	}

	// check for upscaling
	if (!$upscale && ($height < $new_height || $width < $new_width)) {
		// determine if we can scale it down at all
		// (ie, if only one dimension is too small)
		// if not, just use original size.
		if ($height < $new_height && $width < $new_width) {
			$ratio = 1;
		} elseif ($height < $new_height) {
			$ratio = $new_width / $width;
		} elseif ($width < $new_width) {
			$ratio = $new_height / $height;
		}

		$selection_height = $height;
		$selection_width = $width;
	}

	$params = array(
		'newwidth' => $new_width,
		'newheight' => $new_height,
		'selectionwidth' => $selection_width,
		'selectionheight' => $selection_height,
		'xoffset' => $widthoffset,
		'yoffset' => $heightoffset,
	);

	return $params;
}

function ImagemagickNativeCropThumbnailImage($src,  $width , $height ){

    $image = new Imagick($src);
    //crop and resize the image
    $image->cropThumbnailImage($width,$height);
    return $image;

}

