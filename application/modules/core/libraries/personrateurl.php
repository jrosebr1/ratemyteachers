<?php

/**
 * @ingroup core
 *
 * @brief
 * Class used to build URL to collect ratings.
 *
 * This class is used to build the URL to collect ratings.
 *
 * @see url.php
 * @author Adrian Rosebrock
 */

class PersonRateURL extends URL
{
	/**
	 * ID of the person the rating is for.
	 *
	 * @var $personID
	 */
	var $personID = null;
	/**
	 * URL trailer to be used during URL construction.
	 *
	 * @var $trailer
	 */
	var $trailer = null;
	
	/**
	 * Construct the PersonRateURL.
	 *
	 * @param $personID
	 *  The ID of the person the rating is for.
	 */
	public function __construct($personID)
	{
		// call the parent constructor, store the person ID, and get
		// the URL trailer
		parent::__construct();
		$this->personID = $personID;
		$this->trailer = $this->coreTrailers["PeopleRate"];
	}

	/**
	 * Build the URL to collect ratings.
	 *
	 * @param $personID
	 *  The ID of the person the rating is for.
	 * @param $personName
	 *  The name of the person the rating is for.
	 *
	 * @return
	 *  The rully constructed URL used to collect ratings.
	 */
	public function build($personID, $personName = null)
	{		
		// if the request URL has already been built, return it
		if (!empty($this->url))
		{
			return $this->url;
		}
		
		// if the person name was already supplied, then we can skip
		// the database step
		if (empty($personName))
		{
			// construct the query to get the name of the person with
			// the supplied ID
			$sql = "SELECT " . $this->coreSchemas["People"]["FirstName"] . ", ";
			$sql .= $this->coreSchemas["People"]["LastName"] . " ";
			$sql .= "FROM " . $this->coreTables["People"] . " ";
			$sql .= "WHERE " . $this->coreSchemas["People"]["ID"] . "=? AND ";
			$sql .= $this->coreSchemas["People"]["Status"] . " >= " . $this->coreConstants["min_person_status"] . ";";
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
			$firstName = $row[$this->coreSchemas["People"]["FirstName"]];
			$lastName = $row[$this->coreSchemas["People"]["LastName"]];
			$personName = trim($firstName . " " . $lastName);
		}
		
		// the URL is valid
		$this->setValid(true);
		
		// sanitize the name and finish off the construction of the
		// URL
		$personName = preg_replace("/[^a-z0-9]/", "-", strtolower($personName));
		$url = "/" . $personName . "/" . $personID . "-" . $this->trailer;
		$this->url = $this->clean($url);
		
		// return the fully constructed URL
		return $this->url;
	}

	/**
	 * Get the next tier (the organization) that can be redirected
	 * to if the rating collection URL is invalid.
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
	 * Determine if the rating collection URL is correct or not.
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
		// supplied URI
		return ($correctURL == $uri);
	}
}

?>