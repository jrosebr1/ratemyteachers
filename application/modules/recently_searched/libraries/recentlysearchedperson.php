<?php

class RecentlySearchedPerson extends RecentlySearched
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
		// persons
		$this->genRecentlySearched();
		$this->genRecentlyRated();
	}

	public function getRecentlySearched($limit = 5)
	{
		// fetch the set of recently searched for persons
		return $this->fetchRecent($this->modTypeMappings["person"]["search"], $limit);
	}

	public function getRecentlyRated($limit = 5)
	{
		// fetch the set of recently rated persons
		return $this->fetchRecent($this->modTypeMappings["person"]["rated"], $limit);
	}
	
	private function fetchRecent($type, $limit)
	{
		// initialize the list of recent activity
		$recent = array();
		
		// construct and execute the query to get a set of
		// recently searched for persons
		$sql = "SELECT " . $this->modSchemas["RecentlySearchedPersons"]["ID"] . ", ";
		$sql .= $this->modSchemas["RecentlySearchedPersons"]["Name"] . " ";
		$sql .= "FROM " . $this->modTables["RecentlySearchedPersons"] . " ";
		$sql .= "WHERE " . $this->modSchemas["RecentlySearchedPersons"]["Type"] . "=? ";
		$sql .= "AND " . $this->modSchemas["RecentlySearchedPersons"]["CountryID"] . "=? ";
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
			// grab the ID and name of the person
			$id = $row[$this->modSchemas["RecentlySearchedPersons"]["ID"]];
			$name = $row[$this->modSchemas["RecentlySearchedPersons"]["Name"]];
			
			// build the review URL for the person
			$ub = new PersonReviewURL($id);
			$url = $ub->build($id, $name);
		
			$recent[] = array(
				"id" => $id,
				"url" => $url,
				"text" => $type["text"],
				"name" => $name);
		}
		
		// return the list of recent activity
		return $recent;
	}

	private function genRecentlySearched()
	{
		// construct the query to grab the recently searched for
		// persons
		$sql = "SELECT p." . $this->coreSchemas["People"]["ID"] . ", ";
		$sql .= "p." . $this->coreSchemas["People"]["FirstName"] . ", ";
		$sql .= "p." . $this->coreSchemas["People"]["LastName"] . ", ";
		$sql .= "o." . $this->coreSchemas["Organizations"]["CountryID"] . " ";
		$sql .= "FROM " . $this->coreTables["People"] . " p ";
		$sql .= "INNER JOIN " . $this->coreTables["Organizations"] . " o ";
		$sql .= "ON p." . $this->coreSchemas["People"]["OrgID"] . "=o." . $this->coreSchemas["Organizations"]["ID"] . " ";
		$sql .= "WHERE p." . $this->coreSchemas["People"]["NumRatings"] . " >= ? ";
		$sql .= "AND o." . $this->coreSchemas["Organizations"]["CountryID"] . "=? ";
		$sql .= "ORDER BY RAND() ";
		$sql .= "LIMIT ?;";
		
		// define the bindings array
		$bindings = array(
			$this->modConstants["search_num_person_ratings"],
			$this->coreConstants["country_id"],
			$this->modConstants["num_person_grabs"]);

		// execute the query
		$query = $this->ci->db->query($sql, $bindings);
		
		// start bulding the query to insert the recently rated
		// persons
		$sql = "INSERT INTO " . $this->modTables["RecentlySearchedPersons"] . "(";
		$sql .= $this->modSchemas["RecentlySearchedPersons"]["ID"] . ", ";
		$sql .= $this->modSchemas["RecentlySearchedPersons"]["CountryID"] . ", ";
		$sql .= $this->modSchemas["RecentlySearchedPersons"]["Type"] . ", ";
		$sql .= $this->modSchemas["RecentlySearchedPersons"]["Name"] . ") VALUES ";
		$bindings = array();
		
		// loop over the results
		foreach ($query->result_array() as $row)
		{
			// construct the name of the person
			$name = $row[$this->coreSchemas["People"]["FirstName"]];
			$name .= " " . $row[$this->coreSchemas["People"]["LastName"]];
			$name = trim($name);
			
			// update the query to include the insert and then create
			// the binding array
			$sql .= "(?, ?, ?, ?), ";
			$binding = array(
				$row[$this->modSchemas["RecentlySearchedPersons"]["ID"]],
				$this->coreConstants["country_id"],
				$this->modTypeMappings["person"]["search"]["num"],
				$name);
			
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
		// construct the query to grab the recently rated persons
		$sql = "SELECT p." . $this->coreSchemas["People"]["ID"] . ", ";
		$sql .= "p." . $this->coreSchemas["People"]["FirstName"] . ", ";
		$sql .= "p." . $this->coreSchemas["People"]["LastName"] . ", ";
		$sql .= "o." . $this->coreSchemas["Organizations"]["CountryID"] . " ";
		$sql .= "FROM " . $this->coreTables["People"] . " p ";
		$sql .= "INNER JOIN " . $this->coreTables["Organizations"] . " o ";
		$sql .= "ON p." . $this->coreSchemas["People"]["OrgID"] . "=o." . $this->coreSchemas["Organizations"]["ID"] . " ";
		$sql .= "WHERE p." . $this->coreSchemas["People"]["NumRatings"] . " >= ? ";
		$sql .= "AND o." . $this->coreSchemas["Organizations"]["CountryID"] . "=? ";
		$sql .= "ORDER BY p." . $this->coreSchemas["People"]["LastRatedDate"] . " DESC ";
		$sql .= "LIMIT ?;";
		
		// define the bindings array
		$bindings = array(
			$this->modConstants["rated_num_person_ratings"],
			$this->coreConstants["country_id"],
			$this->modConstants["num_person_grabs"]);
			
		// execute the query
		$query = $this->ci->db->query($sql, $bindings);

		// start bulding the query to insert the recently rated
		// persons
		$sql = "INSERT INTO " . $this->modTables["RecentlySearchedPersons"] . "(";
		$sql .= $this->modSchemas["RecentlySearchedPersons"]["ID"] . ", ";
		$sql .= $this->modSchemas["RecentlySearchedPersons"]["CountryID"] . ", ";
		$sql .= $this->modSchemas["RecentlySearchedPersons"]["Type"] . ", ";
		$sql .= $this->modSchemas["RecentlySearchedPersons"]["Name"] . ") VALUES ";
		$bindings = array();
		
		// loop over the results
		foreach ($query->result_array() as $row)
		{
			// construct the name of the person
			$name = $row[$this->coreSchemas["People"]["FirstName"]];
			$name .= " " . $row[$this->coreSchemas["People"]["LastName"]];
			$name = trim($name);
			
			// update the query to include the insert and then create
			// the binding array
			$sql .= "(?, ?, ?, ?), ";
			$binding = array(
				$row[$this->modSchemas["RecentlySearchedPersons"]["ID"]],
				$this->coreConstants["country_id"],
				$this->modTypeMappings["person"]["rated"]["num"],
				$name);
			
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
		$sql = "DELETE FROM " . $this->modTables["RecentlySearchedPersons"] . " ";
		$sql .= "WHERE " . $this->modSchemas["RecentlySearchedPersons"]["CountryID"] . "=?;";
		$bindings = array($this->coreConstants["country_id"]);
		
		// execute the query
		$this->ci->db->query($sql, $bindings);
	}
}

?>