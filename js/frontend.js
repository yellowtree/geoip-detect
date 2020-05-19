import Record from './models/record';
import { getLocalStorage, setLocalStorage } from './localStorageAccess';
import _ from './lodash.custom';
import { makeJSONRequest } from './xhr';

if (!window.geoip_detect) {
    console.error('Geoip-detect: the JS variable window.geoip_detect is missing - this is needed for the options')
}
const options = window.geoip_detect.options || {};

let ajaxPromise = null;

function get_info_raw() {
    if (!ajaxPromise) {
        // Do Ajax Request only once per page load
        const url = options.ajaxurl + '?action=geoip_detect2_get_info_from_current_ip'

        ajaxPromise = makeJSONRequest(url);
    }

    return ajaxPromise;
}

async function get_info_cached() {
    let response = false;

    // 1) Load Info from cookie cache, if possible
    if (options.cookie_name) {
        response = getLocalStorage(options.cookie_name)
        if (response && response.extra) {
            // This might be an error object - cache it anyway
            return response;
        }
    }

    // 2) Get response
    try {
        response = await get_info_raw();
    } catch(err) {
        response = err.responseJSON || err;
    }

    // 3) Save info to cookie cache
    if (options.cookie_name) {
        setLocalStorage(options.cookie_name, response, options.cookie_duration_in_days * 24 * 60 * 60)
    }

    return response;
}


export async function get_info() {
    let response = await get_info_cached();

    if (typeof(response) !== 'object') {
        console.error('Geoip-detect: Record should be an object, not a ' + typeof(response), response);
        response = { 'extra': { 'error': response || 'Network error, look at the original server response ...' }};
    }

    const record = new Record(response, options.default_locales);
    return record;
}

async function add_body_classes() {
    const record = await get_info();

    if (record.error()) {
        console.error('Geodata Error (could not add CSS-classes to body): ' + record.error());
    }

    const css_classes = {
        country:   record.get('country.iso_code'),
        'country-is-in-european-union': record.get('country.is_in_european_union'),
        continent: record.get('continent.code'),
        province:  record.get('most_specific_subdivision.iso_code'),
    };

    const body = document.getElementsByTagName('body')[0];
    for(let key of Object.keys(css_classes)) {
        const value = css_classes[key];
        if (value) {
            if (typeof(value) == 'string') {
                body.classList.add(`geoip-${key}-${value}`);
            } else {
                body.classList.add(`geoip-${key}`);
            }
        }
    }
}
if (options.do_body_classes) {
    add_body_classes();
}

// Extend window object 
window.geoip_detect.get_info = get_info;