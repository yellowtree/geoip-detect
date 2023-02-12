<br><br>
	<p class="legal_notices">
	<br />
	<?php printf(__('This extension is "charity-ware". You can use it for free but if you want to do me a favor, please <a href="%s" target="_blank">donate</a> to <a href="%s" target="_blank">this charity</a>. (See <a href="%s" target="_blank">FAQ</a> for more infos.)', 'geoip-detect'),
		'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BSYUZHS8FH3CL',
		__('http://www.jmem-hainichen.de/homepage', 'geoip-detect'),
				'https://github.com/yellowtree/wp-geoip-detect/wiki/FAQ#what-you-mean-by-this-plugin-is-charity-ware'); ?>
	</p>
	<p class="legal_notices">
		<?php _e('This product includes GeoLite2 data created by MaxMind, available from <a href="http://www.maxmind.com/">http://www.maxmind.com</a>.', 'geoip-detect'); ?>
	</p>

<style>
.legal_notices {
	font-size: 80%;
	font-style: italic;
}

.geoip_detect_error {
	display:block;
	clear: both;
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
.geoip_detect_success {
	border-left: #00a32a solid 4px;
}

.detail-box {
	display: block;
	margin-left: 50px;
	color: #777;
}
.geoip-detect-wrap select {
	max-width: 100%;
}
</style>