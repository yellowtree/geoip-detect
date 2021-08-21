import { selectItemByAttribute, triggerNativeEvent } from "../lib/html";
import { set_override_with_merge } from "../lookup";
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

let _listener_active = false; // for recursion detection (maybe remove later)
let _disable_change_listener = false; // Ignore change events from the plugin itself
let _change_counter = 0; // ToDo remove later!

export function event_listener_autosave_on_change(event) {
    if (_disable_change_listener) return;

    const target = event.target;
    if (target.matches('.js-geoip-detect-input-autosave')) {
console.log('autosave on change', target);
        const property = get_options(target).property;
        const value = target.value;

        if (value) {
            _change_counter++;
            if (_listener_active || _change_counter > 100) {
                console.warn('Thats weird! autosave change detected a recursion!');
                debugger;
                return;
            } else {
                _listener_active = true;
                set_override_with_merge(property, value); // might call do_shortcodes etc.
                _listener_active = false;
            }
        }
    }
}

export function do_shortcode_country_select(el, record) {
    let country = record.get_country_iso();

    selectItemByAttribute(el, 'data-c', country);
    _disable_change_listener = true;
    triggerNativeEvent(el, 'change');
    _disable_change_listener = false;
}

export function do_shortcode_text_input(el, record) {
    el.value = get_value_from_record(el, record);
    _disable_change_listener = true;
    triggerNativeEvent(el, 'change');
    _disable_change_listener = false;
}