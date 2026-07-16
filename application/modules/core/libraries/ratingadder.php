<?php

/**
 * @ingroup core
 *
 * @brief
 * Class used to add ratings to the database.
 *
 * This class provides a mechanism to add ratings provided by a
 * user to the database.
 *
 * @see adder.php
 * @author Adrian Rosebrock
 */

class RatingAdder extends Adder
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
	 * The numerical fields for the rating form.
	 *
	 * @var $numerFields
	 * @see site.php
	 */
	var $numerFields = array();
	/**
	 * The attribute (qualitative) fields for the rating form.
	 *
	 * @var $attrFields
	 * @see site.php
	 */
	var $attrFields = array();
	/**
	 * The dictionary of rating numerical values.
	 *
	 * @var $numerValues
	 */
	var $numerValues = array();
	/**
	 * The dictionary of rating attribute values.
	 *
	 * @var $attrValues
	 */
	var $attrValues = array();
	/**
	 * The ID of the user adding the rating.
	 *
	 * @var $userID
	 */
	var $userID = null;
	/**
	 * The IP address of the user adding the rating.
	 *
	 * @var $userIP
	 */
	var $userIP = null;
	/**
	 * The user agents string of the user adding the rating.
	 *
	 * @var $userAgent
	 */
	var $userAgent = null;
	/**
	 * The referring page of the user adding the rating.
	 *
	 * @var $userReferer
	 */
	var $userReferer = null;
	/**
	 * The session ID of the user adding the rating.
	 *
	 * @var $sessionID
	 */
	var $sessionID = null;

	/**
	 * Construct the RatingAdder.
	 *
	 * @param $numerValues
	 *  The numerical values of the rating form.
	 * @param $attrValues
	 *  The attribute (qualitative) values of the rating form.
	 * @param $userID
	 *  The ID of the user adding the rating.
	 * @param $userIP
	 *  The IP address of the user adding the rating.
	 * @param $userAgent
	 *  The user agents string of the user adding the rating.
	 * @param $userReferer
	 *  The referring page of the user adding the rating.
	 * @param $sessionID
	 *  The session ID of the user adding the rating.
	 */
	public function __construct($numerValues, $attrValues, $userID, $userIP, $userAgent, $userReferer, $sessionID)
	{
		// call the parent constructor and grab the core tables, schemas,
		// constants, numerical fields, and attribute files
		parent::__construct();
		$this->coreTables = $this->ci->config->item("core.tables");
		$this->coreSchemas = $this->ci->config->item("core.tables.schemas");
		$this->coreConstants = $this->ci->config->item("core.constants");
		$this->numerFields = $this->ci->config->item("site.ratings.numerical_fields");
		$this->attrFields = $this->ci->config->item("site.ratings.attr_fields");

		// store the numerical field values, attribute field values, user
		// ID, user IP address, the user's user agent of the browser, the
		// user's referring page, and the user's session ID
		$this->numerValues = $numerValues;
		$this->attrValues = $attrValues;
		$this->userID = $userID;
		$this->userIP = $userIP;
		$this->userAgent = $userAgent;
		$this->userReferer = $userReferer;
		$this->sessionID = $sessionID;
		
		// if the user ID is anonymous, then store a null value rather
		// than the constant value
		if ($userID == $this->coreConstants["anon_user_id"])
		{
			$this->userID = null;
		}
	}

	/**
	 * Add the rating to the database.
	 *
	 * @return
	 *  The ID of the rating entry added to the database.
	 */
	public function add()
	{
		// start constructing the query to insert the rating and
		// initialize the bindings array
		$insertSQL = "INSERT INTO " . $this->coreTables["Ratings"] . " (";
		$valueSQL = "VALUES(";
		$bindings = array();
		
		// loop over the attribute values
		foreach ($this->attrValues as $field => $value)
		{
			// grab the column for the current field and update the
			// insert and value query portions
			$column = $this->attrFields[$field]["column"];
			$insertSQL .= $column . ", ";
			$valueSQL .= "?, ";
			
			// update the bindings array
			$bindings[] = $value;
		}
		
		// loop over the numerical values
		foreach ($this->numerValues as $field => $value)
		{
			// grab the column name for the current field and update the
			// insert and value query portions
			$column = $this->numerFields[$field]["column"];
			$insertSQL .= $column . ", ";
			$valueSQL .= "?, ";
			
			// update the bindings array
			$bindings[] = $value;
		}
		
		// update the insert query portion for the user ID, user IP, user
		// agent, referring page, and session ID
		$insertSQL .= $this->coreSchemas["Ratings"]["UserID"] . ", ";
		$insertSQL .= $this->coreSchemas["Ratings"]["UserIP"] . ", ";
		$insertSQL .= $this->coreSchemas["Ratings"]["UserAgent"] . ", ";
		$insertSQL .= $this->coreSchemas["Ratings"]["UserReferer"] . ", ";
		$insertSQL .= $this->coreSchemas["Ratings"]["UserSessionID"] . ", ";		
	
		// update the value query portion for the user ID, user IP, user
		// agent, referring page, and session ID
		$valueSQL .= str_repeat("?, ", 5);
		
		// update the bindings array
		$bindings[] = $this->userID;
		$bindings[] = $this->userIP;
		$bindings[] = $this->userAgent;
		$bindings[] = $this->userReferer;
		$bindings[] = $this->sessionID;
		
		// lastly, update the insert query portion an value query portion
		// for the timestamp of the rating
		$insertSQL .= $this->coreSchemas["Ratings"]["Date"] . ") ";
		$valueSQL .= "CURRENT_TIMESTAMP)";
		
		// finish constructing the query
		$sql = $insertSQL . " " . $valueSQL . ";";
		
		// execute the query and grab the ID of the rating just added
		$this->ci->db->query($sql, $bindings);
		$ratingID = $this->ci->db->insert_id();
		
		// construct the query to update the person's last rated date
		$sql = "UPDATE " . $this->coreTables["People"] . " ";
		$sql .= "SET " . $this->coreSchemas["People"]["LastRatedDate"] . "=CURRENT_TIMESTAMP ";
		$sql .= "WHERE " . $this->coreSchemas["People"]["ID"] . "=?;";
		$bindings = array($this->attrValues["person_id"]);

		// execute the query
		$this->ci->db->query($sql, $bindings);
				
		// return the ID of the rating just added
		return $ratingID;
	}

	/**
	 * Determines if the rating is addable or not based on the user
	 * ID supplied. If the user has submitted another rating within
	 * the minimum time interval, then the rating should be denied.
	 *
	 * @see core.php
	 * @param $personID
	 *  The ID of the user adding the rating.
	 * @return
	 *  TRUE if the rating is addable, FALSE otherwise.
	 */
	public function isAddable($personID)
	{	
		// construct the query to determine if the rating is addable or
		// not
		$sql = "SELECT COUNT(*) AS total ";
		$sql .= "FROM " . $this->coreTables["Ratings"] . " ";
		$sql .= "WHERE " . $this->coreSchemas["Ratings"]["PersonID"] . "=? AND ";
		$sql .= $this->coreSchemas["Ratings"]["Status"] . " >= " . $this->coreConstants["min_rating_status"] . " AND ";
		$sql .= "(" . $this->coreSchemas["Ratings"]["UserSessionID"] . "=? OR ";
		$sql .= "(" . $this->coreSchemas["Ratings"]["UserIP"] . "=? AND ";
		$sql .= $this->coreSchemas["Ratings"]["Date"] . " > DATE_SUB(NOW(), INTERVAL " . $this->coreConstants["min_rating_time_interval"] . ")));";

		// define the bindings array
		$bindings = array(
			$personID,
			$this->sessionID,
			$this->userIP);
		
		// execute the query and grab the row
		$query = $this->ci->db->query($sql, $bindings);
		$row = $query->row_array();
		
		// the rating is addable if the number of rows matching the
		// query is zero
		return ($row["total"] == 0);
	}
}

?>