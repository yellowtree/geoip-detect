import { get_info } from './lookup/get_info';
import { remove_override, set_override, set_override_with_merge } from './lookup/override';
import { main } from './main';

// Evaluate shortcodes, body classes, etc.
main();


// Extend window object 
window.geoip_detect.get_info = get_info;

window.geoip_detect.set_override = set_override;
window.geoip_detect.set_override_with_merge = set_override_with_merge;
window.geoip_detect.remove_override = remove_override;