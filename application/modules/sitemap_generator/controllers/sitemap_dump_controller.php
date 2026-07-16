<?php

class Sitemap_Dump_Controller extends CIEXT_CoreController
{
	public function __construct()
	{
		// construct the controller and load the sitemap generator
		// module
		parent::__construct();
		PackageLoader::load("sitemap_generator");
	}
	
	public function dump()
	{
		// grab the module configuration of countries
		$modCountries = $this->config->item("sitemap_generator.countries");

		// loop over each country
		foreach ($modCountries as $countryID => $countryConfig)
		{
			// check to see if the organization and persons should be
			// dumped
			if (!empty($countryConfig["orgs_persons_path"]))
			{
				// dump the organization and person information
				$dumper = new OrganizationPersonDump($countryID, $countryConfig["orgs_persons_path"]);
				$dumper->dump();
			}
			
			// check to see if the ratings should be dumped
			if (!empty($countryConfig["ratings_path"]))
			{
				// dump the rating information
				$dumper = new RatingDump($countryID, $countryConfig["ratings_path"]);
				$dumper->dump();
			}
		}
	}
}

?>