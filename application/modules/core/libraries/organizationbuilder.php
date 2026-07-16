<?php

/**
 * @ingroup core
 *
 * @brief
 * Class used to build Organization objects.
 *
 * This class is used to build Organization objects by handling the
 * database access as well as the construction of the Organization
 * through the core classes.
 *
 * @see rateablebuilder.php
 * @see organization.php
 * @see core.php
 * @author Adrian Rosebrock
 */

class OrganizationBuilder extends RateableBuilder
{
	/**
	 * ID of the organization.
	 *
	 * var $orgID
	 */
	var $orgID = null;

	/**
	 * Construct the OrganizationBuilder.
	 *
	 * @param $orgID
	 *  The ID of the organization.
	 */
	public function __construct($orgID)
	{
		// call the parent constructor and store the organization ID
		parent::__construct();
		$this->orgID = $orgID;
	}
	
	/**
	 * Build the organization.
	 *
	 * @return Organization
	 *  A built Organization object.
	 *
	 * @see organization.php
	 */
	public function build()
	{
		// grab the core tables, schemas, and classes
		$coreTables = $this->ci->config->item("core.tables");
		$coreSchemas = $this->ci->config->item("core.tables.schemas");
		$coreClasses = $this->ci->config->item("core.classes");
		
		// construct the query to get the organization information
		$sql = "SELECT * ";
		$sql .= "FROM " . $coreTables["Organizations"] . " ";
		$sql .= "WHERE " . $coreSchemas["Organizations"]["ID"] . "=?;";
		$bindings = array($this->orgID);
		
		// execute the query
		$query = $this->ci->db->query($sql, $bindings);
		
		// if the number of returned rows is zero, then return null
		// from the method since the organization is invalid
		if ($query->num_rows() == 0)
		{
			return null;
		}
		
		// otherwise, the organization exists and we can update the 'valid'
		// variable and grab the row
		$this->setValid(true);
		$row = $query->row_array();
		
		// build the organization
		$Class = $coreClasses["Organization"];
		$org = new $Class($row);

		// return the built organization
		return $org;
	}
}

?>