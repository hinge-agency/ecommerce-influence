$(function(){

	var formheight = $('.topDripForm').height();
	var requiredheight = formheight + 80;
	var offset = $(document).scrollTop();
	console.log(offset);
	var newheight = 0;

	var height = "";

	//PLACEHOLDER PLUGIN FOR LEGACY BROWSERS
	$('input, textarea').placeholder();

	$('.topDripForm__closeButton').on('click', function(e){
		e.preventDefault();
		$('.topDripForm__inner').addClass('topDripForm__inner--close');

			newheight = requiredheight + "px";

		$('.masthead__nav-responsive').css({"top": newheight, "height" : 'calc(100% - ' + newheight + ')'});
	});

	// Responsive navigation menu toggle
	$('.masthead__right-responsive-link').on('click', function(e){
		e.preventDefault();

			//Adjust position of the responsive navigiation
			console.log(offset + ' OFFSET');
			console.log(requiredheight + ' FORM');

			newheight = requiredheight - offset;
			height = newheight + "px";

			console.log(requiredheight + ' REQUIRED');

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
				requiredheight = formheight + 80;
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
				console.log(offset);
			}else{
				offset = requiredheight - 80;
				console.log(offset);
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
});