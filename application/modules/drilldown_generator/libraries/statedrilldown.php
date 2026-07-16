<?php

class StateDrillDown extends DrillDownDumper
{
	/**
	 * Path to where the state drillbit file resides.
	 *
	 * @var $drillBitPath
	 */
	var $drillBitPath = null;
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

	public function __construct($drillBitPath, $outputDir, $dumpDir, $countryCode, $countryID)
	{
		// call the parent constructor and store the drillbit path, output
		// directory, dump directory, country code, and country ID
		parent::__construct();
		$this->drillBitPath = $drillBitPath;
		$this->outputDir = $outputDir;
		$this->dumpDir = $dumpDir;
		$this->countryCode = $countryCode;
		$this->countryID = $countryID;
		
		// create the directory structure needed for the dump
		$this->createDirStructure();
	}

	public function dump()
	{
		// read the drill bits and break them into a list
		$drillBits = explode("\n", file_get_contents($this->drillBitPath));

		// loop over the drillbits
		for ($enumBits = 0; $enumBits < count($drillBits) - 1; $enumBits++)
		{
			// grab the current state
			$state = $drillBits[$enumBits];

			// construct the query to fetch the organizations that belong
			// to the current state
			$sql = "SELECT * FROM (";
			$sql .= "SELECT (@i:=(@i+1)) AS i, ";
			$sql .= $this->coreSchemas["Organizations"]["ID"] . ", ";
			$sql .= $this->coreSchemas["Organizations"]["Name"] . " ";
			$sql .= "FROM " . $this->coreTables["Organizations"] . " ";
			$sql .= "WHERE " . $this->coreSchemas["Organizations"]["CountryID"] . "=? ";
			$sql .= "AND " . $this->coreSchemas["Organizations"]["State"] . " LIKE ? ";
			$sql .= "ORDER BY " . $this->coreSchemas["Organizations"]["Name"] . " ASC ";
			$sql .= ") AS tbl;";

			// define the bindings array
			$bindings = array(
				$this->countryID,
				$state . "%");

			// reset the counter and then grab the organizations
			$this->ci->db->query("SET @i=0;");
			$query = $this->ci->db->query($sql, $bindings);

			// open the dump file for writing and write the number of rows
			// fetch to file
			$dumpFile = fopen($this->dumpPath . "/" . $state . ".txt", "w");
			fwrite($dumpFile, $query->num_rows() . "\n");
			
			// loop over the results
			foreach ($query->result_array() as $row)
			{
				// grab the counter, organization ID, and organization name
				$counter = $row["i"];
				$orgID = $row[$this->coreSchemas["Organizations"]["ID"]];
				$orgName = $row[$this->coreSchemas["Organizations"]["Name"]];
				
				// sanitize the organization name
				$orgName = str_replace(array("\n", "\r", "\t"), "", $orgName);
				
				// write the block to file
				fwrite($dumpFile, $counter . "\n");
				fwrite($dumpFile, $orgID . "\n");
				fwrite($dumpFile, $orgName . "\n");
			}
			
			// close the dump file
			fclose($dumpFile);
		}
	}
	
	public function createDirStructure()
	{
		// ensure the 'state' directory exists in both the dump
		// output directory and the output directory
		@mkdir($this->dumpDir . "/state");
		@mkdir($this->outputDir . "/state");
		
		// construct the dump path and then ensure the specific site
		// directory exists
		$this->dumpPath = $this->dumpDir . "/state/" . $this->countryCode;
		@mkdir($this->dumpPath);
		@mkdir($this->outputDir . "/state/" . $this->countryCode);
	}
}

?>