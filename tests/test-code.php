<?php

class CodeTest extends WP_UnitTestCase_GeoIP_Detect {
	
	function setUp()
	{
		parent::setUp();
	}
	
	function tearDown()
	{
		parent::tearDown();
	}
	
	function testIfShorttagsAreUsed() {
		$folders = array('data-sources', 'views');
		
		$plugin_dir = dirname(dirname(__FILE__));
		foreach ($folders as $f) {
			foreach (glob($plugin_dir . '/' . $f . '/**') as $filename) {
				
				$code = file_get_contents($filename);
				$this->assertNotContains('<? ', $code, 'File ' . $filename . ' contains the shortcode <?  which is not supported on all hosts');
			}
		}
	}
}
