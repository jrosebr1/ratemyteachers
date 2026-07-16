<?php

abstract class RSSFeed
{
	/**
	 * Variable used to interface with CodeIgniter.
	 *
	 * @var $ci
	 */
	var $ci = null;
	/**
	 * Core configuration of tables.
	 *
	 * @var $coreTables
	 * @see site.php
	 */
	var $coreTables = array();
	/**
	 * Core configuration of table schemas.
	 *
	 * @var $coreSchemas
	 * @see site.php
	 */
	var $coreSchemas = array();
	/**
	 * Core configuration of constants.
	 *
	 * @var $coreConstants
	 */
	var $coreConstants = array();
	/**
	 * Module configuration of tables.
	 *
	 * @var $modTables
	 */
	var $modTables = array();
	/**
	 * Module configuration of table schemas.
	 *
	 * @var $modSchemas
	 */
	var $modSchemas = array();
	/**
	 * Module configuration of constants.
	 *
	 * @var $modConstants
	 */
	var $modConstants = array();
	/**
	 * Data from the RSS feed table.
	 *
	 * @var $data
	 */
	var $data = array();

	/**
	 * Construct the RSS feed.
	 */
	public function __construct()
	{
		// connect the class with CodeIgniter
		$this->ci = &get_instance();
		
		// grab the core tables, schemas, and constants
		$this->coreTables = $this->ci->config->item("core.tables");
		$this->coreSchemas = $this->ci->config->item("core.tables.schemas");
		$this->coreConstants = $this->ci->config->item("core.constants");
		
		// grab the module tables, schemas, and constants
		$this->modTables = $this->ci->config->item("rss_feeds.tables");
		$this->modSchemas = $this->ci->config->item("rss_feeds.tables.schemas");
		$this->modConstants = $this->ci->config->item("rss_feeds.constants");
	}
	
	/**
	 * Abstract method that should be implemented to fetch the RSS
	 * fed.
	 */
	abstract public function getFeed();
	
	/**
	 * Abstract method that should be implemented to deterine if
	 * The RSS feed is valid or not.
	 */
	abstract public function isValid();
	
	/**
	 * Abstract method that should be implemented to grab the feed
	 * data from the database.
	 */
	abstract protected function getFeedData();
	
	/**
	 * Abstract method that should be implemented to generate the
	 * RSS feed.
	 */
	abstract protected function generateFeed();
	
	/**
	 * Abstract method that should be implemented to store the generated
	 * feed in the database.
	 *
	 * @param $feed
	 *  RSS feed to be cached in the database.
	 */
	abstract protected function saveFeed($feed);
	
	/**
	 * Abstract method that should be implemented to determine if the
	 * RSS feed is regenerateable or not.
	 */
	abstract protected function isRegen();
}

?>