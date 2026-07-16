<?php

/**
 * @ingroup core
 *
 * @brief
 * Class used to represent the third tier of the rating platform.
 *
 * This class is used to represent the third tier of the rating
 * platform, the rating. A rating consists of a set of numerical
 * and attribute fields, along with any rebuttals (the fourth and
 * optional tier of the rating platform).
 *
 * @see rateable.php
 * @author Adrian Rosebrock
 */

class Rating extends Rateable
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
	 * Core configuration of generic rateable classes.
	 *
	 * @var $coreClasses
	 * @see site.php
	 */
	var $coreClasses = array();
	/**
	 * Rating numerical fields.
	 *
	 * @var $siteNumericalFields
	 */
	var $siteNumericalFields = array();
	/**
	 * Person that the rating belongs to.
	 *
	 * @var Person $person
	 */
	var $person = null;
	
	/**
	 * Construct the rating.
	 *
	 * @param $person
	 *  Person that the rating belongs to.
	 * @param $data
	 *  Dictionary of data from the database to represent the
	 *  rating.
	 */
	public function __construct($person, $data)
	{
		// call the parent constructor and store the parent person and
		// the data that coressponds to the rating
		parent::__construct();
		$this->person = $person;
		$this->data = $data;

		// get the core tables, schemas, constants, and classes
		$this->coreTables = $this->ci->config->item("core.tables");
		$this->coreSchemas = $this->ci->config->item("core.tables.schemas");
		$this->coreConstants = $this->ci->config->item("core.constants");
		$this->coreClasses = $this->ci->config->item("core.classes");
		
		// get the rating numerical fields
		$this->siteNumericalFields = $this->ci->config->item("site.ratings.numerical_fields");
	}

	/**
	 * Get the ID of the rating.
	 *
	 * @return
	 *  The ID Of the rating.
	 */
	public function getID()
	{
		// return the ID of the person
		return $this->getInfo($this->coreSchemas["Ratings"]["ID"]);
	}
	
	/**
	 * Get the person that the rating belongs to.
	 *
	 * @return Person
	 *  Person that the rating belings to.
	 */
	public function getPerson()
	{
		// return the parent person of the rating
		return $this->person;
	}
	
	/**
	 * Get the name of the person that the rating belongs to.
	 *
	 * @return
	 *  The name of the person that the rating belongs to.
	 */
	public function getName()
	{
		// return the name of the person that the rating belongs to
		return $this->person->getName();
	}
	
	/**
	 * Get the formatted date of the rating.
	 *
	 * @return
	 *  The formatted date of the rating.
	 */
	public function getDate()
	{
		// get the unformatted date
		$date = $this->getInfo($this->coreSchemas["Ratings"]["Date"]);
		
		// format the date
		$dt = new DateTime($date);
		$date = $dt->format("m/d/y");
		
		// return the formatted date of the rating
		return $date;
	}
	
	/**
	 * Get the comment of the rating.
	 *
	 * @return
	 * The comment of the rating.
	 */
	public function getComment()
	{
		// return the comment of the rating
		return $this->getInfo($this->coreSchemas["Ratings"]["Comment"]);
	}
	
	/**
	 * Get the numerical rating score for the supplied field
	 * name.
	 *
	 * @param $fieldName
	 *  The column name for the score to be returned.
	 *
	 * @return
	 *  The rating score in the $fieldName column.
	 */
	public function getScore($fieldName)
	{
		// grab the field information for the field with the supplied
		// name
		$fieldInfo = $this->siteNumericalFields[$fieldName];
		
		// if the field information is invalid, then the field name is
		// invalid so return null
		if (empty($fieldInfo))
		{
			return null;
		}
		
		// return the score of the supplied field name
		return $this->getInfo($fieldInfo["column"]);
	}
	
	/**
	 * Get all displayable rating scores, as configured in site.php.
	 *
	 * @return array()
	 *  A list of all the displayable scores for the rating.
	 *
	 * @see site.php
	 */
	public function getScores()
	{
		// initialize the list of score fields
		$scores = array();
		
		// loop over each of the numerical fields
		foreach ($this->siteNumericalFields as $field => $fieldInfo)
		{
			// if the field is displayable, add the field to the the
			// score fields
			if ($fieldInfo["displayable"])
			{
				$scores[] = array(
					"field_label" => $fieldInfo["field_label"],
					"field_score" => $this->getInfo($fieldInfo["column"]));
			}
		}
		
		// return the list of score fields
		return $scores;
	}
	
	/**
	 * Get the previous rating.
	 *
	 * @return Rating
	 *  The previous rating.
	 */
	public function getPreviousRating()
	{
		// get the previous rating
		return $this->getOrderedRating("<", "DESC");
	}
	
	/**
	 * Get the next rating.
	 *
	 * @return Rating
	 *  The next rating.
	 */
	public function getNextRating()
	{
		// get the next rating
		return $this->getOrderedRating(">", "ASC");
	}
	
	/**
	 * Helper method used by getPreviousRating() and getNextRating()
	 * to avoid redudant code.
	 *
	 * @param $symbol
	 *  Either '<' or '>'.
	 * @param $direction
	 *  Either 'ASC' or 'DESC'.
	 *
	 * @return Rating
	 *  The previous or the next rating.
	 */
	private function getOrderedRating($symbol = "<", $direction = "DESC")
	{
		// construct the query to get the rating
		$sql = "SELECT * ";
		$sql .= "FROM " . $this->coreTables["Ratings"] . " ";
		$sql .= "WHERE " . $this->coreSchemas["Ratings"]["PersonID"] . "=? AND ";
		$sql .= $this->coreSchemas["Ratings"]["ID"] . " " . $symbol . " ? AND ";
		$sql .= $this->coreSchemas["Ratings"]["Status"] . " >= " . $this->coreConstants["min_rating_status"] . " ";
		$sql .= "ORDER BY " . $this->coreSchemas["Ratings"]["ID"] . " " . $direction . ";";
		$bindings = array($this->person->getID(), $this->getID());

		// execute the query
		$query = $this->ci->db->query($sql, $bindings);
		
		// if the number of rows is zero, then there is not a previous
		// rating, so return false
		if ($query->num_rows() == 0)
		{
			return false;
		}
		
		// grab the row and construct the rating
		$row = $query->row_array();
		$Class = $this->coreClasses["Rating"];
		$rating = new $Class($this->person, $row);
		
		// return the built rating
		return $rating;
	}
	
	/**
	 * Get the list of rebuttals based on the rebuttal filter that
	 * belong to this rating.
	 *
	 * @return array()
	 *  A list of rebuttals that belong to the rating
	 *
	 * @see rebuttalfilter.php
	 */
	public function getRebuttals()
	{
		// apply the filter
		$rf = new RebuttalFilter($this->getID());
		$rebuttals = $rf->apply();
		
		// return the rebuttals
		return $rebuttals;
	}
}

?>