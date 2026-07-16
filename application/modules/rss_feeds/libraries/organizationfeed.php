<?php

class OrganizationFeed extends RSSFeed
{
	/**
	 * ID of the organization the RSS feed is for.
	 *
	 * @var $orgID
	 */
	var $orgID = null;
	/**
	 * Organization that the RSS feed is for.
	 *
	 * @var Organization $org
	 */
	var $org = null;
	
	public function __construct($orgID)
	{
		// call the parent constructor and store the organization ID
		parent::__construct();
		$this->orgID = $orgID;
	}
	
	public function getFeed()
	{
		// if the organization is invalid, then return nothing
		if (!$this->isValid())
		{
			return null;
		}
		
		// grab the feed data from the organizations RSS table
		$this->data = $this->getFeedData();
				
		// if the organization is valid, but there is no record of
		// the RSS feed data, then insert the initial values
		if (empty($this->data))
		{
			// construct the query to insert the initial entry for
			// the organization RSS feed
			$sql = "INSERT INTO " . $this->modTables["OrganizationRSS"] . "(";
			$sql .= $this->modSchemas["OrganizationRSS"]["OrgID"] . ") ";
			$sql .= "VALUES(?);";
			$bindings = array($this->orgID);

			// execute the query and re-grab the feed data
			$this->ci->db->query($sql, $bindings);
			$this->data = $this->getFeedData();
		}
		
		// check to see if the organization feed should be regenerated
		if ($this->isRegen())
		{
			// generate and store the feed
			$feed = $this->generateFeed();
			$this->saveFeed($feed);

			// return the newly generated feed
			return $feed;
		}
		
		// the organization feed is unchanged (since it did not have to
		// be regenerated), so return the feed cached in the database
		return json_decode($this->data[$this->modSchemas["OrganizationRSS"]["FeedData"]]);
	}
	
	public function isValid()
	{
		// if the organization has already been constructed, then
		// it is valid and there is no need to rebuild it
		if (!is_null($this->org))
		{
			return true;
		}
		
		// build an organization from the ID
		$ob = new OrganizationBuilder($this->orgID);
		$this->org = $ob->build();
		
		// the RSS feed is valid as long as the organization is non-null
		return ($this->org != null);
	}
	
	protected function getFeedData()
	{
		// construct the query to get the organization RSS data from
		// the organization RSS table
		$sql = "SELECT *, ";
		$sql .= "UNIX_TIMESTAMP(" . $this->modSchemas["OrganizationRSS"]["GenerateDate"] . ") AS unix_stamp ";
		$sql .= "FROM " . $this->modTables["OrganizationRSS"] . " ";
		$sql .= "WHERE " . $this->modSchemas["OrganizationRSS"]["OrgID"] . "=?;";
		$bindings = array($this->orgID);

		// execute the query
		$query = $this->ci->db->query($sql, $bindings);

		// return the data from the organizations RSS table
		return $query->row_array();
	}
	
