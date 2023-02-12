<?php

namespace YellowTree\GeoipDetect\DynamicReverseProxies;

class DataCloudflare implements DataProvider {
    public function getIps() : array {
        $urls = apply_filters('geoip_detect2_dynamic_reverse_proxies_cloudflare_urls', ['https://www.cloudflare.com/ips-v4', 'https://www.cloudflare.com/ips-v6']);

        $ips = [];
        foreach ($urls as $url) {
            $response = wp_safe_remote_get( $url, [ 'timeout' => 30  ] );
            if (is_wp_error($response)) {
                // TODO log error
                continue;
            }
            $body = wp_remote_retrieve_body( $response );
            $ips = array_merge($ips, explode("\n", $body));
        }
        return $ips;
    }
}
