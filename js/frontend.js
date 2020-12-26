import { do_shortcodes } from './shortcodes';
import { get_info, options } from './lookup';
import { domReady } from './lib/html';


async function add_body_classes() {
    const record = await get_info();

    if (record.error()) {
        console.error('Geodata Error (could not add CSS-classes to body): ' + record.error());
        return;
    }

    const css_classes = {
        country:   record.get('country.iso_code'),
        'country-is-in-european-union': record.has_property('country.is_in_european_union') && record.get('country.is_in_european_union'),
        continent: record.get('continent.code'),
        province:  record.get('most_specific_subdivision.iso_code'),
    };

    await domReady;

    const body = document.getElementsByTagName('body')[0];
    for(let key of Object.keys(css_classes)) {
        const value = css_classes[key];
        if (value) {
            if (typeof(value) == 'string') {
                body.classList.add(`geoip-${key}-${value}`);
            } else {
                body.classList.add(`geoip-${key}`);
            }
        }
    }
}
if (options.do_body_classes) {
    add_body_classes();
}
if (options.do_shortcodes) {
    do_shortcodes();
}

// Extend window object 
window.geoip_detect.get_info = get_info;