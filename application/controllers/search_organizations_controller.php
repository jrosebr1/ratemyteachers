<?php

/**
 * @ingroup core
 *
 * @brief
 * Controller used to load the organization search results.
 *
 * This controller is used to load the organization search results provided
 * that a search query was supplied. Organizations are filtered based on
 * the POST data supplied and displayed to the user.
 *
 * @see organizationfilter.php
 * @author Adrian Rosebrock
 */

class Search_Organizations_Controller extends CIEXT_CoreController
{
	/**
	 * Construct the controller.
	 */
	public function __construct()
	{
		// create the controller
		parent::__construct();
	}
	
	/**
	 * Load the page used to display the organization search results.
	 */
	public function index()
	{
		// initialize the list of returned organizations as well as the data
		// dictionary for the template
		$orgs = array();
		$data = array("search_results" => array());

		if ($this->form_validation->run("search") == TRUE)
		{
			// grab the search query and filter the organizations
			$query = $this->input->post("search_org");
			$filter = new OrganizationFilter();
			$filter->addContains($query);
			$orgs = $filter->apply();
		}
		
		// loop over each organization
		foreach ($orgs as $org)
		{
			// construct a URL for the organization
			$ub = new OrganizationURL();
			$orgURL = $ub->build($org->getID());
		
			// add each organization to the search results
			$data["search_results"][] = array(
					"org_name" => ucwords(strtolower($org->getName())),
					"org_city" => ucwords(strtolower($org->getCity())),
					"org_state" => $org->getState(),
					"org_url" => $orgURL);
		}
		
		// add general information to the data dictionary
		$data["num_results"] = count($orgs);
		$data["query"] = isset($query) ? $query : "";
		
		// load the search results view
		$this->load->view("search_organizations_results", $data);
	}
}

?>