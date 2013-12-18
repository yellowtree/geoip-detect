<?php 
$date_format = get_option('date_format') . ' ' . get_option('time_format')
?>
<div class="wrap">
	<h2><?php _e('GeoIP Detect', 'geoip-detect');?></h2>

	<?php if (!empty($message)): ?>
		<p class="error" style="margin-top:10px;">
		<?php echo $message; ?>
		</p>
	<?php endif; ?>
	
	<p>
		<?php printf(__('Last updated: %s', 'geoip-detect'), $last_update ? date_i18n($date_format, $last_update) : __('Never', 'geoip-detect')); ?>
	</p>

	<?php if (!defined('GEOIP_DETECT_AUTO_UPDATE_DEACTIVATED')) : ?>
	<p>
		<?php printf(__('Next update: %s', 'geoip-detect'), $next_cron_update ? date_i18n($date_format, $next_cron_update) : __('Never', 'geoip-detect')); ?><br />
		<em><?php _e('(The file is updated automatically once a week.)', 'geoip-detect'); ?></em>
	</p>
	<?php endif; ?>
	
	<form method="post" action="#">
		<input type="hidden" name="action" value="update" />
		<input type="submit" value="<?php _e('Update now'); ?>" />
	</form>
	
	<br/>
	<h3>GeoIP Lookup</h3>
	<form method="post" action="#">
		<input type="hidden" name="action" value="lookup" />
		<input type="text" name="ip" value="<?php echo isset($_REQUEST['ip']) ? esc_attr($_REQUEST['ip']) : esc_attr($_SERVER['REMOTE_ADDR']); ?>" />
		<input type="submit" value="<?php _e('Lookup', 'geoip-detect'); ?>" />
	</form>
	<?php if ($ip_lookup_result !== false) :
			if (is_object($ip_lookup_result)) : ?>
	<p>
		<?php printf(__('The function %s returns an object:', 'geoip-detect'), "<code>geoip_detect_get_info_from_ip('" . esc_html($_POST['ip']) . "')</code>"); ?>
	</p>
	<table>
		<thead>
			<th><?php _e('Key', 'geoip-detect'); ?></th>
			<th><?php _e('Property Value', 'geoip-detect'); ?></th>
			</thead>
	<?php 
		foreach ($ip_lookup_result as $key => $value)
		{
	?>
		<tr>
			<td><?php echo esc_html($key);?></td>
			<td><?php echo esc_html($value);?></td>
		</tr>
	<?php 
		}
	?>
	</table>
		<?php elseif ($ip_lookup_result == 0) : ?>
			<p>
				<?php _e('No information found about this IP.', 'geoip-detect')?>
			</p>
		<?php endif; ?>
	<?php endif; ?>
	
	<p>
		<br />
		<small><em>This product includes GeoLite data created by MaxMind, available from <a href="http://www.maxmind.com/">http://www.maxmind.com</a>.</em></small>
	</p>
</div>