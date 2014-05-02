<script type="text/javascript">
$(function() {

$(".imagelightbox").fancybox({
	'padding': 0,
	'type' : 'image',
	'hideOnOverlayClick' : false,
	'titleShow':false
});

$(".ajaxlightbox").fancybox({
	'padding': 0,
	'type' : 'ajax',
	'hideOnOverlayClick' : false,
	'titleShow':false,
	'centerOnScroll':<?php echo boolToTrueFalse(!isMobile())?>,
	'resizeOnWindowResize':<?php echo boolToTrueFalse(!isMobile())?>,
	'onStart': function() {
		disable('#smartUploadOverlay');
		fancyboxBackToMenu = {reopen: false};
	},
	'onCleanup' : function() {
		if (needCloseConfirmation !== undefined){
	        return confirmCloseFancybox();
		} else if (needCloseConfirmation === undefined && escKeyPressed == true){
			escKeyPressed = false;
			return false;
		}
		if (window.app && !app.isClosed &&
				(typeof app.close == "function")) {
			app.close();
			return false;
		}
    },
    'onClosed' : function(){
        needCloseConfirmation = false;
        escKeyPressed = false;
        enable('#smartUploadOverlay');
        if (fancyboxBackToMenu.reopen){
        	fancyboxGoToMenuOnClose(fancyboxBackToMenu.category);
        	fancyboxBackToMenu = {reopen: false};
        }
	},
	'onComplete' : function() {
		if(typeof callbackAjaxlightboxComplete === 'function') {
			return callbackAjaxlightboxComplete.call(this);
		}
	}
<?php /*?>
    'onCleanup': function(){
        if ( window.app &&
            !app.isClosed &&
            (typeof app.close == "function")) {
            app.close();
            return false;
        }
    }
    <?php */ ?>
});

$(".ajaxlightboxForms").fancybox({
	'padding': 0,
	'type' : 'ajax',
	'hideOnOverlayClick' : false,
	'titleShow':false,
	'showCloseButton' : false,
	'enableEscapeButton': false
});


$('.tooltip').tooltip({
	cursor:'pointer',
	bgcolor:'#ededed',
	bordercolor:'#b0b0b0',
	fontcolor:'#505050',
	fontsize:'13px',
	width:'0px',
	padding:'0px'
});

$('#fancybox-wrap').unbind('mousewheel.fb');

$(".touchable").addTouch();

$( ".sortable" ).sortable({tolerance: 'pointer', cancel:'.noSortable'}).bind('mousedown', function(oEvent, ui){
	oEvent.stopPropagation();
});


$("body").keydown(function(event) {
	if ( event.which == 27 ) {
		escKeyPressed = true;
	}
});




multiSortable('.multiSortable', 'selected');

});

</script>
