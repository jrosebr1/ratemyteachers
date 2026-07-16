<?php

class Sitemap_Controller extends CIEXT_CoreController
{
	public function __construct()
	{
		// construct the controller and load the sitemap module
		parent::__construct();
		PackageLoader::load("sitemap");
	}
	
	public function index()
	{
		// grab the core constants and the module loader mappings
		$coreConstants = $this->config->item("core.constants");
		$modLoaderMappings = $this->config->item("sitemap.loader_mappings");

		// grab the sitemap loader class for the current country and
		// then build the loader
		$Class = $modLoaderMappings[$coreConstants["country_code"]];
		$loader = new $Class();
		
		// check to see if the loader uses states as its main form of
		// geographic dismabiguation
		if ($loader->useStates())
		{
			echo $loader->isCityValid("Union");
		}
		
		else
		{
			echo "does not use states";
		}
	}
}

?>