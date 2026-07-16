<?php

/**
 * @ingroup core
 *
 * @brief
 * Class used to sync the total number of ratings and the aggregate
 * scores for the person.
 *
 * This class is used to sync the total number of ratings and the
 * aggregate scores for a person.
 *
 * @see sync.php
 * @author Adrian Rosebrock
 */

class SyncPerson extends Sync
{
	/**
	 * Core numerical field names.
	 *
	 * @var $numerFields
	 * @see site.php
	 */
	var $numerFields = array();
	/**
	 * ID of the person to sync.
	 *
	 * @var $personID
	 */
	var $personID = null;
	
	/**
	 * Constrct the SyncPerson.
	 *
	 * @param $personID
	 *  ID of the person to sync.
	 */
	public function __construct($personID)
	{
		// call the parent constructor and store the person ID
		parent::__construct();
		$this->personID = $personID;
		
		// grab the rating numerical fields so we can figure out
		// which fields are aggregate
		$this->numerFields = $this->ci->config->item("site.ratings.numerical_fields");
	}
	
	/**
	 * Sync the person.
	 */
	public function sync()
	{
		// sync the total number of ratings for the person and the
		// aggregate fields
		$this->syncTotalRatings();
		$this->syncAggregates();
	}
	
	/**
	 * Sync the total number of ratings for the person.
	 */
	public function syncTotalRatings()
	{
		// construct the query to get the total number of ratings
		// for the person
		$sql = "SELECT COUNT(*) AS total_ratings ";
		$sql .= "FROM " . $this->coreTables["Ratings"] . " ";
		$sql .= "WHERE " . $this->coreSchemas["Ratings"]["PersonID"] . "=? AND ";
		$sql .= $this->coreSchemas["Ratings"]["Status"] . ">= " . $this->coreConstants["min_rating_status"] . ";";
		$bindings = array($this->personID);
		
		// execute the query
		$query = $this->ci->db->query($sql, $bindings);
		
		// if the number of rows returned is zero, then the person
		// does not exist in our tables
		if ($query->num_rows() == 0)
		{
			return false;
		}
		
		// grab the row and construct the query to update the
		// total number of ratings for the person
		$row = $query->row_array();
		$sql = "UPDATE " . $this->coreTables["People"] . " ";
		$sql .= "SET " . $this->coreSchemas["People"]["NumRatings"] . "=? ";
		$sql .= "WHERE " . $this->coreSchemas["People"]["ID"] . "=?;";
		$bindings = array($row["total_ratings"], $this->personID);
		
		// execute the query
		$this->ci->db->query($sql, $bindings);
	}
	
	/**
	 * Sync the aggregate scores for the person by examining the
	 * columns that can have aggregate scores calculated for.
	 *
	 * @see site.php
	 */
	public function syncAggregates()
	{
		// initialize the list of aggregate field names along
		// with their average names in the 'AS' portion of the
		// query
		$aggregates = array();
	
		// start building the query to calculate the aggregate
		// fields from the ratings table
		$sql = "SELECT ";
		
		// loop over the aggregate fields
		foreach ($this->numerFields as $field => $fieldInfo)
		{
			// check if the field is aggregateable
			if (!empty($fieldInfo["aggregate"]))
			{
				// add the aggregate field to the query, as
				// well as the aggregate list
				$sql .= "AVG(" . $fieldInfo["column"] . ") AS avg_" . $field . ", ";
				$aggregates[] = array(
					"aggr_column" => $fieldInfo["aggregate"],
					"aggr_column_alias" => "avg_" . $field);
			}
		}
		
		// continue constructing the query
		$sql = substr($sql, 0, -2) . " ";
		$sql .= "FROM " . $this->coreTables["Ratings"] . " ";
		$sql .= "WHERE " . $this->coreSchemas["Ratings"]["PersonID"] . "=? AND ";
		$sql .= $this->coreSchemas["Ratings"]["Status"] . ">= " . $this->coreConstants["min_rating_status"] . ";";
		$bindings = array($this->personID);

		// execute the query
		$query = $this->ci->db->query($sql, $bindings);
		
		// if the number of rows is zero, then the person does not
		// exist in the persons table
		if ($query->num_rows() == 0)
		{
			return false;
		}
		
		// get the row and start constructing the query to update
		// the person's information and initialize the bindings
		// array
		$row = $query->row_array();
		$sql = "UPDATE " . $this->coreTables["People"] . " SET ";
		$bindings = array();
		
		// loop over the aggregates
		foreach ($aggregates as $aggr)
		{
			// add the column to the query andd the value to the
			// bindings array
			$sql .= $aggr["aggr_column"] . "=?, ";
			$bindings[] = $row[$aggr["aggr_column_alias"]];
		}
		
		// finsh constructing the query and the bindings
		$sql = substr($sql, 0, -2) . " ";
		$sql .= "WHERE " . $this->coreSchemas["People"]["ID"] . "=?;";
		$bindings[] = $this->personID;
		
		// execute the query
		$this->ci->db->query($sql, $bindings);
	}
}

?>