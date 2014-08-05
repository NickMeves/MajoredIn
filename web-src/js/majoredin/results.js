(function (majoredin) {
	var results = majoredin.results = majoredin.results || {};
	
	results.preloadUrl = function (url, store) {
		url = url.replace(/^\/jobs/, '/precache');
		$.get(url);
		return;
	};
	
})(this.majoredin = this.majoredin || {});

$(document).ready(function () {
	$('.pagination .next a').waypoint(function() {
		majoredin.results.preloadUrl($(this).attr('href'));
	}, {
		triggerOnce: true,
		offset: 'bottom-in-view'
	});
	
	$('.job-title').click(function () {
		var count = amplify.store('job_click_count');
		if (count) {
			amplify.store('job_click_count', ++count);
		}
		else {
			count = 1;
			amplify.store('job_click_count', count);
		}
		
		if (count % 7 == 2 && !amplify.store('has_shared') && $('.social-sharing').css('display') != 'none') {
		    setTimeout(function () {
		    	var modal = $('#shareModal');
		    	modal.hide();
		    	modal.removeClass('hidden');
		    	$('#shareModal').modal({});
		    	
		    	if (majoredin.eventTracking) {
		    		var major = $('#search-major').val();
		    		if (major == '') {
		    			major = 'Undeclared';
		    		}
		    		_gaq.push(['_trackEvent', 'Modal', 'Popup', major]);
		    	}
		    }, 300);
		}
	});
	
	$('#advanced-button').click(function () {
		var modal = $('#advancedModal');
		modal.hide();
		modal.removeClass('hidden');
		modal.modal({});
	});
});