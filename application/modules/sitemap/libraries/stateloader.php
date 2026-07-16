<?php

class StateLoader extends SitemapLoader
{
	public function __construct()
	{
		// call the parent constructor
		parent::__construct();
	}
	
	public function useStates()
	{
		// return true, since a state loader will obviously use states
		// as its main form of geographic disambiguation
		return true;
	}
	
	public function loadStates()
	{
		// list of states that belong to the country
		$states = array();
		
		// construct the query to get the set of states in the country
		$sql = "SELECT DISTINCT " . $this->coreSchemas["StateMappings"]["FullName"] . ", ";
		$sql .= $this->coreSchemas["StateMappings"]["Abbrev"] . " ";
		$sql .= "FROM " . $this->coreTables["StateMappings"] . " ";
		$sql .= "WHERE " . $this->coreSchemas["StateMappings"]["CountryID"] . "=? ";
		$sql .= "ORDER BY " . $this->coreSchemas["StateMappings"]["FullName"] . " ASC;";
		$bindings = array($this->coreConstants["country_id"]);

		// execute the query
		$query = $this->ci->db->query($sql, $bindings);
		
		// loop over the results
		foreach ($query->result_array() as $row)
		{
			// add the state to the states list
			$states[] = array(
				"name" => $row[$this->coreSchemas["StateMappings"]["FullName"]],
				"abbrev" => strtoupper($row[$this->coreSchemas["StateMappings"]["Abbrev"]]));
		}
		
		// return the list of states
		return $states;
	}
	
	public function loadOrgs($city = null)
	{
		// list of organizations that belong to the supplied city
		$orgs = array();
		
		// construct the query to get the set of organizations that
		// belong to the supplied city
		$sql = "SELECT " . $this->coreSchemas["Organizations"]["ID"] . ", ";
		$sql .= $this->coreSchemas["Organizations"]["Name"] . " ";
		$sql .= "FROM " . $this->coreTables["Organizations"] . " ";
		$sql .= "WHERE " . $this->coreSchemas["Organizations"]["City"] . " LIKE ? ";
		$sql .= "AND " . $this->coreSchemas["Organizations"]["CountryID"] . "=? ";
		$sql .= "ORDER BY " . $this->coreSchemas["Organizations"]["Name"] . " ASC;";
		$bindings = array($city . "%", $this->coreConstants["country_id"]);

		// execute the query
		$query = $this->ci->db->query($sql, $bindings);

		// loop over the results
		foreach ($query->result_array() as $row)
		{
			// add each organization to the organizations list
			$orgs[] = array(
				"id" => $row[$this->coreSchemas["Organizations"]["ID"]],
				"name" => $row[$this->coreSchemas["Organizations"]["Name"]]);
		}
		
		// return the list of organizations
		return $orgs;
	}
	
	public function getStateInfo($stateAbbrev)
	{
		// dictionary of state information
		$info = array();
		
		// construct the query to get the state information
		$sql = "SELECT " . $this->coreSchemas["StateMappings"]["FullName"] . ", ";
		$sql .= $this->coreSchemas["StateMappings"]["Abbrev"] . " ";
		$sql .= "FROM " . $this->coreTables["StateMappings"] . " ";
		$sql .= "WHERE " . $this->coreSchemas["StateMappings"]["Abbrev"] . "=? ";
		$sql .= "AND " . $this->coreSchemas["StateMappings"]["CountryID"] . "=? ";
		$sql .= "LIMIT 1;";
		$bindings = array($stateAbbrev, $this->coreConstants["country_id"]);

		// execute the query
		$query = $this->ci->db->query($sql, $bindings);
		
		// if the number of rows returned is zero, then the state
		// abbreviation is invalid
		if ($query->num_rows() == 0)
		{
			return null;
		}

		// grab the row and store the state name and abbreviation in the
		// state information dictionary
		$row = $query->row_array();
		$info["name"] = $row[$this->coreSchemas["StateMappings"]["FullName"]];
		$info["abbrev"] = strtoupper($row[$this->coreSchemas["StateMappings"]["Abbrev"]]);
		
		// if the abbreviation is empty, set it to the name of the state
		if (empty($info["abbrev"]))
		{
			$info["abbrev"] = $row[$this->coreSchemas["StateMappings"]["FullName"]];
		}
		
		// return the state info
		return $info;
	}
	
	public function getCityInfo($city)
	{
		// construct the query to get the name of the city
		$sql = "SELECT " . $this->coreSchemas["Organizations"]["City"] . " ";
		$sql .= "FROM " . $this->coreTables["Organizations"] . " ";
		$sql .= "WHERE " . $this->coreSchemas["Organizations"]["City"] . " LIKE ? ";
		$sql .= "AND " . $this->coreSchemas["Organizations"]["CountryID"] . "=? ";
		$sql .= "LIMIT 1;";
		$bindings = array($city . "%", $this->coreConstants["country_id"]);

		// execute the query
		$query = $this->ci->db->query($sql, $bindings);
		
		// if the number of returned rows is zero, then the city
		// name is invalid
		if ($query->num_rows() == 0)
		{
			return null;
		}
		
		// grab the row
		$row = $query->row_array();
		
		// return the name of the city
		return array("name" => $row[$this->coreSchemas["Organizations"]["City"]]);
	}
	
	public function getStateInfoFromCity($city)
	{
		// dictionary of state information
		$info = array();
		
		// construct the query to get the state the supplied city
		// resides in
		$sql = "SELECT s." . $this->coreSchemas["StateMappings"]["FullName"] . ", ";
		
		// return the state information
		return $info;
	}
	
	public function isStateValid($state)
	{
		// get the state information
		$stateInfo = $this->getStateInfo($state);

		// return true if the state name exists in the dictionary
		return isset($stateInfo["name"]);
	}
	
	public function isCityValid($city)
	{
		// get the city information
		$cityInfo = $this->getCityInfo($city);
		
		// return true if the city name exists in the dictionary
		return isset($cityInfo["name"]);
	}
}

?>