<?php

class PersonFeed extends ActivityFeed
{
	/**
	 * ID of the person the feed is for.
	 *
	 * @var $personID
	 */
	var $personID = null;
	/**
	 * Name of the person that was rated.
	 *
	 * @var $personName
	 */
	var $personName = null;
	
	public function __construct($personID, $personName)
	{
		// call the parent constructor and then store the person ID
		// and person name
		parent::__construct();
		$this->personID = $personID;
		$this->personName = $personName;
	}
	
	public function getFeed($limit = 5)
	{
		// initialize the list of feed items
		$feedItems = array();
		
		// construct the query to get the latest rating ID and date
		// for the person
		$sql = "SELECT " . $this->coreSchemas["Ratings"]["ID"] . ", ";
		$sql .= $this->coreSchemas["Ratings"]["Date"] . " ";
		$sql .= "FROM " . $this->coreTables["Ratings"] . " ";
		$sql .= "WHERE " . $this->coreSchemas["Ratings"]["PersonID"] . "=? ";
		$sql .= "AND " . $this->coreSchemas["Ratings"]["Status"] . " >= " . $this->coreConstants["min_rating_status"] . " ";
		$sql .= "ORDER BY " . $this->coreSchemas["Ratings"]["ID"] . " DESC ";
		$sql .= "LIMIT ?;";
		$bindings = array($this->personID, $limit);

		// execute the query
		$query = $this->ci->db->query($sql, $bindings);
		
		// loop over the latest ratings
		foreach ($query->result_array() as $row)
		{
			// grab the rating ID and rating date
			$ratingID = $row[$this->coreSchemas["Ratings"]["ID"]];
			$ratingDate = $row[$this->coreSchemas["Ratings"]["Date"]];

			// construct the URL for the rating
			$ub = new RatingURL($ratingID, $this->personName);
			$url = $ub->build($ratingID);
			
			// add the item to the list of feed items
			$feedItems[] = array(
				"type" => "rated",
				"url" => $url,
				"date" => $ratingDate);
		}
		
		// return the list of feed items
		return $feedItems;
	}
}

?>