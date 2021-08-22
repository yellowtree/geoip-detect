import { getLocalStorage, setLocalStorage } from './lib/localStorageAccess';
import { options } from './lookup';
import Record, { camelToUnderscore } from './models/record';
import _set from 'just-safe-set';
import _compare from 'just-compare';
import { main } from './main';

/**
 * Override only one property, leave the other properties as-is.
 * @param {string} property 
 * @param {*} value 
 * @param {number} duration_in_days 
 */
export function set_override_with_merge(property, value, duration_in_days) {
    let record = getRecordDataFromLocalStorage() || {};
    property = camelToUnderscore(property);
    _set(record, property, value);
    set_override(record, duration_in_days);
}

/**
 * This functions allows to override the geodetected data manually (e.g. a country selector)
 * 
 * @api
 * @param {*} record 
 * @param {number} duration_in_days When this override expires (default: 1 week later)
 * @return boolean TRUE if override data changed
 */
export function set_override(record, duration_in_days) {
    if (record && typeof (record.serialize) === 'function') {
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
    newData = newData || {};
    _set(newData, 'extra.override', true);

    const oldData = getRecordDataFromLocalStorage();
    setLocalStorage(options.cookie_name, newData, duration_in_days * 24 * 60 * 60);

    if (!_compare(newData, oldData)) {
        // if data has changed, trigger re-evaluation for shortcodes etc
        setTimeout(function () {
            main();
        }, 10);
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
    setLocalStorage(options.cookie_name, {}, -1);
    return true;
}


// Sync function in case it is known that no AJAX will occur
export function getRecordDataFromLocalStorage() {
    return getLocalStorage(options.cookie_name);
}

export function setRecordDataToLocalStorage(data, cache_duration) {
    setLocalStorage(options.cookie_name, data, cache_duration);
}

export function get_info_stored_locally_record() {
    return new Record(getRecordDataFromLocalStorage(), options.default_locales);
}