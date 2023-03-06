/*
 Variant "Minimal": 
 
 A minimal JS file: 
 - No shortcodes, no body classes. 
 - Returns JSON instead of Record class.
*/

import { get_info_minimal } from './lookup/get_info';


window.geoip_detect.get_info = get_info_minimal;
