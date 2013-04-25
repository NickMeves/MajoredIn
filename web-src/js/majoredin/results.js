$(document).ready(function () {
	//Hack to fix IE8 max-width overflow hidden bug.
	if (majoredin.utils.ie == 8) {
		$('.new-wrapper .new-job + .job-title').each(function () {
				$(this).width($(this).width());
		});
	}
	
	$('.pagination .next a').waypoint(function() {
		majoredin.utils.preloadUrl($(this).attr('href'));
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
		
		if (count % 10 == 3 && !amplify.store('has_shared')) {
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
});