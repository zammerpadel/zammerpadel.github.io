<?php echo loadView('navigation/htmlHeader', array("title" => $params['title'])); ?>
<body>
<?php
$smartUpload = getInputArray('smartUpload', $params, false);
if ($smartUpload){
	echo loadView("smartUpload/smartUploadPopup", array("entity" => getInputArray('entity', $params, null)));
}?>
<div id="loading" style="display:none;">
	<img src='<?php echo WWWSTATIC?>images/loading.gif' alt='' />
</div>
<div id="uploadContent" style="display:none;">
	<div id="uploadContentContainer">
		<img src='<?php echo WWWROOT?>images/wait.gif' alt='' />
		<span class="uploading" ><?php echo langEcho("uploading")?></span>
		<span class="wait"><?php echo langEcho("wait")?></span>
		<div id="progress" style="display:none">
			<span id="progressNumber">0%</span>
			<span id="progressBar"></span>
			<a onclick="sendKeyEvent('keyup', 27,false);return false;" href="#"><?php echo langEcho("cancel:upload")?></a>
		</div>
	</div>
</div>
<div id="header">
	<div class="wrapper">
<?php
if(isUserLoggedIn()){
	$user = getUserLoggedIn();
	$product = new Product($user->attributes->productId);
	$srcImage = $product->getLogoURL();
	$userType = $product->getTypeText();
	$autoLoginToken = $user->setAutoLoginToken();
	if(isset($_SESSION["haveToShowDowngradeMessage"]) && $_SESSION["haveToShowDowngradeMessage"] == true){
		$link = MARKETINGWEB_UPGRADE.'?alk='.$autoLoginToken;
		$productHistory = new UserProductHistoric();
		$productHistory->getLastByUserId($user->attributes->id);
		$oldProductMessage = new Product($productHistory->attributes->oldProductId);
		$newProductMessage = new Product($user->attributes->productId);?>
		<script type="text/javascript">
			$(document).ready(function() {
				showContinueMessageWithStyle('<?php echo langEcho("downgrade:messageTitle:login")?>','<?php echo langEchoReplaceVariables(("downgrade:message:login"),array ("downgradeMessageFormLink" => $link,"downgradeMessageLinkUpgrade"=> $link,"downgradeMessageOldProd" => $oldProductMessage->attributes->name,"downgradeMessageNewProd" => $newProductMessage->attributes->name))?>','<?php echo langEcho("downgrade:messageButton:login")?>','600');
			});
		</script>
	<?php
	 	$_SESSION["haveToShowDowngradeMessage"] = false;
	}
?>
		<ul class="menu topnav left">
			<li class="home" onclick="window.location='<?php echo WWWROOT . "actions/user/autoLoginApp.php" ?>'" style="cursor:pointer">
				<a title="<?php echo langEcho("headerHome")?>" class="homeEngage hasOnIco"  href="<?php echo WWWROOT . "actions/user/autoLoginApp.php" ?>"></a>
			</li>
			<li onclick="window.location='<?php echo WWWROOT . "presentations.php" ?>'" class="create" style="cursor:pointer">

				<a title="<?php echo langEcho("mypresentations")?>" class="create hasOnIco" href="<?php echo WWWROOT . "presentations.php" ?>"></a>
				<p> <?php echo langEcho("mypresentations")?> </p>
			</li>
			<li onclick="window.location='<?php echo WWWROOT . "reports.php" ?>'" class="assess"  style="cursor:pointer">
				<div class="menuItemSeparator"> </div>
				<a title="<?php echo langEcho("myreports")?>" class="assess  hasOnIco" href="<?php echo WWWROOT . "reports.php" ?>"></a>
				<p> <?php echo langEcho("myreports")?> </p>
			</li>
			<?php if (isAdminLoggedIn()){?>
				<a title="<?php echo langEcho("Admin")?>" class="admin" href="<?php echo WWWROOT . "admin.php" ?>"></a>
			<?php }?>
		</ul>

		<div class="contentLogo">
			<a href="<?php echo WWWROOT . "presentations.php" ?>" target="_self" class="logo center"><img src="<?php echo UPLOADCDN;?>Images/<?php echo LOGOIMG;?>"></a>
		</div>
		<ul class="menu topnav right">
			<li class="account">
				<a class="account" href="#" onclick="return false;">
					<?php  echo $user->attributes->firstName . " ".  $user->attributes->lastName;?>
					<img class="account" src="<?php echo $srcImage ?>"/>
					<p class="text"> <?php echo $userType ?> </p>
				</a>
		        <ul class="subnav" id="headerSubMenu">
		        	<?php if($user->attributes->productId == Product::$silver){
								 ?>
							<li class="upgradeBox" >
								<a class="upgrade" href="<?php echo MARKETINGWEB_UPGRADE.'?alk='.$autoLoginToken?>"><?php echo langEcho("menu:upgrade")?>
									<img src="<?php echo  WWWSTATIC . "images/header/upgrade.png" ?>" />
								</a>
							</li>
					<?php } ?>
		            <li class="status">
		            	<a class="status" ><?php echo langEcho("menu:activity") ?>
							<?php
								$user = getUserLoggedIn();
							?>
							<div class="containerProgress">
								<input type="button" class="progress"/>
								<input type="button" class="progressBar" style="width:<?php echo round((($user->attributes->storage / 1048576 ) / $user->attributes->maxStorage) * 100);?>px"/>
							</div>
							<p><?php echo round(($user->attributes->storage / 1048576 ));?> mb</p>
						</a>
					</li>
					<li >
						<a class="settings ajaxlightbox" id="profile" href="<?php echo WWWROOT . "account.php"?>"><?php echo langEcho("menu:account")?>
						<img src="<?php echo  WWWSTATIC . "images/header/settings.png" ?>" />
						</a>
					</li>
		            <?php
		            	if(validateUserPermission(array(UserRole::$roleAdmin, UserRole::$roleSchoolAdmin, UserRole::$roleSupport, UserRole::$roleDistrictAdmin))){
		            		$menuAdmin = "menu:admin";
                            if (isSchoolAdminLoggedIn()){
                                $menuAdmin = "menu:admin:school";
                            }else if (isDistrictAdminLoggedIn()){
                                $menuAdmin = "menu:admin:district";
                            }
		            ?>
					<li>
						<a class="manage" href="<?php echo WWWROOT . "admin"?>"><?php echo langEcho($menuAdmin)?>
							<img src="<?php echo  WWWSTATIC . "images/header/manage.png" ?>" />
						</a>
					</li>
		            <?php
		            	}
		            ?>
		            <li>
			            <a class="logout" href='#' onclick="userLogout();" ><?php echo langEcho("logout")?>
			            	<img src="<?php echo  WWWROOT . "images/header/logout.png" ?>" />
			            </a>
		            </li>
		        </ul>
			</li>
		</ul>
<?php
		if(isUserLoggedIn()){
			$user = getUserLoggedIn();
			$productHistory = new UserProductHistoric();
			$productHistory->getLastByUserId($user->attributes->id);
			$allProductHistory = new UserProductHistoric();
			$allProductHistory = $allProductHistory->getReferralByUserId($user->attributes->id);
			if(sizeOf($allProductHistory) == 0 && $user->attributes->productId != Product::$gold){
				if($user->attributes->productId == Product::$silver &&
				(sizeOf($allProductHistory) == 0 || $productHistory && $productHistory->attributes &&
				 $productHistory->attributes->productId == Product::$silver &&
				$productHistory->attributes->source != UserProductHistoric::$referral)){ ?>
					<a class="referralMenu" href="<?php echo WWWROOT. 'referral.php' ?>"><?php echo langEcho("menu:referral")?></a>
		<?php
			}
		 	}
		}
	?>
	<script type="text/javascript">
					$("#header ul.topnav li ul.subnav li a.settings").hover(
						function () {
							$("#header ul.topnav li ul.subnav li a.settings img").attr("src", "<?php echo  WWWSTATIC . "images/header/settings-on.png" ?>");
						},
						function () {
							$("#header ul.topnav li ul.subnav li a.settings img").attr("src", "<?php echo  WWWSTATIC . "images/header/settings.png" ?>");
						}
					);
					$("#header ul.topnav li ul.subnav li a.upgrade").hover(
							function () {
								$("#header ul.topnav li ul.subnav li a.upgrade img").attr("src", "<?php echo  WWWSTATIC . "images/header/upgrade-on.png" ?>");
							},
							function () {
								$("#header ul.topnav li ul.subnav li a.upgrade img").attr("src", "<?php echo  WWWSTATIC . "images/header/upgrade.png" ?>");
							}
						);
					$("#header ul.topnav li ul.subnav li a.manage").hover(
							function () {
								$("#header ul.topnav li ul.subnav li a.manage img").attr("src", "<?php echo  WWWSTATIC . "images/header/manage-on.png" ?>");
							},
							function () {
								$("#header ul.topnav li ul.subnav li a.manage img").attr("src", "<?php echo  WWWSTATIC . "images/header/manage.png" ?>");
							}
					);
					$("#header ul.topnav li ul.subnav li a.logout").hover(
							function () {
								$("#header ul.topnav li ul.subnav li a.logout img").attr("src", "<?php echo  WWWSTATIC . "images/header/logout-on.png" ?>");
							},
							function () {
								$("#header ul.topnav li ul.subnav li a.logout img").attr("src", "<?php echo  WWWSTATIC . "images/header/logout.png" ?>");
							}
					);

					$("#header ul.topnav li ul.subnav li a.status").hover(
							function () {
								$("#header ul.topnav li ul.subnav li a.status p").css("color", "white");
							},
							function () {
								$("#header ul.topnav li ul.subnav li a.status p").css("color", "#686868");
							}
					);

				if("<?php echo curPageURL() ?>".indexOf("engage.php") !== -1){
					$(".engage").addClass("engageOn");
					$("li .engage").parent().addClass("linkBottomBordered");
					$("li .engage").parent().css("padding-bottom","0px");
				}else if("<?php echo curPageURL() ?>".indexOf("presentations.php") !== -1){
					$(".create").addClass("createOn");
					$("li .create").parent().addClass("linkBottomBordered");
					$("li .create").parent().css("padding-bottom","0px");
				}else if("<?php echo curPageURL() ?>".indexOf("reports.php") !== -1){
					$(".assess").addClass("assessOn");
					$("li .assess").parent().addClass("linkBottomBordered");
					$("li .assess").parent().css("padding-bottom","0px");
				}else if("<?php echo curPageURL() ?>".indexOf("admin") !== -1){
					$("li .admin").parent().addClass("linkBottomBordered");
					$("li .admin").parent().css("padding-bottom","0px");
				}
		</script>
<?php

}
?>
		<!--
		<ul class="langSelector">
			<li>
				<a href="#" class="active">english</a>
			</li>
			<li>
				<a href="#">português</a>
			</li>
		</ul>-->
		<?php echo loadView('navigation/languageSelector', array()); ?>
		<div id="message" style="display:none">
		<div id="errorMessage" class="errorMsg" style="display:none"></div>
		<div id="successMessage" class="succesMsg" style="display:none"></div>
		<!-- <a href="#" onclick="$('#message').slideUp(1000); return false;"><?php echo langEcho("popup:close")?></a> -->
		</div>
	</div>
</div>

<div id="wrapper">
