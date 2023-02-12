<?php
$options = $currentSource->getParameterHTML();
$currentSourceId = $currentSource->getId();
?>

<div class="wrap">
	<h1><?php _e('Geolocation IP Detection', 'geoip-detect');?></h1>
	<p><a href="tools.php?page=<?php echo GEOIP_PLUGIN_BASENAME ?>"><?php _e('Test IP Detection Lookup', 'geoip-detect')?></a></p>
<?php if (!empty($message)): ?>
		<p class="geoip_detect_error">
		<?php echo $message; ?>
		</p>
<?php endif; ?>
<?php if (!empty($last_cron_error_msg)): ?>
<div class="error notice is-dismissible">
	<p style="float: right">
		<a href="options-general.php?page=<?php echo GEOIP_PLUGIN_BASENAME ?>&geoip_detect_dismiss_log_notice=cron"><?php _e('Dismiss notice', 'geoip-detect'); ?></a>
	</p>
	<p>
			<b><?php _e('An error occured on Cron Execution (background task):', 'geoip-detect'); ?></b><br>
		<?php echo esc_html($last_cron_error_msg); ?>
		<p>
			<a class="button button-secondary" href="options-general.php?page=<?php echo GEOIP_PLUGIN_BASENAME ?>&geoip_detect_dismiss_log_notice=cron"><?php _e('Dismiss notice', 'geoip-detect'); ?></a>
		</p>
	</p>
