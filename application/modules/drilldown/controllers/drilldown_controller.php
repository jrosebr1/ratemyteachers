<?php

class Drilldown_Controller extends CIEXT_CoreController
{
	public function __construct()
	{
		// construct the controller and load the drilldown module
		parent::__construct();
		PackageLoader::load("drilldown");
	}
	
	public function organization($letter = "ALL", $start = -1, $total = -1)
	{
		$this->load_drilldown("organization", $letter, $start, $total);
	}
	
	public function person($letter = "ALL", $start = -1, $total = -1)
	{
		$this->load_drilldown("person", $letter, $start, $total);
	}
	
	private function load_drilldown($type, $letter, $start, $total)
	{
		// grab the core constants, drilldown module constants, and
		// drilldown module type mappings
		$coreConstants = $this->config->item("core.constants");
		$modConstants = $this->config->item("drilldown.constants");
		$modMappings = $this->config->item("drilldown.type_mappings");
		
		// check to see if the drilldown type is invalid
		if (!in_array($type, $modConstants["valid_drilldown_types"]))
		{
			redirect("/", "location", "301");
		}

		// ensure that the start and total variables are valid by making
		// sure that either both 'start' and 'total' are both -1 or not
		// at all
		if (($start == -1 || $total == -1) && !($start == -1 && $total == -1))
		{
			redirect("/" . $type, "location", "301");
		}

		// handle the speical case when the letter is either not present
		// or indicates the 'ALL' character
		if (empty($letter) || $letter == "*")
		{
			$letter = "ALL";
		}
		
		// construct the path to the drilldown output file
		$drilldownPath = $modConstants["drilldown_output_path"] . "/";
		$drilldownPath .= $modMappings[$type]["path_name"] . "/";
		$drilldownPath .= $coreConstants["country_code"] . "/" . $letter . "/";
		
		// if both 'start' and 'total' are -1 then we are are looking at
		// the root
		if ($start == -1 && $total == -1)
		{
			$drilldownPath .= "root.txt";
		}
  
		// we are not looking at the root, so incorporate the 'start' and
		// 'total' into the path
		else
		{
			$drilldownPath .= ($start . "-" . $total . ".txt");
		}
		
		// if the path does not exist, then redirect back to the root level
		// of the type
		if (!file_exists($drilldownPath))
		{
			redirect("/" . $type, "location", "301");
		}
		
		// build the drilldown
		$driller = new DrilldownBuilder($drilldownPath, $type, $letter);
		$ddEntries = $driller->build();
		
		// add data to the view
		$data = array();
		$data["drilldown_alpha_filters"] = Filter::getAlphabetFilters();
		$data["drilldown_filter_letter"] = strtoupper($letter);
		$data["drilldown_type"] = $type;
		$data["drilldown_range_start"] = $driller->getRangeStart();
		$data["drilldown_range_end"] = $driller->getRangeEnd();
		$data["drilldown_entries"] = $ddEntries;
		
		// load the view
		$this->load->view("show_drilldown", $data);
	}
	
	public function generate_random_drilldown()
	{
		// grab the core constants, module constants, and type mappings
		$coreConstants = $this->config->item("core.constants");
		$modConstants = $this->config->item("drilldown.constants");
		$modMappings = $this->config->item("drilldown.type_mappings");
		
		// construct the path to the organization drilldown directory
		$orgPath = $modConstants["drilldown_output_path"] . "/";
		$orgPath .= $modMappings["organization"]["path_name"] . "/";
		$orgPath .= $coreConstants["country_code"];
		
		// construct the path to the person drilldown directory
		$personPath = $modConstants["drilldown_output_path"] . "/";
		$personPath .= $modMappings["person"]["path_name"] . "/";
		$personPath .= $coreConstants["country_code"];
		
		// generate the random organization drilldown
		$rd = new RandomDrilldownBuilder($orgPath);
		$rd->build($modConstants["num_random_org_selects"]);
		$rd->store($coreConstants["country_id"], $modMappings["organization"]);

		// generate the random person drilldown
		$rd = new RandomDrilldownBuilder($personPath);
		$rd->build($modConstants["num_random_person_selects"]);
		$rd->store($coreConstants["country_id"], $modMappings["person"]);
	}
}

?>