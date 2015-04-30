<?php 
$options = $currentSource->getParameterHTML();
?>

<div class="wrap">
	<h2><?php _e('GeoIP Detection', 'geoip-detect');?></h2>
	<a href="tools.php?page=<?= GEOIP_PLUGIN_BASENAME ?>">Test IP Detection Lookup</a>
	<?php if (!empty($message)): ?>
		<p class="geoip_detect_error">
		<?php echo $message; ?>
		</p>
<?php endif; ?>
	
	<p>
		<?php printf(__('<b>Selected data source:</b> %s', 'geoip-detect'), geoip_detect2_get_current_source_description() ); ?>
	</p>
	<p>
		<?php echo $currentSource->getStatusInformationHTML(); ?>
	</p>
	<?php if ($options) : ?>
	<h3>Options for this data source</h3>
	<p>
		<form method="post" action="#">
			<input type="hidden" name="action" value="options-source" />
			<p><?php echo $options; ?></p>
			<p>
			<input type="submit" class="button button-primary" value="<?php _e('Save', 'geoip-detect'); ?>" />
			</p>	
		</form>
	</p>
	<?php endif; ?>
	<br/>

	<br /><br />
	<form method="post" action="#">
		<input type="hidden" name="action" value="choose" />
		<h3>Choose data source: </h3>
		<a href="https://github.com/yellowtree/wp-geoip-detect/wiki/FAQ#which-data-source-should-i-choose">Help</a>
		<?php foreach ($sources as $s) : $id = $s->getId();?>
			<p><input type="radio" name="options[source]" value="<?= $id ?>" <?php if ($currentSource->getId() == $id) { echo 'checked="checked"'; } ?> /><?= $s->getLabel(); ?></p>
			<span class="detail-box">
				<?php echo $s->getDescriptionHTML(); ?>
			</span>
		<?php endforeach; ?>
		<br />
		<input type="submit" class="button button-primary" value="<?php _e('Save', 'geoip-detect'); ?>" />
	</form>
	<form method="post" action="#">
		<input type="hidden" name="action" value="options" />
		<h3>General Options</h3>
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
