<?php

abstract class DrillbitCreator
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

	public function __construct()
	{
		// connect the class with CodeIgniter
		$this->ci = &get_instance();
		
		// grab the core tables and schemas
		$this->coreTables = $this->ci->config->item("core.tables");
		$this->coreSchemas = $this->ci->config->item("core.tables.schemas");
	}
	
	/**
	 * Abstract method that should be implemented to create the set of
	 * drillbits needed.
	 */
	abstract public function createDrillBits();
	
	/**
	 * Abstract method that should be implemented to create the directory
	 * structure needed to store the drillbits.
	 */
	abstract public function createDirStructure();
}

?>