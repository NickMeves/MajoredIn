$(document).ready(function () {
	$('#add-filter-list .more').click(function () {
		$(this).siblings('.hide').slideDown(200);
		$(this).hide();
	});
});