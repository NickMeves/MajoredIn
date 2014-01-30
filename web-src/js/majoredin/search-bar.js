$(document).ready(function () {
	if (majoredin.utils.ie < 8) {
		$('.placeholding-input input')
			.focus(function() {
				$(this).addClass('ie-input-focus');
			}).blur(function() {
				$(this).removeClass('ie-input-focus');
			});
	}
	
	$('.placeholding-input input')
		.keydown(function (e) {
			if (e.which != 8 || $(this).val() != '') {
				$(this).siblings('.placeholder').hide();
			}
		}).blur(function () {
			if ($(this).val() == '') {
				$(this).siblings('.placeholder').show();
			}
		}).keyup(function () {
			if ($(this).val() == '') {
				$(this).siblings('.placeholder').show();
			}
		});
	
	$('#search-bar-form').submit(function(event) {
        event.preventDefault();
        
        var major = majoredin.utils.dash($('#search-major').val());
        major = (major == '') ? '/undeclared' : '/' + major;
        
        var location = majoredin.utils.dash($('#search-location').val());
        location = (location == '') ? '' : '/' + location;
        
        window.location.href = $(this).attr('action') + major + location;
    });
	
	majoredin.utils.autocomplete($('#search-major'),'autocomplete_major_', '/autocomplete/major?term=', amplify.store.memory, function (selected) {
		majoredin.utils.preloadSearch(selected, $('#search-location').val());
	});
	
	majoredin.utils.autocomplete($('#search-location'), 'autocomplete_location_', '/autocomplete/location?term=', amplify.store.memory, function (selected) {
		majoredin.utils.preloadSearch($('#search-major').val(), selected);
	});
	
/***********************
 * Header Lock
 ***********************/
	
	if (!majoredin.globals.isMobile) {
		var nav = $('#nav-header');
		var search = $('#search-header-wrap');
		
		var offset = search.offset().top;
		var fixed = false;
		
		nav.find('.btn-navbar').click(function () {
			var t = setTimeout(function () {
				offset = search.offset().top;
			}, 500); // Hack to get as last click event
		});
		
		$(window).scroll(function(){
			if($(this).scrollTop() > offset) {
				if (!fixed) {
					nav.css({
						height: nav.height() + search.height() + 'px'
					});
				    search.css({
				    	position: 'fixed',
				    	top: '0'
				    });
				    
				    fixed = true;
				}
			}
			else {
				if (fixed) {
					nav.css({
						height: ''
					});
					search.css({
						position: '',
						top: ''
					});
					
					fixed = false;
				}
			}
		});
	}
});