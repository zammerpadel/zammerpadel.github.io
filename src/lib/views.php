<?php

function loadView($view, $params = array()){
	ob_start();
	include(dirname(dirname(__FILE__)). "/views/" . $view . ".php");
	$content = ob_get_clean();
	return $content;
}

function loadTemplate($template, $title = "", $params = array()){
	ob_start();
	include(dirname(dirname(__FILE__)). "/template/" . $template . ".php");

	$content = ob_get_clean();
	return $content;
}

?>