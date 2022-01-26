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
		define('GEOIP_DETECT_IPV6_SUPPORTED', geoip_detect_check_ipv6_support());

	if (!defined('GEOIP_DETECT_USER_AGENT'))
		define('GEOIP_DETECT_USER_AGENT', 'Geolocation Detect ' . GEOIP_DETECT_VERSION);

	if (!defined('GEOIP_DETECT_DEBUG')) {
		define('GEOIP_DETECT_DEBUG', WP_DEBUG);
	}
}
add_action('plugins_loaded', 'geoip_detect_defines');



function geoip_detect_check_ipv6_support() {
	if (defined('AF_INET6')) {
		return true;
	}
	return @inet_pton('::1') !== false;
}

// Load Locales
function geoip_detect_load_textdomain() {
  load_plugin_textdomain( 'geoip-detect', false, GEOIP_PLUGIN_DIR . '/languages' );
}
add_action( 'plugins_loaded', 'geoip_detect_load_textdomain' );


function geoip_detect_enqueue_admin_notices() {
	// Nobody would see these notices them anyway.
	if (!is_admin() ||
		!is_user_logged_in() ||
		(defined('DOING_CRON') && DOING_CRON) ||
		(defined('DOING_AJAX') && DOING_AJAX) ) {

		return;
	}

	global $plugin_page;

	// Suggest Maxmind database installation
	if (get_option('geoip-detect-source') === 'hostinfo' && !get_option('geoip-detect-ui-has-chosen-source', false) && current_user_can('manage_options')) {
		if ($plugin_page == GEOIP_PLUGIN_BASENAME && isset($_POST['action']) && $_POST['action'] == 'update') {
			// Skip because maybe he is currently updating the database
		} else {
			add_action( 'all_admin_notices', 'geoip_detect_admin_notice_database_missing' );
		}
	}

	// Nag user to enter Account ID
	$c = new \YellowTree\GeoipDetect\Lib\RetrieveCcpaBlacklist();
	if ((get_option('geoip-detect-source') === 'auto' || get_option('geoip-detect-source') === 'manual')
		&& ! $c->getCredentialsUser() && $c->getCredentialsPassword() 
		&& current_user_can('manage_options')
		&& $plugin_page !== GEOIP_PLUGIN_BASENAME ) {
		add_action( 'all_admin_notices', 'geoip_detect_admin_notice_license_id_missing' );
	}

}
add_action('admin_init', 'geoip_detect_enqueue_admin_notices');

function geoip_detect_is_ignored_notice($id) {
	$ignored_notices = (array) get_user_meta(get_current_user_id(), 'geoip_detect_dismissed_notices', true);
	if (in_array($id, $ignored_notices))
		return true;
	return false;
}

function geoip_detect_admin_notice_template($id, $title, $body, $addButtonDismiss = false) {
	if (geoip_detect_is_ignored_notice($id))
		return;
?>
<div class="error notice is-dismissible">
	<p style="float: right">
		<a href="tools.php?page=<?php echo GEOIP_PLUGIN_BASENAME ?>&geoip_detect_dismiss_notice=<?php echo $id ?>"><?php _e('Dismiss notice', 'geoip-detect'); ?></a>
	</p>

	<h3><?php echo $title; ?></h3>

	<?php echo $body; ?>
	<?php if ($addButtonDismiss) : ?>
	<p>
		<a class="button button-secondary" href="?geoip_detect_dismiss_notice=<?= $id ?>">Hide this notice</a>
	</p>
	<?php endif; ?>
</div>
<?php
}

function geoip_detect_admin_notice_database_missing() {
	$id = 'hostinfo_used';
	$title = __( 'Geolocation IP Detection: No database installed', 'geoip-detect' );
	$url = '<a href="tools.php?page=' . GEOIP_PLUGIN_BASENAME . '">Geolocation IP Detection</a>';

	$line1 = sprintf(__('The Plugin %s is currently using the Webservice <a href="http://hostip.info" target="_blank">hostip.info</a> as data source. <br />You can choose a different data source in the options page.', 'geoip-detect' ), $url);
	$line2 = __('For comparison of the different options, see <a href="https://github.com/yellowtree/geoip-detect/wiki/FAQ#which-data-source-should-i-choose" target="_blank">Which data source should I choose?</a>.', 'geoip-detect');

	$label_options = __('Options', 'geoip-detect');
	$label_keep_using_hostinfo = __('Keep using hostip.info', 'geoip-detect');
	$body = <<<BODY
<p>$line1</p>
<p>$line2</p>

<p>
	<a class="button button-primary" href="options-general.php?page=geoip-detect/geoip-detect.php#choose-source">$label_options</a>
	<a class="button button-secondary" href="?geoip_detect_dismiss_notice=$id">$label_keep_using_hostinfo</a>
</p>
BODY;

	geoip_detect_admin_notice_template($id, $title, $body);
}

