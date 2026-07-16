<?php

/**
 * @ingroup core
 *
 * @brief
 * Controller used to flag a rebuttal via user action.
 *
 * This controller handles the requests to load the flag rebuttal page and
 * perform the actual flagging of the rebuttal which is normally done via
 * an AJAX call. This controller also has a method used to check that the
 * rebuttal ID being flagged is a valid ID. The rebuttal ID validation method
 * is used by the CodeIgniter form validation library/helper.
 *
 * @see form_validation.php
 * @author Adrian Rosebrock
 */

class Flag_Rebuttal_Controller extends CIEXT_CoreController
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
	 * Handles the request to load the page that contains the flag rebuttal
	 * form.
	 *
	 * @param $rebuttalID
	 *  The rebuttal ID of the rebuttal to be flagged.
	 */
	public function index($rebuttalID)
	{
		// grab the core constants and then initialize the data dictionary
		// for the view
		$coreConstants = $this->config->item("core.constants");
		$data = array();
				
		// build the rebuttal
		$rb = new RebuttalBuilder($rebuttalID);
		$rebuttal = $rb->build();
				
		// initialize the flag rebuttal URL builder
		$ub = new FlagRebuttalURL($rebuttalID);
		
		// if the rebuttal is empty, indicating that either the rebuttal
		// does not exist in the tables or has a status that is below the
		// viewable threshold, then redirect to the next highest tier
		if (empty($rebuttal))
		{
			redirect($ub->nextTier(), "location", "301");
		}
		
		// the URL is valid, so it is safe to grab the rating, person,
		// and organization the rebuttal belongs to, as well as build
		// the flag rebuttal URL
		$rating = $rebuttal->getRating();
		$person = $rating->getPerson();
		$org = $person->getOrganization();
		$flagURL = $ub->build($rebuttalID, $person->getName());

		// if the URL is valid but not correct, then redirect to the
		// correct URL
		if (!$ub->isCorrect($_SERVER["REQUEST_URI"], $rebuttalID))
		{
			redirect($flagURL, "location", "301");
		}
		
		// build the URL for the organization
		$ub = new OrganizationURL();
		$orgURL = $ub->build($org->getID());
		
		// build the URL for the request page
		$ub = new PersonRequestURL($person->getID());
		$reqURL = $ub->build($person->getID(), $person->getName());
		
		// build the URL for the rating itself
		$ub = new RatingURL($rating->getID());
		$ratingURL = $ub->build($rating->getID(), $person->getName());

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

		// add content to the page
		$orgName = ucwords(strtolower($org->getName()));
		$data["org_id"] = $org->getID();
		$data["org_name"] = $orgName;
		$data["org_city"] = ucwords(strtolower($org->getCity()));
		$data["org_state"] = $org->getState();
		$data["person_name"] = $person->getName();
		$data["rating_review_url"] = $ratingURL;
		$data["rebuttal_id"] = $rebuttalID;
		$data["rebuttal_comment"] = $rebuttal->getComment();
		$data["rebuttal_date"] = $rebuttal->getDate();
		$data["max_flag_reason_chars"] = $coreConstants["max_flag_reason_chars"];
		$data["org_filter_letter"] = $filterOptions["letter"];
		$data["org_filter_orderby"] = $filterOptions["order_by"];
		$data["org_filter_orderdir"] = $filterOptions["order_dir"];
		$data["org_filter_mappings"] = json_encode($coreConstants["filter_person_mappings"]);

		// define the breadcrumb dictionary
		$breadcrumb = array();
		$breadcrumb["type"] = "rebuttal";
		$breadcrumb["org_name"] = $orgName;
		$breadcrumb["org_url"] = $orgURL;
		$breadcrumb["per_dept"] = $person->getDepartment();
		$breadcrumb["per_name"] = $person->getName();
		$breadcrumb["per_req_url"] = $reqURL;
		$data["breadcrumb"] = $breadcrumb;

		// load the flag rating view
		$this->load->view("flag_rebuttal", $data);
	}

	/**
	 * Handles the incoming request to perform the actual flagging of
	 * the rebuttal which is normally done via an AJAX call.
	 */
	public function flag_rebuttal()
	{
		// initialize the JSON dictionary
		$json = array("success" => 0, "errors" => array());
		
		// check to see if the form validation failed
		if ($this->form_validation->run("flag_rebuttal") == FALSE)
		{
			// grab the errors from the form and put them in the JSON
			// dictionary
			$errors = $this->form_validation->error_array();
			$json["errors"] = $errors;
		}
		
		// otherwise, the form validated correctly
		else
		{
			// grab the flag rebuttal ID, reason, and user's IP address
			$rebuttalID = set_value("rebuttal_id");
			$reason = set_value("flag_reason");
			$userIP = $this->input->ip_address();
			
			// add the flagged rebuttal to the database
			$fa = new FlaggedRebuttalAdder($rebuttalID, $this->user->getID(), $userIP, $reason);
			$fa->add();
			
			// set the flash data that the rebuttal was successfully
			// flagged
			$this->session->set_flashdata("flag_rebuttal_successful", true);
			
			// update the JSON dictionary to indicate that adding
			// the rating was a success
			$json["success"] = 1;
		}

		// endode the JSON dictionary, set the output type to JSON and
		// show the data
		$json = json_encode($json);
		$this->output->set_content_type("application/json")->set_output($json);
	}
	
	/**
	 * Validates that the supplied rebuttal ID is indeed a valid one.
	 *
	 * @param $rebuttalID
	 *  The rebuttal ID of the rebuttal to be checked for existence and a
	 *  valid status in our database.
	 *
	 * @return
	 *  TRUE if the rebuttal is valid, FALSE if not.
	 */
	public function rebuttal_id_check($rebuttalID)
	{
		// try to build a rebuttal with the supplied rebuttal ID
		$rb = new RebuttalBuilder($rebuttalID);
		$rebuttal = $rb->build();
		
		// if the rebuttal is empty, then the rebuttal ID is invalid
		if (empty($rebuttal))
		{
			$this->form_validation->set_message("rebuttal_id_check", "The Rebuttal ID field is invalid.");
			return FALSE;
		}
		
		// otherwise, the rebuttal ID is valid
		return TRUE;
	}
}

?>