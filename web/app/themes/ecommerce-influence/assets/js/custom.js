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
				offset = $(document).scrollTop();
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

});
/* HTML5 Placeholder jQuery Plugin - v2.3.1
 * Copyright (c)2015 Mathias Bynens
 * 2015-12-16
 */
!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):a("object"==typeof module&&module.exports?require("jquery"):jQuery)}(function(a){function b(b){var c={},d=/^jQuery\d+$/;return a.each(b.attributes,function(a,b){b.specified&&!d.test(b.name)&&(c[b.name]=b.value)}),c}function c(b,c){var d=this,f=a(this);if(d.value===f.attr(h?"placeholder-x":"placeholder")&&f.hasClass(n.customClass))if(d.value="",f.removeClass(n.customClass),f.data("placeholder-password")){if(f=f.hide().nextAll('input[type="password"]:first').show().attr("id",f.removeAttr("id").data("placeholder-id")),b===!0)return f[0].value=c,c;f.focus()}else d==e()&&d.select()}function d(d){var e,f=this,g=a(this),i=f.id;if(!d||"blur"!==d.type||!g.hasClass(n.customClass))if(""===f.value){if("password"===f.type){if(!g.data("placeholder-textinput")){try{e=g.clone().prop({type:"text"})}catch(j){e=a("<input>").attr(a.extend(b(this),{type:"text"}))}e.removeAttr("name").data({"placeholder-enabled":!0,"placeholder-password":g,"placeholder-id":i}).bind("focus.placeholder",c),g.data({"placeholder-textinput":e,"placeholder-id":i}).before(e)}f.value="",g=g.removeAttr("id").hide().prevAll('input[type="text"]:first').attr("id",g.data("placeholder-id")).show()}else{var k=g.data("placeholder-password");k&&(k[0].value="",g.attr("id",g.data("placeholder-id")).show().nextAll('input[type="password"]:last').hide().removeAttr("id"))}g.addClass(n.customClass),g[0].value=g.attr(h?"placeholder-x":"placeholder")}else g.removeClass(n.customClass)}function e(){try{return document.activeElement}catch(a){}}var f,g,h=!1,i="[object OperaMini]"===Object.prototype.toString.call(window.operamini),j="placeholder"in document.createElement("input")&&!i&&!h,k="placeholder"in document.createElement("textarea")&&!i&&!h,l=a.valHooks,m=a.propHooks,n={};j&&k?(g=a.fn.placeholder=function(){return this},g.input=!0,g.textarea=!0):(g=a.fn.placeholder=function(b){var e={customClass:"placeholder"};return n=a.extend({},e,b),this.filter((j?"textarea":":input")+"["+(h?"placeholder-x":"placeholder")+"]").not("."+n.customClass).not(":radio, :checkbox, [type=hidden]").bind({"focus.placeholder":c,"blur.placeholder":d}).data("placeholder-enabled",!0).trigger("blur.placeholder")},g.input=j,g.textarea=k,f={get:function(b){var c=a(b),d=c.data("placeholder-password");return d?d[0].value:c.data("placeholder-enabled")&&c.hasClass(n.customClass)?"":b.value},set:function(b,f){var g,h,i=a(b);return""!==f&&(g=i.data("placeholder-textinput"),h=i.data("placeholder-password"),g?(c.call(g[0],!0,f)||(b.value=f),g[0].value=f):h&&(c.call(b,!0,f)||(h[0].value=f),b.value=f)),i.data("placeholder-enabled")?(""===f?(b.value=f,b!=e()&&d.call(b)):(i.hasClass(n.customClass)&&c.call(b),b.value=f),i):(b.value=f,i)}},j||(l.input=f,m.value=f),k||(l.textarea=f,m.value=f),a(function(){a(document).delegate("form","submit.placeholder",function(){var b=a("."+n.customClass,this).each(function(){c.call(this,!0,"")});setTimeout(function(){b.each(d)},10)})}),a(window).bind("beforeunload.placeholder",function(){var b=!0;try{"javascript:void(0)"===document.activeElement.toString()&&(b=!1)}catch(c){}b&&a("."+n.customClass).each(function(){this.value=""})}))});