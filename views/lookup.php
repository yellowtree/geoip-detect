<?php
use YellowTree\GeoipDetect\DataSources\DataSourceRegistry;
$date_format = get_option('date_format') . ' ' . get_option('time_format');

$current_source = DataSourceRegistry::getInstance()->getCurrentSource();

$is_ajax_enabled = !!get_option('geoip-detect-ajax_enabled');
?>
<div class="wrap geoip-detect-wrap">
	<h1><?php _e('GeoIP Detection', 'geoip-detect');?></h1>
	<p>
		<a href="options-general.php?page=<?php echo GEOIP_PLUGIN_BASENAME ?>"><?php _e('Options', 'geoip-detect');?></a>
	</p>

	<p>
		<?php printf(__('<b>Selected data source:</b> %s', 'geoip-detect'), geoip_detect2_get_current_source_description() ); ?>
	</p>

	<p>
		<?php echo $current_source->getStatusInformationHTML(); ?>
		<br />
		<br />
	</p>

	<p>
		<b><?php _e('Your current IP:', 'geoip-detect');?></b> <?php echo geoip_detect2_get_client_ip(); ?>
		<a href="options-general.php?page=<?php echo GEOIP_PLUGIN_BASENAME ?>&geoip_detect_part=client-ip">(<?php _e('Not correct?', 'geoip-detect');?>)</a>
	</p>

	<h2><?php _e('Test IP Detection Lookup ', 'geoip-detect');?></h2>
	<form method="post" action="#">
		<?php wp_nonce_field( 'geoip_detect_lookup' ); ?>
		<input type="hidden" name="action" value="lookup" />
		<?php _e('IP', 'geoip-detect')?>: <input type="text" placeholder="<?php _e('Enter an IP (v4 or v6)', 'geoip-detect')?>" name="ip" value="<?php echo isset($_REQUEST['ip']) ? esc_attr($_REQUEST['ip']) : esc_attr(geoip_detect2_get_client_ip()); ?>" /><br />
		<label><?php _e('Use these locales:', 'geoip-detect'); ?>
			<select name="locales">
				<option value="" <?php if (empty($_POST['locales'])) echo 'selected="selected"'?>><?php _e('Default (Current site language, English otherwise)', 'geoip-detect')?></option>
				<option value="en" <?php if (!empty($_POST['locales']) && $_POST['locales'] == 'en') echo 'selected="selected"'?>><?php _e('English only', 'geoip-detect')?></option>
				<option value="fr,en" <?php if (!empty($_POST['locales']) && $_POST['locales'] == 'fr,en') echo 'selected="selected"'?>><?php _e('French, English otherwise', 'geoip-detect')?></option>
			</select> 
		</label><br />
		<label><?php _e('Which syntax:', 'geoip_detect'); ?>
			<select name="syntax">
				<option value="php" <?php if (empty($_POST['syntax']) || $_POST['syntax'] === 'php') echo 'selected="selected"'?>><?= __('PHP Syntax') ?></option>
				<option value="shortcode" <?php if (!empty($_POST['syntax']) && $_POST['syntax'] === 'shortcode') echo 'selected="selected"'?>><?= __('Shortcode Syntax') ?></option>
				<option value="js" <?php if (!empty($_POST['syntax']) && $_POST['syntax'] === 'js') echo 'selected="selected"'?>><?= __('JS Syntax') ?></option>
			</select>
		</label><br>
		<label><input type="checkbox" name="skip_cache" value="1" <?php if (!empty($_POST['skip_cache'])) echo 'checked="checked"'?>/><?php _e('Skip cache', 'geoip-detect')?></label><br />
		<br />
		<input type="submit" class="button button-primary" value="<?php _e('Lookup', 'geoip-detect'); ?>" />
	</form>
	<?php if ($ip_lookup_result !== false) :
			if (is_object($ip_lookup_result)) :
				$record = $ip_lookup_result;
				$data = _geoip_detect_improve_data_for_lookup($record->jsonSerialize());
				$data_short = _geoip_detect_improve_data_for_lookup($record->jsonSerialize(), true);

			?>
	<h3><?php _e('Lookup Result', 'geoip-detect'); ?></h3>
	<p>
		<?php if ($_POST['syntax'] == 'php') : ?>
		<?php printf(__('The function %s returns an object:', 'geoip-detect'), "<code>\$record = geoip_detect2_get_info_from_ip('" . esc_html($request_ip) . "', " . var_export($request_locales, true) . ($request_skipCache ? ', TRUE' : '') .");</code>"); ?><br />
		<?= sprintf(__('See %s for more information.', 'geoip-detect'), '<a href="https://github.com/yellowtree/geoip-detect/wiki/API:-PHP">API: PHP</a>'); ?>
		<?php elseif ($_POST['syntax'] == 'shortcode') : ?>
		<?= sprintf(__('You can use the following shortcodes.', 'geoip-detect')); ?><br />
		<?= sprintf(__('See %s for more information.', 'geoip-detect'), '<a href="https://github.com/yellowtree/geoip-detect/wiki/API:-Shortcodes">API: Shortcodes</a>'); ?>
		<?php elseif ($_POST['syntax'] == 'js') : ?>
		<?= sprintf(__('AJAX and JS must be enabled in the preferences!', 'geoip-detect')); ?><br />
		<?= sprintf(__('See %s for more information.', 'geoip-detect'), '<a href="https://github.com/yellowtree/geoip-detect/wiki/API:-AJAX">API: AJAX</a>'); ?>
		<?php endif; ?>
	</p>
	<p>
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
			<th></th>
			<th style="text-align: left"><?php _e('Value', 'geoip-detect'); ?></th>
		</tr>

		<?php 
		function show_row($record, $key_1, $key_2, $value = null, $class = '') {
			if (is_array($value)) {
				if ($key_2 === 'names') {
					show_row($record, $key_1, 'name', null, $class);
					return;
				}
				if ($key_1 === 'subdivisions') {
					foreach ($value as $key_3 => $v) {
						show_row($record, 'most_specific_subdivision', $key_3, $v, $class);
					}
					return;
				}
			}
			$camel_key_1 = _geoip_dashes_to_camel_case($key_1);
			$camel_key_2 = _geoip_dashes_to_camel_case($key_2);

			try {
				if (is_object($record) ) {
					$value = $record->$camel_key_1;
					if (is_object($value)) {
						$value = $value->$camel_key_2;
					}
				}
			} catch(\RuntimeException $e) {
				return; // Access did not work.
			}
			if (!is_string($value)) {
				$value = var_export($value, true);
			}

			switch($_POST['syntax']) {
				case 'shortcode':
					$extra = '';
					if (!empty($_POST['locales']) && $key_2 === 'name') {
						$extra .= ' lang="' . $_POST['locales'] . '"';
					}
					if (!empty($_POST['skip_cache'])) {
						$extra .= ' skip_cache="true"';
					}

					$access = '[geoip_detect2 property="' . $camel_key_1 . '.' . $camel_key_2 . '"' . $extra . ']';
					break;

				case 'js':
					$prop = '"' . $key_1 . '.' . $key_2 . '"';
					if (!empty($_POST['locales']) && $key_2 === 'name') {
						$access = 'record.get_with_locales(' . $prop . ', ' . json_encode(explode(',', $_POST['locales'])) .  ')';
					} else {
						$access = 'record.get(' . $prop . ')';
					}
					break;

				case 'php':
				default:
					$access = '$record->' . $camel_key_1 . '->' . $camel_key_2;
					break;
			}
?>
		<tr class="<?= $class ?>">
			<td><code><?= esc_html($access) ?></code></td>
			<td><?= esc_html($value);?></td>
		</tr>

<?php

		}
		
		foreach($data as $key_1 => $value_1) {
			if (is_array($value_1)) {
				foreach($value_1 as $key_2 => $value_2) {
					show_row($record, $key_1, $key_2, $value_2, 'all');
				}
			}
		} 

		foreach($data_short as $key_1 => $value_1) {
			if (is_array($value_1)) {
				foreach($value_1 as $key_2 => $value_2) {
					show_row($record, $key_1, $key_2, $value_2, 'short');
				}
			}
		} 
		
		?>

	</table>

	<p class="all"><a href="#" onclick="geoip_properties_toggle('short', 'all'); return false;"><?= __('Show only the most common properties', 'geoip-detect') ?></a></p>
	<p class="short"><a href="#" onclick="geoip_properties_toggle('all', 'short'); return false;"><?= __('Show all available properties', 'geoip-detect') ?></a></p>
	<p><?= __('(More properties might be available for other IPs and with other data sources.)', 'geoip-detect'); ?></p>

		<?php elseif ($ip_lookup_result === 0 || is_null($ip_lookup_result)) : ?>
			<p>
				<?php _e('No information found about this IP.', 'geoip-detect')?>
			</p>
		<?php endif; ?>
	<?php endif; ?>
	<p>
		<?php printf(__('See %s for more documentation.', 'geoip-detect'), '<a href="http://dev.maxmind.com/geoip/geoip2/web-services/" target="_blank">http://dev.maxmind.com/geoip/geoip2/web-services/</a>');?>
	</p>
	<?php require(GEOIP_PLUGIN_DIR . '/views/footer.php'); ?>
</div>
<script>
function geoip_properties_toggle(show, hide) {
	jQuery('.' + show).show();
	jQuery('.' + hide).hide();
}
jQuery('document').ready(function() {
	jQuery('.all').hide();
})
</script>