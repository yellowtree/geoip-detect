import { makeJSONRequest } from '../lib/xhr';
import { options } from './options';
import { getRecordDataFromLocalStorage, setRecordDataToLocalStorage } from "./storage";

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

export async function get_info_cached() {
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

