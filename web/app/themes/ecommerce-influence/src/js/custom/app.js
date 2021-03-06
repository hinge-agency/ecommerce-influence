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

	$(".bottomDripForm__form-block-button").on('click', function() {
		var errors = $(".bottomDripForm__form-errors");
		errors.hide();

		if ( $("#bottomDrip-First-Name").val() && isEmail($("#bottomDrip-email").val()) ) {
			$(".bottomDripForm__form").submit();
		} else {
			errors.show();
			return false;
		}
	});

	$(".popUpTemplate__formBlock-form-block-button").on('click', function() {
		var errors = $(".popUpTemplate__form-errors");
		errors.hide();

		if ( $("#popUpTemplate-First-Name").val() && isEmail($("#popUpTemplate-email").val()) ) {
			$(".popUpTemplate__formBlock-form").submit();
		} else {
			errors.show();
			return false;
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

		var previewTranscript = $('.episode__content-preview-transcript');
		var fullTranscript = $('.episode__content-full-transcript');

		show.toggle();
		hide.toggle();

		previewTranscript.toggleClass('episode__transcript-show');
		fullTranscript.toggleClass('episode__transcript-hide');

    });

	/* Toggle Related Posts */
    var related_posts_total = $('.related__post').length;
    var toggle_position = 0;
    /* Desktop */
    var show_posts = 3;
    if ($(window).width() <= 650) {
        show_posts = 2;
    }

    /* Page Load */
    if (related_posts_total <= show_posts) {
        $('.related__toggle').hide();
        $('.related__post').show();
    } else {

        $('.related__toggle-link--right').removeClass('disabled');

        var post_start = toggle_position * show_posts;
        var post_end = post_start + show_posts;

        var posts = $('.related__post');

        for (var i = 0; i < related_posts_total; i++) {


            if (i + 1 > post_end) {
                $(posts[i]).hide();
            } else {
                $(posts[i]).show();
            }

        }
    }

    $('.related__toggle-link--right').on('click', function() {

        toggle_position++;
        if (toggle_position >= related_posts_total / show_posts) {
            toggle_position--;
            $(this).addClass('disabled');
            return;
        } else if (toggle_position === (related_posts_total / show_posts - 1) || toggle_position === Math.floor(related_posts_total / show_posts)) {
            $(this).addClass('disabled');
            $('.related__toggle-link--left').removeClass('disabled');
        }else {
            $(this).removeClass('disabled');
            $('.related__toggle-link--left').removeClass('disabled');
        }

        showPosts();

    });

    $('.related__toggle-link--left').on('click', function() {

        toggle_position--;
        if (toggle_position < 0) { /* Can't toggle */
            toggle_position = 0;
            $(this).addClass('disabled');
            return;
        } else if (toggle_position === 0) { /* We are on the last toggle */
            $(this).addClass('disabled');
            $('.related__toggle-link--right').removeClass('disabled');
        } else { /* We can toggle */
            $(this).removeClass('disabled');
            $('.related__toggle-link--right').removeClass('disabled');
        }

        showPosts();
    });

    /* Show Similar Posts */
    function showPosts() {

        var post_start = toggle_position * show_posts;
        var post_end = post_start + show_posts;

        var posts = $('.related__post');

        for (var i = 1; i <= related_posts_total; i++) {
            if (i <= post_start || i > post_end) {
                $(posts[i-1]).hide();
            } else {
                $(posts[i-1]).show();
            }

        }
    }

});
