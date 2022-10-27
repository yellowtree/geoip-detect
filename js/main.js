import { do_shortcodes, do_shortcodes_init } from './shortcodes/index';
import { add_body_classes } from './body_classes';
import { options } from './lookup/get_info';
import { setRecordDataLastEvaluated } from "./lookup/storage";

let firstCall = true;

export function main() {
    if (process.env.NODE_ENV !== 'production') {
        console.log('Do Main');
    }

    if (firstCall) {
        do_shortcodes_init();
        firstCall = false;        
    }

    if (options.do_body_classes) {
        add_body_classes();
    }

    // Do all the shortcodes that are in the HTML. Even if shortcodes is not enabled globally, they might be enabled for a specific shortcode.
    do_shortcodes();

    setRecordDataLastEvaluated();
}