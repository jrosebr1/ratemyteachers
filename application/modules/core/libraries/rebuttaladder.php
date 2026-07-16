<?php

/**
 * @ingroup core
 *
 * @brief
 * Class used to add rebuttals to the database.
 *
 * This class provides a mechanism to add rebuttals provided by a
 * user to the database.
 *
 * @see adder.php
 * @author Adrian Rosebrock
 */

class RebuttalAdder extends Adder
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
	 * The ID of the rating that the rebuttal belongs to.
	 *
	 * @var $ratingID
	 */
	var $ratingID = null;
	/**
	 * The ID of the user adding the rebuttal.
	 *
	 * @var $userID
	 */
	var $userID = null;
	/**
	 * The IP address of the user adding the rebuttal.
	 *
	 * @var $userIP
	 */
	var $userIP = null;
	/**
	 * The session ID of the user adding the rebuttal.
	 *
	 * @var $sessionID
	 */
	var $sessionID = null;
	/**
	 * The rebuttal comment to be added.
	 *
	 * @var $comment
	 */
	var $comment = null;

	/**
	 * Construct the RebuttalAdder.
	 *
	 * @param $ratingID
	 *  The ID of the rating that the rebuttal belongs to.
	 * @param $userID
	 *  The ID of the user adding the rebuttal.
	 * @param $userIP
	 *  The IP address of the user adding the rebuttal.
	 * @param $sessionID
	 *  The ID of the session of the user adding the rebuttal.
	 * @param $comment
	 *  The rebuttal comment to be added.
	 */
	public function __construct($ratingID, $userID, $userIP, $sessionID, $comment)
	{
		// call the parent constructor and grab the core tables, schemas,
		// and constants
		parent::__construct();
		$this->coreTables = $this->ci->config->item("core.tables");
		$this->coreSchemas = $this->ci->config->item("core.tables.schemas");
		$this->coreConstants = $this->ci->config->item("core.constants");
		
		// store the rating ID, user ID, user's IP address, user's session
		// ID and comment
		$this->ratingID = $ratingID;
		$this->userID = $userID;
		$this->userIP = $userIP;
		$this->sessionID = $sessionID;
		$this->comment = $comment;

		// if the user ID is anonymous, then store a null value rather
		// than the constant value
		if ($userID == $this->coreConstants["anon_user_id"])
		{
			$this->userID = null;
		}
	}

	/**
	 * Add the rebuttal to the database.
	 *
	 * @return
	 *  The ID of the rebuttal entry added to the database.
	 */
	public function add()
	{
		// construct the query to insert the rebuttal
		$sql = "INSERT INTO " . $this->coreTables["Rebuttals"] . "(";
		$sql .= $this->coreSchemas["Rebuttals"]["RatingID"] . ", ";
		$sql .= $this->coreSchemas["Rebuttals"]["UserID"] . ", ";
		$sql .= $this->coreSchemas["Rebuttals"]["UserIP"] . ", ";
		$sql .= $this->coreSchemas["Rebuttals"]["UserSessionID"] . ", ";
		$sql .= $this->coreSchemas["Rebuttals"]["Date"] . ", ";
		$sql .= $this->coreSchemas["Rebuttals"]["Comment"] . ", ";
		$sql .= $this->coreSchemas["Rebuttals"]["Status"] . ") ";
		$sql .= "VALUES(?, ?, ?, ?, CURRENT_TIMESTAMP, ?, 2);";
		
		// define the bindings array
		$bindings = array(
			$this->ratingID,
			$this->userID,
			$this->userIP,
			$this->sessionID,
			$this->comment);

		// execute the query
		$this->ci->db->query($sql, $bindings);
		
		// return the ID of the rebuttal just added
		return $this->ci->db->insert_id();
	}
	
	/**
	 * Determines if the rebuttal is addable or not based on the
	 * session ID supplied. If the user has submitted another rebuttal
	 * within the minimum time interval, then the rebuttal should be
	 * denied.
	 *
	 * @see core.php
	 *
	 * @return
	 *  TRUE if the rebuttal is addable, FALSE otherwise.
	 */
	public function isAddable()
	{
		// construct the query to determine if the rebuttal is addable or
		// not
		$sql = "SELECT COUNT(*) AS total ";
		$sql .= "FROM " . $this->coreTables["Rebuttals"] . " ";
		$sql .= "WHERE " . $this->coreSchemas["Rebuttals"]["RatingID"] . "=? AND ";
		$sql .= $this->coreSchemas["Rebuttals"]["Status"] . " >= " . $this->coreConstants["min_rebuttal_status"] . " AND ";
		$sql .= "((" . $this->coreSchemas["Rebuttals"]["UserSessionID"] . "=? OR ";
		$sql .= $this->coreSchemas["Rebuttals"]["UserIP"] . "=?) AND ";
		$sql .= $this->coreSchemas["Rebuttals"]["Date"] . " > DATE_SUB(NOW(), INTERVAL " . $this->coreConstants["min_rebuttal_time_interval"] . "));";

		// define the bindings array
		$bindings = array(
			$this->ratingID,
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