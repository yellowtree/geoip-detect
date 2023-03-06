/*
 Variant "Base": 
 
 A reduced JS file: 
 - No shortcodes, no body classes. 
*/

import { get_info } from './lookup/get_info';


window.geoip_detect.get_info = get_info;
