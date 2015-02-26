<?php 
$date_format = get_option('date_format') . ' ' . get_option('time_format')
?>
<div class="wrap">
	<h2><?php _e('GeoIP Detection', 'geoip-detect');?></h2>

	<?php if (!empty($message)): ?>
		<p class="geoip_detect_error">
		<?php echo $message; ?>
		</p>
	<?php endif; ?>
	
	<?php if ($last_update_db) : ?>
	<p>
		<?php printf(__('Database data from: %s', 'geoip-detect'), date_i18n($date_format, $last_update_db) ); ?>
	</p>
	<?php endif; ?>

	<p>
		<?php printf(__('Last updated: %s', 'geoip-detect'), $last_update ? date_i18n($date_format, $last_update) : __('Never', 'geoip-detect')); ?>
		
		<?php if (!defined('GEOIP_DETECT_AUTO_UPDATE_DEACTIVATED') || !GEOIP_DETECT_AUTO_UPDATE_DEACTIVATED) : ?>
			<br />
			<?php printf(__('Next update: %s', 'geoip-detect'), $next_cron_update ? date_i18n($date_format, $next_cron_update) : __('Never', 'geoip-detect')); ?><br />
			<em><?php _e('(The file is updated automatically once a month.)', 'geoip-detect'); ?></em>
		<?php endif; ?>
	</p>
	
	<form method="post" action="#">
		<input type="hidden" name="action" value="update" />
		<input type="submit" class="button button-primary" value="<?php _e('Update now'); ?>" />
	</form>
	
	<br/>
	<h3>Test GeoIP Lookup manually</h3>
	<form method="post" action="#">
		<input type="hidden" name="action" value="lookup" />
		<input type="text" placeholder="Enter an IP (v4 or v6)" name="ip" value="<?php echo isset($_REQUEST['ip']) ? esc_attr($_REQUEST['ip']) : esc_attr(geoip_detect2_get_client_ip()); ?>" />
		<input type="submit" class="button button-secondary" value="<?php _e('Lookup', 'geoip-detect'); ?>" />
	</form>
	<?php if ($ip_lookup_result !== false) :
			if (is_object($ip_lookup_result)) : $record = $ip_lookup_result; ?>
	<p>
		<?php printf(__('The function %s returns an object:', 'geoip-detect'), "<code>\$record = geoip_detect2_get_info_from_ip('" . esc_html($_POST['ip']) . "')</code>"); ?>
	</p>
	
	<table>
		<tr>
			<th><?php _e('Key', 'geoip-detect'); ?></th>
			<th><?php _e('Value', 'geoip-detect'); ?></th>
		</tr>
	
		<tr>
			<td><code>$record-&gt;city-&gt;name</code></td>
			<td><?php echo esc_html($record->city->name);?></td>
		</tr>
		<tr>
			<td><code>$record-&gt;mostSpecificSubdivision-&gt;isoCode</code></td>
			<td><?php echo esc_html($record->mostSpecificSubdivision->name);?></td>
		</tr>
		<tr>
			<td><code>$record-&gt;mostSpecificSubdivision-&gt;name</code></td>
			<td><?php echo esc_html($record->mostSpecificSubdivision->name);?></td>
		</tr>
		<tr>
			<td><code>$record-&gt;country-&gt;isoCode</code></td>
			<td><?php echo esc_html($record->country->isoCode);?></td>
		</tr>
		<tr>
			<td><code>$record-&gt;country-&gt;name</code></td>
			<td><?php echo esc_html($record->country->name);?></td>
		</tr>
		<tr>
			<td><code>$record-&gt;location-&gt;latitude</code></td>
			<td><?php echo esc_html($record->location->latitude);?></td>
		</tr>
		<tr>
			<td><code>$record-&gt;location-&gt;longitude</code></td>
			<td><?php echo esc_html($record->location->longitude);?></td>
		</tr>
		<tr>
			<td><code>$record-&gt;continent-&gt;code</code></td>
			<td><?php echo esc_html($record->continent->code);?></td>
		</tr>
		<tr>
			<td><code>$record-&gt;location-&gt;timeZone</code></td>
			<td><?php echo esc_html($record->location->timeZone);?></td>
		</tr>

	</table>
		<?php elseif ($ip_lookup_result === 0 || is_null($ip_lookup_result)) : ?>
			<p>
				<?php _e('No information found about this IP.', 'geoip-detect')?>
			</p>
		<?php endif; ?>
	<?php endif; ?>
	<p>
		<?php printf(__('See %s for more documentation.', 'geoip-detect'), '<a href="http://dev.maxmind.com/geoip/geoip2/web-services/" target="_blank">http://dev.maxmind.com/geoip/geoip2/web-services/</a>');?>
	</p>
	
	
	<br /><br />
	<h3>Options</h3>
	<form method="post" action="#">
		<input type="hidden" name="action" value="options" />
	
		<p>
			<input type="checkbox" name="options[set_css_country]" value="1" <?php if ($options['set_css_country']) { echo 'checked="checked"'; } ?>>&nbsp;<?php _e('Add a country-specific CSS class to the &lt;body&gt;-Tag.', 'geoip-detect'); ?><br />
		</p>
		<?php if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) : ?>
		<p>
			<input type="checkbox" name="options[has_reverse_proxy]" value="1" <?php if ($options['has_reverse_proxy']) { echo 'checked="checked"'; } ?>>&nbsp;<?php printf(__('The server is behind a reverse proxy (With Proxy: %s - Without Proxy: %s)', 'geoip-detect'), $_SERVER['HTTP_X_FORWARDED_FOR'], $_SERVER['REMOTE_ADDR']); ?><br />
		</p>
		<?php endif; ?>
		<p>
			<input type="submit" class="button button-primary" value="<?php _e('Save', 'geoip-detect'); ?>" />
		</p>
	</form>
	<p>
		<br />
		<small><em>This product includes GeoLite2 data created by MaxMind, available from <a href="http://www.maxmind.com/">http://www.maxmind.com</a>.</em></small>
	</p>
</div>
<style>
.geoip_detect_error {
    background-color: rgb(255, 255, 255);
    border-left: rgb(255, 0, 0) solid 4px;
    box-shadow: 0 1px 1px 0 rgba(0, 0, 0, 0.1);
    display: inline-block;
    font-size: 14px;
    line-height: 19px;
    margin-bottom: 0;
    margin-left: 2px;
    margin-right: 20px;
    margin-top: 25px;
    padding-bottom: 11px;
    padding-left: 15px;
    padding-right: 15px;
    padding-top: 11px;
    text-align: left;
}
</style>