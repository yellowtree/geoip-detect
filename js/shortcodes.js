// Get Options from data-options and json parse them
function get_options(el) {

}

async function do_shortcode_normal() {
    document.getElementsByClassName('js-geoip-detect-shortcode');
}

async function do_shortcode_flags() {
    document.getElementsByClassName('js-geoip-detect-flag');

    'flag-icon-de' // Use options.default if country cannot be detected
}

async function do_shortcodes() {
    do_shortcode_normal();
    do_shortcode_flags();
}
export const do_shortcodes;