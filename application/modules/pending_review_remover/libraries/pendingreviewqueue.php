<?php

class PendingReviewQueue extends Queue
{
	public function __construct()
	{
		// call the parent constructor
		parent::__construct();
	}
	
	public function queue($limit = 100)
	{
		// initialize the queue list
		$queue = array();
		
		// construct the query to fetch the queue of ratings that need to
		// be removed due to them being pending review
		$sql = "SELECT r."  . $this->coreSchemas["Ratings"]["ID"] . ", ";
		$sql .= "r." . $this->coreSchemas["Ratings"]["PersonID"] . ", ";
		$sql .= "p." . $this->coreSchemas["People"]["OrgID"] . " ";
		$sql .= "FROM " . $this->coreTables["Ratings"] . " r ";
		$sql .= "INNER JOIN " . $this->coreTables["People"] . " p ";
		$sql .= "ON r." . $this->coreSchemas["Ratings"]["PersonID"] . "=p." . $this->coreSchemas["People"]["ID"] . " ";
		$sql .= "WHERE r." . $this->coreSchemas["Ratings"]["Status"] . " <= ? ";
		$sql .= "AND r." . $this->coreSchemas["Ratings"]["Status"] . " >= ? ";
		$sql .= "LIMIT ?;";

		// define the bindings array
		$bindings = array(
			$this->coreConstants["min_rating_status"] + 1,
			$this->coreConstants["min_rating_status"] - 1,
			$limit);

		// execute the query
		$query = $this->ci->db->query($sql, $bindings);

		// loop over the results
		foreach ($query->result_array() as $row)
		{
			// add each row to the queue
			$queue[] = array(
				"rating_id" => $row[$this->coreSchemas["Ratings"]["ID"]],
				"person_id" => $row[$this->coreSchemas["Ratings"]["PersonID"]],
				"org_id" => $row[$this->coreSchemas["People"]["OrgID"]]);
		}

		// return the queue
		return $queue;		
	}
}

?>