<?php

namespace YellowTree\GeoipDetect\DataSources;

class DataSourceRegistry {
	
	private static $instance;
	/* singleton */
	private function __construct() {  
		$this->sources = array();
	}
	public static function getInstance() { 
		if (!self::$instance) { 
			self::$instance = new static(); 
		} 
		return self::$instance;
	}

	
	protected $sources;
	
	/**
	 * Register a Data source
	 * @param \YellowTree\GeoipDetect\DataSources\AbstractDataSource $source
	 */
	public function register($source) {
		$id = $source->getId();
		$this->sources[$id] = $source;
	}
	
	
	const DEFAULT_SOURCE = 'hostinfo';
	
	/**
	 * Returns the currently chosen source.
	 * @return \YellowTree\GeoipDetect\DataSources\AbstractDataSource
	 */
	public function getCurrentSource() {
		$currentSource = get_option('geoip-detect-source', self::DEFAULT_SOURCE);
		if (isset($this->sources[$currentSource]))
			return $this->sources[$currentSource];
		
		if (WP_DEBUG)
			trigger_error('Current source has id "' . $currentSource . '", but no such source was found. Using default source instead.', E_USER_NOTICE);
		
		if (isset($this->sources[self::DEFAULT_SOURCE]))
			return $this->sources[self::DEFAULT_SOURCE];
		
		return null;
	}
	
	/**
	 * Returns the source known by this id.
	 * @param string Source id (if empty, use current one)
	 * @return \YellowTree\GeoipDetect\DataSources\AbstractDataSource
	 */
	public function getSource($id = '') {
		if (!$id)
			return $this->getCurrentSource();

		if (isset($this->sources[$id]))
			return $this->sources[$id];
		
		return null;
	}
	
	/**
	 * Choose a new source as "current source".
	 * @param string $id
	 */
	public function setCurrentSource($id) {
		$oldSource = $this->getCurrentSource();
		$newSource = $this->getSource($id);
		
		if ($oldSource->getId() != $newSource->getId()) {
			$oldSource->deactivate();
			update_option('geoip-detect-source', $newSource->getId());
			$newSource->activate();
		}
		
		update_option('geoip-detect-ui-has-chosen-source', true);
	}
	
	/**
	 * Returns all registered sources.
	 * 
	 * @return array(\YellowTree\GeoipDetect\DataSources\AbstractDataSource)
	 */
	public function getAllSources() {
		return $this->sources;
	}
}
