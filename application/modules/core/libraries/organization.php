<?php

/**
 * @ingroup core
 *
 * @brief
 * Class used to represent the first tier of the rating platform.
 *
 * This class is used to represent the first tier of the rating
 * platform, the organization. An organization consists of a list
 * of persons.
 *
 * @see rateable.php
 * @author Adrian Rosebrock
 */

class Organization extends Rateable
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
	 * Construct the Organization.
	 *
	 * @param $data
	 *  Dictionary of data from the database to represent the
	 *  organization.
	 */
	public function __construct($data)
	{
		// call the parent constructor, store the data dictionary and
		// get the core tables, schema, and constants
		parent::__construct();
		$this->data = $data;
		$this->coreTables = $this->ci->config->item("core.tables");
		$this->coreSchemas = $this->ci->config->item("core.tables.schemas");
		$this->coreConstants = $this->ci->config->item("core.constants");
	}
	
	/**
	 * Get the ID of the organization.
	 *
	 * @return
	 *  The ID of the organization.
	 */
	public function getID()
	{
		// return the ID of the organization
		return $this->getInfo($this->coreSchemas["Organizations"]["ID"]);
	}
	
	/**
	 * Get the name of the organization.
	 *
	 * @return
	 *  The name of the organization.
	 */
	public function getName()
	{
		// return the name of the organization
		return $this->getInfo($this->coreSchemas["Organizations"]["Name"]);
	}
	
	/**
	 * Get the name of the city that the organization resides in.
	 *
	 * @return
	 *  The city the organization resides in.
	 */
	public function getCity()
	{
		// return the city of the organization
		return $this->getInfo($this->coreSchemas["Organizations"]["City"]);
	}
	
	/**
	 * Get the state that the organization resides in.
	 *
	 * @return
	 *  The state that the organization resides in.
	 */
	public function getState()
	{
		// return the state of the organization
		return $this->getInfo($this->coreSchemas["Organizations"]["State"]);
	}
	
	/**
	 * Get the total number of ratings for the organization.
	 *
	 * @return
	 *  The total number of ratings for the organization.
	 */
	public function getTotalRatings()
	{
		// return the total number of ratings for the organiztion
		return $this->getInfo($this->coreSchemas["Organizations"]["NumRatings"]);
	}
	
	/**
	 * Get the list of people that belong to the organization,
	 * based on the current page number, and whether the filter
	 * options are applied.
	 *
	 * @param $pageNum
	 *  The current page number the user is viewing.
	 * @param $useCleanSlate
	 *  Boolean variable indicating whether or not to use a clean
	 *  person filter.
	 *
	 * @return array()
	 *  A dictionary containing the list of the persons and the
	 *  filters used to derive the list.
	 *
	 * @see personfilterparser.php
	 * @see personfilter.php
	 */
	public function getPersons($pageNum, $useCleanSlate = false)
	{
		// grab the person filter options from the session and
		$filterOptions = $this->ci->session->userdata("person_filter_options");
		$filterOptions = (array)json_decode($filterOptions);

		// use a clean slate if the clean slate variable is explicity
		// set to true
		if ($useCleanSlate)
		{
			$filterOptions = PersonFilterParser::cleanSlate($this->getID());
		}

		// check to see if the filter options are not empty
		else if (!empty($filterOptions))
		{			
			// if the ID of the current organization does not match
			// the organization ID in the filter options, then make
			// a clean slate out of the filter options
			if ($filterOptions["id"] != $this->getID())
			{
				$filterOptions = PersonFilterParser::cleanSlate($this->getID());
			}
		}
		
		// otherwise, the filter options are empty and should be
		// initialized to their defaults
		else
		{
			$filterOptions = PersonFilterParser::cleanSlate($this->getID());
		}

		// apply the filter
		$pf = new PersonFilter($this->getID(), $pageNum);
		$pf->setOrderBy($this->coreSchemas["People"][$filterOptions["order_by"]]);
		$pf->setOrderDirection($filterOptions["order_dir"]);
		$pf->addStartsWith($filterOptions["letter"]);
		$pf->addDepartment($filterOptions["dept"]);
		$persons = $pf->apply();
		
		// add the filter options to the persons dictionary and update
		// the session
		$persons["filter_options"] = $filterOptions;
		$filterOptions = json_encode($filterOptions);
		$this->ci->session->set_userdata("person_filter_options", $filterOptions);
		
		// return the persons dictionary
		return $persons;
	}
	
	/**
	 * Get the list of departments that exist within this
	 * particular organization.
	 *
	 * @return array()
	 *  The list of departments that exist within this organization.
	 */
	public function getDepartments()
	{
		// get the person filter options and initialize the list of
		// departments and the query bindings
		$filterOptions = $this->ci->session->userdata("person_filter_options");
		$filterOptions = (array)json_decode($filterOptions);
		$depts = array("All Departments");
		$bindings = array();
	
		// construct the query to get the set of departments for
		// this particular organization
		$sql = "SELECT DISTINCT " . $this->coreSchemas["People"]["Department"] . " ";
		$sql .= "FROM " . $this->coreTables["People"] . " ";
		$sql .= "WHERE " . $this->coreSchemas["People"]["Status"] . " >= " . $this->coreConstants["min_person_status"] . " ";
		
		// if the letter option is not empty, add the restriction to
		// the query
		if (!empty($filterOptions["letter"]))
		{
			$sql .= "AND (SUBSTRING(" . $this->coreSchemas["People"]["LastName"];
			$sql .= ", 1, 1) IN (?)) ";
			$bindings[] = $filterOptions["letter"];
		}
		
		$sql .= "ORDER BY " . $this->coreSchemas["People"]["Department"] . ";";

		// execute the query
		$query = $this->ci->db->query($sql, $bindings);
		
		// loop over the rows and add each department to the department
		// list
		foreach ($query->result_array() as $row)
		{
			$depts[] = $row[$this->coreSchemas["People"]["Department"]];
		}
		
		// return the list of departments
		return $depts;
	}
	
	/**
	 * Get the aggregate statistics for the organization.
	 *
	 * @return
	 *  The aggregate statistics for the organization.
	 *
	 * @see organizationstats.php
	 */
	public function getStats()
	{
		// calculate statistics for the organization
		$sb = new OrganizationStats($this->getID());
		$sb->calculate();

		// return the statistics
		return $sb->getStats();		
	}	
}

?>