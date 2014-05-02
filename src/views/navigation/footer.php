</div>
<div id="footer">
	<div class="wrapper">
		<div class="firma">
			<a href="<?php echo WWWROOT ?>" target="_self"><img src="http://www.nearpod.com/wp-content/themes/nearpod2012Theme/img/footer/logo.png" width="146" height="34"></a>
			<p><?php echo sprintf(langEcho("footer:powered"), date('Y')); echo " v.".VERSION ?> </p>
			<span class="footerSupportContent"><?php echo langEcho("footer:support")?></span>
        	<a class="support" target="_blank" href="https://twitter.com/nearpodhelp"><?php echo langEcho("footer:nearpod:help")?></a>
			<a class="support" target="_blank" href="http://community.nearpod.com/"><?php echo langEcho("footer:ideas")?></a>
     		<br/><a class="support" href="<?php echo MARKETINGWEB . "help" ?>">Help room</a>
		</div>
		<div class="social">
			<ul>
				<li>
					<a href="https://www.facebook.com/nearpod" target="_blank"><img src="http://www.nearpod.com/wp-content/themes/nearpod2012Theme/img/footer/social/fb.png" width="24" height="24"><p><?php echo langEcho("footer:likefb")?></p></a>
				</li>
				<li>
					<a href="https://twitter.com/#!/nearpod" target="_blank"><img src="http://www.nearpod.com/wp-content/themes/nearpod2012Theme/img/footer/social/tw.png" width="24" height="24"><p><?php echo langEcho("footer:liketw")?></p></a>
				</li>
				<li>
					<a href="https://plus.google.com/s/nearpod" target="_blank"><img src="http://www.nearpod.com/wp-content/themes/nearpod2012Theme/img/footer/social/g+.png" width="24" height="24"><p><?php echo langEcho("footer:likegoogle")?></p></a>
				</li>
			</ul>
			<ul>
				<li>
					<a href="http://vimeo.com/nearpod" target="_blank"><img src="http://www.nearpod.com/wp-content/themes/nearpod2012Theme/img/footer/social/vm.png" width="24" height="24"><p><?php echo langEcho("footer:watchvideos")?></p></a>
				</li>
				<li>
					 <a href="<?php echo MARKETINGWEB?>feed/" target="_blank"><img src="http://www.nearpod.com/wp-content/themes/nearpod2012Theme/img/footer/social/rss.png" width="24" height="24"><p><?php echo langEcho("footer:receive");?></p></a>
				</li>
				<li>
					 <a href="http://pinterest.com/nearpod/" target="_blank"><img src="http://stg.nearpod.com/wp-content/themes/nearpod2012Theme/img/footer/social/pin.png" width="24" height="24"><p><?php echo langEcho("footer:pinterest");?></p></a>
				</li>
			</ul>
		</div>
		<div class="siteMap">
			<ul>
				<li>
					<?php echo langEcho("footer:tools")?>
				</li>
				<div class="menu-about-container">
					<ul id="menu-about" class="menu">
						<li>
							<a href="<?php echo WWWROOT . "presentations.php" ?>"><?php echo langEcho("headerMyPresentations")?></a>
						</li>
						<li>
							<a href="<?php echo WWWROOT . "reports.php" ?>"><?php echo langEcho("footer:reporting")?></a>
						</li>
						<li>
							<a href="<?php echo WEB_APP; ?>" target="_blank"><?php echo langEcho("footer:studentwebapp")?></a>
						</li>
                        <!--<li>
							<a href="<?php echo WWWROOT . "nowshowing.php" ?>" class="ajaxlightbox"><?php echo langEcho("footer:nowshowing")?></a>
						</li> -->
						<?php /*?>
						<li id="menu-item-811" class="inSessionLink menu-item menu-item-type-custom menu-item-object-custom menu-item-811">
                        <a href="#"><?php echo langEcho("insession")?></a>
                        </li>
                        <?php */ ?>
					</ul>
				</div>
			</ul>
			<ul>
				<li>
					<?php echo langEcho("downloads")?>
				</li>
				<div class="menu-users-container">
					<ul id="menu-users" class="menu">
						<li id="menu-item-58" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-58">
							<a href="<?php echo APP_DOWNLOAD_LINK; ?>" target="_blank"><?php echo langEcho("footer:nearpod:app")?> </a>
						</li>
						<li id="menu-item-59" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-58">
							<a href="<?php echo APP_DOWNLOAD_LINK_ANDROID; ?>" target="_blank"><?php echo langEcho("footer:nearpod:android:app")?> </a>
						</li>
					</ul>
				</div>
			</ul>
			<ul>
				<li>
					<?php echo langEcho("footer:staytuned");?>
				</li>
				<div class="menu-publishers-container">
					<ul id="menu-publishers" class="menu">
						<li>
							<a href="<?php echo MARKETINGWEB ?>news/" target="_blank"><?php echo langEcho("footer:community")?></a>
						</li>
						<li>
							<a href="<?php echo MARKETINGWEB ?>contact/" target="_blank"><?php echo langEcho("footer:contact:us")?></a>
						</li>
						<li>
							<a href="<?php echo MARKETINGWEB ?>publishers/" target="_blank"><?php echo langEcho("footer:for:publishers")?></a>
						</li>
						<li>
				     		<a  href="<?php echo MARKETINGWEB . "help" ?>" target="_blank"><?php echo langEcho("footer:help")?></a>
						</li>
					</ul>
				</div>
			</ul>
			<ul>
				<li>
					<?php echo langEcho("footer:legal");?>
				</li>
				<div class="menu-contact-container">
					<ul id="menu-contact" class="menu">
						<li id="menu-item-287" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-287">
							<a href="http://www.nearpod.com/privacy-policy/"><?php echo langEcho("footer:privacy")?></a>
						</li>
						<li id="menu-item-286" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-286">
							<a href="http://www.nearpod.com/terms-conditions/"><?php echo langEcho("footer:terms")?></a>
						</li>
					</ul>
				</div>
			</ul>
		</div>
	</div>
