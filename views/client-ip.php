<div class="wrap geoip-detect-wrap">
	<h1><?php _e('GeoIP Detection', 'geoip-detect');?> - Client IP Debug Panel</h1>
	<p>
		<a href="tools.php?page=<?php echo GEOIP_PLUGIN_BASENAME ?>"><?php _e('Test IP Detection Lookup', 'geoip-detect')?></a>
		|
		<a href="options-general.php?page=<?php echo GEOIP_PLUGIN_BASENAME ?>"><?php _e('Options', 'geoip-detect');?></a>
	</p>
<pre>This debug panel is listing all relevant informations to debug when the detected client ip is wrong (in case of reverse proxies etc.)</pre>
	<h2>Current IP informations (as detected by the plugin)</h2>
	<p>
		Detected client IP: <?php echo geoip_detect2_get_client_ip(); ?><br>
		External Server IP: <?php echo geoip_detect2_get_external_ip_adress(); ?><br>
		Real client IP (detected without the plugin): <span id="ajax_get_client_ip"><i>Detecting ...</i></span>
	</p>
	<p>
		REMOTE_ADDR: <?php echo $_SERVER['REMOTE_ADDR']; ?><br>
		HTTP_X_FORWARDED_FOR: <?php echo isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : __('(unset)', 'geoip-detect'); ?><br>
		<?php if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && !get_option('geoip-detect-has_reverse_proxy')): ?>
		<i>(Probably you should enable the reverse proxy option.)</i>
		<?php endif; ?>
	</p>
	<h3>Settings</h3>
	<ul>
		<li>Use reverse proxy: <?php echo get_option('geoip-detect-has_reverse_proxy', 0) ? 'yes' : 'no' ?></li>
		<li>Whitelist for known reverse proxies (optional if only one): <?php echo get_option('geoip-detect-trusted_proxy_ips') ?: '(none)'; ?></li>
	</ul>

<script>
	jQuery.ajax(<?php echo json_encode(_geoip_detect2_get_external_ip_services(1, true)[0]); ?>, {
		type: 'GET',
		dataType: 'text',
	}).done(function(ret) {
		jQuery('#ajax_get_client_ip').text(ret);
	}).fail(function(ret) {
		jQuery('#ajax_get_client_ip').text('Failed: ' + ret);
	});
</script>

	<?php require(GEOIP_PLUGIN_DIR . '/views/footer.php'); ?>
</div>
