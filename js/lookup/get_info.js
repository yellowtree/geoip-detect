import Record from '../models/record';

import { makeJSONRequest } from '../lib/xhr';
import { getRecordDataFromLocalStorage, setRecordDataToLocalStorage } from "./storage";


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
        storedResponse = getRecordDataFromLocalStorage()
        if (storedResponse?.extra /* this is the only property that is guarantueed */) {
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
        console.log('Weird: Uncaught error...', err);
        response = err.responseJSON || err;
    }

    if (process.env.NODE_ENV !== 'production') {
        console.log('Got response:', response);
    }

    // 3) Save info to localstorage cookie cache
    if (options.cookie_name) {

        // Check if Override has been set now
        storedResponse = getRecordDataFromLocalStorage()
        if (storedResponse?.extra?.override === true) {
            console.info('Geolocation IP Detection: Using cached response (override)');
            return storedResponse;
        }

        let cache_duration = options.cookie_duration_in_days * 24 * 60 * 60;
        if (response?.extra?.error) {
            cache_duration = 60; // Cache errors only for 1 minute, then try again
        }
        
        setRecordDataToLocalStorage(response, cache_duration);
    }

    return response;
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

/**
 * Load data - but leave away some functionality to get a minimal bundle size
 * 
 * Does not include:
 * - LocalStorage caching
 * - Accessing the data via the Record class
 * 
 * @api
 * @returns Promise(object|string)
 */
export async function get_info_minimal() {
    let response = await get_info_cached();
    return response;
}
