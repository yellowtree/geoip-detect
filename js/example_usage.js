jQuery(document).ready(function($) {
	$.ajax(geoip_detect.ajaxurl, {
		dataType: 'json',
		type: 'GET',
		data: {
			action: 'geoip_detect2_get_info_from_current_ip',
			locales: 'de,en'
		}
		
	}).done(function(data) {
		// Ok case
		$('.site-description').text(data.country.name);
	}).fail(function(data) {
		// Error case
		console.log('Error');
		//alert(data.error);
	});
});