<?php

/**
 * @ingroup core
 *
 * @brief
 * Class used to sync the organization's total ratings.
 *
 * This class is used to sync the total number of ratings that
 * belong to an organization.
 *
 * @see sync.php
 * @author Adrian Rosebrock
 */

class SyncOrganization extends Sync
{
	/**
	 * ID of the organization to be synced.
	 *
	 * @var $orgID
	 */
	var $orgID = null;
	
	/**
	 * Construct the SyncOrganization.
	 *
	 * @param $orgID
	 *  ID of the organization to be synced.
	 */
	public function __construct($orgID)
	{
		// call the parent constructor and store the organization ID
		parent::__construct();
		$this->orgID = $orgID;
	}
	
	/**
	 * Sync the organization.
	 */
	public function sync()
	{
		// sync the total number of ratings in the organization
		$this->syncTotalRatings();
	}
	
	/**
	 * Sync the total number of ratings for the organization.
	 *
	 * @return
	 *  TRUE if the total number of ratings for the organization was
	 *  successfully synced, FALSE if not.
	 */
	public function syncTotalRatings()
	{
		// construct the query to get the total number of ratings for
		// the organization
		$sql = "SELECT SUM(" . $this->coreSchemas["People"]["NumRatings"] . ") AS total_ratings ";
		$sql .= "FROM " . $this->coreTables["People"] . " ";
		$sql .= "WHERE " . $this->coreSchemas["People"]["OrgID"] . "=? AND ";
		$sql .= $this->coreSchemas["People"]["Status"] . " >= " . $this->coreConstants["min_person_status"] . ";";
		$bindings = array($this->orgID);

		// execute the query
		$query = $this->ci->db->query($sql, $bindings);
		
		// if the total number of rows returned is zero, then the
		// organization does not exist in the tables
		if ($query->num_rows() == 0)
		{
			return false;
		}
		
		// get the row and construct the query to update the total
		// number of ratings
		$row = $query->row_array();
		$sql = "UPDATE " . $this->coreTables["Organizations"] . " ";
		$sql .= "SET " . $this->coreSchemas["Organizations"]["NumRatings"] . "=? ";
		$sql .= "WHERE " . $this->coreSchemas["Organizations"]["ID"] . "=?;";
		$bindings = array($row["total_ratings"], $this->orgID);
		
		// execute the query
		$this->ci->db->query($sql, $bindings);
		
		// the total number of ratings for the organization was
		// successfully synced
		return true;
	}
}

?>