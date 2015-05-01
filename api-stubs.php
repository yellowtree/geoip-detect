<?php
// This file contains dummy method in case the requirements are not met.
// Those functions don't do anything but at least there is no Fatal Error or so.

function geoip_detect2_get_info_from_ip($ip) { return new stdClass; }
function geoip_detect2_get_info_from_current_ip() { return geoip_detect2_get_info_from_ip(); }
function geoip_detect2_get_reader() { return NULL; }
function geoip_detect2_get_current_source_description() { return ''; }
function geoip_detect2_get_client_ip() {return ''; }
function geoip_detect2_get_external_ip_adress() { return ''; }