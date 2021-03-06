(function (majoredin) {
	majoredin.eventTracking = true;
})(this.majoredin = this.majoredin || {});

$(document).ready(function () {
	var major = $('#search-major').val();
	if (major == '') {
		major = 'Undeclared';
	}
	
	//Job clicks
	$('.sponsored .job-title').click(function () {
		_gaq.push(['_trackEvent', 'Jobs', 'Sponsored Click', major]);
	});
	
	$('.organic .job-title').click(function () {
		_gaq.push(['_trackEvent', 'Jobs', 'Organic Click', major]);
	});
	
	//Cache logging
	if ($('#search .cache-hit').length) {
		_gaq.push(['_trackEvent', 'Cache', 'Hit', major]);
	}
	
	if ($('#search .cache-miss').length) {
		_gaq.push(['_trackEvent', 'Cache', 'Miss', major]);
	}
	
	//Error logging
	if ($('.error500').length) {
		_gaq.push(['_trackEvent', 'Error', '500', window.location.href]);
	}
	
	if ($('.error501').length) {
		_gaq.push(['_trackEvent', 'Error', '501', window.location.href]);
	}
	
	if ($('.error502').length) {
		_gaq.push(['_trackEvent', 'Error', '502', window.location.href]);
	}
	
	if ($('.error504').length) {
		_gaq.push(['_trackEvent', 'Error', '504', window.location.href]);
	}
	
	if ($('.error404').length) {
		_gaq.push(['_trackEvent', 'Error', '404', window.location.href]);
	}
	
	//Social
	$('.facebook-follow').click(function () {
		_gaq.push(['_trackEvent', 'Follow', 'Facebook', major]);
	});
	$('.twitter-follow').click(function () {
		_gaq.push(['_trackEvent', 'Follow', 'Twitter', major]);
	});
	$('.googleplus-follow').click(function () {
		_gaq.push(['_trackEvent', 'Follow', 'Google+', major]);
	});
	$('.linkedin-follow').click(function () {
		_gaq.push(['_trackEvent', 'Follow', 'LinkedIn', major]);
	});
	$('.pinterest-follow').click(function () {
		_gaq.push(['_trackEvent', 'Follow', 'Pinterest', major]);
	});
	
	var timeout;
	$('.social-wrapper-front .social-sharing .facebook').mouseenter(function () {
		timeout = setTimeout(function () {
			_gaq.push(['_trackEvent', 'Share - Homepage', 'Facebook']);
		}, 500);
	}).mouseleave(function () {
		clearTimeout(timeout);
	});
	$('#shareModal .social-sharing .facebook').mouseenter(function () {
		timeout = setTimeout(function () {
			_gaq.push(['_trackEvent', 'Share - Popup', 'Facebook', major]);
		}, 500);
	}).mouseleave(function () {
		clearTimeout(timeout);
	});
	$('.social-wrapper-front .social-sharing .twitter').mouseenter(function () {
		timeout = setTimeout(function () {
			_gaq.push(['_trackEvent', 'Share - Homepage', 'Twitter']);
		}, 500);
	}).mouseleave(function () {
		clearTimeout(timeout);
	});
	$('#shareModal .social-sharing .twitter').mouseenter(function () {
		timeout = setTimeout(function () {
			_gaq.push(['_trackEvent', 'Share - Popup', 'Twitter', major]);
		}, 500);
	}).mouseleave(function () {
		clearTimeout(timeout);
	});
	$('.social-wrapper-front .social-sharing .googleplus').mouseenter(function () {
		timeout = setTimeout(function () {
			_gaq.push(['_trackEvent', 'Share - Homepage', 'Google+']);
		}, 500);
	}).mouseleave(function () {
		clearTimeout(timeout);
	});
	$('#shareModal .social-sharing .googleplus').mouseenter(function () {
		timeout = setTimeout(function () {
			_gaq.push(['_trackEvent', 'Share - Popup', 'Google+', major]);
		}, 500);
	}).mouseleave(function () {
		clearTimeout(timeout);
	});
	$('.social-wrapper-front .social-sharing .linkedin').mouseenter(function () {
		timeout = setTimeout(function () {
			_gaq.push(['_trackEvent', 'Share - Homepage', 'LinkedIn']);
		}, 500);
	}).mouseleave(function () {
		clearTimeout(timeout);
	});
	$('#shareModal .social-sharing .linkedin').mouseenter(function () {
		timeout = setTimeout(function () {
			_gaq.push(['_trackEvent', 'Share - Popup', 'LinkedIn', major]);
		}, 500);
	}).mouseleave(function () {
		clearTimeout(timeout);
	});
});