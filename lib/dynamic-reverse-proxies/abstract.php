<?php

namespace YellowTree\GeoipDetect\DynamicReverseProxies;

require_once(__DIR__ . '/aws.php');
require_once(__DIR__ . '/cloudflare.php');

interface DataProvider {
    function getIps() : array;
}

function init() : void {
    $enabled = get_option('geoip-detect-dynamic_reverse_proxies', 0);
    if (!$enabled) return;

    add_filter('geoip_detect2_client_ip_whitelist', __NAMESPACE__ . '\addDynamicIps');
    add_filter('geoip_detect2_client_ip_use_whitelist', '__return_true');
}
add_filter('plugins_loaded', function() {
    init();
});

function addDynamicIps($ipList = []) : array {
    $type = get_option('geoip-detect-dynamic_reverse_proxy_type', '');
    if (!$type) return $ipList;

    $manager = new DataManager($type);
    $ipList = array_merge($ipList, $manager->getIpsFromCache());

    return $ipList;
}

class DataManager {
    protected $name;

    protected const CACHE_OPTION_NAME = 'geoip_detect_dynamic_rp_ips';

    public function __construct(string $name) {
        $this->name = sanitize_key($name);
    }

    public static function getDataProvider(string $name) : ?DataProvider {
        $className = '\YellowTree\GeoipDetect\DynamicReverseProxies\Data' . ucfirst($name);
        if (!class_exists($className)) {
            return null;
        }
        return new $className;
    }

    public function reload($forceSave = false) : bool {
        $provider = self::getDataProvider($this->name);

        $ips = $provider->getIps();
        $ips = apply_filters('geoip_detect2_dynamic_reverse_proxies_ips', $ips, $forceSave);
        $ip_list = geoip_detect_sanitize_ip_list(implode(',', $ips));

        if (empty($ip_list)) {
            if ($foceSave) {
                delete_option(self::CACHE_OPTION_NAME);
            }
            return false;
        }
        update_option(self::CACHE_OPTION_NAME, $this->name . '|' . $ip_list);

        return true;
    }

    public function getIpsFromCache() : array {
        $cache = get_option(self::CACHE_OPTION_NAME, false);
        if ($cache === false) {
            return [];
        }
        list($name, $list) = explode('|', $cache, 2);
        if ($name != $this->name) {
            if (GEOIP_DETECT_DEBUG) {
                trigger_error('Weird! Requesting ips for "' . $this->name . '", but found IPs for "' . $name . " in the database. This should not happen. Please file a bug report.");
            }
            return [];
        }
        return explode(',', $list);
    }
}

