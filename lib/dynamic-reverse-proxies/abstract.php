<?php

namespace YellowTree\GeoipDetect\DynamicReverseProxies;

require_once(__DIR__ . '/aws.php');
require_once(__DIR__ . '/cloudflare.php');

interface DataProvider {
    function getIps() : array;
}

class DataManager {
    protected $name;

    protected const CACHE_OPTION_NAME = 'geoip_detect_dynamic_rp_ips';

    public function __construct(string $name) {
        $this->name = $name;
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
        update_option(self::CACHE_OPTION_NAME, $ip_list);

        return true;
    }

    public function getIpsFromCache() : array {
        $cache = get_option(self::CACHE_OPTION_NAME, false);
        if ($cache === false) {
            return [];
        }
        return explode(',', $cache);
    }
}