<?php

/**
 * Register the Smart Tag so it will be available to select in the form builder.
 *
 * @link   https://wpforms.com/developers/how-to-create-a-custom-smart-tag/
 */
 
function geoip_detect2_shortcode_register_smarttag( $tags ) { 
    $tags[ 'geoip_detect2_user_info' ]                      = __('Geolocation IP Detection: All detected user infos', 'geoip-detect');
    $tags[ 'geoip_detect2_property_country' ]               = __('Geolocation IP Detection: User country', 'geoip-detect');
    $tags[ 'geoip_detect2_property_region' ]                = __('Geolocation IP Detection: User state or region', 'geoip-detect');
    $tags[ 'geoip_detect2_property_city' ]                  = __('Geolocation IP Detection: User city', 'geoip-detect');
    $tags[ 'geoip_detect2_get_client_ip' ]                  = __('Geolocation IP Detection: User IP address', 'geoip-detect');
    $tags[ 'geoip_detect2_get_current_source_description' ] = __('Geolocation IP Detection: Description of current data source', 'geoip-detect');
 
    return $tags;
}
add_filter( 'wpforms_smart_tags', 'geoip_detect2_shortcode_register_smarttag', 10, 1 );
 
/**
 * Process the Smart Tag.
 *
 * @link   https://wpforms.com/developers/how-to-create-a-custom-smart-tag/
 */
 
function geoip_detect2_shortcode_process_smarttag( $content, $tag ) { 
    if (str_starts_with($tag, 'geoip_detect2_')) {
        $generatedContent = geoip_detect2_shortcode_user_info_wpcf7( '', $tag, true );
  
        if ($generatedContent) {
            $content = str_replace( '{' . $tag . '}', $generatedContent, $content );
        }

    }
 
    return $content;
}
add_filter( 'wpforms_smart_tag_process', 'geoip_detect2_shortcode_process_smarttag', 10, 2 );