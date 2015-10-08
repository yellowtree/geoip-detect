function geoip_detect_ajax_promise(locales) {
	locales = locales || '';
	
	var promise = $.ajax(geoip_detect.ajaxurl, {
		dataType: 'json',
		type: 'GET',
		data: {
			action: 'geoip_detect2_get_info_from_current_ip',
			locales: locales
		}
	});
	
	return promise;
}

// Example usage

jQuery(document).ready(function($) {
	geoip_detect_ajax_promise('de, en')
	.done(function(data) {
		// Ok case
		$('.site-description').text(data.country.name);
	}).fail(function(data) {
		// Error case
		console.log('Error: ' + data.error);
	});
});