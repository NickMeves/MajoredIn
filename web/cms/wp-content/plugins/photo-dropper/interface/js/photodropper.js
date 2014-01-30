jQuery(function(){
	var PHOTODROPPER_INIT = function(){
		var $ = jQuery;
		var script = $('<script/>')
			.attr('type', 'text/javascript')
			.attr('src', PDRP.APPURL);
		$('head').append(script);
	}();
});