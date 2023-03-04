import { getLocalStorage, setLocalStorage } from '../lib/localStorageAccess';
import { options as globalOptions } from './get_info';
import Record from '../models/record';

// Sync function in case it is known that no AJAX will occur
export const getRecordDataFromLocalStorage = () => {
    return getLocalStorage(globalOptions.cookie_name);
}

export const setRecordDataToLocalStorage = (data, cache_duration) => {
    setLocalStorage(globalOptions.cookie_name, data, cache_duration);
}

let lastEvaluated = {};
export const getRecordDataLastEvaluated = () => {
    return lastEvaluated;
}
export const setRecordDataLastEvaluated = () => {
    lastEvaluated = getRecordDataFromLocalStorage();
}

export const get_info_stored_locally_record = () => {
    return new Record(getRecordDataFromLocalStorage(), globalOptions.default_locales);
}
