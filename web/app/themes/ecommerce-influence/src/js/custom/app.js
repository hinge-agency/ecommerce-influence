$(function(){

	var formheight = 0;
	var socialTop = 0;
	var newheight = 0;
	var padding = 60;
	var height = "";

	if ($('.topDripForm').length){
		var formheight = $('.topDripForm').height(); // TOP DRIP FORM INITIAL HEIGHT
	}

	var masthead = $('.masthead').height(); // HEADER INITIAL HEIGHT
	var requiredheight = (formheight - 1) + masthead; // REQUIRED HEIGHT
	var offset = $(document).scrollTop(); // DETERMINE HOW MUCH WE SCROLLED
	
	//Calculate the top position needed for the social banner
	if(offset < requiredheight){
		socialTop  = (requiredheight + padding - offset);
	}else if(offset > requiredheight && $('.topDripForm').length){
		socialTop = (masthead + padding);
	}else{
		socialTop = (requiredheight + padding);
	}
	
	//Set interval to set social banner position - this is in case of slow network and load time of the plugin
	var setSocial = setInterval(function(){ 

		if( $('#at4-share').length ){

			$('#at4-share').addClass('socialPlugin');
			$('#at4-share').attr('style', 'top: ' + socialTop + 'px !important');

			clearInterval(setSocial);			
		}

	}, 2000);

	//Force Stop Interval
	setTimeout(function(){
		clearInterval(setSocial);
	}, 20000);

	//PLACEHOLDER PLUGIN FOR LEGACY BROWSERS
	$('input, textarea').placeholder();

	//CLOSE TOP DRIP FORM
	$('.topDripForm__closeButton').on('click', function(e){
		e.preventDefault();

		// When closed, set a cookie to prevent the popup showing again until site is revisited
		Cookies.set('hidetopdripform', true);

		$('.topDripForm__inner').addClass('topDripForm__inner--close');

		requiredheight = masthead;

		$('.masthead__nav-responsive').css({"top": requiredheight, "height" : 'calc(100% - ' + requiredheight + 'px)'});
		$('#at4-share').attr('style', 'top: ' + (requiredheight + padding) + 'px !important');
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
		if (!$('.topDripForm__inner--close').length){

			setTimeout(function() { 
		  		formheight = $('.topDripForm').height();
		  		masthead = $('.masthead').height()
				requiredheight = (formheight - 1) + masthead;
				newheight = requiredheight - offset;
				height = newheight + "px";

				$('.masthead__nav-responsive').css({"top": height, "height" : 'calc(100% - ' + height + ')'});
			}, 300);
		}

		// Set equal height for each slide
		setTimeout(function() { 
			$('.stories__bottom-carousel').on('setPosition', function () {

		      $(this).find('.stories__bottom-carousel-item').height('auto');
		      var slickTrack = $(this).find('.slick-track');
		      var slickTrackHeight = $(slickTrack).height();
		      $(this).find('.stories__bottom-carousel-item').css('height', slickTrackHeight + 'px');  

	     	});

		}, 300); 
	});

	// Watch scroll
	$(window).scroll(function(){
		if ($('.topDripForm__inner').length){
			var setoffset = $(document).scrollTop();

			if (setoffset < requiredheight){
				offset = setoffset;

				socialTop = (requiredheight + padding - offset);
				
				$('#at4-share').attr('style', 'top: ' + socialTop + 'px !important');

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
		        infinite: true,
		        slidesToShow: 1
		      }
		    },
		    {
		      breakpoint: 601,
		      settings: {
		        infinite: true,
		        slidesToShow: 1
		      }
		    }
		  ]
	  });

	$('.relatedContent__carousel').slick({
		infinite: true,
		dots: true,
		slidesToShow: 3,
		slidesToScroll: 3,
		prevArrow: $('.relatedContent__topBar-pagination-button-link--left'),
		nextArrow: $('.relatedContent__topBar-pagination-button-link--right'),

		 responsive: [
		    {
		      breakpoint: 1000,
		      settings: {
		        slidesToShow: 2,
				slidesToScroll: 2
		      }
		    },
		    {
		      breakpoint: 600,
		      settings: {
		        slidesToShow: 1,
				slidesToScroll: 1
		      }
		    }
		  ]
	});

	//Set equal height for each slide
	$('.stories__bottom-carousel').on('setPosition', function () {

	      $(this).find('.stories__bottom-carousel-item').height('auto');
	      var slickTrack = $(this).find('.slick-track');
	      var slickTrackHeight = $(slickTrack).height();
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

	 // RESPONSIVE SEARCH
	 $('.masthead__right-openSearch').on('click', function(e){

	 	e.preventDefault();

	 	$(this).hide();
	 	$('.masthead__right-responsive').hide();
	 	$('.masthead__left-logo').hide();

	 	$('.masthead__inner').addClass('responsive-open-search-active');
	 	$('.masthead__left').addClass('responsive-open-search-active');
	 	$('.masthead__right').addClass('responsive-open-search-active');
	 	$('.masthead__right-search-input').addClass('responsive-open-search-active');
	 	$('.masthead__right-search').addClass('responsive-open-search-active');
	 	$('.masthead__right-closeSearch').addClass('responsive-open-search-active');

	 	$('.masthead__right-search-input').focus();
	 });

	 $('.masthead__right-closeSearch').on('click', function(e){

	 	e.preventDefault();

	 	$(this).removeClass('responsive-open-search-active');
	 	$('.masthead__right-responsive').show();
	 	$('.masthead__right-openSearch').show();
	 	$('.masthead__left-logo').show();

	 	$('.masthead__inner').removeClass('responsive-open-search-active');
	 	$('.masthead__left').removeClass('responsive-open-search-active');
	 	$('.masthead__right').removeClass('responsive-open-search-active');
	 	$('.masthead__right-search-input').removeClass('responsive-open-search-active');
	 	$('.masthead__right-search').removeClass('responsive-open-search-active');
	 	$('.masthead__right-closeSearch').removeClass('responsive-open-search-active');
	 });

	$(".topDripForm__form-block-button").on('click', function() {

		var errors = $(".topDripForm__form-errors");
		errors.hide();

		if ( $("#drip-First-Name").val() && isEmail($("#drip-email").val()) ) {
			// Form submitted, don't show again for 28 days
			Cookies.set('hidetopdripform', true, { expires: 28 });
			$(".topDripForm__form").submit();
		} else {
			errors.show();
		}
	});

	function isEmail(email) {
		var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	    return regex.test(email);
	}

	/* Toggle Transcript */
	$('.episode__transcript-toggle').on('click', function() {

	    var show = $(this).find('.episode__transcript-show');
        var hide = $(this).find('.episode__transcript-hide');
        var transcript = $( this ).parent().next('.episode__content--transcript');

        show.toggle();
        hide.toggle();
        transcript.toggle();

    });

});
