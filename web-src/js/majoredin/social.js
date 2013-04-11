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
	
	if (! (majoredin.utils.ie < 8)) {
		var social = $('.social-sharing');
		Socialite.load(social);
		setTimeout(function () {
			social.hide();
			social.removeClass('hidden');
			$('.social-sharing').fadeIn();
		}, 1250);
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