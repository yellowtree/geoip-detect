import { selectItemByAttribute, triggerNativeEvent } from "../lib/html";
import { set_override } from "../lookup";
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

export function event_listener_autosave_on_change(event) {
    const target = event.target;
    if (target.matches('.js-geoip-detect-input-autosave')) {
        const property = get_options(target).property || 'country' /* country select */;
        const value = target.value;

        if (value) {
            set_override_with_merge(property, value);
        }
    }
}

export function do_shortcode_country_select(el, record) {
    let country = record.get_country_iso();

    selectItemByAttribute(el, 'data-c', country);
    triggerNativeEvent(el, 'change');
}

export function do_shortcode_text_input(el, record) {
    el.value = get_value_from_record(el, record);
    triggerNativeEvent(el, 'change');
}