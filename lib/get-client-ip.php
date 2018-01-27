<?php

/*
Copyright 2013-2018 Yellow Tree, Siegen, Germany
Author: Benjamin Pick (wp-geoip-detect| |posteo.de)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

namespace YellowTree\GeoipDetect\Lib;

class GetClientIp {
	protected $proxyWhitelist = [];
	
	public function __construct() {
		$this->proxyWhitelist[] = '';
		$this->proxyWhitelist[] = '::1';
		$this->proxyWhitelist[] = '127.0.0.1';
		
		$this->addProxyWhitelisteFromOption();
	}
	
	protected function addProxyWhitelisteFromOption() {
		// TODO: Expose option to UI. comma-seperated list of IPv4 and v6 adresses.			
		$trusted_proxies = explode(',', get_option('geoip-detect-trusted_proxy_ips'));
		$trusted_proxies = array_map('geoip_detect_normalize_ip', $trusted_proxies);
		
		$this->proxyWhitelist = array_merge($trusted_proxies, $this->proxyWhitelist);
	}
	
	public function getIp() {
		_geoip_maybe_disable_pagecache();

		$ip = '';

		if (isset($_SERVER['REMOTE_ADDR']))
			$ip = $_SERVER['REMOTE_ADDR'];

		// REMOTE_ADDR may contain multiple adresses? https://wordpress.org/support/topic/php-fatal-error-uncaught-exception-invalidargumentexception?replies=2#post-8128737
		$ip_list = explode(',', $ip);

		if (get_option('geoip-detect-has_reverse_proxy', 0) && isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
		{
			$ip_list = explode(',', @$_SERVER["HTTP_X_FORWARDED_FOR"]);
			$ip_list = array_map('geoip_detect_normalize_ip', $ip_list);

			$ip_list[] = $ip;

			$ip_list = array_diff($ip_list, $this->proxyWhitelist);
		}	
		// Fallback IP
		array_unshift($ip_list, '::1');

		// Each Proxy server append their information at the end, so the last IP is most trustworthy.
		$ip = end($ip_list);
		$ip = geoip_detect_normalize_ip($ip);

		if (!$ip)
			$ip = '::1'; // By default, use localhost

		// @deprecated: this filter was added by mistake
		$ip = apply_filters('geoip2_detect2_client_ip', $ip, $ip_list);
		// this is the correct one!
		$ip = apply_filters('geoip_detect2_client_ip', $ip, $ip_list);

		return $ip;
	}
}