<?php //echo loadView('navigation/header');?>
<body>
	<!-- Slideshow HTffML -->
	<?php //echo loadView('navigation/tab');?>
	<div id="slideshow">
	  <div id="slidesContainer">
	    <div class="slide">
			<img src="images/img_slide_01.jpg" alt="Smiley face" width="560" height="263">
	    </div>
	    <div class="slide">
			<img src="images/img_slide_02.jpg" alt="Smiley face">
	    </div>
	    <div class="slide">
			<img src="images/img_slide_03.jpg" alt="Smiley face">
	    </div>
	    <div class="slide">
			<img src="images/img_slide_04.jpg" alt="Smiley face">
	    </div>
	  </div>
	</div>
	<!-- Slideshow HTML -->
</body>

<script>
$(document).ready(function(){
  var currentPosition = 0;
  var slideWidth = 560;
  var slides = $('.slide');
  var numberOfSlides = slides.length;
  
  // Remove scrollbar in JS
  $('#slidesContainer').css('overflow', 'hidden');
  
  // Wrap all .slides with #slideInner div
  slides
  .wrapAll('<div id="slideInner"></div>')
  // Float left to display horizontally, readjust .slides width
  .css({
    'float' : 'left',
    'width' : slideWidth
  });
  
  // Set #slideInner width equal to total width of all slides
  $('#slideInner').css('width', slideWidth * numberOfSlides);
  
  // Insert left and right arrow controls in the DOM
  $('#slideshow')
    .prepend('<span class="control" id="leftControl">Move left</span>')
    .append('<span class="control" id="rightControl">Move right</span>');

  // Hide left arrow control on first load
  manageControls(currentPosition);
 
  // Create event listeners for .controls clicks
  $('.control')
    .bind('click', function(){
    // Determine new position
      currentPosition = ($(this).attr('id')=='rightControl') 
    ? currentPosition+1 : currentPosition-1;
  
      // Hide / show controls
      manageControls(currentPosition);
      // Move slideInner using margin-left
      $('#slideInner').animate({
        'marginLeft' : slideWidth*(-currentPosition)
      });
    });
  
  // manageControls: Hides and shows controls depending on currentPosition
  function manageControls(position){
    // Hide left arrow if position is first slide
    if(position==0){ $('#leftControl').hide() }
    else{ $('#leftControl').show() }
    // Hide right arrow if position is last slide
    if(position==numberOfSlides-1){ $('#rightControl').hide() } 
    else{ $('#rightControl').show() }
    } 
  });
</script>
