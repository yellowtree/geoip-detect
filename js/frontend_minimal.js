/*
 Variant "Minimal": 
 
 A minimal JS file: 
 - No shortcodes, no body classes. 
 - Returns JSON instead of Record class.
*/

import { get_info_cached } from './lookup/get_info_cached';

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


window.geoip_detect.get_info = get_info_minimal;
