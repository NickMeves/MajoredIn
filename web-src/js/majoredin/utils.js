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
	
	utils.updateQueryString = function (uri, key, value) {
		var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
		var separator = uri.indexOf('?') !== -1 ? "&" : "?";
		if (uri.match(re)) {
			return uri.replace(re, '$1' + key + "=" + value + '$2');
		}
		else {
		    return uri + separator + key + "=" + value;
		}
	};
	
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
	
})(this.majoredin = this.majoredin || {});