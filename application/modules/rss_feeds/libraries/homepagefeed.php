<?php

class HomepageFeed extends RSSFeed
{
	public function __construct()
	{
		// call the parent constructor
		parent::__construct();
	}
	
	public function getFeed()
	{
		// if the feed is invalid, then return nothing
		if (!$this->isValid())
		{
			return null;
		}
		
		// grab the feed data from the homepage RSS table
		$this->data = $this->getFeedData();

		// return the cached homepage feed
		return json_decode($this->data[$this->modSchemas["HomepageRSS"]["FeedData"]]);
	}
	
	public function isValid()
	{
		// the homepage RSS feed is always valid
		return true;
	}
	
	protected function getFeedData()
	{
		// construct the query to get the homepage feed data for
		// the country being viewed
		$sql = "SELECT *, ";
		$sql .= "UNIX_TIMESTAMP(" . $this->modSchemas["HomepageRSS"]["GenerateDate"] . ") AS unix_stamp ";
		$sql .= "FROM " . $this->modTables["HomepageRSS"] . " ";
		$sql .= "WHERE " . $this->modSchemas["HomepageRSS"]["CountryID"] . "=?;";
		$bindings = array($this->coreConstants["country_id"]);

		// execute the query
		$query = $this->ci->db->query($sql, $bindings);
		
		// return the data from the homepage RSS table
		return $query->row_array();
	}
	
	public function generateFeed()
	{
		// grab the feed data from the homepage RSS table
		$this->data = $this->getFeedData();
		
		// if there is no record of the homepage feed, then insert
		// the initial values
		if (empty($this->data))
		{
			// construct the query to insert the initial entry for
			// the homepage feed
			$sql = "INSERT INTO " . $this->modTables["HomepageRSS"] . "(";
			$sql .= $this->modSchemas["HomepageRSS"]["CountryID"] . ") ";
			$sql .= "VALUES(?);";
			$bindings = array($this->coreConstants["country_id"]);

			// execute the query and re-grab the feed data
			$this->ci->db->query($sql, $bindings);
			$this->data = $this->getFeedData();
		}

		// construct and execute the query to create the publish timestamp
		// in RFC-822 format
		$sql = "SELECT DATE_FORMAT('" . $this->data[$this->modSchemas["HomepageRSS"]["CreationDate"]] . "', '%a, %d %b %Y %T') AS rfcdate;";
		$query = $this->ci->db->query($sql);
		$row = $query->row_array();
		$publishStamp = $row["rfcdate"] . " " . date("T");
     
		// construct and execute the query to create the last build timestamp
		// in RFC-822 format
		$sql = "SELECT DATE_FORMAT(CURRENT_TIMESTAMP, '%a, %d %b %Y %T') AS rfcdate;";
		$query = $this->ci->db->query($sql);
		$row = $query->row_array();
		$buildStamp = $row["rfcdate"] . " " . date("T");
		
		// set variables relative to the header of the RSS feed
		$feed = array();
		$feed["generator"] = $this->ci->config->item("core.sitename") . " RSS 1.0";
		$feed["title"] = "Recently Rated Organizations RSS Feed | " . $this->ci->config->item("core.sitename");
		$feed["link"] = $this->ci->config->item("core.base_url");
		$feed["description"] = "RSS feed for recently rated organizations on " . $this->ci->config->item("core.sitename") . ".";
		$feed["language"] = "en-us";
		$feed["publish_date"] = $publishStamp;
		$feed["last_build_date"] = $buildStamp;
		$feed["items"] = array();
		
		// construct the query to get the recently rated organizations
		$sql = "SELECT DISTINCT o." . $this->coreSchemas["Organizations"]["ID"] . ", ";
		$sql .= "o." . $this->coreSchemas["Organizations"]["Name"] . " ";
		$sql .= "FROM " . $this->coreTables["Organizations"] . " o ";
		$sql .= "INNER JOIN " . $this->coreTables["People"] . " p ";
		$sql .= "ON o." . $this->coreSchemas["Organizations"]["ID"] . "=p." . $this->coreSchemas["People"]["OrgID"] . " ";
		$sql .= "WHERE " . $this->coreSchemas["Organizations"]["CountryID"] . "=? ";
		$sql .= "AND " . $this->coreSchemas["People"]["Status"] . " >= ? ";
		$sql .= "ORDER BY p." . $this->coreSchemas["People"]["LastRatedDate"] . " DESC ";
		$sql .= "LIMIT ?;";
		
		// define the bindings array
		$bindings = array(
			$this->coreConstants["country_id"],
			$this->coreConstants["min_person_status"],
			$this->modConstants["homepage_person_limit"]);

		// execute the query
		$query = $this->ci->db->query($sql, $bindings);
		
		// loop over the organizations
		foreach ($query->result_array() as $row)
		{
			// create a new array to hold the RSS item
			$item = array();
			
			// grab the organization ID and name
			$orgID = $row[$this->coreSchemas["Organizations"]["ID"]];
			$orgName = ucwords(strtolower($row[$this->coreSchemas["Organizations"]["Name"]]));
			
			// build the URL for the organization
			$ub = new OrganizationURL($orgID);
			$orgURL = $ub->build($orgID, $orgName);

			// set variables relative to the item of the RSS feed
			$item["title"] = $orgName . " | " . $this->ci->config->item("core.sitename");
			$item["link"] = $this->ci->config->item("core.base_url") . $orgURL;
			$item["desc"] = $orgName . " was recently rated on " . $this->ci->config->item("core.sitename") . ".";
			$item["guid"] = $this->ci->config->item("core.base_url") . $orgURL;
       
			// save the RSS feed item
			$feed["items"][] = $item;
		}
		
		// return the RSS feed dictionary
		return $feed;
	}
	
	public function saveFeed($feed)
	{
		// construct the query to save the new RSS feed in the table
		$sql = "UPDATE " . $this->modTables["HomepageRSS"] . " ";
		$sql .= "SET " . $this->modSchemas["HomepageRSS"]["GenerateDate"] . "=CURRENT_TIMESTAMP, ";
		$sql .= $this->modSchemas["HomepageRSS"]["FeedData"] . "=? ";
		$sql .= "WHERE " . $this->modSchemas["HomepageRSS"]["CountryID"] . "=?;";
		
		// define the bindings array
		$bindings = array(
			json_encode($feed),
			$this->coreConstants["country_id"]);
			
		// execute the query
		$this->ci->db->query($sql, $bindings);
	}
	
	protected function isRegen()
	{
		// generating the RSS feed for the homepage will be done via
		// cronjob so this method will always return false
		return false;
	}
}

?>