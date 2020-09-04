<?php

/*
Copyright 2013-2020 Yellow Tree, Siegen, Germany
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

use YellowTree\GeoipDetect\DataSources\DataSourceRegistry;
use  YellowTree\GeoipDetect\Logger;

class CcpaBlacklistOnLookup {
    protected static $list = null;

    public function __construct() {
        $this->addFilters();
    }

    public function addFilters() {
        add_filter('geoip_detect2_record_data_override_lookup', array($this, 'onBeforeLookup'), 9, 3);
    }

    public function onBeforeLookup($data, $ip, $options) {
        $exclusionReason = $this->ipOnListGetReason($ip);
        
        if ($exclusionReason) {
            $data = array();
            $currentSourceId = DataSourceRegistry::getInstance()->getSource($options['source'])->getId();
            $errorMessage = sprintf(__('This IP has no informations attached by request of the IP owner (Reason: %s).', 'geoip-detect'), $exclusionReason);
            
            $data = _geoip_detect2_record_enrich_data($data, $ip, $currentSourceId, $errorMessage);
        }
        return $data;
    }

    protected function ipOnListGetReason($ip) {
        self::lazyLoadList();

        foreach (self::$list as $row) {
            if ($this->doesIpMatchRow($ip, $row)) {
                return $row['exclusion_type'];
            }
        }
        return false;
    }

    protected function doesIpMatchRow($ip, $row) {
        if (empty($row['data_type']) || empty($row['value'])) {
            return false;
        }

        switch($row['data_type']) {
            case 'network':
                return geoip_detect_is_ip_equal($ip, $row['value']);
            default:
                return false;
        }
    }

    protected static function lazyLoadList() {
        // Only load once        
        if (!is_null(self::$list)) return;

        $list = array();

        /**
         * Filter: geoip_detect2_maxmind_ccpa_blacklist_ip_subnets
         * @param array(array) $data (The array key names are documented on the maxmind page)
         *      @param string $data_type Currently, only the value 'network' (IP/Subnet in CIDR notation) is supported
         *      @param string $value     The IP/Subnet
         *      @param string $exclusion_type Reason for the exclusion.
         * @see https://dev.maxmind.com/geoip/privacy-exclusions-api/
         */
        $list = apply_filters('geoip_detect2_maxmind_ccpa_blacklist_ip_subnets', $list);

        self::$list = $list;
    }

    public static function resetList() {
        self::$list = null;
    }
}
new CcpaBlacklistOnLookup;
/*
if (WP_DEBUG) {

    add_filter('geoip_detect2_maxmind_ccpa_blacklist_ip_subnets', function() {
        $ccpaBlacklistStub = [];

        $ccpaBlacklistStub[] = [   
            'exclusion_type' => 'mytest',
            'data_type' => 'network',
            'value' => '1.1.1.1'
        ];
        $ccpaBlacklistStub[] = [   
            'exclusion_type' => 'mytest',
            'data_type' => 'network',
            'value' => '2.2.2.2/24'
        ];
        $ccpaBlacklistStub[] = [   
            'exclusion_type' => 'mytest',
            'data_type' => 'network',
            'value' => '2:2:2:2::2/24'
        ];
        return $ccpaBlacklistStub;
    });
}
*/

class RetrieveCcpaBlacklist {
    public function __construct() {
        $this->addFilters();
    }

    public function addFilters() {
        add_filter('geoip_detect2_maxmind_ccpa_blacklist_ip_subnets', array($this, 'onBlacklistLoad'));
    }

    public function onBlacklistLoad($list) {
        $loadedList = get_option('geoip_detect2_maxmind_ccpa_blacklist');
        if (!is_array($loadedList)) {
            return $list;
        }

        $list = array_merge($list, $loadedList);
        return $list;
    }

    public function retrieveBlacklist() {
        $this->loadCredentials();
        $url = 'https://' . apply_filters('geoip_detect2_maxmind_ccpa_blacklist_url', 'api.maxmind.com/privacy/exclusions');
        $args = array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode( $this->user . ':' . $this->password )
            )
        );
        $response = wp_remote_request($url, $args);
        $body = wp_remote_retrieve_body($response);
        $bodyDecoded = json_decode($body, true);
        if (wp_remote_retrieve_response_code($response) != 200) {
            if (isset($bodyDecoded['error'])) {
                return $bodyDecoded['error'];
            }
            return 'HTTP Error ' . wp_remote_retrieve_response_code($response) . ': ' . $body;
        }
        if (!is_array($bodyDecoded)) {
            return 'Strange: Invalid Json: '  . $body;
        }
        
        return $bodyDecoded;
    }
    
    public function storeBlacklist() {
        $time = time();
        $ret = $this->retrieveBlacklist();
        if (is_string($ret)) {
            if (defined('DOING_CRON') && DOING_CRON) {
                Logger::logIfError($ret, Logger::CATEGORY_CRON);
            }
            return $ret;
        } else {
            $exclusions = $ret['exclusions'];
            update_option('geoip_detect2_maxmind_ccpa_blacklist', $exclusions);
            update_option('geoip_detect2_maxmind_ccpa_blacklist_last_updated', $time);
            return true;
        }
    }

    protected $user = null;
    protected $password = null;

    protected function loadCredentials() {
        if (!is_null($this->user)) return;

        $this->user = get_option(); // ToDo
        $this->password = get_option(); // ToDO
    }

    public function setCredentials($user, $password) {
        $this->user = $user;
        $this->password = $password;
    }
}
new RetrieveCcpaBlacklist;

class CcpaBlacklistCron {

}