<?php
header ('Content-type: text/html; charset=utf-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php echo langEcho("title:defaultTitle") . $params["title"]?></title>
<meta name="format-detection" content="telephone=no" />
<link rel="shortcut icon"  type="type-x/icon" href="<?php echo WWWSTATIC1 . VERSION ."/images/favicon.ico"?>">
<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans:400,700"></link>
<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.10.3/themes/ui-lightness/jquery-ui.css"></link>
<link rel="stylesheet" type="text/css" href="<?php echo WWWSTATIC1 . VERSION ."/views/style/jquery.fancybox-1.3.4.css"?>"></link>
<link rel="stylesheet" type="text/css" href="<?php echo WWWSTATIC1 . VERSION ."/views/style/jquery.jqplot.min.css"?>"></link>
<link rel="stylesheet" type="text/css" href="<?php echo WWWSTATIC1 . VERSION ."/views/style/jHtmlArea.css"?>"></link>
<link rel="stylesheet" type="text/css" href="<?php echo WWWSTATIC1 . VERSION ."/views/style/jHtmlArea.ColorPickerMenu.css"?>"></link>
<link rel="stylesheet" type="text/css" href="<?php echo WWWSTATIC1 . VERSION ."/views/style/default.css"?>"></link>
<link rel="stylesheet" type="text/css" href="<?php echo WWWSTATIC1 . VERSION ."/views/style/fonts.css"?>"></link>
<link rel="stylesheet" type="text/css" href="<?php echo WWWSTATIC1 . VERSION ."/views/style/style.css"?>"></link>
<link rel="stylesheet" type="text/css" href="<?php echo WWWSTATIC1 . VERSION ."/views/style/reports_style.css"?>"></link>
<link rel="stylesheet" type="text/css" href="<?php echo WWWSTATIC1 . VERSION ."/views/style/reports_info.css"?>"></link>
<link rel="stylesheet" type="text/css" href="<?php echo WWWSTATIC1 . VERSION ."/views/style/" . getLanguage() . ".css"?>"></link>
<?php if (isMobile()) { ?>
<link rel="stylesheet" type="text/css" href="<?php echo WWWSTATIC1 . VERSION ."/views/style/mobile.css"?>"></link>
<?php } ?>
<?php if (isIOSDevice()) { ?>
<link rel="stylesheet" type="text/css" href="<?php echo WWWSTATIC1 . VERSION ."/views/style/ipad.css"?>"></link>
<?php } ?>
<link rel="stylesheet" type="text/css" href="<?php echo WWWSTATIC1 . VERSION ."/views/style/memotest.css"?>"></link>
<link rel="stylesheet" type="text/css" href="<?php echo WWWSTATIC1 . VERSION ."/views/style/boxNetStyle.css"?>"></link></link>
<link rel="stylesheet" type="text/css" href="<?php echo WWWSTATIC1 . VERSION ."/views/style/jquery.mCustomScrollbar.css"?>"></link>
<script type="text/javascript" src="<?php echo WWWSTATIC2 . VERSION ."/views/js/functions.js"?>"></script>
<script type="text/javascript" src="<?php echo WWWSTATIC2 . VERSION ."/views/js/smartUpload/smartUploader.js"?>"></script>
<script type="text/javascript" src="<?php echo WWWSTATIC2 . VERSION ."/views/js/smartUpload/smartAudio.js"?>"></script>
<script type="text/javascript" src="<?php echo WWWSTATIC2 . VERSION ."/views/js/smartUpload/smartImage.js"?>"></script>
<script type="text/javascript" src="<?php echo WWWSTATIC2 . VERSION ."/views/js/smartUpload/smartSlideshow.js"?>"></script>
<script type="text/javascript" src="<?php echo WWWSTATIC2 . VERSION ."/views/js/smartUpload/smartVideo.js"?>"></script>
<script type="text/javascript" src="<?php echo WWWSTATIC2 . VERSION ."/views/js/smartUpload/smartPdf.js"?>"></script>
<script type="text/javascript" src="<?php echo WWWSTATIC2 . VERSION ."/views/js/jquery-1.10.2.min.js"?>"></script>
<script type="text/javascript" src="<?php echo WWWSTATIC2 . VERSION ."/views/js/jquery-ui.min.js"?>"></script>
<script type="text/javascript" src="<?php echo WWWSTATIC2 . VERSION ."/views/js/jquery-migrate-1.2.1.min.js"?>"></script>
<script type="text/javascript" src="<?php echo WWWSTATIC2 . VERSION ."/views/js/jquery-form.js"?>"></script>
<script type="text/javascript" src="<?php echo WWWSTATIC2 . VERSION ."/views/js/jquery.fancybox-1.3.4.pack.js"?>"></script>
<script type="text/javascript" src="<?php echo WWWSTATIC2 . VERSION ."/views/js/jHtmlArea-0.7.0.min.js"?>"></script>
<script type="text/javascript" src="<?php echo WWWSTATIC2 . VERSION ."/views/js/jHtmlArea.ColorPickerMenu-0.7.0.min.js"?>"></script>
<script type="text/javascript" src="<?php echo WWWSTATIC2 . VERSION ."/views/js/jquery.fileDownload.js"?>"></script>


<!--  -->
</script>
<!-- The Templates plugin is included to render the upload/download listings -->
<script src="<?php echo WWWSTATIC2 . VERSION ."/views/js/tmpl.min.js"?>"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="<?php echo WWWSTATIC2 . VERSION ."/views/js/load-image.min.js"?>"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="<?php echo WWWSTATIC2 . VERSION ."/views/js/canvas-to-blob.min.js"?>"></script>
<!-- blueimp Gallery script -->
<script src="<?php echo WWWSTATIC2 . VERSION ."/views/js/jquery.blueimp-gallery.min.js"?>"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="<?php echo WWWSTATIC2 . VERSION ."/views/js/jquery.iframe-transport.js"?>"></script>
<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE 8 and IE 9 -->
<!--[if (gte IE 8)&(lt IE 10)]>
<script src="<?php echo WWWSTATIC2 . VERSION ."/views/js/cors/jquery.xdr-transport.js"?>"></script>
<![endif]-->

<!--  -->

<script type="text/javascript" src="<?php echo WWWSTATIC2 . VERSION ."/views/js/textChange.js"?>"></script>
<script type="text/javascript" src="<?php echo WWWSTATIC2 . VERSION ."/views/js/tooltip.jquery.js"?>"></script>
<script type="text/javascript" src="<?php echo WWWSTATIC2 . VERSION ."/views/js/jquery.styledselect.js"?>"></script>
<script type="text/javascript" src="<?php echo WWWSTATIC2 . VERSION ."/views/js/touchable.js"?>"></script>
<script type="text/javascript" src="<?php echo WWWSTATIC2 . VERSION ."/views/js/cufon-yui.js"?>"></script>
<script type="text/javascript" src="<?php echo WWWSTATIC2 . VERSION ."/views/js/video.js"?>"></script>
<script type="text/javascript" src="<?php echo WWWSTATIC2 . VERSION ."/views/js/jquery.jqplot.min.js"?>"></script>
<script type="text/javascript" src="<?php echo WWWSTATIC2 . VERSION ."/views/js/jqplot.barRenderer.min.js"?>"></script>
<script type="text/javascript" src="<?php echo WWWSTATIC2 . VERSION ."/views/js/jqplot.categoryAxisRenderer.min.js"?>"></script>
<script type="text/javascript" src="<?php echo WWWSTATIC2 . VERSION ."/views/js/jqplot.cursor.min.js"?>"></script>
<script type="text/javascript" src="<?php echo WWWSTATIC2 . VERSION ."/views/js/jqplot.dateAxisRenderer.min.js"?>"></script>
<script type="text/javascript" src="<?php echo WWWSTATIC2 . VERSION ."/views/js/jqplot.highlighter.min.js"?>"></script>
<script type="text/javascript" src="<?php echo WWWSTATIC2 . VERSION ."/views/js/jquery.balloon.min.js"?>"></script>
<script type="text/javascript" src="<?php echo WWWSTATIC2 . VERSION ."/fonts/Myriad_Pro_400-Myriad_Pro_700-Myriad_Pro_700-Myriad_Pro_italic_700.font.js"?>"></script>
<script type="text/javascript" src="<?php echo WWWSTATIC2 . VERSION ."/views/js/jquery.mCustomScrollbar.js"?>"></script>
<script type="text/javascript" src="<?php echo WWWSTATIC2 . VERSION ."/views/js/jquery.mousewheel.min.js"?>"></script>
<script type="text/javascript" src="<?php echo WWWSTATIC1 . VERSION ."/views/js/jquery.tinycarousel.min.js"?>"></script>
<script type="text/javascript" src="<?php echo WWWSTATIC1 . VERSION ."/views/js/fmDrawCarousel.js"?>"></script>

<!--[if IE 9]>
  <link rel="stylesheet" type="text/css" href="<?php echo WWWSTATIC1.VERSION."/"; ?>views/style/ie9.css" />
<![endif]-->
<!--[if IE 8]>
  <link rel="stylesheet" type="text/css" href="<?php echo WWWSTATIC1.VERSION."/"; ?>views/style/ie8.css" />
<![endif]-->
<!--[if IE 7]>
  <link rel="stylesheet" type="text/css" href="<?php echo WWWSTATIC1.VERSION."/"; ?>views/style/ie7.css" />
<![endif]-->
<?php
if(EXTRASTYLE) {
	$styles = explode(",", EXTRASTYLE);
	foreach ($styles as $style): ?>
	<link rel="stylesheet" type="text/css" href="<?php echo WWWSTATIC1 . VERSION ."/views/style/".$style?>"></link>
	<?php endforeach;
}?>




<script type="text/javascript">
var userAgent = navigator.userAgent.toLowerCase();
var chrome = /chrome/.test(navigator.userAgent.toLowerCase());
var mozilla = /firefox/.test(navigator.userAgent.toLowerCase());
var ie = /msie/.test(navigator.userAgent.toLowerCase());
var ie10 = /msie 10/.test(navigator.userAgent.toLowerCase());
<?php

if(isIOSGreaterOrEqualThan(6)){
?>
(function (window) {

    // This library re-implements setTimeout, setInterval, clearTimeout, clearInterval for iOS6.
    // iOS6 suffers from a bug that kills timers that are created while a page is scrolling.
    // This library fixes that problem by recreating timers after scrolling finishes (with interval correction).
// This code is free to use by anyone (MIT, blabla).
// Author: rkorving@wizcorp.jp

    var timeouts = {};
    var intervals = {};
    var orgSetTimeout = window.setTimeout;
    var orgSetInterval = window.setInterval;
    var orgClearTimeout = window.clearTimeout;
    var orgClearInterval = window.clearInterval;


    function createTimer(set, map, args) {
            var id, cb = args[0], repeat = (set === orgSetInterval);

            function callback() {
                    if (cb) {
	                    	try{
    	                        cb.apply(window, arguments);
							}catch(err){
								eval(cb);
							}

                            if (!repeat) {
                                    delete map[id];
                                    cb = null;
                            }
                    }
            }

            args[0] = callback;

            id = set.apply(window, args);

            map[id] = { args: args, created: Date.now(), cb: cb, id: id };

            return id;
    }


    function resetTimer(set, clear, map, virtualId, correctInterval) {
            var timer = map[virtualId];

            if (!timer) {
                    return;
            }

            var repeat = (set === orgSetInterval);

            // cleanup

            clear(timer.id);

            // reduce the interval (arg 1 in the args array)

            if (!repeat) {
                    var interval = timer.args[1];

                    var reduction = Date.now() - timer.created;
                    if (reduction < 0) {
                            reduction = 0;
                    }

                    interval -= reduction;
                    if (interval < 0) {
                            interval = 0;
                    }

                    timer.args[1] = interval;
            }

            // recreate

            function callback() {
                    if (timer.cb) {
                            timer.cb.apply(window, arguments);
                            if (!repeat) {
                                    delete map[virtualId];
                                    timer.cb = null;
                            }
                    }
            }

            timer.args[0] = callback;
            timer.created = Date.now();
            timer.id = set.apply(window, timer.args);
    }


    window.setTimeout = function () {
            return createTimer(orgSetTimeout, timeouts, arguments);
    };


    window.setInterval = function () {
            return createTimer(orgSetInterval, intervals, arguments);
    };

    window.clearTimeout = function (id) {
            var timer = timeouts[id];

            if (timer) {
                    delete timeouts[id];
                    orgClearTimeout(timer.id);
            }
    };

    window.clearInterval = function (id) {
            var timer = intervals[id];

            if (timer) {
                    delete intervals[id];
                    orgClearInterval(timer.id);
            }
    };

    window.addEventListener('scroll', function () {
            // recreate the timers using adjusted intervals
            // we cannot know how long the scroll-freeze lasted, so we cannot take that into account

            var virtualId;

            for (virtualId in timeouts) {
                    resetTimer(orgSetTimeout, orgClearTimeout, timeouts, virtualId);
            }

            for (virtualId in intervals) {
                    resetTimer(orgSetInterval, orgClearInterval, intervals, virtualId);
            }
    });

}(window));
<?php
}

if(!getTimezoneOffset()){
?>
	var date = new Date();
	$.post("<?php echo WWWROOT ?>actions/user/setTimezone.php", { offset: date.getTimezoneOffset() * -1} );
<?php
}
?>

userAgent = navigator.userAgent.toLowerCase();
chrome = /chrome/.test(navigator.userAgent.toLowerCase());
mozilla = /firefox/.test(navigator.userAgent.toLowerCase());
ie = /msie/.test(navigator.userAgent.toLowerCase());
ie10 = /msie 10/.test(navigator.userAgent.toLowerCase());


if(chrome){
document.write('<link rel="stylesheet" type="text/css" href="<?php echo WWWSTATIC1.VERSION."/"; ?>views/style/chrome.css" />');
}
if(mozilla){
document.write('<link rel="stylesheet" type="text/css" href="<?php echo WWWSTATIC1.VERSION."/"; ?>views/style/ff.css" />');
}
if(ie){
	document.write('<link rel="stylesheet" type="text/css" href="<?php echo WWWSTATIC1.VERSION."/"; ?>views/style/ie.css" />');
}
if(ie10){
	document.write('<link rel="stylesheet" type="text/css" href="<?php echo WWWSTATIC1.VERSION."/"; ?>views/style/ie10.css" />');
}

var startLoadingOnAjax = true;

$(document).ready(function() {

	// Click Action
	$(".inSessionLink").click(function(e){
		e.preventDefault();
		$.fancybox({
				'type'				: 'iframe',
				'autoDimensions'	: false,
				'width'         	: 500,
				'height'        	: 360,
				'transitionIn'		: 'none',
				'transitionOut'		: 'none',
				'href'				: 'http://www.nearpod.com/wp-content/themes/nearpod2012Theme/inSession.php'
			});
		$('#fancybox-wrap').unbind('mousewheel.fb')
	})



	$.datepicker.setDefaults( $.datepicker.regional["<?php echo getLanguage();?>"] );
	$.datepicker.formatDate('<?php echo langEcho("date:format:real");?>');

	$('#loading').ajaxStart(function() {
			if(startLoadingOnAjax){
	    		$(this).show();
			}
	}).ajaxComplete(function() {
		if(startLoadingOnAjax){
	    	$(this).hide();
		}
	});
	$("form").submit(function() {
		if (!$(this).is('.noLoading')){
			$('#loading').show();
		}
		return true;
	});

	addAjaxSubmit("form:not([regularPost])", false);

	//Cufon.replace('.glossyButton, h1, h2, h3, #quotes .mainTxt');

    $("ul.topnav li a").click(function(event) { //When trigger is clicked...
    	event.stopPropagation();

        //Following events are applied to the subnav itself (moving subnav up and down)
        $(this).parent().find("ul.subnav").slideDown('fast').show(); //Drop down the subnav on click

        $(this).parent().hover(function() {
        }, function(){
            $(this).parent().find("ul.subnav").slideUp('slow'); //When the mouse hovers out of the subnav, move it back up
        });

        <?php if(isMobile()){?>
        	document.addEventListener('touchstart', function (e) {
            	// a -> li -> ul
        		if(e.target.parentNode.parentNode.id != 'headerSubMenu'){
        			$("ul.subnav").slideUp('slow'); //When the mouse hovers out of the subnav, move it back up
        		}
        });
        <?php }?>

        //Following events are applied to the trigger (Hover events for the trigger)
        }).hover(function() {
            $(this).addClass("subhover"); //On hover over, add class "subhover"
        }, function(){  //On Hover Out
            $(this).removeClass("subhover"); //On hover out, remove class "subhover"
    });

});

var currentLang = '<?php echo getLanguage();?>';
var wwwroot = '<?php echo WWWROOT?>';
var wwwstatic = '<?php echo WWWSTATIC?>';

function goToPage(page){
	document.location = "<?php echo WWWROOT?>" +page;
}


</script>
</head>
