import { isInternalEvent } from "../lib/html";
import { set_override_with_merge } from "../lookup_override";

let _listener_active = false; // for recursion detection (maybe remove later)
let _change_counter = 0; // ToDo remove later!

export function init() {
    document.addEventListener('change', event_listener_autosave_on_change, false);
}

function event_listener_autosave_on_change(event) {
    if (isInternalEvent()) return;

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