</div>

<?php
/*
		if(getLanguage() == "en"){
			echo "<a href='".WWWROOT."actions/language/set.php?l=pt'>".langEcho("pt")."</a>";
		}else{
			echo "<a href='".WWWROOT."actions/language/set.php?l=en'>".langEcho("en")."</a>";
		}
*/
?>


<?php
$productHistory = new UserProductHistoric();
$productHistory->getLastByUserId(getUserLoggedInId());
$maxUploadFileSize = $productHistory->attributes->maxUploadFileSize;
$error = "";

if (isset($_SESSION['ERRORMESSAGE'])){
	$error = $_SESSION['ERRORMESSAGE'];
	if(isset($_SESSION['ERRORMESSAGESHOWINALERT'])){
		$errorShowInAlert = $_SESSION['ERRORMESSAGESHOWINALERT'];
	}else{
		$errorShowInAlert = false;
	}
	$_SESSION['ERRORMESSAGE'] = "";
	$_SESSION['ERRORMESSAGESHOWINALERT'] = false;
}

if ($error != ""){
	if($errorShowInAlert){
		//$error = str_replace("<br/>","",$error);
		echo "<script type='text/javascript'>
				$(document).ready(function() {
					showAlertMessage('" . str_replace("'",'"',$error) . "','alertErrorMessageContainer');
				});
			</script>";
	}else{
		echo "<script type='text/javascript'>
				displayErrorMessage('" . str_replace("'",'"',$error) . "');
		</script>";
	}
}

$success = "";

if (isset($_SESSION['SUCCESSMESSAGE'])){
	$success = $_SESSION['SUCCESSMESSAGE'];
	$_SESSION['SUCCESSMESSAGE'] = "";
}

if ($success != ""){
	echo "<script type='text/javascript'>
		displaySuccessMessage('" . str_replace("'",'"',$success) . "');
	</script>";
}

$user = getUserLoggedIn();
$valueDismiss = "";
if ($user){
	$valueDismiss = ($user->attributes->dismissGLogout)?"true":"";
}
echo "<input id='dismissGLogout' type='hidden' value='$valueDismiss'/>";

