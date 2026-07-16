<?php

/**
 * @ingroup core
 *
 * @brief
 * Class used to filter rebuttals.
 *
 * This class is used to filter rebuttals and select the rebuttals
 * that belong to a particular rating.
 *
 * @see filter.php
 * @author Adrian Rosebrock
 */

class RebuttalFilter extends Filter
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
	 * Core configuration of generic rateable objects.
	 *
	 * @var $coreClasses
	 */
	var $coreClasses = array();
	/**
	 * ID of the rating that the rebuttals belong to.
	 *
	 * @var $ratingID
	 */
	var $ratingID = null;

	/**
	 * Construct the RebuttalFilter.
	 *
	 * @param $ratingID
	 *  ID of the rating that the rebuttals belong to.
	 */
	public function __construct($ratingID)
	{
		// call the parent constructor and store the rating ID
		parent::__construct();
		$this->ratingID = $ratingID;
		
		// store the core tables, schemas, constants, and classes
		$this->coreTables = $this->ci->config->item("core.tables");
		$this->coreSchemas = $this->ci->config->item("core.tables.schemas");
		$this->coreConstants = $this->ci->config->item("core.constants");
		$this->coreClasses = $this->ci->config->item("core.classes");
	}

	/**
	 * Apply the filter to fetch the rebuttals that belong to the
	 * rating.
	 *
	 * @return array()
	 *  A list of rebuttals that belong to the rating.
	 */
	public function apply()
	{
		// build the query to fetch the rebuttals, execute it, and
		// initialize the list of rebuttals
		$queryInfo = $this->buildQuery();
		$query = $this->ci->db->query($queryInfo["select_rebuttals"], $queryInfo["bindings"]);
		$rebuttals = array();
		
		// build the rating
		$rb = new RatingBuilder($this->ratingID);
		$rating = $rb->build();
		
		// loop over the results
		foreach ($query->result_array() as $row)
		{
			// create the rebuttal and add it to the rebuttal list
			$Class = $this->coreClasses["Rebuttal"];
			$rebuttals[] = new $Class($rating, $row);
		}
		
		// return the list of rebuttals
		return $rebuttals;
	}

	/**
	 * Builds the query used to select rebuttals.
	 *
	 * @return array()
	 *  The query used to select the rebutatls, along with the
	 *  bindings needed to execute the query.
	 */
	private function buildQuery()
	{
		// build the query to select the rebuttals for the rating
		$sql = "SELECT rb.* ";
		$sql .= "FROM " . $this->coreTables["Rebuttals"] . " rb ";
		$sql .= "INNER JOIN " . $this->coreTables["Ratings"] . " ra ON ";
		$sql .= "rb." . $this->coreSchemas["Rebuttals"]["RatingID"] . "=ra." . $this->coreSchemas["Ratings"]["ID"] . " ";
		$sql .= "WHERE rb." . $this->coreSchemas["Rebuttals"]["RatingID"] . "=? AND ";
		$sql .= "rb." . $this->coreSchemas["Rebuttals"]["Status"] . " >= " . $this->coreConstants["min_rebuttal_status"] . " AND ";
		$sql .= "ra." . $this->coreSchemas["Ratings"]["Status"] . " >= " . $this->coreConstants["min_rating_status"] . " ";
		$sql .= "ORDER BY " . $this->coreSchemas["Rebuttals"]["ID"] . " DESC;";
		
		// define the bindings array
		$bindings = array($this->ratingID);
		
		// create a dictionary to hold the query and bindings
		$query = array(
					"select_rebuttals" => $sql,
					"bindings" => $bindings);

		// return the finished query and bindings
		return $query;
	}
}

?>