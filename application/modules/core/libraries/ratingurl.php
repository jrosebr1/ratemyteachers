<?php

/**
 * @ingroup core
 *
 * @brief
 * Class used to build rating URLs.
 *
 * This class is used to build rating URLs.
 *
 * @see url.php
 * @see rating.php
 * @author Adrian Rosebrock
 */

class RatingURL extends URL
{
	/**
	 * ID of the rating the URL is for.
	 *
	 * @var $ratingID
	 */
	var $ratingID = null;
	/**
	 * URL trailer to be used during URL construction.
	 *
	 * @var $trailer
	 */
	var $trailer = null;
	
	/**
	 * Construct the RatingURL.
	 *
	 * @param $ratingID
	 *  The ID of the rating the URL is for.
	 */
	public function __construct($ratingID)
	{
		// call the parent constructor, store the rating ID  and
		// the trailer used for the rating URL
		parent::__construct();
		$this->ratingID = $ratingID;
		$this->trailer = $this->coreTrailers["Ratings"];
	}
	
	/**
	 * Build the rating URL.
	 *
	 * @param $ratingID
	 *  The ID of the rating the rating URL is for.
	 * @param $personName
	 *  The name of the person the rating belongs to.
	 *
	 * @return
	 *  The fully constructed rating URL.
	 */
	public function build($ratingID, $personName = null)
	{
		// if the rating URL has already been built, return it
		if (!empty($this->url))
		{
			return $this->url;
		}

		// if the person name was already supplied, then we can skip
		// the database step
		if (empty($personName))
		{
			// construct the query to get the name of the person with
			// the supplied rating ID
			$sql = "SELECT p." . $this->coreSchemas["People"]["FirstName"] . ", ";
			$sql .= "p." . $this->coreSchemas["People"]["LastName"] . " ";
			$sql .= "FROM " . $this->coreTables["People"]  . " p ";
			$sql .= "INNER JOIN " . $this->coreTables["Ratings"] . " r ON ";
			$sql .= "p." . $this->coreSchemas["People"]["ID"] . "=r." . $this->coreSchemas["Ratings"]["PersonID"] . " ";
			$sql .= "WHERE r." . $this->coreSchemas["Ratings"]["ID"] . "=? AND ";
			$sql .= "r." . $this->coreSchemas["Ratings"]["Status"] . ">= " . $this->coreConstants["min_rating_status"] . " AND ";
			$sql .= "p." . $this->coreSchemas["People"]["Status"] . " >= " . $this->coreConstants["min_person_status"] . ";";
			$bindings = array($ratingID);

			// execute the query
			$query = $this->ci->db->query($sql, $bindings);
		
			// if the number of rows is zero, then the rating either does
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
		
		// sanitize the name, grab the URL trailer, and finish off
		// the construction of the URL
		$personName = preg_replace("/[^a-z0-9]/", "-", strtolower($personName));
		$url = "/" . $personName . "/" . $ratingID . "-" . $this->trailer;
		$this->url = $this->clean($url);
		
		// return the fully constructed URL
		return $this->url;
	}

	/**
	 * Get the next tier (rating review URL) that can be redirected
	 * to if the rating URL is invalid.
	 *
	 * @return
	 *  The redirect URL.
	 */	
	public function nextTier()
	{
		// construct the query to get the person ID from the rating
		$sql = "SELECT " . $this->coreSchemas["Ratings"]["PersonID"] . " ";
		$sql .= "FROM " . $this->coreTables["Ratings"] . " ";
		$sql .= "WHERE " . $this->coreSchemas["Ratings"]["ID"] . "=?;";
		$bindings = array($this->ratingID);
		
		// execute the query
		$query = $this->ci->db->query($sql, $bindings);

		// if the row could not be found, then the rating ID does
		// not exist in the table, so the next tier should be the
		// homepage
		if ($query->num_rows() == 0)
		{
			return "/";
		}

		// grab the row and build the person review URL from the
		// the person ID
		$row = $query->row_array();
		$personID = $row[$this->coreSchemas["Ratings"]["PersonID"]];
		$ub = new PersonReviewURL($personID);
		$reviewURL = $ub->build($personID);
		
		// return the person review URL
		return $reviewURL;
	}

	/**
	 * Determine if the rating URL is correct or not.
	 *
	 * @param $uri
	 *  The URI the user is currently viewing.
	 * @param $ratingID
	 *  The ID of the rating.
	 *
	 * @return
	 *  TRUE if the URL is correct, FALSE if not.
	 */
	public function isCorrect($uri, $ratingID)
	{
		// build the correct URL
		$correctURL = $this->build($ratingID);

		// the URL is correct if the URL just built matches the
		// supplied URI
		return ($correctURL == $uri);
	}
}

?>