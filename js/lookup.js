import Record from './models/record';
import { getLocalStorage, setLocalStorage } from './lib/localStorageAccess';
import _ from './lodash.custom';
import { makeJSONRequest } from './lib/xhr';


if (!window.geoip_detect) {
    console.error('Geoip-detect: the JS variable window.geoip_detect is missing - this is needed for the options')
}
export const options = window.geoip_detect?.options || {};

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
    } catch (err) {
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

    if (typeof (response) !== 'object') {
        console.error('Geoip-detect: Record should be an object, not a ' + typeof (response), response);
        response = { 'extra': { 'error': response || 'Network error, look at the original server response ...' } };
    }

    const record = new Record(response, options.default_locales);
    return record;
}