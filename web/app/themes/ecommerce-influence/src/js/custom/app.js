$(function(){
	//PLACEHOLDER PLUGIN FOR LEGACY BROWSERS
	$('input, textarea').placeholder();

	$('.topDripForm__closeButton').on('click', function(e){
		$('.topDripForm__inner').addClass('topDripForm__inner--close');
	});
});