import { domReady, selectItemByAttribute, triggerNativeEvent } from "./lib/html";
import { get_info } from "./lookup";
import _ from './lodash.custom'; // we might use lodash-es in the future

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
        console.error('Geolocation IP Detection Error (' + errorMessage + '): ' + record.error());
        return;
    }

    Array.from(elements)
        .forEach(el => callback(el, record));
}

function get_value_from_record(el, record, property = null) {
    const opt = get_options(el);
    property = property || opt.property;
    if (opt.skip_cache) {
        console.warn("Geolocation IP Detection: The property 'skip_cache' is ignored in AJAX mode. You could disable the response caching on the server by setting the constant GEOIP_DETECT_READER_CACHE_TIME.");
    }

    return record.get_with_locales(property, opt.lang, opt.default);
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
    const opt = get_options(el);
    const evaluated = geoip_detect2_shortcode_evaluate_conditions(opt.parsed, opt, record);

    if (!evaluated) {
        el.style.display = "none !important";
    } else {
        el.style.display = '';
    }
}

function geoip_detect2_shortcode_evaluate_conditions(parsed, opt, record) {
    const alternativePropertyNames = [
        'name',
        'iso_code',
        'iso_code3',
        'code',
        'geoname_id',
    ];

    let isConditionMatching = (parsed.op === 'or') ? false : true;

    parsed.conditions.forEach(c => {
        let subConditionMatching = false;
        let values = [];
        
        const raw_value = record.get_raw(c.p);

        if (raw_value === null) {
            subConditionMatching = false;
        } else {
            if (typeof(raw_value) === 'object') {
                alternativePropertyNames.forEach(name => {
                    if (raw_value[name]) {
                        values.push(raw_value[name]);
                    } else if (name == 'name') {
                        values.push(record.get_with_locales(c.p, opt.lang, opt.default));
                    }
                })
            } else {
                values = [ raw_value ]
            }
        }

        subConditionMatching = geoip_detect2_shortcode_check_subcondition(c.v, values);

        if (c.not) {
            subConditionMatching = ! subConditionMatching;
        }

        if (parsed.op === 'or') {
            isConditionMatching = isConditionMatching || subConditionMatching;
        } else {
            isConditionMatching = isConditionMatching && subConditionMatching;
        }

    });

    if (parsed.not) {
        isConditionMatching = ! isConditionMatching;
    }
    
    return isConditionMatching;
}

function geoip_detect2_shortcode_check_subcondition(expectedValues, actualValues) {
    if (actualValues[0] === true) {
        actualValues = ['true', 'yes', 'y', '1'];
    } else if (actualValues[0] === false) {
        actualValues = ['false', 'no', 'n', '0', ''];
    }

    actualValues = actualValues.map(x => String(x).toLowerCase())

    expectedValues = expectedValues.split(',');

    const intersect = _.intersection(expectedValues, actualValues);

    return intersect.length > 0;
}


export const do_shortcodes = async function do_shortcodes() {
    await domReady;

    // These are called in parallel, as they are async functions
    action_on_elements('js-geoip-detect-shortcode', 
        'could not execute shortcode(s) [geoip_detect2]', do_shortcode_normal);

    action_on_elements('js-geoip-detect-flag', 
        'could not configure the flag(s)', do_shortcode_flags);

    action_on_elements('js-geoip-text-input', 
        'could not set the value of the text input field(s)', do_shortcode_text_input);

    action_on_elements('js-geoip-detect-country-select', 
        'could not set the value of the select field(s)', do_shortcode_country_select);

    action_on_elements('js-geoip-detect-show-if',
        'could not execute the show-if/hide-if conditions', do_shortcode_show_if);

};