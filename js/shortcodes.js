import { get_info } from "./lookup";

// Get Options from data-options and json parse them
function get_options(el) {
    const raw = el.getAttribute('data-options');
    try {
        return JSON.parse(raw);
    } catch(e) {
        return {};
    }
}

async function do_shortcode_normal() {
    const elements = document.getElementsByClassName('js-geoip-detect-shortcode');
    if (!elements.length) return;

    const record = await get_info();

    if (record.error()) {
        console.error('Geodata Error (could not execute shortcode [geoip_detect2]): ' + record.error());
        return;
    }

    Array.from(elements).forEach(el => {

    });

}

async function do_shortcode_flags() {
    const elements = document.getElementsByClassName('js-geoip-detect-flag');
    if (!elements.length) return;

    const record = await get_info();

    if (record.error()) {
        console.error('Geodata Error (could not configure the flags): ' + record.error());
        return;
    }

    let country = record.get('country.iso_code');
    if (country) {
        country = country.substr(0, 2).toLowerCase();
    }

    Array.from(elements)
        .forEach(el => {
            const c = country || get_options(el).default;
            if (c) {
                el.classList.add('flag-icon-' + c)
            }
        });
}

export const do_shortcodes = async function do_shortcodes() {
    do_shortcode_normal();
    do_shortcode_flags();
};