import { get_info } from "../lookup/get_info";

// Get Options from data-options and json parse them
export function get_options(el) {
    const raw = el.getAttribute('data-options');
    try {
        return JSON.parse(raw);
    } catch (e) {
        return {};
    }
}

export async function action_on_elements(className, errorMessage, callback) {
    const elements = document.getElementsByClassName(className);
    if (!elements.length) return;

    const record = await get_info();

    if (record.error()) {
        console.error('Geolocation IP Detection Error (' + errorMessage + '): ' + record.error());
        return;
    }

    Array.from(elements)
        .forEach(el => callback(el, record));
}

export function get_value_from_record(el, record, property = null) {
    const opt = get_options(el);
    property = property || opt.property;
    if (opt.skip_cache) {
        console.warn("Geolocation IP Detection: The property 'skip_cache' is ignored in AJAX mode. You could disable the response caching on the server by setting the constant GEOIP_DETECT_READER_CACHE_TIME.");
    }

    return record.get_with_locales(property, opt.lang, opt.default);
}