<?php 

?>

<div class="wrap">
	<h2><?php _e('GeoIP Detection', 'geoip-detect');?></h2>
<?php if (!empty($message)): ?>
		<p class="geoip_detect_error">
		<?php echo $message; ?>
		</p>
<?php endif; ?>
	
	<p>
		<?php printf(__('Selected data source: %s', 'geoip-detect'), geoip_detect2_get_current_source_description() ); ?>
	</p>
	<p>
		<?php echo $currentSource->getStatusInformationHTML(); ?>
	</p>
	<?php if ($options['source'] == 'hostinfo') : ?>
	<p>
		You can choose a Maxmind database below.
	</p>
	<?php endif; ?>
	<br/>
	<a href="tools.php?page=<?= GEOIP_PLUGIN_BASENAME ?>">Test IP Detection Lookup</a>

	<br /><br />
	<h3>Options</h3>
	<form method="post" action="#">
		<input type="hidden" name="action" value="options" />
	
		<p>
			<input type="checkbox" name="options[set_css_country]" value="1" <?php if ($options['set_css_country']) { echo 'checked="checked"'; } ?>>&nbsp;<?php _e('Add a country-specific CSS class to the &lt;body&gt;-Tag.', 'geoip-detect'); ?><br />
		</p>
		<p>
			<input type="checkbox" name="options[has_reverse_proxy]" value="1" <?php if ($options['has_reverse_proxy']) { echo 'checked="checked"'; } ?>>&nbsp;The server is behind a reverse proxy<em>
			<span class="detail-box">
			<?php if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) : ?>
			<?php printf(__('(With Proxy: %s - Without Proxy: %s)', 'geoip-detect'), $_SERVER['HTTP_X_FORWARDED_FOR'], $_SERVER['REMOTE_ADDR']); ?><br />
			<?php else: ?>
			<?php echo "(This doesn't seem to be the case.)"; ?>
			<?php endif; ?>
			</em>
			</span>
		</p>

		<h4>Data source: </h4>
		<?php foreach ($sources as $s) : $id = $s->getId();?>
			<p><input type="radio" name="options[source]" value="<?= $id ?>" <?php if ($currentSource->getId() == $id) { echo 'checked="checked"'; } ?> /><?= $s->getLabel(); ?></p>
			<span class="detail-box">
				<?php echo $s->getDescriptionHTML(); ?>
				<?php echo $s->getParameterHTML(); ?>
			</span>
		<?php endforeach; ?>
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
.detail-box {
	display: block;
	margin-left: 50px;
	color: #777;
}
</style>