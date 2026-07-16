<?php

class OrganizationFeed extends ActivityFeed
{
	/**
	 * ID of the organization that the feed is for.
	 *
	 * @var $orgID
	 */
	var $orgID = null;
	
	public function __construct($orgID)
	{
		// call the parent constructor and store the organization ID
		parent::__construct();
		$this->orgID = $orgID;
	}
	
	public function getFeed($limit = 5)
	{
		// initialize the list of feed items and the preliminary
		// dictionary used to hold the dates of the events
		$feedItems = array();
		$events = array();
		
		// get the latest rated persons and the latest added persons
		$latestRated = $this->getLatestRated($limit);
		$latestAdded = $this->getLatestAdded($limit);
		
		// loop over the latest rated persons and add each of the
		// persons to the event dictionary
		foreach ($latestRated as $row)
		{
			$key = $row[$this->coreSchemas["Ratings"]["Date"]] . "-rated";
			$row["type"] = "rated";
			$events[$key] = $row;
		}
		
		// loop over the latest added persons and add each of the
		// persons to the event dictionary
		foreach ($latestAdded as $row)
		{
			$key = $row[$this->coreSchemas["People"]["DateAdded"]] . "-added";
			$row["type"] = "added";
			$events[$key] = $row;
		}
		
		// sort the events
		krsort($events);

		// loop over each of the sorted events
		foreach ($events as $event)
		{
			// if the number of entries in the feed items list
			// equals the limit, then break
			if (count($feedItems) == $limit)
			{
				break;
			}
			
			// grab the ID and the name of the person
			$personID = $event[$this->coreSchemas["People"]["ID"]];
			$personName = trim($event[$this->coreSchemas["People"]["FirstName"]] . " " . $event[$this->coreSchemas["People"]["LastName"]]);
			
			// check if the type is a 'rating'
			if ($event["type"] == "rated")
			{
				$date = $event[$this->coreSchemas["Ratings"]["Date"]];
			}
			
			// otherwise, the type is 'added'
			else
			{
				$date = $event[$this->coreSchemas["People"]["DateAdded"]];
			}
			
			// construct the URL for the person
			$ub = new PersonReviewURL($personID);
			$personURL = $ub->build($personID, $personName);
			
			// add the item to the list of feed items
			$feedItems[] = array(
				"type" => $event["type"],
				"url" => $personURL,
				"name" => $personName,
				"date" => $date);
		}
		
		// return the feed items list
		return $feedItems;
	}
	
	private function getLatestRated($limit)
	{
		// construct and execute the query to get the latest set
		// of ratings for the organization
		$sql = "SELECT p." . $this->coreSchemas["People"]["ID"] . ", ";
		$sql .= "p." . $this->coreSchemas["People"]["FirstName"] . ", ";
		$sql .= "p." . $this->coreSchemas["People"]["LastName"] . ", ";
		$sql .= "r." . $this->coreSchemas["Ratings"]["Date"] . " ";
		$sql .= "FROM " . $this->coreTables["People"] . " p ";
		$sql .= "INNER JOIN " . $this->coreTables["Ratings"] . " r ";
		$sql .= "ON p." . $this->coreSchemas["People"]["ID"] . "=r." . $this->coreSchemas["Ratings"]["PersonID"] . " ";
		$sql .= "WHERE p." . $this->coreSchemas["People"]["OrgID"] . "=? ";
		$sql .= "AND " . $this->coreSchemas["People"]["Status"] . " >= " . $this->coreConstants["min_person_status"] . " ";
		$sql .= "AND " . $this->coreSchemas["Ratings"]["Status"] . " >= " . $this->coreConstants["min_rating_status"] . " ";
		$sql .= "ORDER BY r." . $this->coreSchemas["Ratings"]["ID"] . " DESC ";
		$sql .= "LIMIT ?;";
		$bindings = array($this->orgID, $limit);
		
		// execute the query
		$query = $this->ci->db->query($sql, $bindings);

		// return the set of results
		return $query->result_array();
	}
	
	private function getLatestAdded($limit)
	{
		// construct and execute the query to get the latest
		// persons added to the organization
		$sql = "SELECT " . $this->coreSchemas["People"]["ID"] . ", ";
		$sql .= $this->coreSchemas["People"]["FirstName"] . ", ";
		$sql .= $this->coreSchemas["People"]["LastName"] . ", ";
		$sql .= $this->coreSchemas["People"]["DateAdded"] . " ";
		$sql .= "FROM " . $this->coreTables["People"] . " ";
		$sql .= "WHERE " . $this->coreSchemas["People"]["OrgID"] . "=? ";
		$sql .= "AND " . $this->coreSchemas["People"]["Status"] . " >= " . $this->coreConstants["min_person_status"] . " ";
		$sql .= "ORDER BY " . $this->coreSchemas["People"]["DateAdded"] . " DESC ";
		$sql .= "LIMIT ?;";
		$bindings = array($this->orgID, $limit);
		
		// execute the query
		$query = $this->ci->db->query($sql, $bindings);
		
		// return the set of results
		return $query->result_array();
	}
}

?>