<?php

function geoip_detect_version_check() {
   global $wp_version;
   
    if ( version_compare( PHP_VERSION, GEOIP_REQUIRED_PHP_VERSION, '<' ) ) {
        $flag = 'PHP';
    	$min = GEOIP_REQUIRED_PHP_VERSION;
    }
    elseif ( version_compare( $wp_version, GEOIP_REQUIRED_WP_VERSION, '<' ) ) {
        $flag = 'WordPress';
   		$min = GEOIP_REQUIRED_WP_VERSION;
    }
    else
        return;
        
    deactivate_plugins( basename( GEOIP_PLUGIN_FILE ) );
    wp_die('<p>The plugin <strong>GeoIP Detection</strong> plugin requires '.$flag.'  version '.$min.' or greater and was therefore deactivated.</p><p>You can try to install an 1.x version of this plugin.</p>','Plugin Activation Error',  array( 'response'=>200, 'back_link'=>TRUE ) );
}
add_action( 'admin_init', 'geoip_detect_version_check' );



function geoip_detect_defines() {
	if (!defined('GEOIP_DETECT_AUTO_UPDATE_DEACTIVATED'))
		define('GEOIP_DETECT_AUTO_UPDATE_DEACTIVATED', false);
	if (!defined('GEOIP_DETECT_IP_CACHE_TIME'))
		define('GEOIP_DETECT_IP_CACHE_TIME', 2 * HOUR_IN_SECONDS);
}
add_action('plugins_loaded', 'geoip_detect_defines');




function geoip_detect_enqueue_admin_notices() {
	// Nobody would see them anyway.
	if (!is_admin() || 
		(defined('DOING_CRON') && DOING_CRON) || 
		(defined('DOING_AJAX') && DOING_AJAX) )
		return;
	
	$db_file = geoip_detect_get_abs_db_filename();
	if (!$db_file || !file_exists($db_file))
		add_action( 'all_admin_notices', 'geoip_detect_admin_notice_database_missing' );
}
add_action('admin_init', 'geoip_detect_enqueue_admin_notices');

function geoip_detect_admin_notice_database_missing() {
	$ignored_notices = (array) get_user_meta(get_current_user_id(), 'geoip_detect_dismissed_notices', true);
	if (in_array('database_missing', $ignored_notices))
		return;

    ?>
    <div class="error">
       	<p style="float: right"><a href="?geoip_detect_dismiss_notice=database_missing"><?php _e('Dismiss notice', 'geoip-detect'); ?></a>
    	<h3><?php _e( 'GeoIP Detection: Database missing', 'geoip-detect' ); ?></h3>
        <p><?php printf(__( 'The Plugin %s can\'t do its work before you install the IP database. Click on the button below to download and install Maxmind GeoIPv2 Lite City now.', 'geoip-detect' ), '<a href="tools.php?page=geoip-detect/geoip-detect.php">GeoIP Detection</a>'); ?></p>
        <form action="tools.php?page=geoip-detect/geoip-detect.php" method="post">
	        <p>
	        		<input type="hidden" name="action" value="update" />
	        		<input type="submit" value="Install now" class="button button-primary" />
	        </p>
       	</form>
    </div>
    <?php
}

function geoip_detect_dismiss_message() {
	if (!isset($_GET['geoip_detect_dismiss_notice']))
		return;
		
	$dismiss = $_GET['geoip_detect_dismiss_notice'];
	if ($dismiss) {
		$ignored_notices = (array) get_user_meta(get_current_user_id(), 'geoip_detect_dismissed_notices', true);
		
		if (!in_array($dismiss, $ignored_notices)) {
			$ignored_notices[] = $dismiss;
			update_user_meta(get_current_user_id(), 'geoip_detect_dismissed_notices', $ignored_notices);	
		}
	}
}
add_action('admin_init', 'geoip_detect_dismiss_message');
