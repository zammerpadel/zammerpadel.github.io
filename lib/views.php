<?php

function loadView($view, $params = array()){
	foreach ($params as $key => $value){
		$_REQUEST[$key] = $value;
	}
	include(dirname(dirname(__FILE__)). "/views/" . $view . ".php");
}

function loadTemplate($template, $title = "", $params = array()){
	ob_start();
	include(dirname(dirname(__FILE__)). "/template/" . $template . ".php");

	$content = ob_get_clean();
	return $content;
}

?>