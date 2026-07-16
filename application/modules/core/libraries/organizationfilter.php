<?php

/**
 * @ingroup core
 *
 * @brief
 * Class used to filter organizations, such as in search results.
 *
 * This class extends the abstract Filter class and is used to
 * filter organizations according to set of rules, such as the
 * letter it starts with, whether it contains a piece of text
 * or not, and if it belongs to a set of states.
 *
 * @see filter.php
 * @author Adrian Rosebrock
 */

class OrganizationFilter extends Filter
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
	 * Core configuration of generic rateable objects.
	 *
	 * @var $coreClasses
	 * @see site.php
	 */
	var $coreClasses = array();
	/**
	 * Core configuration of constants.
	 *
	 * @var $coreConstants
	 * @see core.php
	 */
	var $coreConstants = array();
	/**
	 * Array of letters that an organization's name could start
	 * with.
	 *
	 * @var $letters
	 */
	var $letters = array();
	/**
	 * Array of strings that could be part of an organization's
	 * name.
	 *
	 * @var $contains
	 */
	var $contains = array();
	/**
	 * Array of states that an organization could belong to.
	 *
	 * @var $states
	 */
	var $states = array();

	/**
	 * Construct the OrganizationFilter.
	 */
	public function __construct()
	{
		// call the parent constructor and grab the core tables, schemas,
		// classes, and constants
		parent::__construct();
		$this->coreTables = $this->ci->config->item("core.tables");
		$this->coreSchemas = $this->ci->config->item("core.tables.schemas");
		$this->coreClasses = $this->ci->config->item("core.classes");
		$this->coreConstants = $this->ci->config->item("core.constants");
	}
	
	/**
	 * Add the supplied letter to the list of letters that an
	 * organization's name could start with.
	 *
	 * @param $letter
	 *  Letter that an organization's name could start with.
	 */
	public function addStartsWith($letter)
	{	
		// add the letter to the array of letters that an organization
		// could start with if it is not empty
		if (!empty($letter))
		{
			$this->letters[] = $this->ci->db->escape(strtoupper($letter));
		}
	}
	
	/**
	 * Add the supplied string to the list of strings that an
	 * organization's name could contain.
	 *
	 * @param $contain
	 *  String that an organization's name could contain.
	 */
	public function addContains($contain)
	{
		// add the string to the array of strings that could appear in
		// the organization if it is not empty
		if (!empty($contain))
		{
			$this->contains[] = $this->ci->db->escape("%" . strtoupper($contain) . "%");
		}
	}
	
	/**
	 * Add the supplied state to the list of states that an
	 * organization could exist in.
	 *
	 * @param $state
	 *  State that an organization could exist in.
	 */
	public function addState($state)
	{
		// add the state to the arry of states that an organization
		// could be from if it is not empty
		if (!empty($state))
		{
			$this->states[] = $this->ci->db->escape($state);
		}
	}
	
	/**
	 * Apply the letter, contains, and state filters, then return
	 * a list of organizations that match those rules.
	 *
	 * @return array()
	 *  A list of organizations that match the letter, contains,
	 *  and state filters.
	 */
	public function apply()
	{
		// initialize the list of organizations
		$orgs = array();
	
		// only perform the filter if the query filter is buildable
		if ($this->isQueryBuildable())
		{
			// build the query based on the filters and execute it
			$queryInfo = $this->buildQuery();
			$query = $this->ci->db->query($queryInfo["select_orgs"], $queryInfo["bindings"]);
		
			// loop over the results and build an organization from
			// the row
			foreach ($query->result_array() as $row)
			{
				$Class = $this->coreClasses["Organization"];
				$orgs[] = new $Class($row);
			}
		}
		
		// return the list of organizations
		return $orgs;
	}
	
	/**
	 * Builds the query used to filter organizations.
	 *
	 * @return array()
	 *  The query used to filter the organization, along with the
	 *  bindings needed to execute the query.
	 */
	private function buildQuery()
	{
		// build the 'starts with', 'contains' and 'state' portion of
		// the filter query, and initialize the number of portions that
		// have been added
		$sqlLetter = $this->buildStartsWith();
		$sqlContains = $this->buildContains();
		$sqlStates = $this->buildStates();
		$portions = 0;
		
		// start building the query
		$sql = "SELECT * ";
		$sql .= "FROM " . $this->coreTables["Organizations"] . " WHERE ";
		$sql .= $this->coreSchemas["Organizations"]["CountryID"] . "=? AND ";

		// if the 'starts with' portion is not empty, add it to the
		// query and increment the number of portions
		if (!empty($sqlLetter))
		{
			$sql .= $sqlLetter;
			$portions++;
		}
		
		// if the 'contains' portion of the query is not empty, add
		// it to the query and increment the number of portions
		if (!empty($sqlContains))
		{
			// if the number of portions is greater than zero, then
			// we need to place an 'AND' in front of the portion
			if ($portions > 0)
			{
				$sqlContains = " AND " . $sqlContains;
			}
			
			// add the 'contains' portion to the query
			$sql .= $sqlContains;
			$portions++;
		}
		
		// if the 'states' portion of the query is not empty, add
		// it to the query
		if (!empty($sqlStates))
		{
			// if the number of portions is greater than zero, then
			// we need to place an 'AND' in front of the portion
			if ($portions > 0)
			{
				$sqlStates = " AND " . $sqlStates;
			}
			
			// add the 'states' portion to the query
			$sql .= $sqlStates;
		}
		
		// finsih off the query and build the bindings list
		$sql = trim($sql) . ";";
		$bindings = array($this->coreConstants["country_id"]);
		
		// define a dictionary to hold the query information
		$queryInfo = array(
			"select_orgs" => $sql,
			"bindings" => $bindings);
		
		// return query information
		return $queryInfo;
	}
	
	/**
	 * Build the portion of the query that is used to filter organizations
	 * based on the letter that the name starts with.
	 *
	 * @return
	 *  The SQL portion of the query that is used to filter organizations
	 *  based on the letter that the name starts with.
	 */
	private function buildStartsWith()
	{
		// if there are no entries in the letters array, then return an
		// empty string
		if (empty($this->letters))
		{
			return "";
		}
		
		// create the portion of the query that will handle the 'starts
		// with' functionality
		$sqlLetter = "(SUBSTRING(" . $this->coreSchemas["Organizations"]["Name"];
		$sqlLetter .= ", 1, 1) IN (";
		
		// loop over the letters
		for ($enumLetter = 0; $enumLetter < count($this->letters); $enumLetter++)
		{
			// add each letter to the query
			$sqlLetter .= $this->letters[$enumLetter] . ", ";
		}
		
		// remove the trailing comma and finish off the query
		$sqlLetter = substr($sqlLetter, 0, -2);
		$sqlLetter .= "))";
		
		// return the 'starts with' portion of the query
		return $sqlLetter;
	}

	/**
	 * Build the portion of the query that is used to filter organizations
	 * based on if the name contains a certain string.
	 *
	 * @return
	 *  The SQL portion of the query that is used to filter organizations
	 *  based on if the name contains a certain string.
	 */
	private function buildContains()
	{
		// if there are no entries in the contains array, then return an
		// empty string
		if (empty($this->contains))
		{
			return "";
		}
		
		// initialize the contains portion of the query
		$sqlContains = "(";
		
		// loop over the contains array
		for ($enumContains = 0; $enumContains < count($this->contains); $enumContains++)
		{
			// add the current contains entry to the query
			$sqlContains .= "(" . $this->coreSchemas["Organizations"]["Name"] . " ";
			$sqlContains .= "LIKE " . $this->contains[$enumContains] . ") OR ";
		}
		
		// remove the trailing 'OR' from the query
		$sqlContains = substr($sqlContains, 0, -4);
		$sqlContains .= ")";

		// return the 'contains' query
		return $sqlContains;
	}
	
	/**
	 * Build the portion of the query that is used to filter organizations
	 * based on the state that it exists in.
	 *
	 * @return
	 *  The SQL portion of the query that is used to filter organizations
	 *  based on the state that it exists in.
	 */
	private function buildStates()
	{
		// if there are no entries in the states array, then return an
		// empty string
		if (empty($this->states))
		{
			return "";
		}
		
		// create the portion of the query that will handle if an organization
		// belongs to a state or not
		$sqlStates = "(" . $this->coreSchemas["Organizations"]["State"] . " IN (";
		
		// loop over the states array
		for ($enumStates = 0; $enumStates < count($this->states); $enumStates++)
		{
			// add the current state entry to the query
			$sqlStates .= $this->states[$enumStates] . ", ";
		}
		
		// remove the trailing coma
		$sqlStates = substr($sqlStates, 0, -2);
		$sqlStates .= "))";
		
		// return the 'states' query
		return $sqlStates;
	}

	/**
	 * Determines if the filter query is buildable or not.
	 *
	 * @return
	 *  TRUE if the filter query is buildable, FALSE if not.
	 */
	private function isQueryBuildable()
	{
		// return whether or not the query is buildable if there is at least
		// some restriction on the filter
		return ((count($this->letters) > 0) || (count($this->contains) > 0) || (count($this->states) > 0));
	}
}

?>