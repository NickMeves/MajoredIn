(function (majoredin) {
	var utils = majoredin.utils = majoredin.utils || {};
	
	utils.ie = (function(){

	    var undef,
	        v = 3,
	        div = document.createElement('div'),
	        all = div.getElementsByTagName('i');

	    while (
	        div.innerHTML = '<!--[if gt IE ' + (++v) + ']><i></i><![endif]-->',
	        all[0]
	    );

	    return v > 4 ? v : undef;

	}());
	
	utils.canonicalize = function (str) {
		canon = str;
        
        canon = canon.replace(/[\+\-]/g, ' ');
        canon = canon.replace(/[\,\'\.]/g, '');
        canon = canon.toLowerCase();
        
        canon = canon.replace(/\s+/g, ' ');
        canon = canon.replace(/^\s/, '');
        //canon = canon.replace(/\s$/, '');
        
        return canon;
	};
	
	utils.dash = function (str) {
		str = str.replace(/-/g, '_'); //allows - in queries
        
        str = str.replace(/\s+/g, ' ');
        str = str.replace(/^\s/g, '');
        str = str.replace(/\s$/g, '');
        str = str.replace(/\s+/g, '-');
        
        str = str.replace(/\//g, ''); //fixes / and route issues.
        
        return str;
	};
	
	utils.autocomplete = function (element, namespace, url, store, preload) {
		element.typeahead({
			source: function (query, process) {
				var items_max = 20; //value on server (bigger to enable precaching)
				var canon_query = utils.canonicalize(query);
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
	    			    	majoredin[namespace + canon_query.replace(/[^\w]/, '_')] = function(response) { //bind closure function to global scope
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
						    				var new_query = utils.canonicalize(data[i].substr(0,j));
						    				for (var k = 0; k < data.length; ++k) {
						    					if (utils.canonicalize(data[k]).match(new RegExp('^' + new_query))) {
						    						matches.push(data[k]);
						    					}
						    				}
						    				store(namespace + new_query, matches);
						    			}
						    		}
						    		
						    		//Cache empty keys for query + 1 invalid letters
						    		var letters = 'abcdefghijklmnopqrstuvwxyz ';
						    		for (var i = 0; i < data.length; ++i) {
						    			letters = letters.replace(new RegExp(utils.canonicalize(data[i]).charAt(query.length)), '');
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
					    	return "majoredin." + namespace + canon_query.replace(/[^\w]/, '_');
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
		    	var length = utils.canonicalize(this.query).length;
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
	
	utils.preloadSearch = function (major, location) {
		var url = '/precache/' + major + '/' + location;
		var key = 'precached_' + url;
		if (major != '' && location != '' && amplify.store.memory(key) != true) {
			amplify.store.memory(key, true);
			$.get(url);
		}
		return;
	};
	
	utils.preloadUrl = function (url, store) {
		url = url.replace(/^\/jobs/, '/precache');
		$.get(url);
		return;
	};

})(this.majoredin = this.majoredin || {});