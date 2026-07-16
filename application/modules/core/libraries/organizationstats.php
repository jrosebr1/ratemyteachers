<?php

/**
 * @ingroup core
 *
 * @brief
 * Class used to calculate statistics for an organization.
 *
 * This class is used to calculate the average person score and
 * the total number of persons in an organization.
 *
 * @see stats.php
 * @author Adrian Rosebrock
 */

class OrganizationStats extends Stats
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
	 * ID of the organization.
	 *
	 * @var $orgID
	 */
	var $orgID;
	
	/**
	 * Construct the OrganizationStats.
	 *
	 * @param $orgID
	 *  ID of the organization.
	 */
	public function __construct($orgID)
	{
		// call the parent constructor, store the organization ID,
		// and store the core tables, schemas and constants
		parent::__construct();
		$this->orgID = $orgID;
		$this->coreTables = $this->ci->config->item("core.tables");
		$this->coreSchemas = $this->ci->config->item("core.tables.schemas");
		$this->coreConstants = $this->ci->config->item("core.constants");
	}
	
	/**
	 * Calculate the needed statistics for the organization, such
	 * as the average person score and the total number of persons
	 * in the organization.
	 */
	public function calculate()
	{
		// calculate the average person score and the total number
		// of persons in the organizations
		$this->stats["avg_person_score"] = $this->getAvgPersonScore();
		$this->stats["total_persons"] = $this->getTotalPersons();
	}
	
	/**
	 * Calculates the average person score for the persons
	 * in the organization.
	 *
	 * @return
	 *  The average person score for the persons in the organization.
	 */
	private function getAvgPersonScore()
	{
		// construct the query to calculate the average person score
		$sql = "SELECT AVG(" . $this->coreSchemas["People"]["AvgScore"] . ") AS avg_person_score ";
		$sql .= "FROM " . $this->coreTables["People"] . " ";
		$sql .= "WHERE " . $this->coreSchemas["People"]["OrgID"] . "=? AND ";
		$sql .= $this->coreSchemas["People"]["Status"] . " >= " . $this->coreConstants["min_person_status"] . ";";
		$bindings = array($this->orgID);

		// execute the query and get the row
		$query = $this->ci->db->query($sql, $bindings);
		$row = $query->row_array();
		$avgScore = formatFieldScore($row["avg_person_score"]);

		// return the average score
		return $avgScore;
	}
	
	/**
	 * Calculates the total number of persons that belong to
	 * the organization.
	 *
	 * @return
	 *  The total number of persons that belong to the organization.
	 */
	private function getTotalPersons()
	{
		// construct the query to get the total number of persons
		// in the organization
		$sql = "SELECT COUNT(*) AS total ";
		$sql .= "FROM " . $this->coreTables["People"] . " ";
		$sql .= "WHERE " . $this->coreSchemas["People"]["OrgID"] . "=? AND ";
		$sql .= $this->coreSchemas["People"]["Status"] . " >= " . $this->coreConstants["min_person_status"] . ";";
		$bindings = array($this->orgID);

		// execute the query and get the row
		$query = $this->ci->db->query($sql, $bindings);
		$row = $query->row_array();
		
		// return the total number of persons
		return $row["total"];
	}
}

?>