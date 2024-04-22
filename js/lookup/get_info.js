import Record from '../models/record';
import { get_info_cached } from './get_info_cached';
import { options } from './options';

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