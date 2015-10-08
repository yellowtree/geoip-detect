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
	var shortcodes = $('[data-geoip]');
	if (!shortcodes.length)
		return;
	
	var promise = geoip_detect_ajax_promise('en');
	.done(function(data) {
		shortcodes.each(function() {
			var value;
			$(this).text(value);
		});
		// Ok case
		$('.site-description').text(data.country.name);
	}).fail(function(data) {
		// Error case
		console.log('Error: ' + data.error);
	});
});