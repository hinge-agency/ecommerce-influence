$(function(){

	var formheight = $('.topDripForm').height(); // TOP DRIP FORM INITIAL HEIGHT
	var masthead = $('.masthead').height(); // HEADER INITIAL HEIGHT
	var requiredheight = formheight + masthead; // REQUIRED HEIGHT
	var offset = $(document).scrollTop(); // DETERMINE HOW MUCH WE SCROLLED

	var newheight = 0;
	var height = "";

	//PLACEHOLDER PLUGIN FOR LEGACY BROWSERS
	$('input, textarea').placeholder();

	$('.topDripForm__closeButton').on('click', function(e){
		e.preventDefault();
		$('.topDripForm__inner').addClass('topDripForm__inner--close');
		requiredheight = masthead;
	});

	// Responsive navigation menu toggle
	$('.masthead__right-responsive-link').on('click', function(e){
		e.preventDefault();

			//Adjust position of the responsive navigiation
			newheight = requiredheight - offset;
			height = newheight + "px";

		$('.masthead__nav-responsive').css({"top": height, "height" : 'calc(100% - ' + height + ')'});

		$('body,html').toggleClass('nav-active');
		$(this).toggleClass('active');
	});

	// Watch the screen width and remove the nav active class if the screen > 1300
	$(window).resize(function(){
		if ($(window).width() >= 1250 && $('.nav-active')){
			$('body,html').removeClass('nav-active');
			$('.masthead__right-responsive-link').removeClass('active');

		}

		// Only run this if responsive navigation and topDripForm are active
		if (!$('.topDripForm__inner--close').length && $('.nav-active').length){

			setTimeout(function() { 
		  		formheight = $('.topDripForm').height();
		  		masthead = $('.masthead').height()
				requiredheight = formheight + masthead;
				newheight = requiredheight - offset;
				height = newheight + "px";

				$('.masthead__nav-responsive').css({"top": height, "height" : 'calc(100% - ' + height + ')'});
			}, 300);
		}

		// Set equal height for each slide
		setTimeout(function() { 
			$('.stories__bottom-carousel').on('setPosition', function () {

		      $(this).find('.stories__bottom-carousel-item').height('auto');
		      var slickTrackHeight = $(this).height();
		      $(this).find('.stories__bottom-carousel-item').css('height', slickTrackHeight + 'px');     
	     	})
		}, 300); 
	});

	// Watch scroll
	$(window).scroll(function(){
		if ($('.topDripForm__inner').length){
			var setoffset = $(document).scrollTop();

			if (setoffset < requiredheight){
				offset = setoffset;
			}else{
				offset = masthead;
			}
		}
	});

	// Slick Carousel
	$('.stories__bottom-carousel').slick({
			infinite: true,
			slidesToShow: 2,
			slidesToScroll: 1,
			prevArrow: $('.stories__bottom-buttons-button-link--left'),
			nextArrow: $('.stories__bottom-buttons-button-link--right'),

		  responsive: [
		    {
		      breakpoint: 1001,
		      settings: {
		        arrows: false,
		        infinite: true,
		        slidesToShow: 1
		      }
		    },
		    {
		      breakpoint: 601,
		      settings: {
		        arrows: false,
		        infinite: true,
		        slidesToShow: 1
		      }
		    }
		  ]
	  });

	// Set equal height for each slide
	$('.stories__bottom-carousel').on('setPosition', function () {

	      $(this).find('.stories__bottom-carousel-item').height('auto');
	      var slickTrackHeight = $(this).height();
	      $(this).find('.stories__bottom-carousel-item').css('height', slickTrackHeight + 'px');

     });

	// RESPONSIVE FILTER MENU
	 $('#responsive-filter-menu').on('change', function() {
		var loc = $(this).val();

		if (loc) { 
		window.location = loc; // redirect
		}
		return false;
	 });
});