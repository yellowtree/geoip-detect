import Record from './models/record';
import { getLocalStorage, setLocalStorage } from './lib/localStorageAccess';
import { makeJSONRequest } from './lib/xhr';
import _set from 'just-safe-set';
import _compare from 'just-compare';
import { do_shortcodes } from './shortcodes';
import { main } from './main';


export const options = window.geoip_detect?.options || {
    ajaxurl: "/wp-admin/admin-ajax.php",
    default_locales: ['en'],
    cookie_duration_in_days: 7,
    cookie_name: 'geoip-detect-result',
    do_body_classes: false
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
        storedResponse = get_info_stored_locally()
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
        storedResponse = get_info_stored_locally()
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
 * Override only one property, leave the other properties as-is.
 * @param {string} property 
 * @param {*} value 
 * @param {number} duration_in_days 
 */
export function set_override_with_merge(property, value, duration_in_days) {
    let record = get_info_stored_locally();
    _set(record, property, value);
    set_override(record, duration_in_days);
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
function set_override_data(newData, duration_in_days) {
    if (!newData) {
        newData = {};
    }
    if (!newData.extra) {
        newData.extra = {};
    }
    newData.extra.override = true;

    const oldData = get_info_stored_locally();
    setLocalStorage(options.cookie_name, newData, duration_in_days * 24 * 60 * 60);
    if (!_compare(newData, oldData)) {
        // if data has changed, trigger re-evaluation for shortcodes etc
        main();
    }
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

// Sync function in case it is known that no AJAX will occur
export function get_info_stored_locally() {
    return getLocalStorage(options.cookie_name);
}

export function get_info_stored_locally_record() {
    return new Record(get_info_stored_locally(), options.default_locales);
}