function geoip_detect_admin_notice_license_id_missing() {
	$id = 'license_id_missing';
	$title = __( 'Geolocation IP Detection: Maxmind Account ID is missing', 'geoip-detect' );
	$url = '<a href="tools.php?page=' . GEOIP_PLUGIN_BASENAME . '">Geolocation IP Detection</a>';

	$line1 = __('You have entered a License Key in the options, but no Account ID.', 'geoip-detect');
	$line2 = __('Please go to your <a href="https://www.maxmind.com/en/account/login" target="_blank">Maxmind Account</a>, click on "My License Key" and copy your Account ID ("Account/User ID") to the options here.', 'geoip-detect');
	$line3 = __('(It will be used to enable the Privacy Exclusions API.)', 'geoip-detect');

	$label_options = __('Options', 'geoip-detect');
	$body = <<<BODY
<p><i>$line1</i></p>
<p>$line2</p>
<p>$line3</p>

<p>
	<a class="button button-primary" href="options-general.php?page=geoip-detect/geoip-detect.php">$label_options</a>
</p>
BODY;

	geoip_detect_admin_notice_template($id, $title, $body);
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
			$ignored_notices = [];
		} else if (!in_array($dismiss, $ignored_notices)) {
			$ignored_notices[] = $dismiss;
		}

		update_user_meta(get_current_user_id(), 'geoip_detect_dismissed_notices', $ignored_notices);
	}
}
add_action('admin_init', 'geoip_detect_dismiss_message');

// --------------------------------------- Privacy (GDPR) -------------------------------


function geoip_detect_add_privacy_policy_content() {
    if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
        return;
    }

	$registry = DataSourceRegistry::getInstance();

	$caching = '';
 	if (GEOIP_DETECT_READER_CACHE_TIME > 0 && !$registry->isCachingUsed()) {
		$caching = sprintf(__('This site is saving the IP of the visitors of the last %s for performance reasons.', 'geoip-detect'), human_time_diff(0, GEOIP_DETECT_READER_CACHE_TIME));
	}
	if ($registry->isCachingUsed()) {
		$caching = __('This site is sending the visitor\'s IP to (add Provider name) in order to receive the geographic information. (You will need to have a data-processing contract with this provider.)', 'geoip-detect');
	}
	$source = geoip_detect2_get_current_source_description();
    $content = '<p>' . sprintf(__( 'This site is using %s to identify the geographic location of your IP adress. %s (Add here: how this information is used, how long it is retained. Be especially careful when using this information to change prices or selling options, as this might not be legal.)', 'geoip-detect' ),
		$source, $caching) . '</p>';

	if (get_option('geoip-detect-ajax_enabled') ) {
		if ((get_option('geoip-detect-ajax_enqueue_js') || get_option('geoip-detect-set_css_country'))) {
			$content .= '<p>' . __('In order to increase the performance of this site, it is setting a cookie called "geoip-detect-result" containing the geographic information of the current user. (Explain how the information stored in this cookie will be used, e.g.: This information is not used for tracking purposes, but ...) The cookie will automatically deleted after 1 day by your browser.', 'geoip-detect') . '</p>';
		}
	}

    wp_add_privacy_policy_content(
        'Geolocation IP Detection',
        wp_kses_post( wpautop( $content, false ) )
    );
}
add_action( 'admin_init', 'geoip_detect_add_privacy_policy_content' );


/** 
 * This function is called when the user clicks on "Remove" in the wp-admin
 */
function on_uninstall() {
	$registry = DataSourceRegistry::getInstance();
	$registry->uninstall();
}
register_uninstall_hook(GEOIP_PLUGIN_FILE, __NAMESPACE__ . '\\on_uninstall');

// For Debugging purposes ...
/*
if (GEOIP_DETECT_DEBUG && isset($_GET['uninstall']) && $_GET['uninstall'] == 'asdf') {

add_action('plugins_loaded', function() {
	on_uninstall();
});	
}
*/
