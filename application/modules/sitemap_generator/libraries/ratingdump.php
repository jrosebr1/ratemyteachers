<?php

class RatingDump extends SitemapDump
{
	// the output path to where rating dump will be stored
	var $outputPath = null;
	
	public function __construct($countryID, $outputPath)
	{
		// call the parent constructor and store the output path
		parent::__construct($countryID);
		$this->outputPath = $outputPath;
	}
	
	public function dump()
	{
		// construct the query to dump all the valid ratings
		$sql = "SELECT r." . $this->coreSchemas["Ratings"]["ID"] . ", ";
		$sql .= "r." . $this->coreSchemas["Ratings"]["PersonID"] . ", ";
		$sql .= "p." . $this->coreSchemas["People"]["FirstName"] . ", ";
		$sql .= "p." . $this->coreSchemas["People"]["LastName"] . " ";		
		$sql .= "FROM " . $this->coreTables["Ratings"] . " r ";
		$sql .= "INNER JOIN " . $this->coreTables["People"] . " p ";
		$sql .= "ON r." . $this->coreSchemas["Ratings"]["PersonID"] . "=p." . $this->coreSchemas["People"]["ID"] . " ";
		$sql .= "WHERE r." . $this->coreSchemas["Ratings"]["Status"] . " >= ? ";
		$sql .= "AND p." . $this->coreSchemas["People"]["Status"] . " >= ? ";
		$sql .= "ORDER BY r." . $this->coreSchemas["Ratings"]["ID"] . " ASC;";
		
		// define the bindings array
		$bindings = array(
			$this->coreConstants["min_rating_status"],
			$this->coreConstants["min_person_status"]);

		// execute the query
		$query = $this->ci->db->query($sql, $bindings);
		
		// open the output file for writing
		$outputFile = fopen($this->outputPath, "w");
		
		// loop over the results
		foreach ($query->result_array() as $row)
		{
			// grab the rating ID, person ID, and person name
			$ratingID = $row[$this->coreSchemas["Ratings"]["ID"]];
			$personID = $row[$this->coreSchemas["People"]["ID"]];
			$firstName = $row[$this->coreSchemas["People"]["FirstName"]];
			$lastName = $row[$this->coreSchemas["People"]["LastName"]];
			$personName = trim($firstName . " " . $lastName);
			
			// construct the URL of the rating
			$ub = new RatingURL($ratingID);
			$personURL = $ub->build($ratingID, $personName);
			
			// write the block to file
			fwrite($outputFile, $ratingID . "\n");
			fwrite($outputFile, $personID . "\n");
			fwrite($outputFile, $personURL . "\n");
		}
		
		// close the output file
		fclose($outputFile);
	}
}

?>