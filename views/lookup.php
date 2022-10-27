<?php
use YellowTree\GeoipDetect\DataSources\DataSourceRegistry;
$date_format = get_option('date_format') . ' ' . get_option('time_format');

$registry = DataSourceRegistry::getInstance();
$current_source = $registry->getCurrentSource();
$can_be_cached = $registry->isSourceCachable($current_source->getId());

if (!empty($request_ip)) {
	$code = "<code>\$record = ";
	if ($request_ip == $current_ip) {
		$code .= "geoip_detect2_get_info_from_current_ip(";
	} else {
		$code .= "geoip_detect2_get_info_from_ip('" . esc_html($request_ip) . "', ";
	}
	$code .= var_export_short($request_locales, true) . ($request_skipCache ? ', [ \'skipCache\' => TRUE ]' : '') .");</code>";
} else {
	$code = '';
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$message = 'This IP is empty or not in a valid format (IPv4 or IPv6)';
	}
}

$is_ajax_enabled = !!get_option('geoip-detect-ajax_enabled');

// @see https://stackoverflow.com/a/35207172
function var_export_short($data, $return=true)
{
    $dump = var_export($data, true);

    $dump = preg_replace('#(?:\A|\n)([ ]*)array \(#i', '[', $dump); // Starts
    $dump = preg_replace('#\n([ ]*)\),#', "\n$1],", $dump); // Ends
	$dump = preg_replace('#=> \[\n\s+\],\n#', "=> [],\n", $dump); // Empties

    if (gettype($data) == 'object') { // Deal with object states
        $dump = str_replace('__set_state(array(', '__set_state([', $dump);
        $dump = preg_replace('#\)\)$#', "])", $dump);
    } else { 
        $dump = preg_replace('#\)$#', "]", $dump);
	}
	
	$dump = preg_replace('#,[\n\s]*]#m', "\n]", $dump);

    if ($return===true) {
        return $dump;
    } else {
        echo $dump;
    }
}

?>
<div class="wrap geoip-detect-wrap">
	<h1><?= __('Geolocation IP Detection', 'geoip-detect');?></h1>
	<p>
		<a href="options-general.php?page=<?php echo GEOIP_PLUGIN_BASENAME ?>"><?= __('Options', 'geoip-detect');?></a>
	</p>

<?php if (!empty($message)): ?>
	<p class="geoip_detect_error">
		<?php echo $message; ?>
	</p>
