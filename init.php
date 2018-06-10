<?php

use YellowTree\GeoipDetect\DataSources\DataSourceRegistry;

function geoip_detect_defines() {
	if (!defined('GEOIP_DETECT_IP_CACHE_TIME'))
		define('GEOIP_DETECT_IP_CACHE_TIME', 2 * HOUR_IN_SECONDS);
	if (!defined('GEOIP_DETECT_IP_EMPTY_CACHE_TIME'))
		define('GEOIP_DETECT_IP_EMPTY_CACHE_TIME', GEOIP_DETECT_IP_CACHE_TIME);


	if (!defined('GEOIP_DETECT_READER_CACHE_TIME'))
		define('GEOIP_DETECT_READER_CACHE_TIME', 7 * DAY_IN_SECONDS);
	if (!defined('GEOIP_DETECT_DOING_UNIT_TESTS'))
		define('GEOIP_DETECT_DOING_UNIT_TESTS', false);


	if (!defined('GEOIP_DETECT_IPV6_SUPPORTED'))
		define('GEOIP_DETECT_IPV6_SUPPORTED', defined('AF_INET6'));

	if (!defined('GEOIP_DETECT_USER_AGENT'))
		define('GEOIP_DETECT_USER_AGENT', 'GeoIP Detect ' . GEOIP_DETECT_VERSION);
}
add_action('plugins_loaded', 'geoip_detect_defines');


// Load Locales
function geoip_detect_load_textdomain() {
  load_plugin_textdomain( 'geoip-detect', false, GEOIP_PLUGIN_DIR . '/languages' );
}
add_action( 'plugins_loaded', 'geoip_detect_load_textdomain' );


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
	if (in_array('hostinfo_used', $ignored_notices) || !current_user_can('manage_options'))
		return;

	$url = '<a href="tools.php?page=' . GEOIP_PLUGIN_BASENAME . '">GeoIP Detection</a>';
    ?>
<div class="error notice is-dismissible">
	<p style="float: right">
		<a href="tools.php?page=<?php echo GEOIP_PLUGIN_BASENAME ?>&geoip_detect_dismiss_notice=hostinfo_used"><?php _e('Dismiss notice', 'geoip-detect'); ?></a>


	<h3><?php _e( 'GeoIP Detection: No database installed', 'geoip-detect' ); ?></h3>
        <p><?php printf(__('The Plugin %s is currently using the Webservice <a href="http://hostip.info" target="_blank">hostip.info</a> as data source. <br />You can click on the button below to download and install Maxmind GeoIPv2 Lite City now.', 'geoip-detect' ), $url); ?></p>
	<p><?php printf(__('This database is licenced <a href="http://creativecommons.org/licenses/by-sa/3.0/">CC BY-SA</a>. See <a href="http://dev.maxmind.com/geoip/geoip2/geolite2/#License">License</a> for details.', 'geoip-detect')); ?>



	<form action="options-general.php?page=<?php echo GEOIP_PLUGIN_BASENAME; ?>" method="post">
		<?php wp_nonce_field( 'geoip_detect_update' ); ?>
		<input type="hidden" name="source" value="auto" />
		<input type="hidden" name="action" value="update" />
		<p>
				<input type="submit" value="<?php esc_attr_e('Install now', 'geoip-detect');?>" class="button button-primary" /> &nbsp;&nbsp;
				<a href="?geoip_detect_dismiss_notice=hostinfo_used"><?php _e('Keep using hostip.info', 'geoip-detect'); ?></a>
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

// --------------------------------------- Privacy -------------------------------

function geoip_detect_add_privacy_policy_content() {
    if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
        return;
    }

	$caching = '';
 	if (GEOIP_DETECT_READER_CACHE_TIME > 0 && !DataSourceRegistry::getInstance()->isCachingUsed()) {
		$caching = sprintf(__('This site is saving the IP of the visitors of the last %s for performance reasons.', 'geoip-detect'), human_time_diff(0, GEOIP_DETECT_READER_CACHE_TIME));
	}
	$source = geoip_detect2_get_current_source_description();
    $content = sprintf(__( 'This site is using %s to identify the geographic location of your IP adress. %s (Add here: how this information is used, how long it is retained. Be especially careful when using this information to change prices or selling options, as this might not be legal.)', 'geoip-detect' ),
		$source, $caching);

    wp_add_privacy_policy_content(
        'GeoIP Detection',
        wp_kses_post( wpautop( $content, false ) )
    );
}
add_action( 'admin_init', 'geoip_detect_add_privacy_policy_content' );
