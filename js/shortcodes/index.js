import { domReady } from "../lib/html";
import { action_on_elements } from "./helpers";
import { do_shortcode_country_select, do_shortcode_flags, do_shortcode_normal, do_shortcode_text_input } from "./normal";
import { init as onchangeInit }  from "./onchange";
import { do_shortcode_show_if } from "./show-if";


export const do_shortcodes_init = function () {
    onchangeInit();
}

export const do_shortcodes = async function do_shortcodes() {
    // Before doing any of these, the DOM tree needs to be loaded
    await domReady;

    // These are called in parallel, as they are async functions
    action_on_elements('js-geoip-detect-shortcode',
        'could not execute shortcode(s) [geoip_detect2 ...]', do_shortcode_normal);

    action_on_elements('js-geoip-detect-flag',
        'could not configure the flag(s)', do_shortcode_flags);

    action_on_elements('js-geoip-text-input',
        'could not set the value of the text input field(s)', do_shortcode_text_input);

    action_on_elements('js-geoip-detect-country-select',
        'could not set the value of the select field(s)', do_shortcode_country_select);

    action_on_elements('js-geoip-detect-show-if',
        'could not execute the show-if/hide-if conditions', do_shortcode_show_if);
};
