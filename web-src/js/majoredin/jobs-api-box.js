$(document).ready(function() {
	var boxes = $('.jobs-api-box');
	
	boxes.hide();
	boxes.removeClass('hidden');
	
	boxes.each(function () {
		var box = $(this);
		majoredin.utils.populateApiJobs(box, function (box) {
			box.slideDown();
		});
	});
});