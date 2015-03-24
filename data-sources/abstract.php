<?php
/**
Each datasource has:
- An id, name & description
- A reader
- If active, a method or filter needs to be called.
- (optional) Configuration options
- (optional) Data Source Information (only shown for 

Use Class/Interface or WP Filter?
*/

namespace YellowTree\GeoipDetect\DataSources;

abstract class AbstractDataSource {
	
	protected $id;
	protected $label;
	
	public function __construct($id, $label) {
		$this->id = $id;
		$this->label = $label;
	}
	
	public function getId() { return $this->id; }
	public function getLabel() { return $this->label; }
	
	public function getDescriptionHTML() { return ''; }
	public function getStatusInformationHTML() { return ''; }
	public function getParameterHTML() { return ''; }
	
	public function activate() { }
	
	public function getReader() { return null; }
	
	public function isWorking() { return false; }
}


/**
 * This Class extends the Maxmind City with more attributes.
 * 
 * @property isEmpty bool (Wordpress Plugin) If the record is empty or contains any data.
 */
class City extends \GeoIp2\Model\City {
		
}