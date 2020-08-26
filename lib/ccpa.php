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

class CcpaBlacklist {
    protected $list = null;

    public function __construct() {
        $this->addFilters();
    }

    public function addFilters() {
        add_filter('geoip_detect2_record_data_override_lookup', array($this, 'onBeforeLookup'), 9, 3);
    }

    public function onBeforeLookup($data, $ip, $options) {
        $exclusionReason = $this->isIpOnList($ip);
        
        if ($exclusionReason) {
            $data = array();
            $currentSourceId = DataSourceRegistry::getInstance()->getSource($options['source'])->getId();
            $errorMessage = sprintf(__('This IP has no informations attached by request of the IP owner (Reason: %s).', 'geoip-detect'), $exclusionReason);
            
            $data = _geoip_detect2_record_enrich_data($data, $ip, $currentSourceId, $errorMessage);
        }
        return $data;
    }

    protected function isIpOnList($ip) {
        $this->lazyLoadList();

        foreach ($this->list as $row) {
            if ($this->doesIpMatchRow($ip, $row)) {
                return true;
            }
        }
        return false;
    }

    protected function doesIpMatchRow($ip, $row) {
        if (empty($row['data_type']) || empty($row['value'])) return false;

        switch($row['data_type']) {
            case 'network':
                return geoip_detect_is_ip_equal($ip, $row['value']);
            default:
                return false;
        }
    }

    protected function lazyLoadList() {
        if (!is_null($this->list)) return;

        $list = array();
        // ToDo Load from cache

        /**
         * Filter: geoip_detect2_maxmind_ccpa_blacklist_ip_subnets
         * @param array(array) $data (The array key names are documented on the maxmind page)
         *      @param string $data_type Currently, only the value 'network' (IP/Subnet in CIDR notation) is supported
         *      @param string $value     The IP/Subnet
         *      @param string $exclusion_type Reason for the exclusion.
         * @see https://dev.maxmind.com/geoip/privacy-exclusions-api/
         */
        $list = apply_filters('geoip_detect2_maxmind_ccpa_blacklist_ip_subnets', $list);

        $this->list = $list;
    }

    
}
new CcpaBlacklist;