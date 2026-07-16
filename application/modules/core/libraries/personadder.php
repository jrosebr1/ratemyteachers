<?php

/**
 * @ingroup core
 *
 * @brief
 * Class used to add people to an organization.
 *
 * This class provides a mechanism to add people to an organization
 * and store the added person in the database.
 *
 * @see adder.php
 * @author Adrian Rosebrock
 */

class PersonAdder extends Adder
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
	/**
	 * The organization ID the person added will belong to.
	 *
	 * @var $orgID
	 */
	var $orgID = null;
	/**
	 * The ID of the user adding the person.
	 *
	 * @var $userID
	 */
	var $userID = null;
	/**
	 * The IP address of the user adding the person.
	 *
	 * @var $userIP
	 */
	var $userIP = null;
	/**
	 * The first name of the person to be added.
	 *
	 * @var $firstName
	 */
	var $firstName = null;
	/**
	 * The last name of the person to be added.
	 *
	 * @var $lastName
	 */
	var $lastName = null;
	/**
	 * The department of the person to be added.
	 *
	 * @var $dept
	 */
	var $dept = null;
	/**
	 * The gender of the person to be added.
	 *
	 * @var $gender
	 */
	var $gender = null;
	
	/**
	 * Construct the PersonAdder.
	 *
	 * @param $orgID
	 *  The organization ID the person added will belong to.
	 * @param $userID
	 *  The ID of the user adding the person.
	 * @param $userIP
	 *  The IP address of the user adding the person.
	 * @param $firstName
	 *  The first name of the person to be added.
	 * @param $lastName
	 *  The last name of the person to be added.
	 * @param $dept
	 *  The department of the person to be added.
	 * @param $gender
	 *  The gender of the person to be added.
	 */
	public function __construct($orgID, $userID, $userIP, $firstName, $lastName, $dept, $gender)
	{
		// call the parent constructor and grab the core tables, schemas,
		// and constants
		parent::__construct();
		$this->coreTables = $this->ci->config->item("core.tables");
		$this->coreSchemas = $this->ci->config->item("core.tables.schemas");
		$this->coreConstants = $this->ci->config->item("core.constants");
		
		// store the organization ID, user ID, user IP address, first name,
		// last name, department, and gender
		$this->orgID = $orgID;
		$this->userID = $userID;
		$this->userIP = $userIP;
		$this->firstName = $firstName;
		$this->lastName = $lastName;
		$this->dept = $dept;
		$this->gender = $gender;
		
		// if the user ID is anonymous, then store a null value rather
		// than the constant value
		if ($userID == $this->coreConstants["anon_user_id"])
		{
			$this->userID = null;
		}
	}

	/**
	 * Add the person to the database.
	 *
	 * @return
	 *  The ID of the person entry added to the database.
	 */
	public function add()
	{
		// construct the query to insert the person
		$sql = "INSERT INTO " . $this->coreTables["People"] . "(";
		$sql .= $this->coreSchemas["People"]["OrgID"] . ", ";
		$sql .= $this->coreSchemas["People"]["UserID"] . ", ";
		$sql .= $this->coreSchemas["People"]["UserIP"] . ", ";
		$sql .= $this->coreSchemas["People"]["LastName"] . ", ";
		$sql .= $this->coreSchemas["People"]["FirstName"] . ", ";
		$sql .= $this->coreSchemas["People"]["Department"] . ", ";
		$sql .= $this->coreSchemas["People"]["Gender"] . ", ";
		$sql .= $this->coreSchemas["People"]["DateAdded"] . ") ";
		$sql .= "VALUES(?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP);";
		
		// create the bindings array
		$bindings = array(
			$this->orgID,
			$this->userID,
			$this->userIP,
			$this->lastName,
			$this->firstName,
			$this->dept,
			$this->gender);

		// execute the query
		$this->ci->db->query($sql, $bindings);
		
		// return the ID of the person that was just added
		return $this->ci->db->insert_id();
	}
	
	/**
	 * Determines if the supplied department is valid or not by
	 * examining the "Departments" entry in the core tables.
	 *
	 * @see site.php
	 * @param $dept
	 *  The department to be checked for validity.
	 *
	 * @return
	 *  TRUE if the department is valid, FALSE if it is not.
	 */
	public static function isDeptValid($dept)
	{
		// grab the CodeIgniter instances, core tables, and schemas
		$ci = &get_instance();
		$coreTables = $ci->config->item("core.tables");
		$coreSchemas = $ci->config->item("core.tables.schemas");
	
		// construct the query to determine if the supplied department
		// is valid or not
		$sql = "SELECT COUNT(*) AS total ";
		$sql .= "FROM " . $coreTables["Departments"] . " ";
		$sql .= "WHERE " . $coreSchemas["Departments"]["Department"] . "=?;";
		$bindings = array($dept);
		
		// execute the query
		$query = $ci->db->query($sql, $bindings);
		
		// the department is valid if the row count is greater than
		// zero
		return ($query->num_rows() > 0);
	}
}

?>