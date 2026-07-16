<?php

class CityDrillDown extends DrillDownDumper
{
	/**
	 * Path to the output directory where the output files from the
	 * drilldown process will reside.
	 *
	 * @var $outputDir
	 */
	var $outputDir = null;
	/**
	 * Base path to the dump directory where the dump files will be
	 * stored.
	 *
	 * @var $dumpDir
	 */
	var $dumpDir = null;
	/**
	 * Path to where all dump files will be stored for easy access.
	 *
	 * @var $dumpPath
	 */
	var $dumpPath = null;
	/**
	 * Country code of the country that the drill bits are being generated
	 * for.
	 *
	 * @var $countryCode
	 */
	var $countryCode = null;
	/**
	 * Country ID of the country that the drill bits are being generated
	 * for.
	 *
	 * @var $countryID
	 */
	var $countryID = null;

	public function __construct($outputDir, $dumpDir, $countryCode, $countryID)
	{
		// call the parent constructor and store the output directory, dump
		// directory, country code, and country ID
		parent::__construct();
		$this->outputDir = $outputDir;
		$this->dumpDir = $dumpDir;
		$this->countryCode = $countryCode;
		$this->countryID = $countryID;
		
		// create the directory structure needed for the dump
		$this->createDirStructure();
	}
	
	public function dump()
	{
		// list of alpahbet characters for looping
		$alphabet = "*abcdefghijklmnopqrstuvwxyz";
		
		// loop over the alphabet
		for ($enumAlpha = 0; $enumAlpha < strlen($alphabet); $enumAlpha++)
		{
			// check to see if the current letter is the 'ALL' character
			if ($alphabet[$enumAlpha] == "*")
			{
				// store the letter and construct the query to fetch the
				// organizations
				$letter = "ALL";
				$sql = "SELECT * FROM (";
				$sql .= "SELECT DISTINCT " . $this->coreSchemas["Organizations"]["City"] . ", ";
				$sql .= "(@i:=(@i+1)) AS i ";
				$sql .= "FROM " . $this->coreTables["Organizations"] . " ";
				$sql .= "WHERE " . $this->coreSchemas["Organizations"]["CountryID"] . "=? ";
				$sql .= "ORDER BY " . $this->coreSchemas["Organizations"]["City"] . " ASC ";
				$sql .= ") AS tbl;";
				$bindings = array($this->countryID);
			}
			
			// otherwise, the letter is just a normal letter
			else
			{
				// store the letter and construct the query to fetch the
				// organizations
				$letter = strtoupper($alphabet[$enumAlpha]);
				$sql = "SELECT * FROM (";
				$sql .= "SELECT " . $this->coreSchemas["Organizations"]["City"] . ", ";
				$sql .= "(@i:=(@i+1)) AS i ";
				$sql .= "FROM " . $this->coreTables["Organizations"] . " ";
				$sql .= "WHERE " . $this->coreSchemas["Organizations"]["CountryID"] . "=? ";
				$sql .= "AND " . $this->coreSchemas["Organizations"]["City"] . " LIKE ? ";
				$sql .= "ORDER BY " . $this->coreSchemas["Organizations"]["City"] . " ASC ";
				$sql .= ") AS tbl;";
				$bindings = array($this->countryID, $letter . "%");
			}

			// reset the counter and then grab the organizations
			$this->ci->db->query("SET @i=0;");
			$query = $this->ci->db->query($sql, $bindings);
			
			// open the dump file for writing and write the number of rows
			// fetch to file
			$dumpFile = fopen($this->dumpPath . "/" . $letter . ".txt", "w");
			fwrite($dumpFile, $query->num_rows() . "\n");
			
			// loop over the results
			foreach ($query->result_array() as $row)
			{
				// grab the counter and the city
				$counter = $row["i"];
				$city = $row[$this->coreSchemas["Organizations"]["City"]];
				
				// sanitize the city name
				$city = str_replace(array("\n", "\r", "\t"), "", $city);
				
				// write the block to file
				fwrite($dumpFile, $counter . "\n");
				fwrite($dumpFile, "0\n");
				fwrite($dumpFile, $city . "\n");
			}
			
			// close the dump file
			fclose($dumpFile);
		}
	}
	
	public function createDirStructure()
	{
		// ensure the 'organization' directory exists in both the dump
		// output directory and the output directory
		@mkdir($this->dumpDir . "/city");
		@mkdir($this->outputDir . "/city");
		
		// construct the dump path and then ensure the specific site
		// directory exists
		$this->dumpPath = $this->dumpDir . "/city/" . $this->countryCode;
		@mkdir($this->dumpPath);
		@mkdir($this->outputDir . "/city/" . $this->countryCode);
	}
}

?>