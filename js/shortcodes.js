import { domReady, selectItemByValue, triggerNativeEvent } from "./lib/html";
import { get_info } from "./lookup";

// Get Options from data-options and json parse them
function get_options(el) {
    const raw = el.getAttribute('data-options');
    try {
        return JSON.parse(raw);
    } catch (e) {
        return {};
    }
}

async function action_on_elements(className, errorMessage, callback) {
    const elements = document.getElementsByClassName(className);
    if (!elements.length) return;

    const record = await get_info();

    if (record.error()) {
        console.error('Geodata Error (' + errorMessage + '): ' + record.error());
        return;
    }

    Array.from(elements)
        .forEach(el => callback(el, record));
}

function get_value_from_record(el, record) {
    const opt = get_options(el);
    if (opt.skip_cache) {
        console.warn("The property 'skip_cache' is ignored in AJAX mode. You could disable the response caching on the server by setting the constant GEOIP_DETECT_READER_CACHE_TIME.");
    }

    return record.get_with_locales(opt.property, opt.lang, opt.default);
}


function do_shortcode_normal(el, record) {
    el.innerText = get_value_from_record(el, record);
}

function do_shortcode_flags(el, record) {
    const country = record.get_country_iso() || get_options(el).default;
    if (country) {
        el.classList.add('flag-icon-' + country)
    }
}

function do_shortcode_country_select(el, record) {
    let country = record.get_country_iso();

    selectItemByAttribute(el, 'data-c', country);
    triggerNativeEvent(el, 'change');
}

function do_shortcode_text_input(el, record) {
    el.value = get_value_from_record(el, record);
    triggerNativeEvent(el, 'change');
}


function do_shortcode_show_if(el, record) {
    // later ...
}


export const do_shortcodes = async function do_shortcodes() {
    await domReady;

    // These are called in parallel, as they are ajax functions
    action_on_elements('js-geoip-detect-shortcode', 
        'could not execute shortcode(s) [geoip_detect2]', do_shortcode_normal);

    action_on_elements('js-geoip-detect-flag', 
        'could not configure the flag(s)', do_shortcode_flags);

    action_on_elements('js-geoip-text-input', 
        'could not set the value of the text input field(s)', do_shortcode_text_input);

    action_on_elements('js-geoip-detect-country-select', 
        'could not set the value of the select field(s)', do_shortcode_country_select);
};