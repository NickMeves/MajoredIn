$(document).ready(function() {
	Socialite.setup({
	    facebook: {
	        lang: 'en_US',
	        appId: 621030121246066
	    },
		googleplus: {
	        lang: 'en-US'
	    }
	});
	
	var social = $('.social-sharing');
	if (!(majoredin.utils.ie < 8) && social.css('display') != 'none') {
		Socialite.load(social);
		setTimeout(function () {
			social.hide();
			social.removeClass('hidden');
			social.fadeIn();
		}, 1500);
	}
	
	var timeout;
	$('.social-sharing .socialite').mouseenter(function () {
		timeout = setTimeout(function () {
			amplify.store('has_shared', true);
		}, 500);
	}).mouseleave(function () {
		clearTimeout(timeout);
	});
});