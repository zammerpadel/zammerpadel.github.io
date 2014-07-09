<?php
include_once(dirname(__FILE__) . "/config.php");

foreach (glob("data_access/*.php") as $filename){
	include $filename;
}

foreach (glob("business/*.php") as $filename){
	include $filename;
}


/* Include specific files manually */
$files = array(
	"lib/utilities.php",
	"lib/views.php",
);

foreach ($files as $file){
	include_once(PATHROOT. "/" .$file);
}