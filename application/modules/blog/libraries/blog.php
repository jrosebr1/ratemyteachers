<?php

class Blog
{
	/**
	 * Variable used to interface with CodeIgniter.
	 *
	 * @var $ci
	 */
	var $ci = null;
	/**
	 * Module configuration of tables.
	 *
	 * @var $modTables
	 */
	var $modTables = array();
	/**
	 * Module configuration of table schemas.
	 *
	 * @var $modSchemas
	 */
	var $modSchemas = array();
	 
	public function __construct()
	{
		// connect the class with CodeIgniter and then grab the module
		// tables and schemas
		$this->ci = &get_instance();
		$this->modTables = $this->ci->config->item("blog.tables");
		$this->modSchemas = $this->ci->config->item("blog.tables.schemas");
	}
	
	public function getFirstEntry()
	{
		// construct the query to get the first blog entry
		$sql = "SELECT *, ";
		$sql .= "DATE_FORMAT(" . $this->modSchemas["Blog"]["LiveDate"] . ", '%D %M %Y') AS live_format_date ";
		$sql .= "FROM " . $this->modTables["Blog"] . " ";
		$sql .= "WHERE " . $this->modSchemas["Blog"]["LiveDate"] . " <= CURRENT_TIMESTAMP ";
		$sql .= "ORDER BY " . $this->modSchemas["Blog"]["ID"] . " ASC ";
		$sql .= "LIMIT 1;";

		// execute the query
		$query = $this->ci->db->query($sql);
		
		// return the first entry
		return $query->row_array();
	}
	
	public function getLatestEntry()
	{
		// construct the query to get the latest blog entry
		$sql = "SELECT *, ";
		$sql .= "DATE_FORMAT(" . $this->modSchemas["Blog"]["LiveDate"] . ", '%D %M %Y') AS live_format_date ";
		$sql .= "FROM " . $this->modTables["Blog"] . " ";
		$sql .= "WHERE " . $this->modSchemas["Blog"]["LiveDate"] . " <= CURRENT_TIMESTAMP ";
		$sql .= "ORDER BY " . $this->modSchemas["Blog"]["ID"] . " DESC ";
		$sql .= "LIMIT 1;";

		// execute the query
		$query = $this->ci->db->query($sql);
		
		// return the latest entry
		return $query->row_array();
	}
	
	public function getPreviousEntry($entryID)
	{
		// construct the query to get the previous blog entry
		$sql = "SELECT *, ";
		$sql .= "DATE_FORMAT(" . $this->modSchemas["Blog"]["LiveDate"] . ", '%D %M %Y') AS live_format_date ";
		$sql .= "FROM " . $this->modTables["Blog"] . " ";
		$sql .= "WHERE " . $this->modSchemas["Blog"]["ID"] . " < ? ";
		$sql .= "AND " . $this->modSchemas["Blog"]["LiveDate"] . " <= CURRENT_TIMESTAMP ";
		$sql .= "ORDER BY " . $this->modSchemas["Blog"]["ID"] . " DESC ";
		$sql .= "LIMIT 1;";
		$bindings = array($entryID);

		// execute the query
		$query = $this->ci->db->query($sql, $bindings);
		
		// return the latest entry
		return $query->row_array();
	}
	
	public function getNextEntry($entryID)
	{
		// construct the query to get the next blog entry
		$sql = "SELECT *, ";
		$sql .= "DATE_FORMAT(" . $this->modSchemas["Blog"]["LiveDate"] . ", '%D %M %Y') AS live_format_date ";
		$sql .= "FROM " . $this->modTables["Blog"] . " ";
		$sql .= "WHERE " . $this->modSchemas["Blog"]["ID"] . " > ? ";
		$sql .= "AND " . $this->modSchemas["Blog"]["LiveDate"] . " <= CURRENT_TIMESTAMP ";
		$sql .= "ORDER BY " . $this->modSchemas["Blog"]["ID"] . " ASC ";
		$sql .= "LIMIT 1;";
		$bindings = array($entryID);

		// execute the query
		$query = $this->ci->db->query($sql, $bindings);
		
		// return the latest entry
		return $query->row_array();
	}
	
	public function getEntry($entryID)
	{
		// construct the query to get blog entry
		$sql = "SELECT *, ";
		$sql .= "DATE_FORMAT(" . $this->modSchemas["Blog"]["LiveDate"] . ", '%D %M %Y') AS live_format_date ";
		$sql .= "FROM " . $this->modTables["Blog"] . " ";
		$sql .= "WHERE " . $this->modSchemas["Blog"]["ID"] . " =? ";
		$sql .= "AND " . $this->modSchemas["Blog"]["LiveDate"] . " <= CURRENT_TIMESTAMP;";
		$bindings = array($entryID);

		// execute the query
		$query = $this->ci->db->query($sql, $bindings);
		
		// return the latest entry
		return $query->row_array();
	}
	
	public function isValidEntry($entryID)
	{
		// construct the query to determine of the entry ID is valid
		// or not
		$sql = "SELECT COUNT(*) AS total ";
		$sql .= "FROM " . $this->modTables["Blog"] . " ";
		$sql .= "WHERE " . $this->modSchemas["Blog"]["ID"] . "=? ";
		$sql .= "AND " . $this->modSchemas["Blog"]["LiveDate"] . " <= CURRENT_TIMESTAMP ";
		$bindings = array($entryID);

		// execute the query and get the row
		$query = $this->ci->db->query($sql, $bindings);
		$row = $query->row_array();
		
		// the entry ID is valid if the total is one
		return ($row["total"] == 1);
	}
}

?>