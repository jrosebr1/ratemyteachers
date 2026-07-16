<?php

class RecentlySearchedOrg extends RecentlySearched
{
	public function __construct()
	{
		// call the parent constructor
		parent::__construct();
	}
	
	public function generate()
	{
		// clean the entries in the recently searched table by
		// deleting the old entries
		$this->clean();
		
		// generate the recently searched and recently rated
		// organizations
		$this->genRecentlySearched();
		$this->genRecentlyRated();
	}
	
	public function getRecentlySearched($limit = 5)
	{
		// fetch the set of recently searched for organizations
		return $this->fetchRecent($this->modTypeMappings["org"]["search"], $limit);
	}
	
	public function getRecentlyRated($limit = 5)
	{
		// fetch the set of recently rated organizations
		return $this->fetchRecent($this->modTypeMappings["org"]["rated"], $limit);
	}
	
	private function fetchRecent($type, $limit)
	{
		// initialize the list of recent activity
		$recent = array();
		
		// construct and execute the query to get a set of
		// recently searched for organizations
		$sql = "SELECT " . $this->modSchemas["RecentlySearchedOrgs"]["ID"] . ", ";
		$sql .= $this->modSchemas["RecentlySearchedOrgs"]["Name"] . " ";
		$sql .= "FROM " . $this->modTables["RecentlySearchedOrgs"] . " ";
		$sql .= "WHERE " . $this->modSchemas["RecentlySearchedOrgs"]["Type"] . "=? ";
		$sql .= "AND " . $this->modSchemas["RecentlySearchedOrgs"]["CountryID"] . "=? ";
		$sql .= "ORDER BY RAND() ";
		$sql .= "LIMIT ?;";

		// define the bindings array
		$bindings = array(
			$type["num"],
			$this->coreConstants["country_id"],
			$limit);
		
		// execute the query
		$query = $this->ci->db->query($sql, $bindings);

		// loop over the results
		foreach ($query->result_array() as $row)
		{
			// grab the organization ID and name
			$id = $row[$this->modSchemas["RecentlySearchedOrgs"]["ID"]];
			$name = $row[$this->modSchemas["RecentlySearchedOrgs"]["Name"]];
			
			// build the URL for the organization
			$ub = new OrganizationURL();
			$url = $ub->build($id, $name);
			
			$recent[] = array(
				"id" => $id,
				"url" => $url,
				"text" => $type["text"],
				"name" => ucwords(strtolower($name)));
		}
		
		// return the list of recent activity
		return $recent;
	}
	
	private function genRecentlySearched()
	{
		// construct the query to grab the recently searched for
		// organizations		
		$sql = "SELECT " . $this->coreSchemas["Organizations"]["ID"] . ", ";
		$sql .= $this->coreSchemas["Organizations"]["Name"] . " ";
		$sql .= "FROM " . $this->coreTables["Organizations"] . " ";
		$sql .= "WHERE " . $this->coreSchemas["Organizations"]["NumRatings"] . " >= ? ";
		$sql .= "AND " . $this->coreSchemas["Organizations"]["CountryID"] . "=? ";
		$sql .= "ORDER BY RAND() ";
		$sql .= "LIMIT ?;";
		
		// define the bindings array
		$bindings = array(
			$this->modConstants["search_num_org_ratings"],
			$this->coreConstants["country_id"],
			$this->modConstants["num_org_grabs"]);

		// execute the query
		$query = $this->ci->db->query($sql, $bindings);
		
		// start building the query to insert the recently searched
		// for organizations
		$sql = "INSERT INTO " . $this->modTables["RecentlySearchedOrgs"] . "(";
		$sql .= $this->modSchemas["RecentlySearchedOrgs"]["ID"] . ", ";
		$sql .= $this->modSchemas["RecentlySearchedOrgs"]["CountryID"] . ", ";
		$sql .= $this->modSchemas["RecentlySearchedOrgs"]["Type"] . ", ";
		$sql .= $this->modSchemas["RecentlySearchedOrgs"]["Name"] . ") VALUES ";
		$bindings = array();
		
		// loop over the results
		foreach ($query->result_array() as $row)
		{
			// update the query to include the insert and then create
			// the binding array
			$sql .= "(?, ?, ?, ?), ";
			$binding = array(
				$row[$this->modSchemas["RecentlySearchedOrgs"]["ID"]],
				$this->coreConstants["country_id"],
				$this->modTypeMappings["org"]["search"]["num"],
				$row[$this->modSchemas["RecentlySearchedOrgs"]["Name"]]);
			
			// store the binding in the bindings array
			$bindings = array_merge($bindings, $binding);
		}
		
		// remove the trailing space and comma and then finish off
		// the query
		$sql = substr($sql, 0, -2);
		$sql .= ";";
		
		// execute the query
		$this->ci->db->query($sql, $bindings);
	}
	
