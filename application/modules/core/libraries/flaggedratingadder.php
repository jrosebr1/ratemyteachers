<?php

/**
 * @ingroup core
 *
 * @brief
 * Class used to add flagged ratings to the database.
 *
 * This class provides a mechanism to flag ratings by a user and
 * record the flagged entry in the database.
 *
 * @see adder.php
 * @author Adrian Rosebrock
 */

class FlaggedRatingAdder extends Adder
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
	 * The ID of the rating to be flagged.
	 *
	 * @var $ratingID
	 */
	var $ratingID = null;
	/**
	 * The ID of the user flagging the rating.
	 *
	 * @var $userID
	 */
	var $userID = null;
	/**
	 * The IP address of the user flagging the rating.
	 *
	 * @var $userIP
	 */
	var $userIP = null;
	/**
	 * The text comment (reason) that the user is flagging the rating.
	 *
	 * @var $reason
	 */
	var $reason = null;

	/**
	 * Construct the FlaggedRatingAdder.
	 *
	 * @param $ratingID
	 *  The ID of the rating to be flagged.
	 * @param $userID
	 *  The ID of the user flagging the rating.
	 * @param $userIP
	 *  The IP address of the user flagging the rating.
	 * @param $reason
	 *  The text comment (reason) that the user is flagging the rating.
	 */
	public function __construct($ratingID, $userID, $userIP, $reason)
	{
		// call the parent constructor and grab the core tables, schemas,
		// and constants
		parent::__construct();
		$this->coreTables = $this->ci->config->item("core.tables");
		$this->coreSchemas = $this->ci->config->item("core.tables.schemas");
		$this->coreConstants = $this->ci->config->item("core.constants");
		
		// store the rating ID, user ID, user IP address, and the reason the
		// rating is being flagged
		$this->ratingID = $ratingID;
		$this->userID = $userID;
		$this->userIP = $userIP;
		$this->reason = $reason;

		// if the user ID is anonymous, then store a null value rather
		// than the constant value
		if ($userID == $this->coreConstants["anon_user_id"])
		{
			$this->userID = null;
		}
	}
	
	/**
	 * Add the flagged rating to the database.
	 *
	 * @return
	 *  The ID of the flagged rating entry added to the database.
	 */
	public function add()
	{
		// construct the query to flag the rating
		$sql = "INSERT INTO " . $this->coreTables["FlaggedRatings"] . "(";
		$sql .= $this->coreSchemas["FlaggedRatings"]["RatingID"] . ", ";
		$sql .= $this->coreSchemas["FlaggedRatings"]["UserID"] . ", ";
		$sql .= $this->coreSchemas["FlaggedRatings"]["UserIP"] . ", ";
		$sql .= $this->coreSchemas["FlaggedRatings"]["Reason"] . ") ";
		$sql .= "VALUES(?, ?, ?, ?);";
		
		// define the bindings array
		$bindings = array(
			$this->ratingID,
			$this->userID,
			$this->userIP,
			$this->reason);

		// execute the query
		$query = $this->ci->db->query($sql, $bindings);
		
		// return the ID of the flag
		return $this->ci->db->insert_id();
	}
}

?>