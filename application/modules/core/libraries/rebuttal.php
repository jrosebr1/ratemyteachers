<?php

/**
 * @ingroup core
 *
 * @brief
 * Class used to represent the fourth, but optional tier of the
 * rating platform.
 *
 * This class is used to represent the fourth, but optional tier
 * of the rating platform, the rebuttal. A rebuttal is nothing
 * more than a comment on a rating, hence the term 'rebuttal'.
 *
 * @see rateable.php
 * @author Adrian Rosebrock
 */

class Rebuttal extends Rateable
{
	/**
	 * Core configuration of table schemas.
	 *
	 * @var $coreSchemas
	 * @see site.php
	 */
	var $coreSchemas = array();
	/**
	 * Rating that the rebuttal belongs to.
	 *
	 * @var Rating $rating
	 */
	var $rating = null;

	/**
	 * Construct the Rebuttal.
	 *
	 * @param $rating
	 *  Rating that the rebuttal belongs to.
	 *
	 * @param $data
	 *  Dictionary of data from the database to represent the
	 *  rebuttal.
	 */
	public function __construct($rating, $data)
	{
		// call the parent constructor and store the parent rating
		// and the data that corresponds to the rebuttal
		parent::__construct();
		$this->rating = $rating;
		$this->data = $data;
		
		// grab the core table schemas
		$this->coreSchemas = $this->ci->config->item("core.tables.schemas");
	}
	
	/**
	 * Get the ID of the rebuttal.
	 *
	 * @return
	 *  The ID of the rebuttal.
	 */
	public function getID()
	{
		// return the ID of the rebuttal
		return $this->getInfo($this->coreSchemas["Rebuttals"]["ID"]);
	}
	
	/**
	 * Get the rating that the rebuttal belongs to.
	 *
	 * @return Rating
	 *  The rating that the rebuttal belongs to.
	 */
	public function getRating()
	{
		// return the rating the rebuttal belongs to
		return $this->rating;
	}
	
	/**
	 * Get the formatted date of the rebuttal.
	 *
	 * @return
	 *  The formatted date of the rebuttal.
	 */
	public function getDate()
	{
		// get the unformatted date
		$date = $this->getInfo($this->coreSchemas["Rebuttals"]["Date"]);
		
		// format the date
		$dt = new DateTime($date);
		$date = $dt->format("m/d/y");
		
		// return the formatted date of the rebuttal
		return $date;
	}

	/**
	 * Get the comment of the rebuttal.
	 *
	 * @return
	 *  The comment of the rebuttal.
	 */
	public function getComment()
	{
		// return the comment of the rebuttal
		return $this->getInfo($this->coreSchemas["Rebuttals"]["Comment"]);
	}
	
	/**
	 * Get the name of the person that the rebuttal belongs to.
	 *
	 * @return
	 *  The name of the person that the rebuttal belongs to.
	 */
	public function getName()
	{
		// return the name of the person that the rebuttal belongs to
		return $this->rating->getName();
	}
}

?>