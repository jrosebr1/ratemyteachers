<?php

abstract class Queue
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
	 * @see core.php
	 */
	var $coreConstants = array();

	public function __construct()
	{
		// connect the class with CodeIgniter and grab the core tables,
		// schemas, and constants
		$this->ci = &get_instance();
		$this->coreTables = $this->ci->config->item("core.tables");
		$this->coreSchemas = $this->ci->config->item("core.tables.schemas");
		$this->coreConstants = $this->ci->config->item("core.constants");
	}
	
	abstract public function queue();
}

?>