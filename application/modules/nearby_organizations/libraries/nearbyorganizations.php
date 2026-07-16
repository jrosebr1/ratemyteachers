<?php

/**
 * @defgroup nearby_organizations Nearby Organizations: fetches organizations
 * that are nearby to the organization provided.
 * @ingroup nearby_organizations
 *
 * @brief
 * Class used to fetch nearby organizations to the one provided.
 *
 * This class is used to fetch organizations that are nearby to the
 * organization provided.
 *
 * @author Adrian Rosebrock
 */

class NearbyOrganizations
{
	/**
	 * Variable used to interface with CodeIgniter.
	 *
	 * @var $ci
	 */
	var $ci = null;
	/**
	 * Core configuration of tables.
	 *
	 * @var $coreTables
	 * @see site.php
	 */
	var $coreTables = array();
	/**
	 * Core configuration of table schemas.
	 *
	 * @var $coreSchemas
	 * @see site.php
	 */
	var $coreSchemas = array();
	/**
	 * Organization ID that is currently being viewed.
	 *
	 * @var $orgID
	 */
	var $orgID = null;
	
	/**
	 * Construct the NearbyOrganizations.
	 *
	 * @param $orgID
	 *  The organization ID that is currently being viewed.
	 */
	public function __construct($orgID)
	{
		// connect the class with CodeIgniter and store the organization
		// ID
		$this->ci = &get_instance();
		$this->orgID = $orgID;

		// grab the core tables and schemas
		$this->coreTables = $this->ci->config->item("core.tables");
		$this->coreSchemas = $this->ci->config->item("core.tables.schemas");		
	}
	
	/**
	 * Get the list of nearby organizations.
	 *
	 * @param $state
	 *  The state of the organization currently being viewed.
	 * @param $city
	 *  The city of the organization currently being viewed.
	 * @param $limit
	 *  The number of nearby organizations to return.
	 *
	 * @return
	 *  A list of nearby organizations, including the ID, name,
	 *  and URL.
	 */
	public function getNearbyOrgs($state, $city, $limit = 5)
	{
		// initialize the bindings and nearby organizations array
		$bindings = array();
		$nearby = array();
		
		// start constructing the query to fetch the nearby organizations
		$sql = "SELECT " . $this->coreSchemas["Organizations"]["ID"] . ", ";
		$sql .= $this->coreSchemas["Organizations"]["Name"] . " ";
		$sql .= "FROM " . $this->coreTables["Organizations"] . " ";
		$sql .= "WHERE ";

		// check to see if the state is empty; if it is, then use the
		// city to find nearby organizations
		if (empty($state))
		{
			$sql .= $this->coreSchemas["Organizations"]["City"] . "=? AND ";
			$bindings[] = $city;
		}
		
		// otherwise, the state is not empty and can be used to find
		// nearby organizations
		else
		{
			$sql .= $this->coreSchemas["Organizations"]["State"] . "=? AND ";
			$bindings[] = $state;
		}
		
		// finish contructing the query to fetch nearby organizations
		$sql .= $this->coreSchemas["Organizations"]["ID"] . " !=? ";
		$sql .= "ORDER BY RAND() LIMIT ?;";
		$bindings[] = $this->orgID;
		$bindings[] = $limit;

		// execute the query
		$query = $this->ci->db->query($sql, $bindings);
		
		// loop over the results
		foreach ($query->result_array() as $row)
		{
			// grab the organization ID and name
			$id = $row[$this->coreSchemas["Organizations"]["ID"]];
			$name = $row[$this->coreSchemas["Organizations"]["Name"]];
			
			// build a URL for the organization
			$orgURL = new OrganizationURL();
			$url = $orgURL->build($id, $name);
			
			// add the organization information to the organizations
			// list
			$nearby[] = array(
				"id" => $id,
				"name" => $name,
				"url" => $url);
		}
		
		// return the list of nearby organizations
		return $nearby;
	}
}

?>