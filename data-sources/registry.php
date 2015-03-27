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
	
	public function register(\YellowTree\GeoipDetect\DataSources\AbstractDataSource $source) {
		$id = $source->getId();
		$this->sources[$id] = $source;
	}
	
	
	const DEFAULT_SOURCE = 'hostinfo';
	
	/**
	 * @return AbstractDataSource
	 */
	public function getCurrentSource() {
		$currentSource = get_option('geoip-detect-source', self::DEFAULT_SOURCE);
		if (isset($this->sources[$currentSource]));
			return $this->sources[$currentSource];
		
		if (WP_DEBUG)
			trigger_error('Current source has id "' . $currentSource . '", but no such source was found. Using default source instead.', E_NOTICE);
		
		if (isset($this->sources[self::DEFAULT_SOURCE]))
			return $this->sources[self::DEFAULT_SOURCE];
		
		return null;
	}
	
	/**
	 * @return array(AbstractDataSource)
	 */
	public function getAllSources() {
		return $this->sources;
	}
}
