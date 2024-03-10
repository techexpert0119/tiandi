$(function() {
	var slideshow = $("#slippry").slippry({
    controls: false
		// transition: 'fade',
		// useCSS: true,
		// speed: 1000,
		// pause: 3000,
		// auto: true,
		// preload: 'visible',
		// autoHover: false
	});

	$('.stop').click(function () {
		slideshow.stopAuto();
	});

	$('.start').click(function () {
		slideshow.startAuto();
	});

	$('.prev').click(function () {
		slideshow.goToPrevSlide();
		return false;
	});
	$('.next').click(function () {
		slideshow.goToNextSlide();
		return false;
	});
	$('.reset').click(function () {
		slideshow.destroySlider();
		return false;
	});
	$('.reload').click(function () {
		slideshow.reloadSlider();
		return false;
	});
	$('.init').click(function () {
		slideshow = $("#slippry").slippry();
		return false;
	});
});