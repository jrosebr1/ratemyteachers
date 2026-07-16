<?php

/**
 * @ingroup core
 *
 * @brief
 * Class used to fetch the set of valid departments. 
 *
 * This class is used to fetch the set of valid departments that
 * a user can select form when adding a person to the database.
 *
 * @author Adrian Rosebrock
 */

class DepartmentFetcher
{
	/**
	 * Fetch the set of valid departments that the user can
	 * select from when adding a person to the database.
	 *
	 * @return array()
	 *  The list of valid departments.
	 */
	public static function fetch()
	{
		// connect with CodeIgniter and grab the core tables and
		// schemas, then initialize the list of departments
		$ci = &get_instance();
		$coreTables = $ci->config->item("core.tables");
		$coreSchemas = $ci->config->item("core.tables.schemas");
		$depts = array();
		
		// construct the query to get the set of departments
		$sql = "SELECT " . $coreSchemas["Departments"]["Department"] . " ";
		$sql .= "FROM " . $coreTables["Departments"] . " ";
		$sql .= "ORDER BY " . $coreSchemas["Departments"]["ID"] . ";";
		
		// execute the query
		$query = $ci->db->query($sql);
		
		// loop over the returned rows
		foreach ($query->result_array() as $row)
		{
			// store the department in the list of departments
			$depts[] = $row[$coreSchemas["Departments"]["Department"]];
		}
		
		// return the list of departments
		return $depts;
	}
}

?>