<?php

namespace YellowTree\GeoipDetect\DynamicReverseProxies;

class DataAws implements DataProvider {
    public function getIps() : array {
        $url = apply_filters('geoip_detect2_dynamic_reverse_proxies_aws_urls', 'https://ip-ranges.amazonaws.com/ip-ranges.json');
        $types = apply_filters('geoip_detect2_dynamic_reverse_proxies_aws_types', ['CLOUDFRONT']);

        $response = wp_safe_remote_get( $url, [ 'timeout' => 30  ] );
        if (is_wp_error($response)) {
            // ToDO log error
            return [];
        }
        $body = wp_remote_retrieve_body($response);
        $ip_ranges = @json_decode($body, true);

        $ip_ranges = apply_filters('geoip_detect2_dynamic_reverse_proxies_aws_pre_ranges', $ip_ranges);

        $prefixes = [];
        foreach(['prefixes', 'ipv6_prefixes'] as $prefix_type) {
            if (isset($ip_ranges[$prefix_type]) && is_array($ip_ranges[$prefix_type])) {
                $prefixes = array_merge($prefixes, $ip_ranges[$prefix_type]);
            }
        }

        $ips = [];
        foreach ($prefixes as $p) {
            if (in_array($p['service'], $types)) {
                if (!empty($p['ipv6_prefix'])) {
                    $ips[] = $p['ipv6_prefix'];
                } else if (!empty($p['ip_prefix'])) {
                    $ips[] = $p['ip_prefix'];
                }
            }
        }

        return $ips;
    }
}
