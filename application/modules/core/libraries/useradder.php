<?php

class UserAdder extends Adder
{
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
	var $firstName = null;
	var $email = null;
	var $password = null;
	var $orgID = null;

	public function __construct($firstName, $email, $password, $orgID)
	{
		// call the parent constructor and grab the core tables, schemas,
		// and constants
		parent::__construct();
		$this->coreTables = $this->ci->config->item("core.tables");
		$this->coreSchemas = $this->ci->config->item("core.tables.schemas");
		$this->coreConstants = $this->ci->config->item("core.constants");
		
		// store the first name, email, password, and organization ID of
		// the user to be added
		$this->firstName = $firstName;
		$this->email = $email;
		$this->password = $password;
		$this->orgID = $orgID;
	}
	
	public function add()
	{
		// construct the query to insert the user
		$sql = "INSERT INTO " . $this->coreTables["Users"] . "(";
		$sql .= $this->coreSchemas["Users"]["OrgID"] . ", ";
		$sql .= $this->coreSchemas["Users"]["Email"] . ", ";
		$sql .= $this->coreSchemas["Users"]["FirstName"] . ", ";
		$sql .= $this->coreSchemas["Users"]["Status"] . ", ";
		$sql .= $this->coreSchemas["Users"]["Password"] . ", ";
		$sql .= $this->coreSchemas["Users"]["DateAdded"] . ") ";
		$sql .= "VALUES(?, ?, ?, ?, ?, CURRENT_TIMESTAMP);";
		
		// create the bindings array
		$bindings = array(
			$this->orgID,
			$this->email,
			$this->firstName,
			$this->coreConstants["min_user_status"],
			$this->password);
		
		// execute the query
		$this->ci->db->query($sql, $bindings);
		
		// return the ID of the user that was just added
		return $this->ci->db->insert_id();
	}
}

?>