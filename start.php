<?php
include_once(dirname(__FILE__) . "/config.php");

/* Include specific files manually */
$files = array(
"lib/utilities.php",
"lib/views.php",
);

foreach ($files as $file){
	include_once(PATHROOT. "/" .$file);
}