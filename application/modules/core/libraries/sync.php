<?php

/**
 * @ingroup core
 *
 * @brief
 * Abstract class used to sync statistical scores.
 *
 * This abstract class is used to sync up statistical scores. For
 * example, when a rating is deleted the person's score will have
 * to be recalculated, which also implies that the organization's
 * aggregate score will also have to be recalculated.
 *
 * @author Adrian Rosebrock
 */

abstract class Sync
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
	
	/**
	 * Construct the Sync.
	 */
	public function __construct()
	{
		// connect the class with CodeIgniter
		$this->ci = &get_instance();
		
		// grab the core tables, schemas, and classes
		$this->coreTables = $this->ci->config->item("core.tables");
		$this->coreSchemas = $this->ci->config->item("core.tables.schemas");
		$this->coreConstants = $this->ci->config->item("core.constants");
	}
}

?>