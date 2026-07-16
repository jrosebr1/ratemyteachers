<?php

abstract class SitemapDump
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
	 * The ID of the country the dump is for.
	 *
	 * @var $countryID
	 */
	var $countryID = null;

	/**
	 * Construct the SitemapDump.
	 */
	public function __construct($countryID)
	{
		// connect the class with CodeIgniter
		$this->ci = &get_instance();
		
		// grab the core tables, schemas, and constants
		$this->coreTables = $this->ci->config->item("core.tables");
		$this->coreSchemas = $this->ci->config->item("core.tables.schemas");
		$this->coreConstants = $this->ci->config->item("core.constants");
		
		// store the country ID
		$this->countryID = $countryID;
	}
	
	/**
	 * Abstract method that should be implemented to dump rows to file.
	 */
	abstract public function dump();
}

?>