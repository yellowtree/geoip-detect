
import { check_recursive_after, check_recursive_before } from "../lib/check-recursive";
import { isInternalEvent } from "../lib/events";
import { set_override_with_merge } from "../lookup/override";
import { get_options } from "./helpers";

export function init() {
    document.addEventListener('change', event_listener_autosave_on_change, false);
}

function event_listener_autosave_on_change(event) {
    if (isInternalEvent()) return;

    const target = event.target;
    if (target.matches('.js-geoip-detect-input-autosave')) {

        if (process.env.NODE_ENV !== 'production') {
            console.log('autosave on change', target);
        }

        const property = get_options(target).property;
        const value = target.value;

        if (!check_recursive_before()) {
            return;
        }

        if (target.matches('select.js-geoip-detect-country-select')) {
            const selected = target.options[target.selectedIndex];
            const isoCode = selected?.getAttribute('data-c');
            set_override_with_merge('country.iso_code', isoCode.toUpperCase(), {reevaluate: false});
        }
        
        set_override_with_merge(property, value); // might call do_shortcodes etc.

        check_recursive_after();
    }
}