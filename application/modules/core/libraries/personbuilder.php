<?php

/**
 * @ingroup core
 *
 * @brief
 * Class used to build Person objects.
 *
 * This class is used to build Person objects by handling the
 * database access as well as the construction of the Person
 * through the core classes.
 *
 * @see rateablebuilder.php
 * @see person.php
 * @see core.php
 * @author Adrian Rosebrock
 */

class PersonBuilder extends RateableBuilder
{
	/**
	 * ID of the person.
	 *
	 * @var $perID
	 */
	var $perID = null;

	/**
	 * Construct the PersonBuilder.
	 *
	 * @param $perID
	 *  The ID of the person.
	 */
	public function __construct($perID)
	{
		// call the parent constructor and store the person ID
		parent::__construct();
		$this->perID = $perID;
	}

	/**
	 * Build the person.
	 *
	 * @return Person
	 *  A built Person object.
	 *
	 * @see person.php
	 */
	public function build()
	{
		// grab the core tables, schemas, constants, and classes
		$coreTables = $this->ci->config->item("core.tables");
		$coreSchemas = $this->ci->config->item("core.tables.schemas");
		$coreConstants = $this->ci->config->item("core.constants");
		$coreClasses = $this->ci->config->item("core.classes");
		
		// construct the query to get the person information
		$sql = "SELECT * ";
		$sql .= "FROM " . $coreTables["People"] . " ";
		$sql .= "WHERE " . $coreSchemas["People"]["ID"] . "=? AND ";
		$sql .= $coreSchemas["People"]["Status"] . " >= " . $coreConstants["min_person_status"] . ";";
		$bindings = array($this->perID);
		
		// execute the query
		$query = $this->ci->db->query($sql, $bindings);
		
		// if the number of returned rows is zero, then return null
		// from the method since the person is invalid
		if ($query->num_rows() == 0)
		{
			return null;
		}
		
		// otherwise, the person exists and we can update the 'valid'
		// variable and grab the row
		$this->setValid(true);
		$row = $query->row_array();
		
		// now that we have the row, we can get the organization ID
		// and build the organization as well
		$ob = new OrganizationBuilder($row[$coreSchemas["People"]["OrgID"]]);
		$org = $ob->build();

		// build the person
		$Class = $coreClasses["People"];
		$person = new $Class($org, $row);

		// return the built person
		return $person;
	}	
}

?>