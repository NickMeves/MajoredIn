(function (majoredin) {
	var search = majoredin.search = majoredin.search || {};

	search.autocomplete = function (element, namespace, url, store, preload) {
		element.typeahead({
			source: function (query, process) {
				var items_max = 20; //value on server (bigger to enable precaching)
				var canon_query = majoredin.utils.canonicalize(query);
		    	var stored = store(namespace + canon_query);
		    	
		    	if (stored) { //if cached, serve cache
		    		process(stored);
		    		if (stored.length == 1) {
		    			preload(stored[0]);
		    		}
		    	}
		    	else if (canon_query.match(/[^a-z ]/)) { //if invalid characters, return blank
		    		process([]);
		    	}
		    	else {
		    		for (var i = canon_query.length; i > 0; --i) { //check if this or subword is already invalidated
		    			if (store('empty_' + namespace + canon_query.substr(0, i)) == 1) {
		    				process([]);
		    				return;
		    			}
		    		}
		    		
	    			$.ajax({
	    			    url: url + canon_query,
	    			    cache: "true",
	    			    dataType: "jsonp",
	    			    jsonpCallback: function () {
	    			    	majoredin[namespace + canon_query.replace(/[^\w]/g, '_')] = function(response) { //bind closure function to global scope
	    			    		var term = response['term'];
	    			    		var data = response['data'];
					    		if (element.val() == term) { //only process if still valid query
					    			process(data);
					    		}

					    		if (jQuery.isEmptyObject(data)) { //if empty, cache in empty cache
					    			store('empty_' + namespace + canon_query, 1);
					    		}
					    		
					    		else if (data.length < items_max) { //precache based on this results set for future
					    			for (var i = 0; i < data.length; ++i) {
						    			for (var j = query.length + 1; j <= data[i].length; ++j) {
						    				var matches = [];
						    				var new_query = majoredin.utils.canonicalize(data[i].substr(0,j));
						    				for (var k = 0; k < data.length; ++k) {
						    					if (majoredin.utils.canonicalize(data[k]).match(new RegExp('^' + new_query))) {
						    						matches.push(data[k]);
						    					}
						    				}
						    				store(namespace + new_query, matches);
						    			}
						    		}
						    		
						    		//Cache empty keys for query + 1 invalid letters
						    		var letters = 'abcdefghijklmnopqrstuvwxyz ';
						    		for (var i = 0; i < data.length; ++i) {
						    			letters = letters.replace(new RegExp(majoredin.utils.canonicalize(data[i]).charAt(query.length)), '');
						    		}
						    		for(var i = 0; i < letters.length; ++i) {
						    			store('empty_' + namespace + query + letters.charAt(i), 1);
						    		}
					    		}
					    		
					    		if (data.length == 1) {
					    			preload(data[0]);
					    		}
					    		store(namespace + canon_query, data);
					    		
					    		return data;
					    	};
					    	return "majoredin." + namespace + canon_query.replace(/[^\w]/g, '_');
	    			    }
			    	}); //ajax call
		    	}
		    }, //end typeahead source
		    matcher: function (item) {
	            return true;
		    },
		    sorter: function (items) {
		        return items;
		    },
		    highlighter: function (item) {
		    	var length = majoredin.utils.canonicalize(this.query).length;
		    	var diff = this.query.length - length;
		    	for (var i = 0; i < length + diff; ++i) {
		    		if (item.charAt(i).match(/[\,\'\.]/)) {
		    			length++;
		    		}
		    	}
		        var regex = new RegExp( '(' + item.substr(length) + ')$');
		        return item.replace(regex, "<strong>$1</strong>");
		    },
		    updater: function (item) {
		    	preload(item);
		        return item;
		    }
		});
	};
	
	search.preloadSearch = function(major, location) {
		var url = '/precache/' + major + '/' + location;
		var key = 'precached_' + url;
		if (major != '' && location != '' && amplify.store.memory(key) != true) {
			amplify.store.memory(key, true);
			$.get(url);
		}
		return;
	};
	
})(this.majoredin = this.majoredin || {});

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
	
	majoredin.search.autocomplete($('#search-major'),'autocomplete_major_', '/autocomplete/major?term=', amplify.store.memory, function (selected) {
		majoredin.search.preloadSearch(selected, $('#search-location').val());
	});
	
	majoredin.search.autocomplete($('#search-location'), 'autocomplete_location_', '/autocomplete/location?term=', amplify.store.memory, function (selected) {
		majoredin.search.preloadSearch($('#search-major').val(), selected);
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