// register some variables in js to use for the language.
?>
<?php
// @fixme: use this code block when must use storage values from User settings (UserProductHistoric)
 if ($user){
 	$productHistory = new UserProductHistoric();
 	$productHistory->getLastByUserId(getUserLoggedInId());
 	$maxUploadImageSize = $productHistory->attributes->maxUploadImageSize;
     $maxUploadVideoSize = $productHistory->attributes->maxUploadVideoSize;
     $maxUploadAudioSize = $productHistory->attributes->maxUploadAudioSize;
     $maxUploadFileSize = $productHistory->attributes->maxUploadFileSize;
 } else {
	$maxUploadImageSize = MAXIMAGEFILESIZE;
    $maxUploadVideoSize = MAXVIDEOFILESIZE;
    $maxUploadAudioSize = MAXAUDIOFILESIZE;
    $maxUploadFileSize = MAXUPLOADFILESIZE;
 }
?>
<script type="text/javascript">
    var WWWBoxFileChooser = '<?php echo WWWROOT . "boxFileChooser.php"?>';
    var WWWGooglePicker = '<?php echo WWWROOT . "googlePicker.php"?>';
    var WWWGoogleDrivePublicExport = 'http://drive.google.com/uc?export=download&id=';

    var MAXIMAGEFILESIZE = '<?php echo $maxUploadImageSize; ?>';
    var MAXVIDEOFILESIZE = '<?php echo $maxUploadVideoSize; ?>';
    var MAXAUDIOFILESIZE = '<?php echo $maxUploadAudioSize; ?>';
    var MAXUPLOADFILESIZE = '<?php echo $maxUploadFileSize; ?>';
    var MAXIMAGEFILESIZE_MSG = '<?php echo sprintf(langEcho("maxImageSize"), $maxUploadImageSize)?>';
    var MAXVIDEOFILESIZE_MSG = '<?php echo sprintf(langEcho("maxVideoSize"), $maxUploadVideoSize)?>';
    var MAXAUDIOFILESIZE_MSG = '<?php echo sprintf(langEcho("maxAudioSize"), $maxUploadAudioSize)?>';
    var MAXUPLOADFILESIZE_MSG = '<?php echo sprintf(langEcho("maxFileSize"), $maxUploadFileSize)?>';

	var language = new Array();
	language["generic:error:message"] = '<?php echo langEcho("generic:error:message")?>';
	language["yes"] = '<?php echo langEcho("yes")?>';
	language["no"] = '<?php echo langEcho("no")?>';
	language["accept"] = '<?php echo langEcho("accept")?>';
	language["cancel"] = '<?php echo langEcho("cancel")?>';
	language["ok"] = '<?php echo langEcho("ok")?>';
	language["confirmCloseDialogTitle"] = '<?php echo langEcho("confirmCloseDialogTitle")?>';
	language["confirmCloseDialogMessage"] = '<?php echo langEcho("confirmCloseDialogMessage")?>';
	language["confirmCloseMemotestDialogTitle"] = '<?php echo langEcho("confirmCloseMemotestDialogTitle")?>';
	language["confirmCloseMemotestDialogMessage"] = '<?php echo langEcho("confirmCloseMemotestDialogMessage")?>';
	language["confirmCloseSmartUploadTitle"] = '<?php echo langEcho("confirmCloseSmartUploadTitle")?>';
	language["confirmCloseSmartUploadMessage"] = '<?php echo langEcho("confirmCloseSmartUploadMessage")?>';
	language["dontRemind"] = "<?php echo langEcho("dontRemind")?>";
	language["makeUpgradeSuggestionTitle"] = '<?php echo langEcho("makeUpgradeSuggestionTitle")?>';
	language["makeUpgradeSuggestionLearnMore"] = '<?php echo langEchoReplaceVariables("makeUpgradeSuggestionLearnMore",array("learnMoreLink" => MARKETINGWEB_UPGRADE));?>';
	language["menu:upgrade"] = '<?php echo langEcho("menu:upgrade")?>';

	language["createSlideshowSuggestionTitle"] = '<?php echo langEcho("createSlideshowSuggestionTitle")?>';
	language["createSlideshowSuggestionMessage"] = '<?php echo langEcho("createSlideshowSuggestionMessage")?>';
	language["createPresentationSuggestionTitle"] = '<?php echo langEcho("createPresentationSuggestionTitle")?>';
	language["createPresentationSuggestionMessage"] = '<?php echo langEcho("createPresentationSuggestionMessage")?>';
	language["clonePresentationSuggestionTitle"] = '<?php echo langEcho("clonePresentationSuggestionTitle")?>';
	language["clonePresentationSuggestionMessage"] = '<?php echo langEcho("clonePresentationSuggestionMessage")?>';

	language['preventRefreshMessage'] = '<?php echo langEcho("preventRefreshMessage")?>';
	language['createSlideshowButton'] = '<?php echo langEcho("createSlideshowButton")?>';
	language['createImageSlidesButton'] = '<?php echo langEcho("createImageSlidesButton")?>';
	language['fileToBig'] = '<?php echo sprintf(langEcho("maxFileSize"), $maxUploadFileSize)?>';
	language["contactUs"] = '<?php echo langEcho("contactUs")?>';

	var marketing_web_upgrade = '<?php echo MARKETINGWEB_UPGRADE?>';
	var marketing_web = '<?php echo MARKETINGWEB ?>';

	$(document).ready(function() {
		var imageBasePath = '<?php echo WWWSTATIC1 . VERSION?>/images/';
		var imageBasePathLanguage = '<?php echo WWWSTATIC1 . VERSION ?>/images/<?php echo getLanguage()?>/';
		preloadImages(
				imageBasePath+"add.png",
                imageBasePath+"add-over.png",
                imageBasePath+"pres-cover-normal.png",
                imageBasePath+"pres-cover-normal-on.png",
                imageBasePath+"pres-cover-published.png",
                imageBasePath + "pres-cover-published-on.png",
                imageBasePath+"btn-newp_browse.png",
                imageBasePath+"btn-newp_browse-h.png",
                imageBasePath + "btn-newp_dropbox.png",
                imageBasePath + "btn-newp_dropbox-h.png",
                imageBasePath + "btn-newp-ecanvas.png",
                imageBasePath+"btn-newp-ecanvas-h.png",
                imageBasePath+"btn-newp-scratch.png",
                imageBasePath+"btn-newp-scratch-on.png",
                imageBasePath+"drag-box.png",
                imageBasePath+"drag-newp_error.png",
                imageBasePath+"slidethumb_draw.png",
                imageBasePath+"slidethumb_draw_h.png",
                imageBasePath+"btn-new.png",
                imageBasePath+"btn-new-h.png",
                imageBasePath+"btn-draw.png",
                imageBasePath+"btn-draw-h.png",
                imageBasePath+"btn-poll.png",
                imageBasePath+"btn-poll-h.png",
                imageBasePath+"btn-quiz.png",
                imageBasePath+"btn-quiz-h.png",
                imageBasePath+"btn-qya.png",
                imageBasePath+"btn-qya-h.png",
                imageBasePath+"btn-slide.png",
                imageBasePath+"btn-slide-h.png",
                imageBasePath+"btn-video.png",
                imageBasePath+"btn-video-h.png",
                imageBasePath+"btn-web.png",
                imageBasePath+"btn-web-h.png",
                imageBasePath+"f-browse.png",
                imageBasePath+"f-browse-on.png",
                imageBasePath+"f-drag.png",
                imageBasePath+"drawit-asset.png",
                imageBasePath+"drawit-asset_error.png",
                imageBasePath+"f-drag-error.png",
                imageBasePathLanguage+"f-dragvideo.png",
                imageBasePathLanguage+"f-dragvideo-error.png",
                imageBasePath+"f-drawit-btn.png",
                imageBasePath+"f-drawit-btnon.png",
                imageBasePath+"f-dropbox.png",
                imageBasePath+"f-dropbox-on.png",
                imageBasePath+"slidethumb_poll.png",
                imageBasePath+"slidethumb_poll_h.png",
                imageBasePath+"slidethumb_internet.png",
                imageBasePath+"slidethumb_internet_h.png",
                imageBasePath+"slidethumb_quiz.png",
                imageBasePath+"slidethumb_quiz_h.png",
                imageBasePath+"slidethumb_qya.png",
                imageBasePath+"slidethumb_qya_h.png",
                imageBasePath+"slidethumb_slideshow.png",
                imageBasePath+"slidethumb_slideshow_h.png",
                imageBasePath+"slidethumb_video.png",
                imageBasePath+"header/logout-on.png",
                imageBasePath+"slidethumb_video_h.png"
                );

	});


	/* Functions to call de logout confirm dialog*/
	function actionLogout(){
		window.location = "<?php echo WWWROOT . "actions/user/logout.php"?>";
	}

	function onCheckedLogout(){
		window.location = "<?php echo WWWROOT . "actions/user/logout.php"?>" + "?dontRemind=true";
	}

	function userLogout(){
	<?php if(isset($_SESSION['google_token']) && $_SESSION['google_token']){?>
		window.location = "https://www.google.com/accounts/Logout?continue=https://appengine.google.com/_ah/logout?continue=<?php echo WWWROOT . "actions/user/logout.php"?>";
	<?php } else { ?>
		window.location = "<?php echo WWWROOT . "actions/user/logout.php"?>";
	<?php }?>
	}

