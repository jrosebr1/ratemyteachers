<?php

class Country
{
	public static function getStates()
	{
		// get the unique set of states for the country
		return Country::getLocation("State");
	}
	
	public static function getCities()
	{
		// get the unique set of cities for the country
		return Country::getLocation("City");
	}
	
	private static function getLocation($type)
	{
		// initialize the list of items
		$items = array();
		
		// grab the core tables and schemas
		$ci = &get_instance();
		$coreTables = $ci->config->item("core.tables");
		$coreSchemas = $ci->config->item("core.tables.schemas");
		
		// construct the query to get the set of unique states
		$sql = "SELECT DISTINCT " . $coreSchemas["Organizations"][$type] . " ";
		$sql .= "FROM " . $coreTables["Organizations"] . " ";
		$sql .= "WHERE " . $coreSchemas["Organizations"]["CountryID"] . "=? ";
		$sql .= "ORDER BY " . $coreSchemas["Organizations"][$type] . " ASC;";
		$bindings = array($ci->config->item("country_id"));
		
		// execute the query
		$query = $ci->db->query($sql, $bindings);
		
		// loop over the results
		foreach ($query->result_array() as $row)
		{
			// add each row to the items list
			$items[] = $row[$coreSchemas["Organizations"][$type]];
		}
		
		// return the list of items
		return $items;
	}
}

?>