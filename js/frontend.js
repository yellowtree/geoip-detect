import Record from './models/record';
import $ from 'jquery';

let ajaxPromise = null;

function get_info_raw() {
    if (!ajaxPromise) {
        // Do Ajax Request only once per page load
        ajaxPromise = $.ajax(geoip_detect.ajaxurl, {
            dataType: 'json',
            type: 'GET',
            data: {
                action: 'geoip_detect2_get_info_from_current_ip',
                locales: locales
            }
        });
    }
    // TODO: Cache result in Session Cookie
    return ajaxPromise;
}


export function get_info() {
    // async or new Promise - Syntax?


}

