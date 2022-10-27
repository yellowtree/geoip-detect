import { domReady } from './lib/html';
import { get_info } from './lookup/get_info';

export function calc_classes(record) {
    return {
        country: record.get('country.iso_code'),
        'country-is-in-european-union': record.get('country.is_in_european_union', false),
        continent: record.get('continent.code'),
        province: record.get('most_specific_subdivision.iso_code'),
        city: record.get('city.names.en')
    };
}

function remove_css_classes_by_prefix(el, prefix) {
    const classes = el.className.split(" ").filter(c => !c.startsWith(prefix));
    el.className = classes.join(" ").trim();
}

export async function add_body_classes() {
    const record = await get_info();

    if (record.error()) {
        console.error('Geolocation IP Detection Error (could not add CSS-classes to body): ' + record.error());
        return;
    }

    await domReady;

    add_classes_to_body(record);
}

// ported from Wordpress PHP
function sanitize_html_class(string) {
    string = string + '';
    string = string.replace(/%[a-fA-F0-9][a-fA-F0-9]/g, '');
    string = string.replace(/[^A-Za-z0-9_-]/g, '');
    return string;
}

export function add_classes_to_body(record) {
    const css_classes = calc_classes(record);

    const body = document.getElementsByTagName('body')[0];
    
    // Remove old classes in case there are any
    remove_css_classes_by_prefix(body, 'geoip-');
    
    for (let key of Object.keys(css_classes)) {
        const value = sanitize_html_class(css_classes[key]);
        if (value) {
            if (typeof (value) == 'string') {
                body.classList.add(`geoip-${key}-${value}`);
            } else {
                body.classList.add(`geoip-${key}`);
            }
        }
    }
}