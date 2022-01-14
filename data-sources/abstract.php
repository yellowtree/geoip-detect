<?php
/**
Each datasource has:
- An id, name & description
- A reader that can generate Maxmind GeoIP records.
- (optional) Configuration options
- (optional) Data Source Information (only shown for 
- activate / deactivate when activated / deactivated by the user

For future compatibility:
Each data source may not rely on the fact that it is the "currently chosen" source. There may be several sources that are "activated" and then used in a fallback manner.
In practise this means that wordpress filters should be used only in conjunction of checks like

if ($record->extra->source == $this->getId()) { ...

*/

namespace YellowTree\GeoipDetect\DataSources {

abstract class AbstractDataSource {
	public function __construct() {}
	
	abstract public function getId();
	public function getLabel() { return ''; }
	
	public function getDescriptionHTML() { return ''; }
	public function getStatusInformationHTML() { return ''; }
	public function getParameterHTML() { return ''; }
	public function saveParameters($post) { }
	public function getShortLabel() { return $this->getLabel(); }
	
	public function activate() { }
	public function deactivate() { }
	public function uninstall() {}
	
	public function getReader($locales = [ 'en' ], $options = []) { return null; }
	
	public function isWorking() { return false; }
}


/**
 * This Class extends the Maxmind City with more attributes.
 * 
 * @property bool $isEmpty (Wordpress Plugin) If the record is empty or contains any data.
 * 
 * @property \YellowTree\GeoipDetect\DataSources\ExtraInformation $extra (Wordpress Plugin) Extra Information added by the Geolocation IP Detection plugin
 */
class City extends \GeoIp2\Model\Insights {
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
 * @property string $source Id of the source that this record is originating from.
 * 
 * @property int $cached 0 if not cached, else Unix Timestamp when it was written to the cache. 
 *
 * @property string $error Error message if one occured during lookup. If multiple errors, they are seperated by \n
 */

class ExtraInformation extends \GeoIp2\Record\AbstractRecord {
	/**
	 * @ignore
	 */
	protected $validAttributes = [ 'source', 'cached', 'error', 'original', 'flag', 'tel', 'countryIsoCode3', 'currencyCode' ];
}

interface ReaderInterface extends \GeoIp2\ProviderInterface {
	/**
     * Closes the database and returns the resources to the system.
     */
	public function close();
}
	
abstract class AbstractReader implements \YellowTree\GeoipDetect\DataSources\ReaderInterface {
	protected $options;
	
	public function __construct($options = []) {
		$this->options = $options;	
	}
	
	public function city($ip) {
		throw new \BadMethodCallException('This datasource does not provide data for city()');
	}
	
	public function country($ip) {
		throw new \BadMethodCallException('This datasource does not provide data for country()');
	}
		
	public function close() {
			
	}
	
}
	

} // end namespace 

namespace { // global namespace
	function geoip_detect2_register_source($source) {
		$registry = \YellowTree\GeoipDetect\DataSources\DataSourceRegistry::getInstance();
		$registry->register($source);
	}
	
	function geoip_detect2_is_source_active($sourceId) {
		$registry = \YellowTree\GeoipDetect\DataSources\DataSourceRegistry::getInstance();
		return $sourceId == $registry->getCurrentSource();
	}
}
