import Record from './models/record';

if (!window.jQuery) {
    console.error('Geoip-detect: window.jQuery is missing!');
}
const $ = window.jQuery;


if (!window.geoip_detect) {
    console.error('Geoip-detect: window.geoip_detect')
}
const options = window.geoip_detect.options || {};

let ajaxPromise = null;

function get_info_raw() {
    if (!ajaxPromise) {
        // Do Ajax Request only once per page load
        ajaxPromise = $.ajax(options.ajaxurl, {
            dataType: 'json',
            type: 'GET',
            data: {
                action: 'geoip_detect2_get_info_from_current_ip'
            }
        });
    }

    return ajaxPromise;
}

async function get_info_cached() {
    // TODO : Load Info from cookie cache, if possible

    let response = false;
    try {
        response = await get_info_raw();
    } catch(err) {
        response = err;
    }

    // TODO : Save info to cookie cache

    return response;
}


export async function get_info() {
    const response = await get_info_cached();

    if (!typeof(response) == 'object') {
        console.error('Geoip-detect: Record should be an object', response);
        return {};
    }

    const record = new Record(response, options.default_locales);

    if (record.message()) {
        throw record; // Reject promise
    }
    return record; // Resolve promise
}

// Extend window object 
window.geoip_detect.get_info = get_info;