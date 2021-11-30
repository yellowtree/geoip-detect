import { triggerNativeEvent } from "../lib/events";
import { selectItemByAttribute } from "../lib/html";
import { get_value_from_record, get_options } from "./helpers";

export function do_shortcode_normal(el, record) {
    el.innerText = get_value_from_record(el, record);
}

export function do_shortcode_flags(el, record) {
    const country = record.get_country_iso() || get_options(el).default;
    if (country) {
        el.classList.add('flag-icon-' + country)
    }
}


export function do_shortcode_country_select(el, record) {
    let country = record.get_country_iso();

    if (selectItemByAttribute(el, 'data-c', country)) {
        triggerNativeEvent(el, 'change');
    } else {
        const defaultValue = get_options(el).default;
        if (get_options(el).default) {
            if (selectItemByAttribute(el, 'data-c', defaultValue)) {
                triggerNativeEvent(el, 'change');
            }
        }
    }
}

export function do_shortcode_text_input(el, record) {
    el.value = get_value_from_record(el, record);
    triggerNativeEvent(el, 'change');
}