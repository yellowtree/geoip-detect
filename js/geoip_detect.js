/**
 * Get a jQuery Promise that will delive the AJAX data.
 * @param locales	Locales to fill in the 'name' field (optional)
 * @returns promise
 */

function geoip_detect_ajax_promise(locales) {
	locales = locales || '';
	
	var promise = jQuery.ajax(geoip_detect.ajaxurl, {
		dataType: 'json',
		type: 'GET',
		data: {
			action: 'geoip_detect2_get_info_from_current_ip',
			locales: locales
		}
	});
	
	return promise;
}

/**
 * Get property value from data
 * 
 * @param data
 * @param property_name
 * @param options
 */
function geoip_detect_get_property_value(data, property_name, options) {
	var _get_name = function(names, locales) {
		if (typeof(locales) === 'string') {
			locales = locales.split(',');
		}
		
		for (var l in locales) {
			//l = l.trim();
			if (typeof(names[l]) != 'undefined' && names[l])
				return names[l];
		}
		return '';
	}
	
	var $ = jQuery;
	var default_options = {
		'locales' : 'en',
		'default' : '',
	};
	options = $.extend(options, default_options);
	
	var properties = property_name.split('.');
	var next_property = properties.shift();
	if (next_property == 'name' || !next_property) {
		if (typeof(data['names']) == 'object') {
			return _get_name(data['names'], options.locales);
		} else {
			return '';
		}
	}
	if (typeof(data[next_property]) == 'undefined')
		return options['default'];
	if (typeof(data[next_property]) == 'string')
		return data[next_property];
	return geoip_detect_get_property_value(data[next_property], properties.join('.'), options);
}

function geoip_detect_fill_data_attributes(el) {
	var $ = jQuery;
	el = $(el);
	// Fill in the shortcodes into the HTML
	var shortcodes = el.find('[data-geoip]');
	if (!shortcodes.length)
		return;
	
	var promise = geoip_detect_ajax_promise('en');
	
	promise.done(function(data) {
		shortcodes.each(function() {
			var options = $(this).data('geoip');
			var value = geoip_detect_get_property_value(data, options.property, options);
			
			if ($(this).data('geoip-method') == 'class')
				$(this).addClass('geoip-' + value);
			else
				$(this).text(value);
			$(this).trigger('geoip_detect.value.success');
		});
	}).fail(function(data) {
		if (typeof(console) != 'undefined' && typeof(console.log) != 'undefined')
			console.log('Error: ' + data.error);
		shortcodes.trigger('geoip_detect.value.failure');
	});
}


jQuery(document).ready(function($) {
	geoip_detect_fill_data_attributes('html');
});