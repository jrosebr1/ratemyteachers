<?php

class OrganizationPersonDump extends SitemapDump
{
	// the output path to where the organization and person dump
	// will be stored
	var $outputPath = null;
	
	public function __construct($countryID, $outputPath)
	{
		// call the parent constructor and store the output path
		parent::__construct($countryID);
		$this->outputPath = $outputPath;
	}
	
	public function dump()
	{
		// construct the query to dump the organization and person
		// information
		$sql = "SELECT o." . $this->coreSchemas["Organizations"]["ID"] . ", ";
		$sql .= "o." . $this->coreSchemas["Organizations"]["Name"] . ", ";
		$sql .= "o." . $this->coreSchemas["Organizations"]["CountryID"] . ", ";
		$sql .= "p." . $this->coreSchemas["People"]["ID"] . ", ";
		$sql .= "p." . $this->coreSchemas["People"]["FirstName"] . ", ";
		$sql .= "p." . $this->coreSchemas["People"]["LastName"] . " ";
		$sql .= "FROM " . $this->coreTables["Organizations"] . " o ";
		$sql .= "INNER JOIN " . $this->coreTables["People"] . " p ";
		$sql .= "ON o." . $this->coreSchemas["Organizations"]["ID"] . "=p." . $this->coreSchemas["People"]["OrgID"] . " ";
		$sql .= "WHERE o." . $this->coreSchemas["Organizations"]["CountryID"] . "=? ";
		$sql .= "AND p." . $this->coreSchemas["People"]["Status"] . " >= ? ";
		$sql .= "ORDER BY p." . $this->coreSchemas["People"]["ID"] . " ASC;";
		
		// define the bindings array
		$bindings = array(
			$this->countryID,
			$this->coreConstants["min_person_status"]);
		
		// execute the query
		$query = $this->ci->db->query($sql, $bindings);
		
		// open the output file for writing
		$outputFile = fopen($this->outputPath, "w");
		
		// loop over the results
		foreach ($query->result_array() as $row)
		{
			// grab the ID, name, and country of the organization
			$orgID = $row[$this->coreSchemas["Organizations"]["ID"]];
			$orgName = $row[$this->coreSchemas["Organizations"]["Name"]];
			$orgCountry = $row[$this->coreSchemas["Organizations"]["CountryID"]];
			
			// grab the ID, first name, and last name of the person
			$personID = $row[$this->coreSchemas["People"]["ID"]];
			$firstName = $row[$this->coreSchemas["People"]["FirstName"]];
			$lastName = $row[$this->coreSchemas["People"]["LastName"]];
			$personName = trim($firstName . " " . $lastName);
			
			// construct the URL for the organization
			$ub = new OrganizationURL();
			$orgURL = $ub->build($orgID, $orgName);
			
			// construct the URL for the person
			$ub = new PersonReviewURL($personID);
			$personURL = $ub->build($personID, $personName);
			
			// write the block to file
			fwrite($outputFile, $orgID . "\n");
			fwrite($outputFile, $orgCountry . "\n");
			fwrite($outputFile, $orgURL . "\n");
			fwrite($outputFile, $personID . "\n");
			fwrite($outputFile, $personURL . "\n");
		}
		
		// close the output file
		fclose($outputFile);
	}
}

?>