</div>
<?php endif; ?>
	<p>
		<?php printf(__('<b>Selected data source:</b> %s', 'geoip-detect'), geoip_detect2_get_current_source_description() ); ?>
	</p>
	<p>
		<?php echo $currentSource->getStatusInformationHTML(); ?>
	</p>
	<?php if ($options) : ?>
	<h2><?php _e('Options for this data source', 'geoip-detect'); ?></h2>
	<p>
		<form method="post" action="#">
			<input type="hidden" name="action" value="options-source" />
			<?php wp_nonce_field( 'geoip_detect_options-source' ); ?>
			<p><?php echo $options; ?></p>
			<p>
			<input type="submit" class="button button-primary" value="<?php _e('Save', 'geoip-detect'); ?>" />
			</p>
		</form>
	</p>
	<?php endif; ?>
	<br/>

	<a name="choose-source"></a>
	<br /><br />
	<form method="post" action="#">
		<input type="hidden" name="action" value="choose" />
		<?php wp_nonce_field( 'geoip_detect_choose' ); ?>
		<h2><?php _e('Choose data source:', 'geoip-detect'); ?></h2>
		<a href="https://github.com/yellowtree/wp-geoip-detect/wiki/FAQ#which-data-source-should-i-choose"><?php _e('Help', 'geoip-detect'); ?></a>
		<?php foreach ($sources as $s) : $id = $s->getId();?>
			<p><label><input type="radio" name="options[source]" value="<?php echo $id ?>" <?php if ($currentSourceId == $id) { echo 'checked="checked"'; } ?> /><?php echo $s->getLabel(); ?></label></p>
			<span class="detail-box">
				<?php echo $s->getDescriptionHTML(); ?>
			</span>
		<?php endforeach; ?>
		<br />
		<input type="submit" class="button button-primary" value="<?php _e('Save', 'geoip-detect'); ?>" />
	</form>
	<form method="post" action="#">
		<input type="hidden" name="action" value="options" />
		<?php wp_nonce_field( 'geoip_detect_options' ); ?>

		<h3><?php _e('General Options', 'geoip-detect'); ?></h3>

	
		<p>
			<label><input type="checkbox" name="options[set_css_country]" value="1" <?php if (!empty($wp_options['set_css_country'])) { echo 'checked="checked"'; } ?>>&nbsp;<?php _e('Add a country-specific CSS class to the &lt;body&gt;-Tag on every page.', 'geoip-detect'); ?></label><br />
		</p>

		<p>
			<label><input type="checkbox" name="options[disable_pagecache]" value="1" <?php if (!empty($wp_options['disable_pagecache'])) { echo 'checked="checked"'; } ?>>&nbsp;<?php _e('Disable caching a page that contains a shortcode or API call to geo-dependent functions.', 'geoip-detect'); ?></label><br />
			<span class="detail-box">
				<?php _e('At least WP SuperCache, W3TotalCache and ZenCache are supported.', 'geoip-detect'); ?>
			</span>
			<?php if (!empty($wp_options['set_css_country']) && !empty($wp_options['disable_pagecache']) && empty($wp_options['ajax_enabled'])): ?>
			<span class="geoip_detect_error"><?php _e('Warning: As the CSS option above is active, this means that all pages are not cached.', 'geoip-detect'); ?></span>
			<?php endif; ?>
		</p>

		<p>
			<label><input type="checkbox" name="options[ajax_enabled]" value="1" <?php if (!empty($wp_options['ajax_enabled'])) { echo 'checked="checked"'; } ?>> <?php _e('Enable AJAX endpoint to get the information for the current IP even on cached pages.', 'geoip-detect'); ?></label>
		</p>
			<?php if (in_array($currentSourceId, [ 'precision', 'ipstack', 'fastah' ]) && !empty($wp_options['ajax_enabled'])): ?>
				<span class="geoip_detect_error" style="margin-top: 0;"><?php printf(__('Warning: In theory, other websites could use your API credits over AJAX, this cannot be prevented completely (see <a href="%s" target="_blank">documentation</a> for more infos). You should use a different data source or disable AJAX.', 'geoip-detect'), 'https://github.com/yellowtree/geoip-detect/wiki/JS-API-Documentation'); ?></span>
			<?php endif; ?>
		<p style="margin-left: 30px;">
			<label><input type="checkbox" name="options[ajax_enqueue_js]" value="1" <?php if (!empty($wp_options['ajax_enqueue_js'])) { echo 'checked="checked"'; } ?>> <?php _e('Add JS Helper functions to all pages.', 'geoip-detect'); ?></label>
			<span class="detail-box">
				<?php _e('This enables you code geo-dependent behavior in JS (see <a href="https://github.com/yellowtree/geoip-detect/wiki/API%3A-AJAX" target="_blank">documentation</a>)', 'geoip-detect'); ?>
			</span>
			<label><input type="checkbox" name="options[ajax_set_css_country]" value="1" <?php if (!empty($wp_options['ajax_set_css_country'])) { echo 'checked="checked"'; } ?>> <?php _e('Add a country-specific CSS class to the &lt;body&gt;-Tag (via AJAX).', 'geoip-detect'); ?></label>
			<span class="detail-box">
				<?php _e('This requires the JS Helper functions, either by ticking the option above, or by enqueuing it manually for the sites that need it.', 'geoip-detect'); ?>
			</span>
			<label><input type="checkbox" name="options[ajax_shortcodes]" value="1" <?php if (!empty($wp_options['ajax_shortcodes'])) { echo 'checked="checked"'; } ?>><?php _e('Resolve shortcodes (via AJAX).', 'geoip-detect'); ?></label>
			<span class="detail-box">
				<?php _e('(JS Helper functions are added automatically for pages that contain ajax shortcodes.)', 'geoip-detect'); ?><br>
			</span>
		</p>

		<p>

			<label><input type="checkbox" name="options[has_reverse_proxy]" value="1" <?php if (!empty($wp_options['has_reverse_proxy'])) { echo 'checked="checked"'; } ?>>&nbsp;<?php _e('The server is behind a reverse proxy', 'geoip-detect')?></label>
			<a href="options-general.php?page=<?php echo GEOIP_PLUGIN_BASENAME ?>&geoip_detect_part=client-ip">(<?php _e('Client IP debug panel', 'geoip-detect');?>)</a>
			<span class="detail-box">
			<?php if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) : ?>
			<?php printf(__('(With Proxy: %s - Without Proxy: %s - Client IP with current configuration: %s)', 'geoip-detect'), $_SERVER['HTTP_X_FORWARDED_FOR'], $_SERVER['REMOTE_ADDR'], geoip_detect2_get_client_ip()); ?><br />
			<?php else: ?>
			<?php echo __("(This doesn't seem to be the case.)", 'geoip-detect'); ?>
			<?php endif; ?>
			</span>
		</p>
		<p style="margin-left: 30px;">
			<label><?php _e('IPs of trusted proxies:', 'geoip-detect'); ?><input type="text" name="options[trusted_proxy_ips]" value="<?php echo esc_attr($wp_options['trusted_proxy_ips']); ?>" placeholder="1.1.1.1, 1234::1, 2.2.2.2/24" />
			<span class="detail-box">
				<?php if (empty($wp_options['has_reverse_proxy']) && !empty($wp_options['trusted_proxy_ips'])) : ?>
					<span style="color:red">
						<?php printf(__('Warning: As you didn\'t tick the option "%s" above, setting trusted IPs has no effect. This is only used for reverse proxies.', 'geoip-detect'), __('The server is behind a reverse proxy', 'geoip-detect') ); ?>
					</span><br>
				<?php endif; ?>
				<?php _e('If specified, only IPs in this list will be treated as proxy.', 'geoip-detect'); ?><br>
				<?php _e('Make sure to add both IPv4 and IPv6 adresses of the proxy!', 'geoip-detect'); ?>
			</span>
			<label><input type="checkbox" name="options[dynamic_reverse_proxies]" value="1" <?php if (!empty($wp_options['dynamic_reverse_proxies'])) { echo 'checked="checked"'; } ?>>
					<?php _e('Add known proxies of this provider:', 'geoip-detect'); ?></label>
			<select name="options[dynamic_reverse_proxy_type]">
				<option value="cloudflare" <?= (!empty($wp_options['dynamic_reverse_proxy_type']) && $wp_options['dynamic_reverse_proxy_type'] == 'cloudflare') ? 'selected="selected"': '' ?>>Cloudflare</option>
				<option value="aws"        <?= (!empty($wp_options['dynamic_reverse_proxy_type']) && $wp_options['dynamic_reverse_proxy_type'] == 'aws'       ) ? 'selected="selected"': '' ?>>AWS CloudFront</option>
			</select>
			<span class="detail-box">
				<?php if (empty($wp_options['has_reverse_proxy']) && !empty($wp_options['dynamic_reverse_proxies'])) : ?>
					<span style="color:red">
						<?php printf(__('Warning: As you didn\'t tick the option "%s" above, setting trusted IPs has no effect. This is only used for reverse proxies.', 'geoip-detect'), __('The server is behind a reverse proxy', 'geoip-detect') ); ?>
					</span><br>
				<?php endif; ?>
				<?php _e('The list of know proxies will be automatically updated daily.', 'geoip-detect'); ?>
				<?php if (!empty($wp_options['dynamic_reverse_proxies'])) {
					printf(__('(%d IPs of known proxies found.)', 'geoip-detect'), count(\YellowTree\GeoipDetect\DynamicReverseProxies\addDynamicIps()));
				} ?>
				<br>
			</span>
		</p>

		<p>
			<label><?php _e('External IP of this server:', 'geoip-detect'); ?> <input type="text" name="options[external_ip]" value="<?php echo esc_attr($wp_options['external_ip']); ?>" placeholder="<?php _e('detect automatically', 'geoip-detect'); ?>" /></label>
			<span class="detail-box">
			<?php _e('Current value:', 'geoip-detect'); ?> <?php echo geoip_detect2_get_external_ip_adress(); ?><br />
			<?php _e('If empty: Try to use an ip service to detect it (Internet connection is necessary). If this is not possible, 0.0.0.0 will be returned.', 'geoip-detect'); ?><br />
			<?php _e('(This external adress will be used when the request IP adress is not a public IP, e.g. 127.0.0.1)', 'geoip-detect'); ?>
			</span>
		</p>


		<p>
			<input type="submit" class="button button-primary" value="<?php _e('Save', 'geoip-detect'); ?>" />
		</p>
	</form>
	<?php if (!$ipv6_supported) : ?>
	<div class="geoip_detect_error">
		<h3><?php _e('IPv6 not supported', 'geoip-detect'); ?></h3>
		<p>
			<?php _e('Your version of PHP is compiled without IPv6-support, so it is not possible to lookup adresses like "2001:4860:4801:5::91". For more information see <a href="https://php.net/manual/en/function.inet-pton.php">PHP documentation & user comments</a>.', 'geoip-detect'); ?>
		</p>
	</div>
	<?php endif; ?>
	<?php require(GEOIP_PLUGIN_DIR . '/views/footer.php'); ?>
</div>
