<?php

/**
 * @ingroup core
 *
 * @brief
 * Class used to build URL that asks users if they want to rate
 * or review a person.
 *
 * This class is used to build the URL that asks users if they
 * want to rate or a review a person.
 *
 * @see url.php
 * @author Adrian Rosebrock
 */

class PersonRequestURL extends URL
{
	/**
	 * ID of the person the request URL is for.
	 *
	 * @var $personID
	 */
	var $personID = null;
	
	/**
	 * Construct the PersonRequestURL.
	 *
	 * @param $personID
	 *  The ID of the person the request URL is for.
	 */
	public function __construct($personID)
	{
		// call the parent constructor and store the person ID 
		parent::__construct();
		$this->personID = $personID;
	}
	
	/**
	 * Build the URL that asks users if they want to rate or
	 * review a person.
	 *
	 * @param $personID
	 *  The ID of the person the request URL is for.
	 * @param $orgName
	 *  The name of the organization the person belings to.
	 * @param $personName
	 *  The name of the person that the request URL is for.
	 *
	 * @return
	 *  The fully constructed request URL.
	 */
	public function build($personID, $orgName = null, $personName = null)
	{		
		// if the request URL has already been built, return it
		if (!empty($this->url))
		{
			return $this->url;
		}
		
		// if the organization name and person name were already supplied
		// then we can skip the database step
		if (empty($orgName) || empty($personName))
		{
			// construct the query to get the person name and organization
			// name
			$sql = "SELECT o." . $this->coreSchemas["Organizations"]["Name"] . ", ";
			$sql .= "p." . $this->coreSchemas["People"]["FirstName"] . ", ";
			$sql .= "p." . $this->coreSchemas["People"]["LastName"] . " ";
			$sql .= "FROM " . $this->coreTables["People"] . " p ";
			$sql .= "INNER JOIN " . $this->coreTables["Organizations"] . " o ";
			$sql .= "ON p." . $this->coreSchemas["People"]["OrgID"] . "=o." . $this->coreSchemas["Organizations"]["ID"] . " ";
			$sql .= "WHERE p." . $this->coreSchemas["People"]["ID"] . "=? AND ";
			$sql .= "p." . $this->coreSchemas["People"]["Status"] . " >= " . $this->coreConstants["min_person_status"] . ";";
			$bindings = array($personID);
		
			// execute the query
			$query = $this->ci->db->query($sql, $bindings);
		
			// if the number of rows is zero, then the person either does
			// not exist in our tables or has a status lower than the minimum
			// status needed
			if ($query->num_rows() == 0)
			{
				// return false since the URL cannot be created
				return false;
			}
		
			// grab the row and the needed variables and construct the
			// person name
			$row = $query->row_array();
			$orgName = $row[$this->coreSchemas["Organizations"]["Name"]];
			$firstName = $row[$this->coreSchemas["People"]["FirstName"]];
			$lastName = $row[$this->coreSchemas["People"]["LastName"]];
			$personName = trim($firstName . " " . $lastName);
		}

		// the URL is valid
		$this->setValid(true);
		
		// sanitize the names
		$orgName = preg_replace("/[^a-z0-9]/", "-", strtolower($orgName));
		$personName = preg_replace("/[^a-z0-9]/", "-", strtolower($personName));
		
		// grab the URL trailer and finish off the construction of the
		// URL
		$trailer = $this->coreTrailers["PeopleRequest"];
		$url = "/" . $orgName . "/" . $personName . "/" . $personID . "-" . $trailer;
		$this->url = $this->clean($url);
		
		// return the fully constructed URL
		return $this->url;
	}

	/**
	 * Get the next tier (the organization) that can be redirected
	 * to if the rating request URL is invalid.
	 *
	 * @return
	 *  The redirect URL.
	 */	
	public function nextTier()
	{
		// construct the query to get the organization ID from the
		// person
		$sql = "SELECT " . $this->coreSchemas["People"]["OrgID"] . " ";
		$sql .= "FROM " . $this->coreTables["People"] . " ";
		$sql .= "WHERE " . $this->coreSchemas["People"]["ID"] . "=?;";
		$bindings = array($this->personID);
		
		// execute the query
		$query = $this->ci->db->query($sql, $bindings);
		
		// if the row could not be found, then the person ID does
		// not exist in the table, so the next tier should be the
		// homepage
		if ($query->num_rows() == 0)
		{
			return "/";
		}
		
		// grab the row and build the organization URL from the row
		$row = $query->row_array();
		$ub = new OrganizationURL();
		$orgURL = $ub->build($row[$this->coreSchemas["People"]["OrgID"]]);
		
		// return the organization URL
		return $orgURL;
	}

	/**
	 * Determine if the request URL is correct or not.
	 *
	 * @param $uri
	 *  The URI the user is currently viewing.
	 * @param $personID
	 *  The ID of the person the rating is for.
	 *
	 * @return
	 *  TRUE if the URL is correct, FALSE if not.
	 */
	public function isCorrect($uri, $personID)
	{
		// build the correct URL
		$correctURL = $this->build($personID);

		// the URL is correct if the URL just built matches the
		// supplied URL
		return ($correctURL == $uri);
	}
}

?>