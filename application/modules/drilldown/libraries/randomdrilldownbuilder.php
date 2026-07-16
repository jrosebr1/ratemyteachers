<?php

class RandomDrilldownBuilder
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
	/**
	 * Base input path to a drilldown directory.
	 *
	 * @var $inputPath
	 */
	var $inputPath = null;
	/**
	 * List of randomly selected drilldown letter, file tuples.
	 *
	 * @var $selected
	 */
	var $selected = array();
	
	public function __construct($inputPath)
	{
		// connect the class with CodeIgniter and then grab the module
		// tables, schemas, and contants
		$this->ci = &get_instance();
		$this->modTables = $this->ci->config->item("drilldown.tables");
		$this->modSchemas = $this->ci->config->item("drilldown.tables.schemas");
		
		// store the base input path to the drilldown directory
		$this->inputPath = $inputPath;
	}
	
	public static function grabRandom($countryID, $type, $total = 5)
	{
		// initialize the list of URLs
		$urls = array();
		
		// grab the module tables and schemas
		$ci = &get_instance();
		$modTables = $ci->config->item("drilldown.tables");
		$modSchemas = $ci->config->item("drilldown.tables.schemas");		
		
		// construct the query to fetch the random drilldown entries
		// for the country and type
		$sql = "SELECT " . $modSchemas["RandomDrilldown"]["URL"] . " ";
		$sql .= "FROM " . $modTables["RandomDrilldown"] . " ";
		$sql .= "WHERE " . $modSchemas["RandomDrilldown"]["CountryID"] . "=? ";
		$sql .= "AND " . $modSchemas["RandomDrilldown"]["Type"] . "=? ";
		$sql .= "ORDER BY RAND() ASC ";
		$sql .= "LIMIT ?;";
		$bindings = array($countryID, $type, $total);
		
		// execute the query
		$query = $ci->db->query($sql, $bindings);

		// loop over the results
		foreach ($query->result_array() as $row)
		{
			// add each URL to the URL list
			$urls[] = $row[$modSchemas["RandomDrilldown"]["URL"]];
		}

		// return the list of URLs
		return $urls;
	}
	
	public function build($total = 5)
	{
		// get the alphabet filters and initialize
		$alpha = Filter::getAlphabetFilters();

		// loop over the total number of random drilldown paths to
		// generate
		for ($enumPaths = 0; $enumPaths < $total; $enumPaths++)
		{			
			// randomy select a letter from the alphabet filters, and
			// then collect the set of files for that letter
			$letter = $alpha[rand(0, count($alpha) - 1)];
			$files = $this->collectFiles($this->inputPath . "/" . $letter);
			
			// randomy select a file
			$file = $files[rand(0, count($files) - 1)];
			
			// add the letter and file to the selected drilldown list
			$this->selected[] = array(
				"letter" => $letter,
				"file" => $file);
		}
	}

	public function store($countryID, $type)
	{
		// construct the query to remove the old drilldown entries
		$sql = "DELETE FROM " . $this->modTables["RandomDrilldown"] . " ";
		$sql .= "WHERE " . $this->modSchemas["RandomDrilldown"]["CountryID"] . "=? ";
		$sql .= "AND " . $this->modSchemas["RandomDrilldown"]["Type"] . "=?;";
		$bindings = array($countryID, $type["type"]);
		
		// execute the query
		$this->ci->db->query($sql, $bindings);
		
		// start constructing the query to insert the the selected
		// random drilldown paths
		$sql = "INSERT INTO " . $this->modTables["RandomDrilldown"] . " (";
		$sql .= $this->modSchemas["RandomDrilldown"]["CountryID"] . ", ";
		$sql .= $this->modSchemas["RandomDrilldown"]["Type"] . ", ";
		$sql .= $this->modSchemas["RandomDrilldown"]["URL"] . ") VALUES";
		$bindings = array();

		// loop over the selected drilldown paths
		foreach ($this->selected as $selected)
		{
			// start constructing the URL
			$url = "/" . $type["display_name"] . "/" . $selected["letter"];
			
			// if the file is not 'root.txt', then the range needs
			// to be added in
			if ($selected["file"] != "root.txt")
			{
				// break the file into the start and end range
				$range = explode("-", str_replace(".txt", "", $selected["file"]));
			
				// finish constructing the URL
				$url .= "/" . $range[0] . "/" . $range[1];
			}
			
			// add each selected URL to the query and bindings list
			$sql .= "(?, ?, ?), ";
			$bindings = array_merge($bindings, array($countryID, $type["type"], $url));
		}


		// remove the trailing space and comma and finish off the
		// query
		$sql = substr($sql, 0, -2);
		$sql .= ";";

		// execute the query
		$this->ci->db->query($sql, $bindings);		
	}
	
	private function collectFiles($path)
	{
		// initialize the list of files
		$files = array();
		
		// open the path
		$openDir = opendir($path);
		
		// loop over the files in the path
		while (($file = readdir($openDir)) !== false)
		{
			// if the file is not hidden, then add the file to the
			// list of files
			if ($file[0] != ".")
			{
				$files[] = $file;
			}
		}
		
		// close the path
		closedir($openDir);
		
		// return the list of files
		return $files;
	}
}

?>