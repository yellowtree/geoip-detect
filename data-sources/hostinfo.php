<?php
try {
	// Setting timeout limit to speed up sites
	$context = stream_context_create(
			array(
					'http' => array(
							'timeout' => 1,
					),
			)
	);
	// Using @file... to supress errors
	// Example output: {"country_name":"UNITED STATES","country_code":"US","city":"Aurora, TX","ip":"12.215.42.19"}
	$data = json_decode(@file_get_contents('http://api.hostip.info/get_json.php?ip=' . $ip, false, $context));
/*	
	if it contains 'unknown' or 'XX' then empty
	if all arey empty, return null
*/
} catch (Exception $e) {
	// If the API isn't available, we have to do this
	return null;
}