<?php

class KPIGenerator
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
	 * Core configuration of constants.
	 *
	 * @var $coreConstants
	 */
	var $coreConstants = array();
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
	/**
	 * Current date that will be used to fetch KPI statistics.
	 *
	 * @var $curDate
	 */
	var $curDate = null;

	public function __construct()
	{
		// connect the class with CodeIgniter and grab the core tables,
		// schemas, and constants
		$this->ci = &get_instance();
		$this->coreTables = $this->ci->config->item("core.tables");
		$this->coreSchemas = $this->ci->config->item("core.tables.schemas");
		$this->coreConstants = $this->ci->config->item("core.constants");
		
		// grab the module tables and schemas
		$this->modTables = $this->ci->config->item("kpi_generator.tables");
		$this->modSchemas = $this->ci->config->item("kpi_generator.tables.schemas");
		
		// grab the current date
		$this->curDate = $this->getCurrentDate();
	}
	
	public function generate()
	{
		// initialize the list of items that will be stored in the
		// KPI table
		$items = array();
		
		// get the total number of ratings, number of accounts, number
		// of emails imported, and number of persons added
		$items[] = $this->getNumRatings();
		$items[] = $this->getNumAccounts();
		$items[] = $this->getNumEmailsImported();
		$items[] = $this->getNumPersonsAdded();
		
		// start constructing the query to insert the KPI data
		$insertSQL = "INSERT INTO " . $this->modTables["KPIs"] . "(";
		$valueSQL = "VALUES(";
		$bindings = array();
		
		// loop over the items
		foreach ($items as $item)
		{
			// add the column to the insert query portion and a binding
			// for the value
			$insertSQL .= $item["column"] . ", ";
			$valueSQL .= "?, ";
			$bindings[] = $item["value"];
		}
		
		// finish constructing the queries
		$insertSQL = substr($insertSQL, 0, -2) . ")";
		$valueSQL = substr($valueSQL, 0, -2) . ");";

		// execute the query to store the KPI data
		$this->ci->db->query($insertSQL . " " . $valueSQL, $bindings);		
	}
	
	private function getCurrentDate()
	{
		// construct and execute the query to get the current date
		$sql = "SELECT CURDATE() as currentdate;";
		$query = $this->ci->db->query($sql);
		
		// grab the row
		$row = $query->row_array();
		
		// return the current date
		return $row["currentdate"];
	}
	
	private function getNumRatings()
	{
		// construct the query to get the total number of ratings for
		// the current day
		$sql = "SELECT COUNT(*) AS total ";
		$sql .= "FROM " . $this->coreTables["Ratings"] . " ";
		$sql .= "WHERE " . $this->coreSchemas["Ratings"]["Date"] . " LIKE ?;";
		$bindings = array($this->curDate . "%");
		
		// execute the query grab the row
		$query = $this->ci->db->query($sql, $bindings);
		$row = $query->row_array();
		
		// create the info dictioanry to store the column name and
		// value
		$info = array(
			"column" => $this->modSchemas["KPIs"]["TotalRatings"],
			"value" => $row["total"]);
		
		// return the info dictionary
		return $info;
	}
	
	private function getNumAccounts()
	{
		// construct the query to get the total number of user
		// accounts created for the current day
		$sql = "SELECT COUNT(*) AS total ";
		$sql .= "FROM " . $this->coreTables["Users"] . " ";
		$sql .= "WHERE " . $this->coreSchemas["Users"]["DateAdded"] . " LIKE ?;";
		$bindings = array($this->curDate . "%");
		
		// execute the query and grab the row
		$query = $this->ci->db->query($sql, $bindings);
		$row = $query->row_array();
		
		// create the info dictionary to store the column name and
		// value
		$info = array(
			"column" => $this->modSchemas["KPIs"]["TotalAccounts"],
			"value" => $row["total"]);
		
		// return the info dictionary
		return $info;
	}
	
	private function getNumEmailsImported()
	{
		// load the contact importer module and then grab the
		// contact importer tables and schemas
		PackageLoader::load("contact_importer");
		$modTables = $this->ci->config->item("contact_importer.tables");
		$modSchemas = $this->ci->config->item("contact_importer.tables.schemas");
		
		// construct the query to get the total number of email
		// addresses imported for the current day
		$sql = "SELECT COUNT(*) AS total ";
		$sql .= "FROM " . $modTables["InviteEmails"] . " ";
		$sql .= "WHERE " . $modSchemas["InviteEmails"]["ImportedDate"] . " LIKE ?;";
		$bindings = array($this->curDate . "%");
		
		// execute the query and grab the row
		$query = $this->ci->db->query($sql, $bindings);
		$row = $query->row_array();

		// create the info dictionary to store the column name and
		// value
		$info = array(
			"column" => $this->modSchemas["KPIs"]["TotalEmailsImported"],
			"value" => $row["total"]);
		
		// return the info dictionary
		return $info;
	}
	
	private function getNumPersonsAdded()
	{
		// construct the query to get the total number of persons
		// added for the current day
		$sql = "SELECT COUNT(*) AS total ";
		$sql .= "FROM " . $this->coreTables["People"] . " ";
		$sql .= "WHERE " . $this->coreSchemas["People"]["DateAdded"] . " LIKE ?;";
		$bindings = array($this->curDate . "%");
		
		// execute the query and grab the row
		$query = $this->ci->db->query($sql, $bindings);
		$row = $query->row_array();

		// create the info dictionary to store the column name and
		// value
		$info = array(
			"column" => $this->modSchemas["KPIs"]["TotalPersons"],
			"value" => $row["total"]);
		
		// return the info dictionary
		return $info;
	}
}

?>