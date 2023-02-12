<?php

namespace YellowTree\GeoipDetect\DynamicReverseProxies;

interface DataProvider {
    function getIps() : array;
}

require_once(__DIR__ . '/aws.php');
require_once(__DIR__ . '/cloudflare.php');


function initFilters() : void {
    $enabled = get_option('geoip-detect-dynamic_reverse_proxies', 0);
    if (!$enabled) return;

    add_filter('geoip_detect2_client_ip_whitelist', __NAMESPACE__ . '\addDynamicIps');
    add_filter('geoip_detect2_client_ip_use_whitelist', '__return_true');
}
add_filter('plugins_loaded', function() {
    initFilters();
});


function addDynamicIps($ipList = []) : array {
    $manager = getDataManager();
    if (!$manager) return $ipList;

    $ipList = array_merge($ipList, $manager->getIpsFromCache());

    return $ipList;
}


function getDataManager() : ?DataManager {
    $type = get_option('geoip-detect-dynamic_reverse_proxy_type', '');
    if (!$type) return null;

    return new DataManager($type);
}

class DataManager {
    protected $name;

    protected const CACHE_OPTION_NAME = 'geoip_detect_dynamic_rp_ips';

    public function __construct(string $name) {
        $this->name = sanitize_key($name);
    }

    public function getName() : string {
        return $this->name;
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
                self::deleteCache();
            }
            return false;
        }
        update_option(self::CACHE_OPTION_NAME, $this->name . '|' . $ip_list);
        update_option('geoip_detect2_dynamic-reverse-proxies_last_updated', time());

        return true;
    }

    public static function deleteCache() {
        delete_option(self::CACHE_OPTION_NAME);
        delete_option('geoip_detect2_dynamic-reverse-proxies_last_updated');
    }

    public function getIpsFromCache() : array {
        $cache = get_option(self::CACHE_OPTION_NAME, false);
        if ($cache === false) {
            return [];
        }
        list($name, $list) = explode('|', $cache, 2);
        if ($name != $this->name) {
            if (GEOIP_DETECT_DEBUG) {
                trigger_error('Weird! Requesting IPs for "' . $this->name . '", but found IPs for "' . $name . " in the database. This should not happen. Please file a bug report.");
            }
            return [];
        }
        return explode(',', $list);
    }
}


class UpdateDynamicReverseProxiesCron {
    public function addFilter() {
        add_action('geoipdetectdynamicproxiesupdate', [ $this, 'hook_cron' ]);

        add_action('geoip_detect2_options_changed', [ $this, 'options_changed']);
    }

    /** 
     * This function is called when options are changed via UI
     */
    public function options_changed() {
        DataManager::deleteCache();
        if (get_option('geoip-detect-dynamic_reverse_proxies')) {
            $this->schedule(true);
        } else {
            $this->unschedule();
        }
    }

    public function hook_cron() {
		/**
		 * Filter:
		 * Cron has fired.
		 * Find out if dynamic reverse proxy data should be updated now.
		 *
		 * @param $do_it 
		 */
        $do_it = apply_filters('geoip_detect2_dynamic-reverse-proxies_do_automatic_update', true);
        
        $this->schedule();

        if ($do_it) {
            $this->run();
        }
    }

    public function run() {
        $last = get_option('geoip_detect2_dynamic-reverse-proxies_last_updated');
        $now = time();
        if( $now - $last < HOUR_IN_SECONDS) {
            return false;
        }
        
        $manager = getDataManager();
        if ($manager) {
            $manager->reload();
        }
    }

    public function schedule($forceReschedule = false) {
        $next = wp_next_scheduled('geoipdetectdynamicproxiesupdate');

        if (!$next || $forceReschedule) {
            $this->schedule_next_cron_run();
        }

        if ($forceReschedule) {
            $this->run();
        }
    }

    public function unschedule() {
        wp_clear_scheduled_hook('geoipdetectdynamicproxiesupdate');
    }

    protected function schedule_next_cron_run() {
        $next = time() + DAY_IN_SECONDS;
        $next += mt_rand(1, HOUR_IN_SECONDS);
        wp_schedule_single_event($next, 'geoipdetectdynamicproxiesupdate');
    }
}
(new UpdateDynamicReverseProxiesCron)->addFilter();