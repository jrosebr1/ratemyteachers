<?php

class StateDrillbitCreator extends DrillbitCreator
{
	/**
	 * Path to the directory where the drill bits will reside.
	 *
	 * @var $outputDir
	 */
	var $outputDir = null;
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
	/**
	 * Path to where the drill bit files will be saved
	 *
	 * @var $writePath
	 */
	var $writePath = null;
	
	public function __construct($outputDir, $countryCode, $countryID)
	{
		// call the parent constructor and store the output directory,
		// country code, and country ID
		parent::__construct();
		$this->outputDir = $outputDir;
		$this->countryCode = $countryCode;
		$this->countryID = $countryID;
		
		// create the directory structure needed for the drill bits
		$this->createDirStructure();
	}
	
	public function createDrillBits($filename = "state.txt")
	{
		// build the query to fetch the unique site names for the
		// current country
		$sql = "SELECT DISTINCT " . $this->coreSchemas["Organizations"]["State"] . " ";
		$sql .= "FROM " . $this->coreTables["Organizations"] . " ";
		$sql .= "WHERE " . $this->coreSchemas["Organizations"]["CountryID"] . "=? ";
		$sql .= "ORDER BY " . $this->coreSchemas["Organizations"]["State"] . " ASC;";
		$bindings = array($this->countryID);
		
		// execute the query and open the output file for writing
		$query = $this->ci->db->query($sql, $bindings);
		$outputFile = fopen($this->writePath . "/" . $filename, "w");
		
		// loop over the results
		foreach ($query->result_array() as $row)
		{
			// sanitize the drill bit
			$drillBit = $row[$this->coreSchemas["Organizations"]["State"]];
			$drillBit = preg_replace("/[^A-Za-z0-9]/", "%", $drillBit);
			$drillBit = strtoupper($drillBit);
			
			// if the drill bit is not empty, write it to file
			if (!empty($drillBit))
			{
				fwrite($outputFile, $drillBit . "\n");
			}
		}
		
		// close the output file
		fclose($outputFile);
	}
	
	public function createDirStructure()
	{
		// build the path to where the drill bits will be saved and then
		// create the directory
		$this->writePath = $this->outputDir . "/" . $this->countryCode;
		@mkdir($this->writePath);
	}
}

?>