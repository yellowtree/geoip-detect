import { get_info, remove_override, set_override } from './lookup';
import { main } from './main';

// Evaluate shortcodes, body classes, etc.
main();


// Extend window object 
window.geoip_detect.get_info = get_info;

window.geoip_detect.set_override = set_override;
window.geoip_detect.remove_override = remove_override;