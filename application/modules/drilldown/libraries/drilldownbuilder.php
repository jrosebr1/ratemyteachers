<?php

class DrilldownBuilder
{
	/**
	 * Variable used to interface with CodeIgniter.
	 *
	 * @var $ci
	 */
	var $ci = null;
	/**
	 * Path to the input drilldown file.
	 *
	 * @var $inputPath
	 */
	var $inputPath = null;
	/**
	 * Type of drilldown that is being created (organization, person,
	 * etc).
	 *
	 * var $type
	 */
	var $type = null;
	/**
	 * Letter of the alphabet that is currently being viewed for the
	 * drilldown.
	 *
	 * @var $letter
	 */
	var $letter = null;
	/**
	 * Contents of the input drilldown file broken into an array.
	 *
	 * @var $contents
	 */
	var $contents = array();
	/**
	 * Name of the entity that starts the drilldown range.
	 *
	 * @var $rangeStart
	 */
	var $rangeStart = null;
	/**
	 * Name of the entity that ends the drilldown range.
	 *
	 * @var $rangeEnd
	 */
	var $rangeEnd = null;
	
	public function __construct($inputPath, $type, $letter)
	{
		// connect the class with CodeIgniter and store the input path,
		// type, and letter
		$this->ci = &get_instance();
		$this->inputPath = $inputPath;
		$this->type = $type;
		$this->letter = $letter;
	}
	
	public function build()
	{
		// if the input path does not exist, return nothing
		if (!file_exists($this->inputPath))
		{
			return null;
		}
		
		// list of drilldown entries parsed
		$entries = array();
		
		// read the contents of the input path and break it into an array
		$this->contents = file_get_contents($this->inputPath);
		$this->contents = explode("\n", $this->contents);
		
		// perform the root generation of links
		if ($this->contents[0] == "leaf")
		{
			// get the start and ending names in the drilldown range
			$this->rangeStart = ucwords(strtolower($this->contents[2]));
			$this->rangeEnd = ucwords(strtolower($this->contents[count($this->contents) - 1]));

			// loop over the contents of the drilldown path
			for ($enumContents = 1; $enumContents < count($this->contents); $enumContents += 4)
			{
				// initialize the drilldown entry
				$entry = array("left" => null, "right" => null);
				
				// build the left leaf node
				$leftNode = $this->buildLeafNode($enumContents);
				$entry["left"] = $leftNode;

				// check to see if there is a right node to be
				// constructed
				if (!empty($this->contents[$enumContents + 2]))
				{
					// build the right leaf node
					$rightNode = $this->buildLeafNode($enumContents + 2);
					$entry["right"] = $rightNode;
				}
				
				// store the drilldown entry
				$entries[] = $entry;
			}
		}
		
		// perform the drilldown generation of links
		else if ($this->contents[0] == "edge")
		{	
			// get the start and ending names in the drilldown range
			$this->rangeStart = ucwords(strtolower($this->contents[2]));
			$this->rangeEnd = ucwords(strtolower($this->contents[count($this->contents) - 2]));

			// loop over the contents of the drilldown path
			for ($enumContents = 1; $enumContents < count($this->contents); $enumContents += 6)
			{
				// initialize the drilldown entry
				$entry = array("left" => null, "right" => null);
			
				// build the left edge node
				$leftNode = $this->buildEdgeNode($enumContents);
				$entry["left"] = $leftNode;
				
				// check to see if there is a right node to be
				// constructed
				if (!empty($this->contents[$enumContents + 3]))
				{
					// build the right edge node
					$rightNode = $this->buildEdgeNode($enumContents + 3);
					$entry["right"] = $rightNode;
				}
				
				// store the drilldown entry
				$entries[] = $entry;
			}
		}
		
		// return the list of drilldown entries
		return $entries;
	}
	
	public function getRangeStart()
	{
		// return the name of the entity that starts the drilldown
		// range
		return $this->rangeStart;
	}
	
	public function getRangeEnd()
	{
		// return the name of the enity that ends the drilldown range
		return $this->rangeEnd;
	}
	
	private function buildLeafNode($index)
	{
		// grab the URL mappings for the drilldown type
		$modURLMappings = $this->ci->config->item("drilldown.url_mappings");
		
		// get the ID and the name
		$id = $this->contents[$index];
		$name = ucwords(strtolower($this->contents[$index + 1]));
		
		// build the URL for the current item and drilldown type
		$Class = $modURLMappings[$this->type];
		$ub = new $Class($id);
		$url = $ub->build($id);
		
		// create the leaf node to hold the information
		$leafNode = array(
			"url" => $url,
			"name" => $name);
			
		// return the leaf node
		return $leafNode;
	}
	
	private function buildEdgeNode($index)
	{
		// get the start name, end name, and range
		$startName = ucwords(strtolower($this->contents[$index]));
		$endName = ucwords(strtolower($this->contents[$index + 1]));
		$range = explode("-", $this->contents[$index + 2]);
     
		// construct the URL for the edge
		$url = "/" . $this->type . "/" . $this->letter . "/";
		$url .= trim($range[0]) . "/" . trim($range[1]);

		// create the edge node to hold the information
		$edgeNode = array(
			"url" => $url,
			"name" => $startName . " - " . $endName);

		// return the edge node
		return $edgeNode;
	}
}

?>