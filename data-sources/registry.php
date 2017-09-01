<?php
/*
Copyright 2013-2016 Yellow Tree, Siegen, Germany
Author: Benjamin Pick (info@yellowtree.de)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

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
	 * @deprecated Use getSource() instead
	 * @return \YellowTree\GeoipDetect\DataSources\AbstractDataSource
	 */
	public function getCurrentSource() {
		$currentSource = get_option('geoip-detect-source', self::DEFAULT_SOURCE);
		return $this->getSource($currentSource);
	}
	
	/**
	 * Returns the source known by this id.
	 * @param string Source id
	 * @return \YellowTree\GeoipDetect\DataSources\AbstractDataSource
	 */
	public function getSource($id) {
		if (isset($this->sources[$id]))
			return $this->sources[$id];
		
		if (WP_DEBUG)
			trigger_error('The source with id "' . $id . '" was requested, but no such source was found. Using default source instead.', E_USER_NOTICE);
		
		if (isset($this->sources[self::DEFAULT_SOURCE]))
			return $this->sources[self::DEFAULT_SOURCE];
		
		return null;
	}
	
	/**
	 * Check if a source named $id exists.
	 * @param string $id
	 * @return boolean
	 */
	public function sourceExists($id) {
		return isset($this->sources[$id]);
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
    
	public function isSourceCachable($source) {
		// Don't cache for file access based sources (not worth the effort/time)
		$sources_not_cachable = apply_filters('geoip2_detect_sources_not_cachable', array('auto', 'manual', 'header'));	
		return !in_array($source, $sources_not_cachable);
	}
}
