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
	
	abstract public function getId();
	public function getLabel() { return ''; }
	
	public function getDescriptionHTML() { return ''; }
	public function getStatusInformationHTML() { return ''; }
	public function getParameterHTML() { return ''; }
	public function getShortLabel() { return $this->getLabel(); }
	
	public function activate() { }
	
	public function getReader() { return null; }
	
	public function isWorking() { return false; }
}


/**
 * This Class extends the Maxmind City with more attributes.
 * 
 * @property bool $isEmpty (Wordpress Plugin) If the record is empty or contains any data.
 * 
 * @property \YellowTree\GeoipDetect\DataSources\ExtraInformation $extra (Wordpress Plugin) Extra Information added by the GeoIP Detect plugin
 */
class City extends \GeoIp2\Model\City {
	/**
	 * @ignore
	 */
	protected $extra;
	
	/**
	 * @ignore
	 */
	public function __construct($raw, $locales) {
		parent::__construct($raw, $locales);
		
		$this->extra = new ExtraInformation($this->get('extra'));
	}

	public function __get($attr) {
		if ($attr == 'isEmpty')
			return $this->raw['is_empty'];
		else
			return parent::__get($attr);
	}
}

/**
 * @property $source string Id of the source that this record is originating from.
 * 
 * @property $cached int 0 if not cached, else Unix Timestamp when it was written to the cache. 
 *
 */

class ExtraInformation extends \GeoIp2\Record\AbstractRecord {
	/**
	 * @ignore
	 */
	protected $validAttributes = array('source', 'cached');
}

interface ReaderInterface extends \GeoIp2\ProviderInterface {
    /**
     * Closes the database and returns the resources to the system.
     */
	public function close();
}