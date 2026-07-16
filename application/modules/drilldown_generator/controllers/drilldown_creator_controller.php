<?php

class Drilldown_Creator_Controller extends CIEXT_CoreController
{
	public function __construct()
	{
		// construct the controller and load the drilldown generator
		// module
		parent::__construct();
		PackageLoader::load("drilldown_generator");
	}
	
	public function create_drillbits()
	{
		// grab the module configuration of countries
		$modCountries = $this->config->item("drilldown_generator.countries");

		// loop over each country
		foreach ($modCountries as $countryID => $countryConfig)
		{
			echo "generating drillbits for " . $countryConfig["country_code"] . "\n";
			
			// check if the drillbit path is not empty
			if (!empty($countryConfig["drillbit_path"]))
			{
				// generate the state drillbits
				$drillbitCreator = new StateDrillbitCreator($countryConfig["drillbit_path"], $countryConfig["country_code"], $countryID);
				$drillbitCreator->createDrillBits();
			}
		}
	}
	
	public function dump()
	{
		// grab the module configuration of countries
		$modCountries = $this->config->item("drilldown_generator.countries");

		// loop over each country
		foreach ($modCountries as $countryID => $countryConfig)
		{
			echo "generating dumps for " . $countryConfig["country_code"] . "\n";
			
			// dump the organizations
			$dumper = new OrganizationDrillDown(
				$countryConfig["drilldown_output_path"],
				$countryConfig["drilldown_dump_path"],
				$countryConfig["country_code"],
				$countryID);
			$dumper->dump();

			// dump the persons
			$dumper = new PersonDrillDown(
				$countryConfig["drilldown_output_path"],
				$countryConfig["drilldown_dump_path"],
				$countryConfig["country_code"],
				$countryID);
			$dumper->dump();
			
			// dump the cities
			$dumper = new CityDrillDown(
				$countryConfig["drilldown_output_path"],
				$countryConfig["drilldown_dump_path"],
				$countryConfig["country_code"],
				$countryID);
			$dumper->dump();

			// check to see if it is okay to dump the states
			if ($countryConfig["dump_states"])
			{
				// dump the states
				$dumper = new StateDrillDown(
					$countryConfig["drillbit_path"] . "/" . $countryConfig["country_code"] . "/state.txt",
					$countryConfig["drilldown_output_path"],
					$countryConfig["drilldown_dump_path"],
					$countryConfig["country_code"],
					$countryID);
				$dumper->dump();
			}
		}
	}
}

?>