	protected function generateFeed()
	{
		// construct and execute the query to create the publish timestamp
		// in RFC-822 format
		$sql = "SELECT DATE_FORMAT('" . $this->data[$this->modSchemas["OrganizationRSS"]["CreationDate"]] . "', '%a, %d %b %Y %T') AS rfcdate;";
		$query = $this->ci->db->query($sql);
		$row = $query->row_array();
		$publishStamp = $row["rfcdate"] . " " . date("T");
     
		// construct and execute the query to create the last build timestamp
		// in RFC-822 format
		$sql = "SELECT DATE_FORMAT(CURRENT_TIMESTAMP, '%a, %d %b %Y %T') AS rfcdate;";
		$query = $this->ci->db->query($sql);
		$row = $query->row_array();
		$buildStamp = $row["rfcdate"] . " " . date("T");
		
		// get the name and the state the organization resides in
		$orgName = ucwords(strtolower($this->org->getName()));
		$orgState = $this->org->getState();
		
		// build the URL for the organization
		$ub = new OrganizationURL($this->orgID);
		$orgURL = $ub->build($this->orgID, $orgName);

		// set variables relative to the header of the RSS feed
		$feed = array();
		$feed["generator"] = $this->ci->config->item("core.sitename") . " RSS 1.0";
		$feed["title"] = $orgName . "'s RSS Feed | " . $this->ci->config->item("core.sitename");
		$feed["link"] = $this->ci->config->item("core.base_url") . $orgURL;
		$feed["description"] = "RSS feed for " . $orgName . " on " . $this->ci->config->item("core.sitename") . ".";
		$feed["language"] = "en-us";
		$feed["publish_date"] = $publishStamp;
		$feed["last_build_date"] = $buildStamp;
		$feed["items"] = array();
		
		// construct the query to fetch the list of persons in the
		// organization (NOTE: we could use the PersonFilter to fetch
		// the persons in the organization, but that would result in
		// a Person object being constructed for each person which would
		// turn into a performance issue)
		$sql = "SELECT " . $this->coreSchemas["People"]["ID"] . ", ";
		$sql .= $this->coreSchemas["People"]["FirstName"] . ", ";
		$sql .= $this->coreSchemas["People"]["LastName"] . " ";
		$sql .= "FROM " . $this->coreTables["People"] . " ";
		$sql .= "WHERE " . $this->coreSchemas["People"]["OrgID"] . "=? ";
		$sql .= "AND " . $this->coreSchemas["People"]["Status"] . " >= " . $this->coreConstants["min_person_status"] . " ";
		$sql .= "ORDER BY " . $this->coreSchemas["People"]["LastName"] . " ASC;";
		$bindings = array($this->orgID);

		// execute the query
		$query = $this->ci->db->query($sql, $bindings);
		
		// loop over the persons
		foreach ($query->result_array() as $row)
		{
			// initialize an item for the person
			$item = array();
			
			// grab the ID and the name of the person
			$personID = $row[$this->coreSchemas["People"]["ID"]];
			$firstName = $row[$this->coreSchemas["People"]["FirstName"]];
			$lastName = $row[$this->coreSchemas["People"]["LastName"]];
			$personName = trim($firstName . " " . $lastName);
			
			// build the URL for the person
			$ub = new PersonReviewURL($personID);
			$personURL = $ub->build($personID, $personName);
			
			// set variables relative to the item of the RSS feed
			$item["title"] = $personName . " - " . $orgName . " | " . $this->ci->config->item("core.sitename");
			$item["link"] = $this->ci->config->item("core.base_url") . $personURL;
			$item["desc"] = $personName . " is on " . $this->ci->config->item("core.sitename") . " | ". $orgName . ".";
			$item["guid"] = $this->ci->config->item("core.base_url") . $personURL;
       
			// save the RSS feed item
			$feed["items"][] = $item;
		}

		// return the RSS feed dictionary
		return $feed;
	}
	
	protected function saveFeed($feed)
	{
		// construct the query to save the new RSS feed in the table
		$sql = "UPDATE " . $this->modTables["OrganizationRSS"] . " ";
		$sql .= "SET " . $this->modSchemas["OrganizationRSS"]["GenerateDate"] . "=CURRENT_TIMESTAMP, ";
		$sql .= $this->modSchemas["OrganizationRSS"]["FeedData"] . "=? ";
		$sql .= "WHERE " . $this->modSchemas["OrganizationRSS"]["ID"] . "=?;";
		
		// define the bindings array
		$bindings = array(
			json_encode($feed),
			$this->data[$this->modSchemas["OrganizationRSS"]["ID"]]);
			
		// execute the query
		$this->ci->db->query($sql, $bindings);
	}
	
	protected function isRegen()
	{
		// get the number of seconds that needs to pass if the organization
		// RSS feed is to be regenerated
		$maxSeconds = $this->modConstants["org_regen_seconds"];

		// the feed can be regenerated only if the timestamp indicates that
		// the last cached version of the RSS feed is older than the maximum
		// number of seconds
		return !(($this->data["unix_stamp"] + $maxSeconds) > time());
	}
}

?>