	private function genRecentlyRated()
	{
		// construct the query to grab the recently rated
		// organizations
		$sql = "SELECT o." . $this->coreSchemas["Organizations"]["ID"] . ", ";
		$sql .= "o." . $this->coreSchemas["Organizations"]["Name"] . " ";
		$sql .= "FROM " . $this->coreTables["Organizations"] . " o ";
		$sql .= "INNER JOIN " . $this->coreTables["People"] . " p ";
		$sql .= "ON o." . $this->coreSchemas["Organizations"]["ID"] . "=p." . $this->coreSchemas["People"]["OrgID"] . " ";
		$sql .= "WHERE o." . $this->coreSchemas["Organizations"]["CountryID"] . "=? ";
		$sql .= "AND o." . $this->coreSchemas["Organizations"]["NumRatings"] . " >= ? ";
		$sql .= "AND p." . $this->coreSchemas["People"]["LastRatedDate"] . " > CURRENT_TIMESTAMP - INTERVAL " . $this->modConstants["last_rated_interval"] . " ";
		$sql .= "ORDER BY p." . $this->coreSchemas["People"]["LastRatedDate"] . " ASC ";
		$sql .= "LIMIT ?;";
		
		// define the bindings array
		$bindings = array(
			$this->coreConstants["country_id"],
			$this->modConstants["rated_num_org_ratings"],
			$this->modConstants["num_person_grabs"]);
		
		// execute the query
		$query = $this->ci->db->query($sql, $bindings);
		
		// start bulding the query to insert the recently rated
		// organizations
		$sql = "INSERT INTO " . $this->modTables["RecentlySearchedOrgs"] . "(";
		$sql .= $this->modSchemas["RecentlySearchedOrgs"]["ID"] . ", ";
		$sql .= $this->modSchemas["RecentlySearchedOrgs"]["CountryID"] . ", ";
		$sql .= $this->modSchemas["RecentlySearchedOrgs"]["Type"] . ", ";
		$sql .= $this->modSchemas["RecentlySearchedOrgs"]["Name"] . ") VALUES ";
		$bindings = array();
		
		// loop over the results
		foreach ($query->result_array() as $row)
		{
			// update the query to include the insert and then create
			// the binding array
			$sql .= "(?, ?, ?, ?), ";
			$binding = array(
				$row[$this->modSchemas["RecentlySearchedOrgs"]["ID"]],
				$this->coreConstants["country_id"],
				$this->modTypeMappings["org"]["rated"]["num"],
				$row[$this->modSchemas["RecentlySearchedOrgs"]["Name"]]);
			
			// store the binding in the bindings array
			$bindings = array_merge($bindings, $binding);
		}
		
		// remove the trailing space and comma and then finish off
		// the query
		$sql = substr($sql, 0, -2);
		$sql .= ";";
		
		// execute the query
		$this->ci->db->query($sql, $bindings);
	}

	protected function clean()
	{
		// construct and execute the query to delete the old
		// entries for this country
		$sql = "DELETE FROM " . $this->modTables["RecentlySearchedOrgs"] . " ";
		$sql .= "WHERE " . $this->modSchemas["RecentlySearchedOrgs"]["CountryID"] . "=?;";
		$bindings = array($this->coreConstants["country_id"]);
		
		// execute the query
		$this->ci->db->query($sql, $bindings);
	}
}

?>