<?php

abstract class DrillDownDumper
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

	public function __construct()
	{
		// connect the class with CodeIgniter
		$this->ci = &get_instance();
		
		// grab the core tables, schemas, and constants
		$this->coreTables = $this->ci->config->item("core.tables");
		$this->coreSchemas = $this->ci->config->item("core.tables.schemas");
		$this->coreConstants = $this->ci->config->item("core.constants");
	}

	/**
	 * Abstract method that should be implemented to dump specific
	 * database information to file.
	 */
	abstract public function dump();
	
	/**
	 * Abstract method that should be implemented to create the needed
	 * directory structure to the dump files.
	 */
	abstract public function createDirStructure();
}

?>