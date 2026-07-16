<?php

/**
 * @ingroup core
 *
 * @brief
 * Class used to build Rebuttal objects.
 *
 * This class is used to build Rebuttal objects by handling the
 * database access as well as the construction of the Rebuttal
 * through the core classes.
 *
 * @see rateablebuilder.php
 * @see rebuttal.php
 * @see core.php
 * @author Adrian Rosebrock
 */

class RebuttalBuilder extends RateableBuilder
{
	/**
	 * ID of the rebuttal.
	 *
	 * @var $rebuttalID
	 */
	var $rebuttalID = null;
	
	/**
	 * Construct the RebuttalBuilder.
	 *
	 * @param $rebuttalID
	 *  The ID of the rebuttal.
	 */
	public function __construct($rebuttalID)
	{
		// call the parent constructor and store the rebuttal ID
		parent::__construct();
		$this->rebuttalID = $rebuttalID;
	}

	/**
	 * Build the rebuttal.
	 *
	 * @return Rebuttal
	 *  A built Rebuttal object.
	 *
	 * @see rebuttal.php
	 */
	public function build()
	{
		// grab the core tables, schemas, constants, and classes
		$coreTables = $this->ci->config->item("core.tables");
		$coreSchemas = $this->ci->config->item("core.tables.schemas");
		$coreConstants = $this->ci->config->item("core.constants");
		$coreClasses = $this->ci->config->item("core.classes");

		// construct the query to get the rebuttal information
		$sql = "SELECT rb.* ";
		$sql .= "FROM " . $coreTables["Rebuttals"] . " rb ";
		$sql .= "INNER JOIN " . $coreTables["Ratings"] . " ra ON ";
		$sql .= "rb." . $coreSchemas["Rebuttals"]["RatingID"] . "=ra." . $coreSchemas["Ratings"]["ID"] . " ";
		$sql .= "WHERE rb." . $coreSchemas["Rebuttals"]["ID"] . "=? AND ";
		$sql .= "rb." . $coreSchemas["Rebuttals"]["Status"] . " >= " . $coreConstants["min_rebuttal_status"] . " AND ";
		$sql .= "ra." . $coreSchemas["Ratings"]["Status"] . " >= " . $coreConstants["min_rating_status"] . ";";
		$bindings = array($this->rebuttalID);
		
		// execute the query
		$query = $this->ci->db->query($sql, $bindings);

		// if the number of returned rows is zero, then return null
		// from the method since the rebuttal is invalid
		if ($query->num_rows() == 0)
		{
			return null;
		}

		// otherwise, the rebuttal exists and we can update the 'valid'
		// variable and grab the row
		$this->setValid(true);
		$row = $query->row_array();
		
		// now that we have the row, we can get the rating ID and build
		// the rating as well
		$rb = new RatingBuilder($row[$coreSchemas["Rebuttals"]["RatingID"]]);
		$rating = $rb->build();
		
		// build the rebuttal
		$Class = $coreClasses["Rebuttal"];
		$rebuttal = new $Class($rating, $row);
		
		// return the built rebuttal
		return $rebuttal;
	}
}

?>