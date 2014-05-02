<?php



echo "<div class='clearFloat'></div>";
if (!isset($params['class'])) {
    $class = '';
}else
{
    $class = $params['class'];
}

if (!isset($params['offset'])) {
$offset = 0;
} else {
$offset = $params['offset'];
}
if ((!isset($params['limit'])) || (!$params['limit'])) {
$limit = 10;
} else {
$limit = (int)$params['limit'];
}
if (!isset($params['count'])) {
$count = 0;
} else {
$count = $params['count'];
}

$labelOffset = "offset";
if (isset($params["labelOffset"])){
	$labelOffset = $params["labelOffset"];	
}

$baseurl = getInputArray("baseUrl",$params,'' );

$totalpages = ceil($count / $limit);
$currentpage = ceil($offset / $limit) + 1;

//only display if there is content to paginate through or if we already have an offset
if ($count > $limit || $offset > 0) {

?>

<div class="pagination <?php echo $class;?>">
<?php

if ($offset > 0) {

	$prevoffset = $offset - $limit;
	if ($prevoffset < 0) $prevoffset = 0;
	
	$prevurl = $baseurl;
	if (substr_count($baseurl,'?')) {
	$prevurl .= "&$labelOffset=" . $prevoffset;
	} else {
	$prevurl .= "?$labelOffset=" . $prevoffset;
	}
	
	echo "<a href=\"{$prevurl}\" class=\"pagination_previous\">&laquo; ". langEcho("previous") ."</a> ";

}

if ($offset > 0 || $offset < ($count - $limit)) {

	$currentpage = round($offset / $limit) + 1;
	$allpages = ceil($count / $limit);
	
	$i = 1;
	$pagesarray = array();
	
	$fixed_pages = 4;
	$fixed_pages_prev = 2;
	$fixed_pages_next = 2;
	
	if ($currentpage == 1){
	$fixed_pages_next = 7;
	}
	
	if ($currentpage == $allpages){
	$fixed_pages_prev = 6;
	}
	
	$top = $currentpage + $fixed_pages_next;
	$i = $currentpage - $fixed_pages_prev;
	
	
	
	if ($i <= 0){
	$i = 1;
	}
	
	if ($i > 1) {
	
	$prevurl = $baseurl;
	if (substr_count($baseurl,'?')) {
	$prevurl .= "&$labelOffset=" . 0;
	} else {
	$prevurl .= "?$labelOffset=" . 0;
	}
	
	echo "<a href=\"{$prevurl}\" class=\"pagination_number\">...</a> ";
	}
	
	
	while ($i <= $allpages && $i <= $top) {
	$pagesarray[] = $i;
	$i++;
	}
	
	sort($pagesarray);
	
	$counter = 0;
	
	$prev = 0;
	foreach($pagesarray as $i) {
	
	$counturl = $baseurl;
	$curoffset = (($i - 1) * $limit);
	if (substr_count($baseurl,'?')) {
	$counturl .= "&$labelOffset=" . $curoffset;
	} else {
	$counturl .= "?$labelOffset=" . $curoffset;
	}
	if ($curoffset != $offset) {
	echo " <a href=\"{$counturl}\" class=\"pagination_number\">{$i}</a> ";
	} else {
	echo "<a class=\"pagination_currentpage\" href='' onclick='return false;' > {$i} </a>";
	}
	$prev = $i;
	
	}
	
	if ($top < $allpages){
	$lastoffset = (($allpages -1) * $limit);
	
	$nexturl = $baseurl;
	
	if (substr_count($baseurl,'?')) {
	$nexturl .= "&$labelOffset=" . $lastoffset;
	} else {
	$nexturl .= "?$labelOffset=" . $lastoffset;
	}
	
	echo " <a href=\"{$nexturl}\" class=\"pagination_number\">...</a>";
	}
	
}


if ($offset < ($count - $limit)) {	
		$nextoffset = $offset + $limit;
		if ($nextoffset >= $count) $nextoffset--;
		
		$nexturl = $baseurl;
		if (substr_count($baseurl,'?')) {
		$nexturl .= "&$labelOffset=" . $nextoffset;
		} else {
		$nexturl .= "?$labelOffset=" . $nextoffset;
		}
		
		echo " <a href=\"{$nexturl}\" class=\"pagination_next\">" . langEcho("next") . " &raquo;</a>";
}

?>
<div class="clearfloat"></div>
</div>
<?php
 }
?>