<?php

/**
 * @ingroup core
 *
 * @brief
 * Class used to filter persons in an organization.
 *
 * This class extends the abstract Filter class and is used to filter
 * persons in an organization, such as the direction (ascending or
 * descending) in which the persons should be ordered and on which
 * column the ordering can be done. Furthermore, persons can also be
 * filtered by the department they belong to and if their name starts
 * with a certain letter.
 *
 * @see filter.php
 * @author Adrian Rosebrock
 */

class PersonFilter extends Filter
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
	 * @see site.php
	 */
	var $coreClasses = array();
	/**
	 * ID of the organization that the persons will be filtered in.
	 *
	 * @var $orgID
	 */
	var $orgID = null;
	/**
	 * Column on which the persons will be filtered on.
	 *
	 * @var $orderBy
	 */
	var $orderBy = null;
	/**
	 * Direction (ascending or descending) in which the persons
	 * will be ordered by.
	 *
	 * @var $orderDir
	 */
	var $orderDir = null;
	/**
	 * Array of letters that a person's name could start with.
	 *
	 * @var $letters
	 */
	var $letters = array();
	/**
	 * Array of departments that a person could belong to.
	 *
	 * @var $depts
	 */
	var $depts = array();
	/**
	 * The page number (of the organization) that is currently
	 * being viewed.
	 *
	 * @var $pageNum
	 */
	var $pageNum = null;
	
	/**
	 * Construct the PersonFilter.
	 *
	 * @param $orgID
	 *  ID of the organization that the persons will be filtered in. 
	 * @param $pageNum
	 *  The page number (of the organization) that is currently being
	 *  viewed.
	 * @param $orderBy
	 *  Column on which the persons will be filtered on.
	 * @param $orderDir
	 *  Direction (ascending or descending) in which the persons will
	 *  be ordered by.
	 */
	public function __construct($orgID, $pageNum, $orderBy = null, $orderDir = null)
	{
		// call the parent constructor and grab the core tables, schemas,
		// constants, and classes
		parent::__construct();
		$this->coreTables = $this->ci->config->item("core.tables");
		$this->coreSchemas = $this->ci->config->item("core.tables.schemas");
		$this->coreConstants = $this->ci->config->item("core.constants");
		$this->coreClasses = $this->ci->config->item("core.classes");
		
		// store the organization number, page number, and initialize the
		// order by column and the order direction
		$this->orgID = $orgID;
		$this->pageNum = $pageNum;
		$this->setOrderBy($this->coreSchemas["People"]["LastName"]);
		$this->setOrderDirection("ASC");

		// if the the order by is not empty, store it
		if (!empty($orderBy))
		{
			$this->setOrderBy($orderBy);
		}
		
		// if the order by direction is not empty, store it
		if (!empty($orderDir))
		{
			$this->setOrderDirection($orderDir);
		}
	}
	
	/**
	 * Set the column used in which persons will be ordered by.
	 *
	 * @param $orderBy
	 *  Column in which persons will be ordered by.
	 */
	public function setOrderBy($orderBy)
	{
		// set the order by column
		$this->orderBy = $orderBy;
	}
	
	/**
	 * Set the direction in which persons will be ordered by.
	 *
	 * @param $orderDir
	 *  Direction in which persons will be ordered by.
	 */
	public function setOrderDirection($orderDir)
	{
		// set the direction that ordering is done
		$this->orderDir = $orderDir;
	}

	/**
	 * Add the supplied letter to the list of letters that a
	 * person's name could start with.
	 *
	 * @param $letter
	 *  Letter that a person's name could start with.
	 */
	public function addStartsWith($letter)
	{
		// add the letter to the array of letters that a person could
		// start with if the letter is not empty
		if (!empty($letter))
		{
			$this->letters[] = $this->ci->db->escape(strtoupper($letter));
		}
	}
	
	/**
	 * Add the supplied department to the list of departments that
	 * a person could belong to.
	 *
	 * @param $dept
	 *  Department that a person could belong to.
	 */
	public function addDepartment($dept)
	{
		// add the department to the array of departments that a person
		// could start with if the department is not empty
		if (!empty($dept))
		{
			$this->depts[] = $this->ci->db->escape($dept);
		}
	}
	
	/**
	 * Apply the letter and department filters with the order by
	 * information, then return a list of persons that match the
	 * filter rules.
	 *
	 * @return array()
	 *  A list of persons that match the letter and department
	 *  filters, ordered by the ordering rules.
	 */
	public function apply()
	{
		// build the queries based on the filters and execute the query
		// to get the total amount of matches
		$queries = $this->buildQueries();
		$query = $this->ci->db->query($queries["select_total"], $queries["bindings"]);
		$row = $query->row_array();
		$totalPersons = $row["total"];
		
		// execute the query to select the persons based on the filters
		// and the pagination, then initialize the persons list
		$query = $this->ci->db->query($queries["select_persons"], $queries["bindings"]);
		$persons = array();
		
		// build the organization
		$ob = new OrganizationBuilder($this->orgID);
		$org = $ob->build();
		
		// loop over the results
		foreach ($query->result_array() as $row)
		{			
			// create the person and add the person to the list
			$Class = $this->coreClasses["People"];
			$persons[] = new $Class($org, $row);
		}
		
		// create a dictionary to store the total amount of persons
		// matching the filter as well as the persons that matched
		// the filter and the pagination
		$personsInfo = array(
						"total_matched" => $totalPersons,
						"persons_list" => $persons);
		
		// return the persons dictionary
		return $personsInfo;
	}

	/**
	 * Builds the query used to filter persons.
	 *
	 * @return array()
	 *  The query used to filter the persons, along with the
	 *  bindings needed to execute the query.
	 */
	private function buildQueries()
	{
		// build the 'starts with' and 'department' portion of the
		// filter query, and initialize the number of portions that
		// have been added
		$sqlLetter = $this->buildStartsWith();
		$sqlDept = $this->buildDepartments();
		$portions = 0;
		
		// start building the query to select the persons
		$sql = "SELECT * ";
		$sql .= "FROM " . $this->coreTables["People"] . " WHERE ";
		$sql .= $this->coreSchemas["People"]["OrgID"] . "=? AND ";
		
		// if the 'starts with' portion is not empty, add it to the
		// query and increment the number of portions
		if (!empty($sqlLetter))
		{
			$sql .= $sqlLetter;
			$portions++;
		}
		
		// if the 'department' portion of the query is not empty, add
		// it to the query
		if (!empty($sqlDept))
		{
			// if the number of portions is greater than zero, then
			// we need to place an 'AND' in front of the portion
			if ($portions > 0)
			{
				$sqlDept = " AND " . $sqlDept;
			}
			
			// add the 'contains' portion to the query
			$sql .= $sqlDept;
		}

		// ensure only the valid persons are returned
		$sqlValid = " AND " . $this->coreSchemas["People"]["Status"] . " >= " . $this->coreConstants["min_person_status"];
		
		// if both the 'starts with' and 'department' portion are
		// empty, then remove the 'AND' from the valid persons
		if (empty($sqlLetter) && empty($sqlDept))
		{
			$sqlValid = substr($sqlValid, 5);
		}
		
		// at this point, create the query that will be used to
		// to the total amount of persons matching the query so
		// that pagination can be done
		$sqlTotal = str_replace("SELECT *", "SELECT COUNT(*) AS total", $sql);
		$sqlTotal .= $sqlValid . ";";
		
		// order the persons by name, provide the offset based on
		// the page number and limit the results
		$perPage = $this->coreConstants["persons_per_page"];
		$sql .= $sqlValid . " ";
		$sql .= "ORDER BY " . $this->orderBy . " " . $this->orderDir . " ";
		$sql .= "LIMIT " . (($this->pageNum - 1) * $perPage) . ", " . $perPage . ";";
		
		// create the bindings array
		$bindings = array($this->orgID);
		
		// create a dictionary to hold the queries and bindings
		$queries = array(
					"select_persons" => $sql,
					"select_total" => $sqlTotal,
					"bindings" => $bindings);
		
		// return the finished queries and bindings
		return $queries;
	}

	/**
	 * Build the portion of the query that is used to filter persons
	 * based on the letter that their name starts with.
	 *
	 * @return
	 *  The SQL portion of the query that is used to filter persons
	 *  based on the letter that their name starts with.
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
		$sqlLetter = "(SUBSTRING(" . $this->coreSchemas["People"]["LastName"];
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
	 * Build the portion of the query that is used to filter persons
	 * based on the department that they belong to.
	 *
	 * @return
	 *  The SQL portion of the query that is used to filter persons
	 *  based on the department that they belong to.
	 */
	private function buildDepartments()
	{
		// if there are no entries in the departments array, then return
		// an empty string
		if (empty($this->depts))
		{
			return "";
		}
		
		// create the portion of the query that will handle if a person
		// is part of a certain department or not
		$sqlDepts = "(" . $this->coreSchemas["People"]["Department"] . " IN (";
		
		// loop over the departments array
		for ($enumDepts = 0; $enumDepts < count($this->depts); $enumDepts++)
		{
			// add the current department entry to the query
			$sqlDepts .= $this->depts[$enumDepts] . ", ";
		}
		
		// remove the trailing coma
		$sqlDepts = substr($sqlDepts, 0, -2);
		$sqlDepts .= "))";
		
		// return the 'department' query
		return $sqlDepts;
	}
	
	/**
	 * Determines if a 'clean slate' filter should be used, since
	 * this filter is tracked via session across the site. A clean
	 * slate should be used if the user has navigated off of an
	 * organization page to a different organization page.
	 *
	 * @param $refURL
	 *  The URL of the last visted page.
	 * @param $curURL
	 *  The current URL the user is viewing.
	 *
	 * @return
	 *  TRUE if a clean slate should be used, FALSE if not.
	 */
	public static function useCleanSlate($refURL, $curURL)
	{
		// remove the 'http://' from the referrer URL, then extract
		// the request URI
		$refURL = str_replace("http://", "", $refURL);
		$refPos = strpos($refURL, "/");
		$refURL = substr($refURL, $refPos);
		
		// break the referrer URL and current URL into pieces based on
		// slashes
		$refURL = explode("/", $refURL);
		$curURL = explode("/", $curURL);

		// if the size of the referring URL is less than two, then a
		// clean slate should be used
		if (count($refURL) < 2)
		{
			return true;
		}
		
		// if the referrer URL controller and the current URL controller
		// match, then a clean slate should not be used
		if ($refURL[1] == $curURL[1])
		{
			return false;
		}
		
		// otherwise, a clean slate should only be used if the referrer
		// controller was not 'person_filter'
		return ($refURL[1] != "person_filter");
	}
}

?>