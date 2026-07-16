<?php

abstract class RecentlySearched
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
	 * Module configuration of type mappings.
	 *
	 * @var $modTypeMappings
	 */
	var $modTypeMappings = array();

	public function __construct()
	{
		// connect the class with CodeIgniter and grab the core tables,
		// schemas, and constants
		$this->ci = &get_instance();
		$this->coreTables = $this->ci->config->item("core.tables");
		$this->coreSchemas = $this->ci->config->item("core.tables.schemas");
		$this->coreConstants = $this->ci->config->item("core.constants");

		// grab the module tables, schemas, constants and type mappings
		$this->modTables = $this->ci->config->item("recently_searched.tables");
		$this->modSchemas = $this->ci->config->item("recently_searched.tables.schemas");
		$this->modConstants = $this->ci->config->item("recently_searched.constants");
		$this->modTypeMappings = $this->ci->config->item("recently_searched.type_mappings");
	}
	
	abstract public function generate();

	abstract protected function clean();
}

?>