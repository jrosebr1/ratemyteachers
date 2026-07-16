<?php

class Recently_Searched_Controller extends CIEXT_CoreController
{
	public function __construct()
	{
		// construct the controller and load the recently searched
		// module
		parent::__construct();
		PackageLoader::load("recently_searched");
	}
	
	public function generate_recently_searched()
	{
		// generate the recently searched for organizations
		$rs = new RecentlySearchedOrg();
		$rs->generate();
		
		// generate the recently searched for persons
		$rs = new RecentlySearchedPerson();
		$rs->generate();
	}
}

?>