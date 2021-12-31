import { getLocalStorage, setLocalStorage } from '../lib/localStorageAccess';
import { options as globalOptions } from './get_info';
import Record from '../models/record';

// Sync function in case it is known that no AJAX will occur
export function getRecordDataFromLocalStorage() {
    return getLocalStorage(globalOptions.cookie_name);
}

export function setRecordDataToLocalStorage(data, cache_duration) {
    setLocalStorage(globalOptions.cookie_name, data, cache_duration);
}

let lastEvaluated = {};
export function getRecordDataLastEvaluated() {
    return lastEvaluated;
}
export function setRecordDataLastEvaluated() {
    lastEvaluated = getRecordDataFromLocalStorage();
}


export function get_info_stored_locally_record() {
    return new Record(getRecordDataFromLocalStorage(), globalOptions.default_locales);
}
