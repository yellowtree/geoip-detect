import { get_info, options, remove_override, set_override } from './lookup';
import { do_shortcodes } from './shortcodes/index';
import { add_body_classes } from './body_classes';



if (options.do_body_classes) {
    add_body_classes();
}

// Always do shortcodes because there might be AJAX exceptions
do_shortcodes();

// Extend window object 
window.geoip_detect.get_info = get_info;

window.geoip_detect.set_override = set_override;
window.geoip_detect.remove_override = remove_override;