<?php

/**
 * @ingroup core
 *
 * @brief
 * Controller used to load the rating form page for a person.
 *
 * This controller is used to load the rating form page. This form
 * will typically consist of numerical fields as well as a comment
 * field. The fields for the rating form can be enabled and disabled
 * by configuring the "site.php" configuration file.
 *
 * @see site.php
 * @author Adrian Rosebrock
 */

class Person_Rating_Controller extends CIEXT_CoreController
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
	 * Loads the rating page for the person with the supplied person ID.
	 *
	 * @param $personID
	 *  The person ID that the rating being added will belong to.
	 */
	public function index($personID)
	{
		// grab the core constants and the numerical fields, then
		// initialize the data dictionary for the view
		$coreConstants = $this->config->item("core.constants");
		$siteNumericalFields = $this->config->item("site.ratings.numerical_fields");
		$data = array();
		
		// build the person
		$pb = new PersonBuilder($personID);
		$person = $pb->build();

		// initialize the person URL builder
		$ub = new PersonRateURL($personID);
		
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
		$personURL = $ub->build($personID, $person->getName());

		// if the URL is valid but not correct, then redirect to the
		// correct URL
		if (!$ub->isCorrect($_SERVER["REQUEST_URI"], $personID))
		{
			redirect($personURL, "location", "301");
		}
		
		// build the URL for the oganization
		$ub = new OrganizationURL();
		$orgURL = $ub->build($org->getID());
		
		// build the URL for the request page
		$ub = new PersonRequestURL($personID);
		$reqURL = $ub->build($personID, $person->getName());
		
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
		
		// initialize the numerical fields list in the data dictionary
		// for the view
		$data["rating_numerical_fields"] = array();
		
		// loop over the numerical field names
		foreach ($siteNumericalFields as $field => $fieldInfo)
		{
			// initialize the range of values for the current field
			// and get the start, end, and increment values so the
			// range list can be generated
			$range = array();
			$start = $fieldInfo["range"]["start"];
			$end = $fieldInfo["range"]["end"];
			$incr = $fieldInfo["range"]["incr"];
			
			// loop over the possible range values and store them in
			// the range list
			for ($enumRange = $start; $enumRange <= $end; $enumRange += $incr)
			{
				$range[] = $enumRange;
			}
			
			// add the numerical field information to the view
			$data["rating_numerical_fields"][] = array(
					"field_name" => $field,
					"field_label" => $fieldInfo["field_label"],
					"field_range" => $range);
		}
		
		// if the person was just added to the database, update the
		// view data to indicate so and remove the session variable
		if ($this->session->userdata("add_person_successful"))
		{
			$data["person_just_added"] = true;
			$this->session->unset_userdata("add_person_successful");
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
		$data["person_id"] = $person->getID();
		$data["person_name"] = $person->getName();
		$data["person_review_url"] = $reviewURL;
		$data["rating_numerical_field_names"] = implode(",", array_keys($siteNumericalFields));
		$data["rating_max_comment_chars"] = $coreConstants["max_rating_comment_chars"];
		$data["org_filter_letter"] = $filterOptions["letter"];
		$data["org_filter_orderby"] = $filterOptions["order_by"];
		$data["org_filter_orderdir"] = $filterOptions["order_dir"];
		$data["org_filter_mappings"] = json_encode($coreConstants["filter_person_mappings"]);
		
		// define the breadcrumb dictionary
		$breadcrumb = array();
		$breadcrumb["type"] = "rate";
		$breadcrumb["org_name"] = $orgName;
		$breadcrumb["org_url"] = $orgURL;
		$breadcrumb["per_dept"] = $person->getDepartment();
		$breadcrumb["per_name"] = $person->getName();
		$breadcrumb["per_req_url"] = $reqURL;
		$data["breadcrumb"] = $breadcrumb;
		
		// load the rating form view
		$this->load->view("add_rating", $data);
	}
}

?>