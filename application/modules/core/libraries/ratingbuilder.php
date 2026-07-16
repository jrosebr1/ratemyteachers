<?php

/**
 * @ingroup core
 *
 * @brief
 * Class used to build Rating objects.
 *
 * This class is used to build Rating objects by handling the
 * database access as well as the construction of the Rating
 * through the core classes.
 *
 * @see rateablebuilder.php
 * @see rating.php
 * @see core.php
 * @author Adrian Rosebrock
 */

class RatingBuilder extends RateableBuilder
{
	/**
	 * ID of the rating.
	 *
	 * @var $ratingID
	 */
	var $ratingID = null;
	
	/**
	 * Construct the RatingBuilder.
	 *
	 * @param $ratingID
	 *  The ID of the rating.
	 */
	public function __construct($ratingID)
	{
		// call the parent constructor and store the rating ID
		parent::__construct();
		$this->ratingID = $ratingID;
	}

	/**
	 * Build the rating.
	 *
	 * @return Rating
	 *  A built Rating object.
	 *
	 * @see rating.php
	 */
	public function build()
	{
		// grab the core tables, schemas, constants, and classes
		$coreTables = $this->ci->config->item("core.tables");
		$coreSchemas = $this->ci->config->item("core.tables.schemas");
		$coreConstants = $this->ci->config->item("core.constants");
		$coreClasses = $this->ci->config->item("core.classes");
		
		// construct the query to get the rating information
		$sql = "SELECT r.* ";
		$sql .= "FROM " . $coreTables["Ratings"] . " r ";
		$sql .= "INNER JOIN " . $coreTables["People"] . " p ON ";
		$sql .= "r." . $coreSchemas["Ratings"]["PersonID"] . "=p." . $coreSchemas["People"]["ID"] . " ";
		$sql .= "WHERE " . $coreSchemas["Ratings"]["ID"] . "=? AND ";
		$sql .= "r." . $coreSchemas["Ratings"]["Status"] . " >= " . $coreConstants["min_rating_status"] . " AND ";
		$sql .= "p." . $coreSchemas["People"]["Status"] . " >= " . $coreConstants["min_person_status"] . ";";
		$bindings = array($this->ratingID);

		// execute the query
		$query = $this->ci->db->query($sql, $bindings);

		// if the number of returned rows is zero, then return null
		// from the method since the rating is invalid
		if ($query->num_rows() == 0)
		{
			return null;
		}

		// otherwise, the rating exists and we can update the 'valid'
		// variable and grab the row
		$this->setValid(true);
		$row = $query->row_array();
		
		// now that we have the row, we can get the person ID and build
		// the person as well
		$pb = new PersonBuilder($row[$coreSchemas["People"]["ID"]]);
		$person = $pb->build();
		
		// build the rating
		$Class = $coreClasses["Rating"];
		$rating = new $Class($person, $row);
		
		// return the built rating
		return $rating;
	}
}

?>