</script>
<?php
	echo loadView("navigation/initOnEveryCallback");
?>
<?php

if (isset($_SESSION['ERRORDIALOG'])){
	$dialog = $_SESSION['ERRORDIALOG'];
	unset($_SESSION['ERRORDIALOG']);
}

if (isset($dialog)){
	$strParams = "";

	if (isset($dialog["params"]) && count($dialog["params"]) > 0){
		$strParams .= "'" . array_shift($dialog["params"]) . "'";
		foreach ($dialog["params"] as $param){
			$strParams .= " , '" . $param . "'";
		}

	}

	echo "<script type='text/javascript'>".
			$dialog["jsFunction"] . "(" . $strParams . ");"
			."</script>";
}

?>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-26812425-1']);
  _gaq.push(['_setDomainName', 'nearpod.com']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<?php if(!contains(curPageBasic(), "login.php")){?>
<div id="getsat-widget-1092"></div>
<!-- The basic File Upload plugin -->
<script src="<?php echo WWWSTATIC2 . VERSION ."/views/js/jquery.fileupload.js"?>"></script>
<!-- The File Upload processing plugin -->
<script src="<?php echo WWWSTATIC2 . VERSION ."/views/js/jquery.fileupload-process.js"?>"></script>
<!-- The File Upload image preview & resize plugin -->
<script src="<?php echo WWWSTATIC2 . VERSION ."/views/js/jquery.fileupload-image.js"?>"></script>
<!-- The File Upload audio preview plugin -->
<script src="<?php echo WWWSTATIC2 . VERSION ."/views/js/jquery.fileupload-audio.js"?>"></script>
<!-- The File Upload video preview plugin -->
<script src="<?php echo WWWSTATIC2 . VERSION ."/views/js/jquery.fileupload-video.js"?>"></script>
<!-- The File Upload validation plugin -->
<script src="<?php echo WWWSTATIC2 . VERSION ."/views/js/jquery.fileupload-validate.js"?>"></script>
<!-- The File Upload user interface plugin -->
<script src="<?php echo WWWSTATIC2 . VERSION ."/views/js/jquery.fileupload-ui.js"?>"></script>
<script type="text/javascript" src="<?php echo WWWSTATIC2 . VERSION ."/views/js/dropbox.js"?>" id="dropboxjs" data-app-key="<?php echo DROPBOX_CONSUMER_KEY?>"></script>
<script type="text/javascript" src="https://loader.engage.gsfn.us/loader.js"></script>
<script type="text/javascript">
if (typeof GSFN !== "undefined") {
	GSFN.loadWidget(1092,{
"containerId":"getsat-widget-1092"});
}
</script>

<?php if (Setting::valueBySKey('ASKUSENABLED')){?>
<script type="text/javascript" src="//assets.zendesk.com/external/zenbox/v2.6/zenbox.js"></script>
<style type="text/css" media="screen, projection">
	@import url(//assets.zendesk.com/external/zenbox/v2.6/zenbox.css);
</style>
<script type="text/javascript">
	if (typeof(Zenbox) !== "undefined") {
		Zenbox.init({
			dropboxID: "20186342",
			url: "https://nearpod.zendesk.com",
			tabTooltip: "Ask Us",
			tabImageURL: "https://assets.zendesk.com/external/zenbox/images/tab_ask_us_right.png",
			tabColor: "#18b7c8",
			tabPosition: "Right"
		});
	}
</script>
<?php } ?>
<?php } ?>
</body>
</html>
