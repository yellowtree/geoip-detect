import Record from './models/record';
import { getLocalStorage, setLocalStorage } from './lib/localStorageAccess';
import { makeJSONRequest } from './lib/xhr';


export const options = window.geoip_detect?.options || {
    ajaxurl: "/wp-admin/admin-ajax.php",
    default_locales: ['en'],
    cookie_duration_in_days: 7,
    cookie_name: 'geoip-detect-result'
};

let ajaxPromise = null;

function get_info_raw() {
    if (!ajaxPromise) {
        // Do Ajax Request only once per page load
        const url = options.ajaxurl + '?action=geoip_detect2_get_info_from_current_ip'

        ajaxPromise = makeJSONRequest(url);
        
        ajaxPromise.then((response) => {
            if (response?.extra?.error) {
                console.error('Geolocation IP Detection Error: Server returned an error: ' + response.extra.error);
            }
        })
    }

    return ajaxPromise;
}

async function get_info_cached() {
    let response = false;
    let storedResponse = false;

    // 1) Load Info from localstorage cookie cache, if possible
    if (options.cookie_name) {
        storedResponse = getLocalStorage(options.cookie_name)
        if (storedResponse && storedResponse.extra) {
            if (storedResponse.extra.override === true) {
                console.info('Geolocation IP Detection: Using cached response (override)');
            } else {
                console.info('Geolocation IP Detection: Using cached response');
            }
            return storedResponse;
        }
    }

    // 2) Get response
    try {
        response = await get_info_raw();
    } catch (err) {
        console.log('Uncaught ERROR ??');
        response = err.responseJSON || err;
    }

    // 3) Save info to localstorage cookie cache
    if (options.cookie_name) {

        // Check if Override has been set now
        storedResponse = getLocalStorage(options.cookie_name)
        if (storedResponse?.extra?.override === true) {
            console.info('Geolocation IP Detection: Using cached response (override)');
            return storedResponse;
        }

        let cache_duration = options.cookie_duration_in_days * 24 * 60 * 60;
        if (response?.extra?.error)
            cache_duration = 60; // Cache errors only for 1 minute, then try again
        
        setLocalStorage(options.cookie_name, response, cache_duration);
    }

    return response;
}


/**
 * This functions allows to override the geodetected data manually (e.g. a country selector)
 * 
 * @api
 * @param {*} record 
 * @param {number} duration_in_days When this override expires (default: 1 week later)
 * @return boolean
 */
export function set_override(record, duration_in_days) {
    if (record && typeof(record.serialize) === 'function') {
        record = record.serialize();
    }

    duration_in_days = duration_in_days || options.cookie_duration_in_days;
    if (duration_in_days < 0) {
        console.warn('Geolocation IP Detection set_override_data() did nothing: A negative duration doesn\'t make sense. If you want to remove the override, use remove_override() instead.');
        return false;
    }

    return set_override_data(record, duration_in_days);
}
function set_override_data(data, duration_in_days) {
    if (!data) {
        data = {};
    }
    if (!data.extra) {
        data.extra = {};
    }
    data.extra.override = true;

    setLocalStorage(options.cookie_name, data, duration_in_days * 24 * 60 * 60);
    return true;
}

/**
 * Remove the override data.
 * On next page load, the record data will be loaded from the server again.
 * 
 * @return boolean
 */
export function remove_override() {
    setLocalStorage(options.cookie_name, {}, -1);
    return true;
}


/**
 * Load the data from the server
 * 
 * (It can also be loaded from the browser localstorage, if the record data is present there already.)
 * 
 * @api
 * @return Promise(Record)
 */
export async function get_info() {
    let response = await get_info_cached();

    if (typeof (response) !== 'object') {
        console.error('Geolocation IP Detection Error: Record should be an object, not a ' + typeof (response), response);
        response = { 'extra': { 'error': response || 'Network error, look at the original server response ...' } };
    }

    const record = new Record(response, options.default_locales);
    return record;
}