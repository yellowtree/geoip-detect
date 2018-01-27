<div class="wrap geoip-detect-wrap">
	<h1><?php _e('GeoIP Detection', 'geoip-detect');?> - Client IP</h1>
	<p>
		<a href="tools.php?page=<?php echo GEOIP_PLUGIN_BASENAME ?>"><?php _e('Test IP Detection Lookup', 'geoip-detect')?></a>
		|
		<a href="options-general.php?page=<?php echo GEOIP_PLUGIN_BASENAME ?>"><?php _e('Options', 'geoip-detect');?></a>
	</p>
	
	<h2>Current IP:</h2>
	<p>
		Detected client IP: <?php echo geoip_detect2_get_client_ip(); ?><br>
		External Server IP: <?php echo geoip_detect2_get_external_ip_adress(); ?>
	</p>
	<p>
		REMOTE_ADDR: <?php echo $_SERVER['REMOTE_ADDR']; ?><br>
		<?php if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])): ?>
		HTTP_X_FORWARDED_FOR: <?php echo $_SERVER["HTTP_X_FORWARDED_FOR"]; ?><br>
		<?php if (!get_option('geoip-detect-has_reverse_proxy')): ?>
		<i>(Probably you should enable the reverse proxy option.)</i>
		<?php endif; ?>
		<?php endif; ?>
	</p>	
	
	<?php require(GEOIP_PLUGIN_DIR . '/views/footer.php'); ?>
</div>	