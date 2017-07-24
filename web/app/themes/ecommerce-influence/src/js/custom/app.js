$(function(){
	//PLACEHOLDER PLUGIN FOR LEGACY BROWSERS
	$('input, textarea').placeholder();

	$('.topDripForm__closeButton').on('click', function(e){
		e.preventDefault();
		$('.topDripForm__inner').addClass('topDripForm__inner--close');

		var formheight = $('.topDripForm').height();
		var requiredheight = formheight + 80;
			requiredheight = requiredheight + "px";

		$('.masthead__nav-responsive').css({"top": requiredheight, "height" : 'calc(100% - ' + requiredheight + ')'});
	});

	// Responsive navigation menu toggle
	$('.masthead__right-responsive-link').on('click', function(e){
		e.preventDefault();

		//Adjust position of the responsive navigiation
		var formheight = $('.topDripForm').height();
		var requiredheight = formheight + 80;
			requiredheight = requiredheight + "px";

		$('.masthead__nav-responsive').css({"top": requiredheight, "height" : 'calc(100% - ' + requiredheight + ')'});

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
		  	var formheight = $('.topDripForm').height();
			var requiredheight = formheight + 80;
				requiredheight = requiredheight + "px";

				console.log(formheight);

				$('.masthead__nav-responsive').css({"top": requiredheight, "height" : 'calc(100% - ' + requiredheight + ')'});
			}, 300);
		}
	});
});