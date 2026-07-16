<?php

class PersonFeed extends RSSFeed
{
	/**
	 * ID of the person the RSS feed is for.
	 *
	 * @var $personID
	 */
	var $personID = null;
	/**
	 * Person that the RSS feed is for.
	 *
	 * @var Person $person
	 */
	var $person = null;
	 
	public function __construct($personID)
	{
		// call the parent constructor and store the person ID
		parent::__construct();
		$this->personID = $personID;
	}
	
	public function getFeed()
	{
		// if the person is invalid, then return nothing
		if (!$this->isValid())
		{
			return null;
		}
		
		// grab the feed data from the person RSS table
		$this->data = $this->getFeedData();
				
		// if the person is valid, but there is no record of the
		// RSS feed data, then insert the initial values
		if (empty($this->data))
		{
			// construct the query to insert the initial entry for
			// the person RSS feed
			$sql = "INSERT INTO " . $this->modTables["PersonRSS"] . "(";
			$sql .= $this->modSchemas["PersonRSS"]["PersonID"] . ") ";
			$sql .= "VALUES(?);";
			$bindings = array($this->personID);

			// execute the query and re-grab the feed data
			$this->ci->db->query($sql, $bindings);
			$this->data = $this->getFeedData();
		}
		
		// check to see if the person feed should be regenerated
		if ($this->isRegen())
		{
			// generate and store the feed
			$feed = $this->generateFeed();
			$this->saveFeed($feed);

			// return the newly generated feed
			return $feed;
		}
		
		// the person feed is unchanged (since it did not have to be
		// regenerated), so return the feed cached in the database
		return json_decode($this->data[$this->modSchemas["PersonRSS"]["FeedData"]]);
	}
	
	public function isValid()
	{
		// if the person has already been constructed, then
		// it is valid and there is no need to rebuild it
		if (!is_null($this->person))
		{
			return true;
		}
		
		// build a person from the ID
		$pb = new PersonBuilder($this->personID);
		$this->person = $pb->build();
		
		// the RSS feed is valid as long as the person is non-null
		return ($this->person != null);
	}
	
	protected function getFeedData()
	{
		// construct the query to get the person RSS data from
		// the people RSS table
		$sql = "SELECT *, ";
		$sql .= "UNIX_TIMESTAMP(" . $this->modSchemas["PersonRSS"]["GenerateDate"] . ") AS unix_stamp ";
		$sql .= "FROM " . $this->modTables["PersonRSS"] . " ";
		$sql .= "WHERE " . $this->modSchemas["PersonRSS"]["PersonID"] . "=?;";
		$bindings = array($this->personID);

		// execute the query
		$query = $this->ci->db->query($sql, $bindings);

		// return the data from the person RSS table
		return $query->row_array();
	}
	
	protected function generateFeed()
	{
		// construct and execute the query to create the publish timestamp
		// in RFC-822 format
		$sql = "SELECT DATE_FORMAT('" . $this->data[$this->modSchemas["PersonRSS"]["CreationDate"]] . "', '%a, %d %b %Y %T') AS rfcdate;";
		$query = $this->ci->db->query($sql);
		$row = $query->row_array();
		$publishStamp = $row["rfcdate"] . " " . date("T");
     
		// construct and execute the query to create the last build timestamp
		// in RFC-822 format
		$sql = "SELECT DATE_FORMAT(CURRENT_TIMESTAMP, '%a, %d %b %Y %T') AS rfcdate;";
		$query = $this->ci->db->query($sql);
		$row = $query->row_array();
		$buildStamp = $row["rfcdate"] . " " . date("T");

		// get the name of the person and the name of the organization that
		// the person belongs to
		$personName = $this->person->getName();
		$orgName = ucwords(strtolower($this->person->getOrganization()->getName()));
		
		// build the URL for the person
		$ub = new PersonReviewURL($this->personID);
		$personURL = $ub->build($this->personID, $this->person->getName());
		
		// set variables relative to the header of the RSS feed
		$feed = array();
		$feed["generator"] = $this->ci->config->item("core.sitename") . " RSS 1.0";
		$feed["title"] = $personName . " from " . $orgName . "'s RSS Feed | " . $this->ci->config->item("core.sitename");
		$feed["link"] = $this->ci->config->item("core.base_url") . $personURL;
		$feed["description"] = "RSS feed for " . $personName . " from " . $orgName . " on " . $this->ci->config->item("core.sitename") . ".";
		$feed["language"] = "en-us";
		$feed["publish_date"] = $publishStamp;
		$feed["last_build_date"] = $buildStamp;
		$feed["items"] = array();
		
		// construct the query to get the ratings for this person
		$sql = "SELECT " . $this->coreSchemas["Ratings"]["ID"] . ", ";
		$sql .= $this->coreSchemas["Ratings"]["Date"] . " ";
		$sql .= "FROM " . $this->coreTables["Ratings"] . " ";
		$sql .= "WHERE " . $this->coreSchemas["Ratings"]["PersonID"] . "=? ";
		$sql .= "AND " . $this->coreSchemas["Ratings"]["Status"] . " >= " . $this->coreConstants["min_rating_status"] . " ";
		$sql .= "ORDER BY " . $this->coreSchemas["Ratings"]["ID"] . " DESC;";
		$bindings = array($this->personID);
		
		// execute the query
		$query = $this->ci->db->query($sql, $bindings);
		
		// loop over the ratings
		foreach ($query->result_array() as $row)
		{
			// initialize the item for the person
			$item = array();
			
			// grab the ID and the date of the rating
			$ratingID = $row[$this->coreSchemas["Ratings"]["ID"]];
			$ratingDate = $row[$this->coreSchemas["Ratings"]["Date"]];
			
			// build the URL for the rating
			$ub = new RatingURL($ratingID);
			$ratingURL = $ub->build($ratingID, $personName);
			
			// set variables relative to the item of the RSS feed
			$item["title"] = $orgName . "'s " . $personName . " was rated on " . $ratingDate . " | " . $this->ci->config->item("core.sitename");
			$item["link"] = $this->ci->config->item("core.base_url") . $ratingURL;
			$item["desc"] = $personName . " from " . $orgName . " received a rating on " . $ratingDate;
			$item["guid"] = $this->ci->config->item("core.base_url") . $ratingURL;
       
			// save the RSS feed item
			$feed["items"][] = $item;
		}

		// return the RSS feed dictionary
		return $feed;
	}
	
	protected function saveFeed($feed)
	{
		// construct the query to save the new RSS feed in the table
		$sql = "UPDATE " . $this->modTables["PersonRSS"] . " ";
		$sql .= "SET " . $this->modSchemas["PersonRSS"]["GenerateDate"] . "=CURRENT_TIMESTAMP, ";
		$sql .= $this->modSchemas["PersonRSS"]["FeedData"] . "=? ";
		$sql .= "WHERE " . $this->modSchemas["PersonRSS"]["ID"] . "=?;";
		
		// define the bindings array
		$bindings = array(
			json_encode($feed),
			$this->data[$this->modSchemas["PersonRSS"]["ID"]]);
			
		// execute the query
		$this->ci->db->query($sql, $bindings);
	}
	
	protected function isRegen()
	{
		// get the number of seconds that needs to pass if the person
		// RSS feed is to be regenerated
		$maxSeconds = $this->modConstants["person_regen_seconds"];

		// the feed can be regenerated only if the timestamp indicates that
		// the last cached version of the RSS feed is older than the maximum
		// number of seconds
		return !(($this->data["unix_stamp"] + $maxSeconds) > time());
	}
}

?>