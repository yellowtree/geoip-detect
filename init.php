<?php
function geoip_detect_defines() {
	if (!defined('GEOIP_DETECT_IP_CACHE_TIME'))
		define('GEOIP_DETECT_IP_CACHE_TIME', 2 * HOUR_IN_SECONDS);
	if (!defined('GEOIP_DETECT_READER_CACHE_TIME'))
		define('GEOIP_DETECT_READER_CACHE_TIME', 7 * DAY_IN_SECONDS);
	if (!defined('GEOIP_DETECT_DOING_UNIT_TESTS'))
		define('GEOIP_DETECT_DOING_UNIT_TESTS', false);
	
	
	if (!defined('GEOIP_DETECT_IPV6_SUPPORTED'))
		define('GEOIP_DETECT_IPV6_SUPPORTED', defined('AF_INET6'));
}
add_action('plugins_loaded', 'geoip_detect_defines');




function geoip_detect_enqueue_admin_notices() {
	// Nobody would see them anyway.
	if (!is_admin() || 
		!is_user_logged_in() ||
		(defined('DOING_CRON') && DOING_CRON) || 
		(defined('DOING_AJAX') && DOING_AJAX) )
		return;

	global $plugin_page;
	
	if (get_option('geoip-detect-source') == 'hostinfo' && get_option('geoip-detect-ui-has-chosen-source', false) == false) {
		if ($plugin_page == GEOIP_PLUGIN_BASENAME && isset($_POST['action']) && $_POST['action'] == 'update') {
			// Skip because maybe he is currently updating the database
		} else {
			add_action( 'all_admin_notices', 'geoip_detect_admin_notice_database_missing' );
		}
	}
}
add_action('admin_init', 'geoip_detect_enqueue_admin_notices');

function geoip_detect_admin_notice_database_missing() {
	$ignored_notices = (array) get_user_meta(get_current_user_id(), 'geoip_detect_dismissed_notices', true);
	if (in_array('hostinfo_used', $ignored_notices))
		return;

	$url = '<a href="tools.php?page=' . GEOIP_PLUGIN_BASENAME . '">GeoIP Detection</a>';
    ?>
<div class="error">
	<p style="float: right">
		<a href="tools.php?page=<?php echo GEOIP_PLUGIN_BASENAME ?>&geoip_detect_dismiss_notice=hostinfo_used"><?php _e('Dismiss notice', 'geoip-detect'); ?></a>
	
	
	<h3><?php _e( 'GeoIP Detection: No database installed', 'geoip-detect' ); ?></h3>
        <p><?php printf(__('The Plugin %s is currently using the Webservice <a href="http://hostip.info" target="_blank">hostip.info</a> as data source. <br />You can click on the button below to download and install Maxmind GeoIPv2 Lite City now.', 'geoip-detect' ), $url); ?></p>
	<p><?php printf(__('This database is licenced <a href="http://creativecommons.org/licenses/by-sa/3.0/">CC BY-SA</a>. See <a href="http://dev.maxmind.com/geoip/geoip2/geolite2/#License">License</a> for details.')); ?>
        
	
	
	<form action="options-general.php?page=<?php echo GEOIP_PLUGIN_BASENAME; ?>"
		method="post">
		<p>
			<input type="hidden" name="source" value="auto" /> <input
				type="hidden" name="action" value="update" /> <input type="submit"
				value="Install now" class="button button-primary" /> &nbsp;&nbsp;<a
				href="?geoip_detect_dismiss_notice=hostinfo_used"><?php _e('Keep using hostip.info', 'geoip-detect'); ?></a>
		</p>
	</form>
    </div>
<?php
}

function geoip_detect_dismiss_message() {
	if (!is_admin() || !is_user_logged_in())
		return;
	
	if (!isset($_GET['geoip_detect_dismiss_notice']))
		return;
		
	$dismiss = $_GET['geoip_detect_dismiss_notice'];
	if ($dismiss) {
		$ignored_notices = (array) get_user_meta(get_current_user_id(), 'geoip_detect_dismissed_notices', true);
		
		if ($dismiss == '-1') { // Undocumented feature: reset dismissed messages
			$ignored_notices = array();
		} else if (!in_array($dismiss, $ignored_notices)) {
			$ignored_notices[] = $dismiss;
		}
		
		update_user_meta(get_current_user_id(), 'geoip_detect_dismissed_notices', $ignored_notices);	
	}
}
add_action('admin_init', 'geoip_detect_dismiss_message');
