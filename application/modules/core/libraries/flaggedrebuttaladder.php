<?php

/**
 * @ingroup core
 *
 * @brief
 * Class used to add flagged rebuttals to the database.
 *
 * This class provides a mechanism to flag rebuttals by a user and
 * record the flagged entry in the database.
 *
 * @see adder.php
 * @author Adrian Rosebrock
 */

class FlaggedRebuttalAdder extends Adder
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
	 * The ID of the rebuttal to be flagged.
	 *
	 * @var $rebuttalID
	 */
	var $rebuttalID = null;
	/**
	 * The ID of the user flagging the rebuttal.
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
	 * The text comment (reason) that the user is flagging the rebuttal.
	 *
	 * @var $reason
	 */
	var $reason = null;

	/**
	 * Construct the FlaggedRebuttalAdder.
	 *
	 * @param $rebuttalID
	 *  The ID of the rebuttal to be flagged.
	 * @param $userID
	 *  The ID of the user flagging the rebuttal.
	 * @param $userIP
	 *  The IP address of the user flagging the rebuttal.
	 * @param $reason
	 *  The text comment (reason) that the user is flagging the
	 *  rebuttal.
	 */
	public function __construct($rebuttalID, $userID, $userIP, $reason)
	{
		// call the parent constructor and grab the core tables, schemas,
		// and constants
		parent::__construct();
		$this->coreTables = $this->ci->config->item("core.tables");
		$this->coreSchemas = $this->ci->config->item("core.tables.schemas");
		$this->coreConstants = $this->ci->config->item("core.constants");
		
		// store the rebuttal ID, user ID, user IP address, and the reason the
		// rebuttal is being flagged
		$this->rebuttalID = $rebuttalID;
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
	 * Add the flagged rebuttal to the database.
	 *
	 * @return
	 *  The ID of the flagged rebuttal entry added to the database.
	 */
	public function add()
	{
		// construct the query to flag the rebuttal
		$sql = "INSERT INTO " . $this->coreTables["FlaggedRebuttals"] . "(";
		$sql .= $this->coreSchemas["FlaggedRebuttals"]["RebuttalID"] . ", ";
		$sql .= $this->coreSchemas["FlaggedRebuttals"]["UserID"] . ", ";
		$sql .= $this->coreSchemas["FlaggedRebuttals"]["UserIP"] . ", ";
		$sql .= $this->coreSchemas["FlaggedRebuttals"]["Reason"] . ") ";
		$sql .= "VALUES(?, ?, ?, ?);";
		
		// define the bindings array
		$bindings = array(
			$this->rebuttalID,
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