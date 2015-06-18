<?php 
use YellowTree\GeoipDetect\DataSources\DataSourceRegistry;
$date_format = get_option('date_format') . ' ' . get_option('time_format');

$current_source = DataSourceRegistry::getInstance()->getCurrentSource();

$shortcode_options = array();
$shortcode_attr = '';
if (!empty($_POST['locales']))
	$shortcode_options['lang'] = $_POST['locales'];
if ($shortcode_options) {
	$shortcode_attr = '';
	foreach($shortcode_options as $key => $value){
		$shortcode_attr .= ' ' . $key.'="'.esc_attr($value).'"';
	}
}
?>
<div class="wrap">
	<h2><?php _e('GeoIP Detection', 'geoip-detect');?></h2>
	<a href="options-general.php?page=<?= GEOIP_PLUGIN_BASENAME ?>">Options</a>
	
	<p>
		<?php printf(__('<b>Selected data source:</b> %s', 'geoip-detect'), geoip_detect2_get_current_source_description() ); ?>
	</p>
		
	<p>
		<?php echo $current_source->getStatusInformationHTML(); ?>
		<br />
		<br />
	</p>	
		
	<form method="post" action="#">
		<input type="hidden" name="action" value="lookup" />
		IP: <input type="text" placeholder="Enter an IP (v4 or v6)" name="ip" value="<?php echo isset($_REQUEST['ip']) ? esc_attr($_REQUEST['ip']) : esc_attr(geoip_detect2_get_client_ip()); ?>" /><br />
		<label>Use these locales: 
			<select name="locales">
				<option value="" <?php if (empty($_POST['locales'])) echo 'selected="selected"'?>>Default (Current site language, English otherwise)</option>
				<option value="en" <?php if (!empty($_POST['locales']) && $_POST['locales'] == 'en') echo 'selected="selected"'?>>English only</option>
				<option value="fr,en" <?php if (!empty($_POST['locales']) && $_POST['locales'] == 'fr,en') echo 'selected="selected"'?>>French, English otherwise</option>
			</select> </label><br />
		<label><input type="checkbox" name="skip_cache" value="1" <?php if (!empty($_POST['skip_cache'])) echo 'checked="checked"'?>/> Skip cache</label><br />
		<br />
		<input type="submit" class="button button-secondary" value="<?php _e('Lookup', 'geoip-detect'); ?>" />
	</form>
	<?php if ($ip_lookup_result !== false) :
			if (is_object($ip_lookup_result)) :
			$record = $ip_lookup_result; 
			?>
	<h3>Lookup Result</h3>
	<p>
		<?php printf(__('The function %s returns an object:', 'geoip-detect'), "<code>\$record = geoip_detect2_get_info_from_ip('" . esc_html($request_ip) . "', " . var_export($request_locales, true) . ($request_skipCache ? ', TRUE' : '') .");</code>"); ?><br />
		<?php printf(__('Lookup duration: %.5f s', 'geoip-detect'), $ip_lookup_duration); ?>
		<?php if ($record->extra->cached) : ?>
			<br /><?php printf(__('(Served from cache. Was cached %s ago)', 'geoip-detect'), human_time_diff($record->extra->cached));?>
		<?php endif; ?>
	</p>
	
	<?php if ($record->extra->error) : ?>
	<p class="geoip_detect_error">
		<?php echo nl2br(esc_html($record->extra->error)); ?>
	</p>
	<?php endif; ?>
	
	<table>
		<tr>
			<th><?php _e('Key', 'geoip-detect'); ?></th>
			<th><?php _e('Value', 'geoip-detect'); ?></th>
			<th><?php _e('Shortcode equivalent', 'geoip-detect'); ?></th>
		</tr>
	
		<tr>
			<td><code>$record-&gt;city-&gt;name</code></td>
			<td><?php echo esc_html($record->city->name);?></td>
			<td><code>[geoip_detect2 property="city"<?php echo $shortcode_attr; ?>]</code></td>
		</tr>
		<tr>
			<td><code>$record-&gt;mostSpecificSubdivision-&gt;isoCode</code></td>
			<td><?php echo esc_html($record->mostSpecificSubdivision->isoCode);?></td>
			<td><code>[geoip_detect2 property="mostSpecificSubdivision.isoCode"]</code></td>
		</tr>
		<tr>
			<td><code>$record-&gt;mostSpecificSubdivision-&gt;name</code></td>
			<td><?php echo esc_html($record->mostSpecificSubdivision->name);?></td>
			<td><code>[geoip_detect2 property="mostSpecificSubdivision"<?php echo $shortcode_attr; ?>]</code></td>
		</tr>
		<tr>
			<td><code>$record-&gt;country-&gt;isoCode</code></td>
			<td><?php echo esc_html($record->country->isoCode);?></td>
			<td><code>[geoip_detect2 property="country.isoCode"]</code></td>
		</tr>
		<tr>
			<td><code>$record-&gt;country-&gt;name</code></td>
			<td><?php echo esc_html($record->country->name);?></td>
			<td><code>[geoip_detect2 property="country"<?php echo $shortcode_attr; ?>]</code></td>
		</tr>
		<tr>
			<td><code>$record-&gt;location-&gt;latitude</code></td>
			<td><?php echo esc_html($record->location->latitude);?></td>
			<td><code>[geoip_detect2 property="location.latitude"]</code></td>
		</tr>
		<tr>
			<td><code>$record-&gt;location-&gt;longitude</code></td>
			<td><?php echo esc_html($record->location->longitude);?></td>
			<td><code>[geoip_detect2 property="location.longitude"]</code></td>
		</tr>
		<tr>
			<td><code>$record-&gt;continent-&gt;code</code></td>
			<td><?php echo esc_html($record->continent->code);?></td>
			<td><code>[geoip_detect2 property="continent.code"]</code></td>
		</tr>
		<tr>
			<td><code>$record-&gt;location-&gt;timeZone</code></td>
			<td><?php echo esc_html($record->location->timeZone);?></td>
			<td><code>[geoip_detect2 property="location.timeZone"]</code></td>
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
