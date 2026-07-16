<?php

/**
 * @ingroup core
 *
 * @brief
 * Controller used to flag a rating via user action.
 *
 * This controller handles the requests to load the flag rating page and
 * perform the actual flagging of the rating which is normally done via
 * an AJAX call. This controller also has a method used to check that the
 * rating ID being flagged is a valid ID. The rating ID validation method
 * is used by the CodeIgniter form validation library/helper.
 *
 * @see form_validation.php
 * @author Adrian Rosebrock
 */

class Flag_Rating_Controller extends CIEXT_CoreController
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
	 * Handles the incoming request to load the page that contains the flag
	 * rating form.
	 *
	 * @param $ratingID
	 *  The rating ID of the rating to be flagged.
	 */
	public function index($ratingID)
	{
		// grab the core constants and then initialize the data dictionary
		// for the view
		$coreConstants = $this->config->item("core.constants");
		$data = array();
				
		// build the rating
		$rb = new RatingBuilder($ratingID);
		$rating = $rb->build();
		
		// initialize the flag rating URL builder
		$ub = new FlagRatingURL($ratingID);
		
		// if the rating is empty, indicating that either the rating does
		// not exist in the tables or has a status that is below the
		// viewable threshold, then redirect to the next highest tier
		if (empty($rating))
		{
			redirect($ub->nextTier(), "location", "301");
		}
		
		// the URL is valid, so it is safe to grab the person and the
		// organization the rating belongs to, as well as build the
		// flag rating URL
		$person = $rating->getPerson();
		$org = $person->getOrganization();
		$flagURL = $ub->build($ratingID, $person->getName());

		// if the URL is valid but not correct, then redirect to the
		// correct URL
		if (!$ub->isCorrect($_SERVER["REQUEST_URI"], $ratingID))
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
		$ub = new RatingURL($ratingID);
		$ratingURL = $ub->build($ratingID, $person->getName());

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
		$data["rating_id"] = $ratingID;
		$data["rating_review_url"] = $ratingURL;
		$data["rating_date"] = $rating->getDate();
		$data["rating_comment"] = $rating->getComment();
		$data["rating_scores"] = $rating->getScores();
		$data["max_flag_reason_chars"] = $coreConstants["max_flag_reason_chars"];
		$data["org_filter_letter"] = $filterOptions["letter"];
		$data["org_filter_orderby"] = $filterOptions["order_by"];
		$data["org_filter_orderdir"] = $filterOptions["order_dir"];
		$data["org_filter_mappings"] = json_encode($coreConstants["filter_person_mappings"]);

		// define the breadcrumb dictionary
		$breadcrumb = array();
		$breadcrumb["type"] = "rating";
		$breadcrumb["org_name"] = $orgName;
		$breadcrumb["org_url"] = $orgURL;
		$breadcrumb["per_dept"] = $person->getDepartment();
		$breadcrumb["per_name"] = $person->getName();
		$breadcrumb["per_req_url"] = $reqURL;
		$data["breadcrumb"] = $breadcrumb;

		// load the flag rating view
		$this->load->view("flag_rating", $data);
	}
	
	/**
	 * Handles the incoming request to perform the actual flagging of
	 * the rating which is normally done via an AJAX call.
	 */
	public function flag_rating()
	{
		// initialize the JSON dictionary
		$json = array("success" => 0, "errors" => array());
		
		// check to see if the form validation failed
		if ($this->form_validation->run("flag_rating") == FALSE)
		{
			// grab the errors from the form and put them in the JSON
			// dictionary
			$errors = $this->form_validation->error_array();
			$json["errors"] = $errors;
		}
		
		// otherwise, the form validated correctly
		else
		{
			// grab the flag rating ID, reason, and user's IP address
			$ratingID = set_value("rating_id");
			$reason = set_value("flag_reason");
			$userIP = $this->input->ip_address();
			
			// add the flagged rating to the database
			$fa = new FlaggedRatingAdder($ratingID, $this->user->getID(), $userIP, $reason);
			$fa->add();
			
			// set the flash data that the rating was successfully
			// flagged
			$this->session->set_flashdata("flag_rating_successful", true);
			
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
	 * Validates that the supplied rating ID is indeed a valid one.
	 *
	 * @param $ratingID
	 *  The rating ID of the rating to be checked for existence and a
	 *  valid status in our database.
	 *
	 * @return
	 *  TRUE if the rating is valid, FALSE if not.
	 */
	public function rating_id_check($ratingID)
	{
		// try to build a rating with the supplied rating ID
		$rb = new RatingBuilder($ratingID);
		$rating = $rb->build();
		
		// if the rating is empty, then the rating ID is invalid
		if (empty($rating))
		{
			$this->form_validation->set_message("rating_id_check", "The Rating ID field is invalid.");
			return FALSE;
		}
		
		// otherwise, the rating ID is valid
		return TRUE;
	}
}

?>