<div class="wrap geoip-detect-wrap">

	<?php if (!empty($errorMessage)): ?>
			<p class="geoip_detect_error">
			<?php echo $errorMessage; ?>
			</p>
	<?php endif; ?>
	<?php if (!empty($successMessage)): ?>
			<p class="geoip_detect_error geoip_detect_success">
			<?php echo $successMessage; ?>
			</p>
	<?php endif; ?>

	<!-- This page cannot be translated yet, as I am not sure how it will look like in the long-term.
The final goal would be to have a step-by-step wizard helping to set all the relevant options semi-automatically. -->
	<h1><?php _e('Geolocation IP Detection', 'geoip-detect');?> - Client IP Debug Panel</h1>
	<p>
		<a href="tools.php?page=<?php echo GEOIP_PLUGIN_BASENAME ?>"><?php _e('Test IP Detection Lookup', 'geoip-detect')?></a>
		|
		<a href="options-general.php?page=<?php echo GEOIP_PLUGIN_BASENAME ?>"><?php _e('Options', 'geoip-detect');?></a>
	</p>
<pre>This debug panel is listing all relevant informations to debug when the detected client ip is wrong (in case of reverse proxies etc.)
Sometimes, there are proxies between your user and the wordpress server application (e.g. a <abbr title="Content Delivery Network: Amazon, Cloudflare, etc.">CDN</abbr>, reverse proxies).
This needs to be known to the plugin to choose the correct IP adress.
</pre>
	<h2>Current IP informations (as detected by the plugin)</h2>
	<p>
		Detected client IP: <b><?php echo geoip_detect2_get_client_ip(); ?></b><br>
		<span class="detail-box">This IP is used for detecting the geo-information of the user. It should be the same as the real client IP below.</span>
		Real client IP (detected without the plugin): <b><span id="ajax_get_client_ip"><i>Detecting ...</i></span></b>
		<span class="detail-box">This IP is detected within the browser, so reverse proxies of the server are not affected. If you see an error instead of an IP there, try using external websites such as <a href="https://www.whatismyip.com/ " target="_blank">https://www.whatismyip.com/</a> or <a href="https://www.showmyip.com/" target="_blank">https://www.showmyip.com/</a><br></span>
		External Server IP: <b><?php echo geoip_detect2_get_external_ip_adress(); ?></b><br>
		<span class="detail-box">In some cases, the server is in the same network as the client (e.g. testing server). As the connection does not use Internet, this plugin uses the IP adress of the server as seen from the internet.<br>
			For performance reasons, this IP is cached for <?php echo human_time_diff(0, GEOIP_DETECT_IP_CACHE_TIME); ?>.<br>
			Also, for servers without internet access, this IP can be set in the options.
		</span>
	</p>
	<p>
		REMOTE_ADDR: <b><?php echo $_SERVER['REMOTE_ADDR']; ?></b><br>
		<span class="detail-box">In server configurations without reverse proxy, this will equal to the "detected client IP". Otherwise, this is the IP of the reverse proxy.</span>
		HTTP_X_FORWARDED_FOR: <b><?php echo isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : __('(unset)', 'geoip-detect'); ?></b><br>
		<span class="detail-box">Reverse proxies usually add this header to indicate the original IP. If several IPs are given here (seperated by a comma), the correct user IP usually is the leftmost one.</span>
		<?php if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && !get_option('geoip-detect-has_reverse_proxy')): ?>
		<i>(Probably you should enable the reverse proxy option.)</i>
		<?php endif; ?>
	</p>
	<h3>Settings of the plugin</h3>
	<ul>
		<li>Use reverse proxy: <b><?php echo get_option('geoip-detect-has_reverse_proxy', 0) ? 'yes' : 'no' ?></b>
			<span class="detail-box">Enable this option if REMOTE_ADDR is not the correct client IP.</span>
		</li>

		<li>Whitelist for known reverse proxies (optional if only one): <b><?php echo get_option('geoip-detect-trusted_proxy_ips') ?: '(none)'; ?></b>
			<span class="detail-box">All IPs in HTTP_X_FORWARDED_FOR that are not the correct client IP are probably known reverse proxies.<br>(For security reasons, this is not assumed by default: maybe the reverse proxy is not of the server, but a Man-In-The-Middle-Attack ... not very probable but possible.)</span>
		</li>

		<li>Add known proxies of a cloud provider enabled: <b><?php echo get_option('geoip-detect-dynamic_reverse_proxies') ? 'Yes, ' . ucfirst(get_option('geoip-detect-dynamic_reverse_proxy_type', '')) : 'No'; ?></b>
			<span class="detail-box">If your site is hosted by CloudFlare or AWS, this should probably be enabled. It will automatically retrieve the many IP adresses that a reverse proxy of this provider can have, and update the list daily.</span>
			<span class="detail-box">Here is the current list of IP adresses: <b><?= implode(', ', \YellowTree\GeoipDetect\DynamicReverseProxies\addDynamicIps()) ?: '(Empty)' ?></b></span>
			<span class="detail-box">
				Last updated: <b><?= geoip_detect_format_localtime($last_update); ?></b><br>
				Next update:  <b><?= geoip_detect_format_localtime(wp_next_scheduled('geoipdetectdynamicproxiesupdate')); ?></b>
				<?php if(get_option('geoip-detect-dynamic_reverse_proxies')) : ?>
				<form method="POST">
					<?php wp_nonce_field( 'geoip_detect_reload-proxies' ); ?>
					<input type="hidden" name="action" value="reload-proxies" />
					<input type="submit" class="button button-primary" value="Reload now" />
				</form>
				<?php endif; ?>
			</span>
		</li>
	</ul>

<script>
	jQuery.ajax(<?php echo wp_json_encode(_geoip_detect2_get_external_ip_services(1, true)[0]); ?>, {
		type: 'GET',
		dataType: 'text',
	}).done(function(ret) {
		jQuery('#ajax_get_client_ip').text(ret);
	}).fail(function(xhr, status, ret) {
		jQuery('#ajax_get_client_ip').text('Failed: ' + status + ret);
	});
</script>

	<?php require(GEOIP_PLUGIN_DIR . '/views/footer.php'); ?>
</div>