<?php endif; ?>
	<p>
		<?php printf(__('<b>Selected data source:</b> %s', 'geoip-detect'), geoip_detect2_get_current_source_description() ); ?>
	</p>

	<p>
		<?php echo $current_source->getStatusInformationHTML(); ?>
		<br />
		<br />
	</p>

	<p>
		<b><?= __('Your current IP:', 'geoip-detect');?></b> <?php echo $current_ip; ?>
		<a href="options-general.php?page=<?php echo GEOIP_PLUGIN_BASENAME ?>&geoip_detect_part=client-ip">(<?= __('Not correct?', 'geoip-detect');?>)</a>
		<?php if (geoip_detect_is_internal_ip(geoip_detect2_get_client_ip())) : ?>
		<br><i>(<?php printf(__('This is an IP internal to your network. When looking up this IP, it will use the external IP of the server instead: %s', 'geoip-detect'), geoip_detect2_get_external_ip_adress()); ?>)</i>
		<?php endif; ?>
	</p>

	<h2><?= __('Test IP Detection Lookup ', 'geoip-detect');?></h2>
	<form method="post" action="#">
		<?php wp_nonce_field( 'geoip_detect_lookup' ); ?>
		<input type="hidden" name="action" value="lookup" />
		<?= __('IP', 'geoip-detect')?>: <input type="text" placeholder="<?= __('Enter an IP (v4 or v6)', 'geoip-detect')?>" name="ip" value="<?php echo isset($_REQUEST['ip']) ? esc_attr($ip) : esc_attr(geoip_detect2_get_client_ip()); ?>" /><br />
		<label><?= __('Use these locales:', 'geoip-detect'); ?>
			<select name="locales">
				<option value="" <?php if (empty($_POST['locales'])) echo 'selected="selected"'?>><?= __('Default (Current site language, English otherwise)', 'geoip-detect')?></option>
				<option value="en" <?php if (!empty($_POST['locales']) && $_POST['locales'] == 'en') echo 'selected="selected"'?>><?= __('English only', 'geoip-detect')?></option>
				<option value="fr,en" <?php if (!empty($_POST['locales']) && $_POST['locales'] == 'fr,en') echo 'selected="selected"'?>><?= __('French, English otherwise', 'geoip-detect')?></option>
			</select> 
		</label><br />
		<label><?= __('Which syntax:', 'geoip-detect'); ?>
			<select name="syntax">
				<option value="php" <?php if (empty($_POST['syntax']) || $_POST['syntax'] === 'php') echo 'selected="selected"'?>><?= __('PHP Syntax') ?></option>
				<option value="shortcode" <?php if (!empty($_POST['syntax']) && $_POST['syntax'] === 'shortcode') echo 'selected="selected"'?>><?= __('Shortcode Syntax') ?></option>
				<option value="js" <?php if (!empty($_POST['syntax']) && $_POST['syntax'] === 'js') echo 'selected="selected"'?>><?= __('JS Syntax') ?></option>
			</select>
		</label><br>
		<label><input type="checkbox" name="skip_cache" value="1" <?php if (!empty($_POST['skip_cache'])) echo 'checked="checked"'?>/><?= __('Skip cache', 'geoip-detect')?></label><br />
		<label><input type="checkbox" name="skip_local_cache" value="1" <?php if (!empty($_POST['skip_local_cache'])) echo 'checked="checked"'?>/><?= __('Skip local cache (only works for <code>geoip_detect2_get_info_from_current_ip</code>)', 'geoip-detect')?></label><br />
		<br />
		<input type="submit" class="button button-primary" value="<?= __('Lookup', 'geoip-detect'); ?>" />
	</form>

	<?php if ($ip_lookup_result !== false) :
			if (is_object($ip_lookup_result)) :
				$record = $ip_lookup_result;
				$data = _geoip_detect_improve_data_for_lookup($record->jsonSerialize());
				$data_short = _geoip_detect_improve_data_for_lookup($record->jsonSerialize(), true);

			?>
	<h3><?= __('Lookup Result', 'geoip-detect'); ?></h3>
	<?php if ($record->extra->cached) : ?>
		<p>
			<i><?php printf(__('(Served from cache. Was cached %s ago)', 'geoip-detect'), human_time_diff($record->extra->cached));?></i>
		</p>
	<?php endif; ?>

	<?php if (geoip_detect_is_internal_ip($request_ip)) : ?>
		<p>
			<i>(<?php printf(__('This is an IP internal to your network. When looking up this IP, it will use the external IP of the server instead: %s', 'geoip-detect'), geoip_detect2_get_external_ip_adress()); ?>)</i>
		</p>
	<?php endif; ?>
	<p>
		<?php if ($_POST['syntax'] == 'php') : ?>
		<?php printf(__('The function %s returns an object:', 'geoip-detect'), $code); ?><br />
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
		<?php printf(__('Lookup duration: %.2f ms', 'geoip-detect'), $ip_lookup_duration * 1000); ?>
		<?php if ($record->extra->cached) : ?><i><?= __('(From cache.)', 'geoip-detect');?></i><?php endif; ?><br>
		<?php printf(__('Lookup duration when called for the second time in the same request: %.4f ms', 'geoip-detect'), $ip_lookup_2nd_duration * 1000); ?>
	</p>
	<?php if ($record->isEmpty) : ?>
	<p class="geoip_detect_error">
		<?php printf(__('No information has been found for the IP %s ...', 'geoip-detect'), esc_html($record->traits->ipAddress)); ?>
	</p>
	<?php endif; ?>
	<?php if ($record->extra->error) : ?>
	<p class="geoip_detect_error">
		<?php echo nl2br(esc_html($record->extra->error)); ?>
	</p>
	<?php endif; ?>

	<table>
		<tr>
			<th></th>
			<th style="text-align: left"><?= __('Value', 'geoip-detect'); ?></th>
		</tr>

		<?php 
		function show_row($record, $key_1, $key_2, $value = null, $class = '') {
			$syntax = sanitize_key($_POST['syntax']);
			$locales = sanitize_text_field(@$_POST['locales']);

			if (is_array($value)) {
				if ($key_2 === 'names') {
					show_row($record, $key_1, 'name', null, $class);
					return;
				}
				if ($key_1 === 'subdivisions') {
					$index = (int) $key_2;
					if ($index === 0) { /* Do it only once! Most specific subdivision is actually not index 0, but the highest index, but it works - as I don't use the index in the following `show_row` */
						foreach ($value as $key_3 => $v) {
							show_row($record, 'most_specific_subdivision', $key_3, $v, $class);
						}
					}
					if ($class == 'all') {
						foreach ($value as $key_3 => $v) {
							$new_key_1 = 'subdivisions' . ( ($syntax === 'php') ? '[' . $index . ']' : '.' . $index);
							show_row($record->subdivisions[$index], $new_key_1, $key_3, $v, $class);
						}
					}
					return;
				}
				/* This is quite complex to do right. Postponed
				if ($key_2 == 'original') {
					// It must be recursive, as we don't know how deep the array is nested. Level 1
					$new_key_1 = $key_1 . ( ($syntax === 'php') ? '->' : '.') . 'original';
					foreach($value as $key_3 => $v) {
						show_row(null, $new_key_1, $key_3, $v, $class);
					}
				}
				if (str_ends_with($key_1, 'original')) {
					// Level 2 and counting
					$new_key_1 = $key_1 . ( ($syntax === 'php') ? '["' . $key_2 . '"]' : '.' . $key_2);
					foreach($value as $key_3 => $v) {
						show_row(null, $new_key_1, $key_3, $v, $class);
					}

				}
				*/
			}
			$camel_key_1 = _geoip_dashes_to_camel_case($key_1);
			$camel_key_2 = _geoip_dashes_to_camel_case($key_2);

			try {
				if (is_object($record) ) {
					if (isset($record->$camel_key_1)) {
						$value = $record->$camel_key_1;
						if (is_object($value)) {
							$value = $value->$camel_key_2;
						}
					} else {
						$value = $record->$camel_key_2;
					}
				}
			} catch(\RuntimeException $e) {
				return; // Access did not work.
			}
			if (!is_string($value)) {
				$value = var_export_short($value, true);
			}

			switch($syntax) {
				case 'shortcode':
					$extra = '';
					if ($locales && $key_2 === 'name') {
						$extra .= ' lang="' . esc_attr($locales) . '"';
					}
					if (!empty($_POST['skip_cache'])) {
						$extra .= ' skip_cache="true"';
					}

					$access = '[geoip_detect2 property="' . $camel_key_1 . '.' . $camel_key_2 . '"' . $extra . ']';
					break;

				case 'js':
					$prop = '"' . $camel_key_1 . '.' . $camel_key_2 . '"';
					if ($locales && $key_2 === 'name') {
						$locales_to_js = array(
							'en' => '"en"',
							'fr,en' => '["fr", "en"]',
						);
						if (isset($locales_to_js[$locales])) {
							$locales_js = $locales_to_js[$locales];
						} else {
							$locales_js = 'NULL';
						}
						$access = 'record.get_with_locales(' . $prop . ', ' . $locales_js .  ')';
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

	<?php if (GEOIP_DETECT_DEBUG) { var_dump($data); } ?>
		<?php elseif ($ip_lookup_result === 0 || is_null($ip_lookup_result)) : ?>
			<p>
				<?= __('No information found about this IP.', 'geoip-detect')?>
			</p>
		<?php endif; ?>
	<?php endif; ?>


	<p><br />
		<?php printf(__('See %s for more documentation.', 'geoip-detect'), '<a href="https://github.com/yellowtree/geoip-detect/wiki/Record-Properties" target="_blank">https://github.com/yellowtree/geoip-detect/wiki/Record-Properties</a>');?>
	</p>

	<?php if ($can_be_cached && GEOIP_DETECT_READER_CACHE_TIME) : ?>
	<p><br />(<?= sprintf(__('All record data from this source is cached locally for %s', 'geoip-detect'), human_time_diff(0, GEOIP_DETECT_IP_CACHE_TIME));?>.)<br />
		<?php if (current_user_can('manage_options') && !wp_using_ext_object_cache()) : ?>
			<form method="post" action="#">
				<?php wp_nonce_field( 'geoip_detect_clear_cache' ); ?>
				<input type="hidden" name="action" value="clear_cache" />
				<input class="button button-secondary" type="submit" value="<?= __('Empty cache', 'geoip-detect')?>" />
			</form>
		<?php endif; ?>
	</p>
	<?php endif; ?>

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