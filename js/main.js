import { do_shortcodes } from './shortcodes/index';
import { add_body_classes } from './body_classes';
import { options } from './lookup';

export function main() {
    if (options.do_body_classes) {
        add_body_classes();
    }

    // Do all the shortcodes that are in the HTML. Even if shortcodes is not enabled globally, they might be enabled for a specific shortcode.
    do_shortcodes();
}