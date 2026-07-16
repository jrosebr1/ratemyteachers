<?php

/**
 * @ingroup core
 *
 * @brief
 * Class used to represent the second tier of the rating platform.
 *
 * This class is used to represent the second tier of the rating
 * platform, the person. A person consists of a list of ratings.
 *
 * @see rateable.php
 * @author Adrian Rosebrock
 */

class Person extends Rateable
{
	/**
	 * Core configuration of table schemas.
	 *
	 * @var $coreSchemas
	 * @see site.php
	 */
	var $coreSchemas = array();
	/**
	 * Organization that the person belongs to.
	 *
	 * @var Organization $org
	 */
	var $org = null;

	/**
	 * Construct the person.
	 *
	 * @param Organization $org
	 *  Organization that the person belongs to.
	 * @param $data
	 *  Dictionary of data from the database to represent the
	 *  person.
	 */
	public function __construct($org, $data)
	{
		// call the parent constructor and store the parent organization
		// and the data that coressponds to the person
		parent::__construct();
		$this->org = $org;
		$this->data = $data;
		
		// get the core schemas
		$this->coreSchemas = $this->ci->config->item("core.tables.schemas");
	}
	
	/**
	 * Get the ID of the person.
	 *
	 * @return
	 *  The ID of the person.
	 */
	public function getID()
	{
		// return the ID of the person
		return $this->getInfo($this->coreSchemas["People"]["ID"]);
	}
	
	/**
	 * Get the organization that the person belongs to.
	 * @return Organization
	 *  The organization that the person belongs to.
	 */
	public function getOrganization()
	{
		// return the parent organization of the person
		return $this->org;
	}
	
	/**
	 * Get the formatted name of the person (usually the first
	 * name and the last name put together).
	 *
	 * @return
	 *  The formatted name of the person.
	 */
	public function getName()
	{
		// return the formatted name of the person
		return trim($this->getFirstName() . " " . $this->getLastName());
	}
	
	/**
	 * Get the first name of the person.
	 *
	 * @return
	 *  The first name of the person.
	 */
	public function getFirstName()
	{
		// return the ID of the person
		return $this->getInfo($this->coreSchemas["People"]["FirstName"]);
	}
	
	/**
	 * Get the last name of the person.
	 *
	 * @return
	 *  The last name of the person.
	 */
	public function getLastName()
	{
		// return the ID of the person
		return $this->getInfo($this->coreSchemas["People"]["LastName"]);
	}
	
	/**
	 * Get the department that the person belongs to.
	 *
	 * @return
	 *  The department the person belongs to.
	 */
	public function getDepartment()
	{
		// return the ID of the person
		return $this->getInfo($this->coreSchemas["People"]["Department"]);
	}
	
	/**
	 * Get the total number of ratings for the person.
	 *
	 * @return
	 *  The total number of ratings for the person.
	 */
	public function getTotalRatings()
	{
		// return the total number of ratings for this person
		return $this->getInfo($this->coreSchemas["People"]["NumRatings"]);
	}
	
	/**
	 * Get the list of ratings for the person, filtered by
	 * the page number.
	 *
	 * @param $pageNum
	 *  The page number the user is currently viewing.
	 *
	 * @return array()
	 *  The list of ratings based on the page number.
	 *
	 * @see ratingfilter.php
	 */
	public function getRatings($pageNum)
	{
		// apply the filter
		$rf = new RatingFilter($this->getID(), $pageNum);
		$ratings = $rf->apply();
		
		// return the ratings dictionary
		return $ratings;
	}
	
	/**
	 * Get the aggregate scores for the person based on the
	 * rating field configuration, which defines which fields
	 * are aggregateable or not.
	 *
	 * @return array()
	 *  The list of aggregate fields and their corresponding
	 *  scores.
	 *
	 * @see site.php
	 */
	public function getAggregates()
	{
		// grab the numerical rating fields from the site configuration
		// and initialize the list of aggregate fields
		$numericalFields = $this->ci->config->item("site.ratings.numerical_fields");
		$aggr = array();
		
		// loop over the numerical fields
		foreach ($numericalFields as $field => $fieldInfo)
		{
			// if the field is an aggregate field, then store the aggregate
			// field name and column name in the aggregate list
			if ($fieldInfo["aggregate"])
			{
				$aggr[] = array(
							"aggr_name" => $fieldInfo["field_label"],
							"aggr_score" => formatFieldScore($this->getInfo($fieldInfo["aggregate"])));
			}
		}
		
		// return the list of aggregate fields
		return $aggr;
	}	
}

?>