<?php

/**
 * @ingroup core
 *
 * @brief
 * Controller used to load a person's "intermediary" page (called the
 * "request" page).
 *
 * This controller is used to load a person's intermediary page where
 * users can choose between leaving a rating for the person or looking
 * at the ratings that already exist for the person.
 *
 * @author Adrian Rosebrock
 */

class Person_Request_Controller extends CIEXT_CoreController
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
	 * Loads the intermediary (request) page for the person with the
	 * supplied person ID.
	 *
	 * @param $personID
	 *  The person ID that the intermediary page belongs to.
	 */	
	public function index($personID)
	{
		// grab the core constants and then initialize the data dictionary
		// for the view
		$coreConstants = $this->config->item("core.constants");
		$data = array();
		
		// build the person
		$pb = new PersonBuilder($personID);
		$person = $pb->build();
		
		// initialize the person URL builder
		$ub = new PersonRequestURL($personID);
		
		// if the person is empty, indicating that either the person does
		// not exist in the tables or has a status that is below the
		// viewable threshold, then redirect to the next highest tier
		if (empty($person))
		{
			redirect($ub->nextTier(), "location", "301");
		}
		
		// the URL is valid, so it is safe to grab the organization the
		// person belongs to and build the URL for the person
		$org = $person->getOrganization();
		$personURL = $ub->build($personID, $org->getName(), $person->getName());

		// if the URL is valid but not correct, then redirect to the
		// correct URL
		if (!$ub->isCorrect($_SERVER["REQUEST_URI"], $personID))
		{
			redirect($personURL, "location", "301");
		}

		// build the URL for the oganization
		$ub = new OrganizationURL();
		$orgURL = $ub->build($org->getID());
		
		// build the URL for the rating page
		$ub = new PersonRateURL($personID);
		$rateURL = $ub->build($personID, $person->getName());
		
		// build the URL for the review page
		$ub = new PersonReviewURL($personID);
		$reviewURL = $ub->build($personID, $person->getName());

		// get the filter options since they will be needed if the
		// user clicks on the 'Department' link in the breadcrumb
		$filterOptions = $this->session->userdata("person_filter_options");
		$filterOptions = (array)json_decode($filterOptions);

		// if the filter options are empty, then default them to a
		// clean slate
		if (empty($filterOptions))
		{
			$filterOptions = PersonFilterParser::cleanSlate($org->getID());
		}
		
		// update the user session to keep track of the last organization
		// that was visited
		$this->session->set_userdata("last_org_visited", $org->getID());

		// add content to the page
		$orgName = ucwords(strtolower($org->getName()));
		$data["org_id"] = $org->getID();
		$data["org_name"] = $orgName;
		$data["org_city"] = ucwords(strtolower($org->getCity()));
		$data["org_state"] = $org->getState();
		$data["person_name"] = $person->getName();
		$data["person_rate_url"] = $rateURL;
		$data["person_review_url"] = $reviewURL;
		$data["org_filter_letter"] = $filterOptions["letter"];
		$data["org_filter_orderby"] = $filterOptions["order_by"];
		$data["org_filter_orderdir"] = $filterOptions["order_dir"];
		$data["org_filter_mappings"] = json_encode($coreConstants["filter_person_mappings"]);

		// define the breadcrumb dictionary
		$breadcrumb = array();
		$breadcrumb["type"] = "request";
		$breadcrumb["org_name"] = $orgName;
		$breadcrumb["org_url"] = $orgURL;
		$breadcrumb["per_dept"] = $person->getDepartment();
		$breadcrumb["per_name"] = $person->getName();
		$data["breadcrumb"] = $breadcrumb;
		
		// load the person request view
		$this->load->view("show_person_request", $data);
	}
}

?>