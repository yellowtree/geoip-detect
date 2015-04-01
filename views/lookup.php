<?php 
$date_format = get_option('date_format') . ' ' . get_option('time_format')
?>
<div class="wrap">
	<h2><?php _e('GeoIP Detection', 'geoip-detect');?></h2>
	<a href="options-general.php?page=<?= GEOIP_PLUGIN_BASENAME ?>">Options</a>
	
	<p>
		<?php printf(__('Selected data source: %s', 'geoip-detect'), geoip_detect2_get_current_source_description() ); ?>
	</p>
	<?php if ($last_update_db) : ?>
	<p>
		<?php printf(__('Database data from: %s', 'geoip-detect'), date_i18n($date_format, $last_update_db) ); ?>
	</p>
	<?php endif; ?>
		
	<form method="post" action="#">
		<input type="hidden" name="action" value="lookup" />
		<input type="text" placeholder="Enter an IP (v4 or v6)" name="ip" value="<?php echo isset($_REQUEST['ip']) ? esc_attr($_REQUEST['ip']) : esc_attr(geoip_detect2_get_client_ip()); ?>" /><br />
		<label>Use these locales: <select name="locales"><option value="">Default (Current site language, english otherwise)</option><option value="en">English only</option><option value="fr,en">French, English otherwise</option></select> </label><br />
		<label><input type="checkbox" name="disable_cache" value="1" /> Do not read values from cache</label><br />
		<br />
		<input type="submit" class="button button-secondary" value="<?php _e('Lookup', 'geoip-detect'); ?>" />
	</form>
	<?php if ($ip_lookup_result !== false) :
			if (is_object($ip_lookup_result)) : $record = $ip_lookup_result; ?>
			<h3>Lookup Result</h3>
	<p>
		<?php printf(__('The function %s returns an object:', 'geoip-detect'), "<code>\$record = geoip_detect2_get_info_from_ip('" . esc_html($_POST['ip']) . "')</code>"); ?><br />
		<?php printf(__('Lookup duration: %.5f s', 'geoip-detect'), $ip_lookup_duration); ?>
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
	
	
	