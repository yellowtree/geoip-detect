import { getLocalStorage, setLocalStorage } from '../lib/localStorageAccess';
import { options as globalOptions } from './get_info';
import Record, { camelToUnderscore } from '../models/record';
import _set from 'just-safe-set';
import _get from 'just-safe-get';
import _is_object_content_equal from 'just-compare';
import { main } from '../main';

function processOptions(options) {
    options = options || {};
    if (typeof(options) == 'number') {
        options = {
            'duration_in_days': options
        };
    }

    options.duration_in_days = options.duration_in_days || globalOptions.cookie_duration_in_days;
    if (options.duration_in_days < 0) {
        console.warn('Geolocation IP Detection set_override_data() did nothing: A negative duration doesn\'t make sense. If you want to remove the override, use remove_override() instead.');
        return false;
    }

    if (typeof (options.reevaluate) == 'undefined' ) {
        options.reevaluate = true;
    }

    return options;
}
/**
 * Override only one property, leave the other properties as-is.
 * @param {string} property 
 * @param {*} value 
 */
export function set_override_with_merge(property, value, options) {
    let record = getRecordDataFromLocalStorage() || {};
    property = property || '';
    
    property = camelToUnderscore(property);

    const oldData = _get(record, property);
    if (typeof(oldData) == 'object' && typeof(oldData.names) == 'object') {
        property += '.name';
    }
    if (property.endsWith('.name')) {
        property += 's';
        value = { 'en' : value };
    }
    
    _set(record, property, value);
    set_override(record, options);

    if (process.env.NODE_ENV !== 'production') {
        console.log("Override is now: ", getRecordDataFromLocalStorage());
    }
}

/**
 * This functions allows to override the geodetected data manually (e.g. a country selector)
 * 
 * @api
 * @param {*} record 
 * @param {object} options
 *   @param {number} duration_in_days When this override expires (default: 1 week later)
 *   @param {boolean} reevaluate If the shortcodes etc. should be re-evaluated (default: true)
 * @return boolean TRUE if override data changed
 */
export function set_override(record, options) {
    options = processOptions(options);

    if (record && typeof (record.serialize) === 'function') {
        record = record.serialize();
    }

    return set_override_data(record, options);
}

function set_override_data(newData, options) {
    newData = newData || {};
    _set(newData, 'extra.override', true);

    const oldData = getRecordDataFromLocalStorage();
    setLocalStorage(globalOptions.cookie_name, newData, options.duration_in_days * 24 * 60 * 60);

    if (options.reevaluate && !_is_object_content_equal(newData, oldData)) {
        main();
        return true;
    }

    return false;
}

/**
 * Remove the override data.
 * On next page load, the record data will be loaded from the server again.
 * 
 * @return boolean
 */
export function remove_override() {
    setLocalStorage(globalOptions.cookie_name, {}, -1);
    return true;
}


// Sync function in case it is known that no AJAX will occur
export function getRecordDataFromLocalStorage() {
    return getLocalStorage(globalOptions.cookie_name);
}

export function setRecordDataToLocalStorage(data, cache_duration) {
    setLocalStorage(globalOptions.cookie_name, data, cache_duration);
}

export function get_info_stored_locally_record() {
    return new Record(getRecordDataFromLocalStorage(), globalOptions.default_locales);
}