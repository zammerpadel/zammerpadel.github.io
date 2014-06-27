<?php
$title = getInputArray("title",$params,'' );
$description = getInputArray("description",$params,'' );
$type = getInputArray("type",$params,'' );
$urlImg = getInputArray("urlImg",$params,'' );
$successCallback = getInputArray("successCallback",$params,"''");
$autoOpenHelper = getInputArray("autoOpenHelper",$params,true);
// $arrowImgUrl = getInputArray('arrowImgUrl', $params, '');
$titleSecondLine = getInputArray("titleSecondLine", $params, '');

$user = getUserLoggedIn();
	if (!$user->mustSeeHelper($type)){
		return;
	}
?>

<div class="helpersContent <?php echo $type ?> hide">
	<h1><?php echo $title ?></h1>
	<?php if($titleSecondLine){ ?>
		<h2><?php echo $titleSecondLine ?></h2>
	<?php } ?>
    <div class="msnHelper">
    	<img src="<?php echo $urlImg; ?>" alt="" class="imgsobresaliente">
   	  	<p><?php echo $description ?></p>
        <span class="btnnHelper"><a id="btnnHelperOk" href="#" onclick="confirmHelper('<?php echo $type ?>',<?php echo $successCallback?>);" ><?php echo langEcho('helperPopUp:btnOk') ?></a></span>
	</div>
</div>
<div id="helperContainer<?php echo $type ?>" class="hide">
	<div class="helperBackground"></div>
</div> 

<script type="text/javascript">
	function showHelperPopUp(type){
		$('.helpersContent.' + type).fadeIn(500);	
		$('#helperContainer'+ type).fadeIn(500);	
	}
	$(document).ready(function() {
<?php 	if($autoOpenHelper){?>
			showHelperPopUp('<?php echo $type ?>');
<?php 	} ?>
		
	});

	function confirmHelper(type,successCallback){
		startLoadingOnAjax = false;
		
		$.get('/actions/user/confirmHelper.php?helperKey='+type);
		if(successCallback !== undefined){
			if (typeof successCallback === "function") {
				successCallback();
			}
		}
		startLoadingOnAjax = true;
		$('.helpersContent.'+type).fadeOut(500);
		$('#helperContainer'+type).fadeOut(500);
	}
</script>
