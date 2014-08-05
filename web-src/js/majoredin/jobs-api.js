(function (majoredin) {
	var jobsApi = majoredin.jobsApi = majoredin.jobsApi || {};
	
	jobsApi.populateApiJobs = function (element, callback) {
		var url = element.data('href');
		if (!url) {
			url = "/jobs-api/v1/undeclared";
		}
		
		url = majoredin.utils.updateQueryString(url, 'callback', '?');
		
		var limit = element.data('limit');
		if (limit == null) {
			limit = 10;
		}
		
		$.getJSON(url, function (data) {
			$.each(data, function (index, listing) {
				if (index < limit) {
					var decoder = $('<div/>'); //used with html().text() hack
					var job = {
						'wrapper': $('<div/>', {
							'class': 'api-job'
						}),
						'title': $('<h3/>', {
							'class': 'api-job-title'
						}),
						'titleLink': $('<a/>', {
							'href': decoder.html(listing['url']).text(),
							'rel': 'nofollow',
							'target': '_blank',
							'onmousedown': 'xml_sclk(this);',
							'text': decoder.html(listing['title']).text()
						}),
						'info': $('<div/>', {
							'class': 'api-job-info',
							'text': decoder.html(listing['company'] + ' - ' + listing['location']).text()
						})
					};
					
					job.title.appendTo(job.wrapper);
					job.titleLink.appendTo(job.title);
					job.info.appendTo(job.wrapper);
					job.wrapper.appendTo(element);
				}
			});
			
			callback(element);
		});
		
		return;
	};
	
})(this.majoredin = this.majoredin || {});

$(document).ready(function() {
	var boxes = $('.jobs-api-box');
	
	boxes.hide();
	boxes.removeClass('hidden');
	
	boxes.each(function () {
		var box = $(this);
		majoredin.jobsApi.populateApiJobs(box, function (box) {
			box.slideDown();
		});
	});
});