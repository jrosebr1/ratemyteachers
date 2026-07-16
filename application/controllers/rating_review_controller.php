<?php

/**
 * @ingroup core
 *
 * @brief
 * Controller used to load a rating page.
 *
 * This controller is used to load a rating page, which includes the
 * comment and numerical fields as specified by the "site.php" configuration
 * file. A list of rebuttals belonging to the rating are also loaded.
 *
 * @see site.php
 * @author Adrian Rosebrock
 */

class Rating_Review_Controller extends CIEXT_CoreController
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
	 * Load the rating page with the supplied rating ID.
	 *
	 * @param $ratingID
	 *  The rating ID of the rating to be loaded and displayed along with
	 *  the list of rebuttals that belong to the rating.
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
		
		// initialize the rating URL builder
		$ub = new RatingURL($ratingID);
		
		// if the rating is empty, indicating that either the rating does
		// not exist in the tables or has a status that is below the
		// viewable threshold, then redirect to the next highest tier
		if (empty($rating))
		{
			redirect($ub->nextTier(), "location", "301");
		}
		
		// the URL is valid, so it is safe to grab the person and the
		// organization the rating belongs to, as well as build the
		// rating URL
		$person = $rating->getPerson();
		$org = $person->getOrganization();
		$ratingURL = $ub->build($ratingID, $rating->getName());

		// if the URL is valid but not correct, then redirect to the
		// correct URL
		if (!$ub->isCorrect($_SERVER["REQUEST_URI"], $ratingID))
		{
			redirect($ratingURL, "location", "301");
		}
		
		// build the URL for the organization
		$ub = new OrganizationURL();
		$orgURL = $ub->build($org->getID());
		
		// build the URL for the request page
		$ub = new PersonRequestURL($person->getID());
		$reqURL = $ub->build($person->getID(), $person->getName());
		
		// buuld the URL for the review page
		$ub = new PersonReviewURL($person->getID());
		$reviewURL = $ub->build($person->getID(), $person->getName());
				
		// build the URL for the flag rating page
		$ub = new FlagRatingURL($ratingID);
		$flagURL = $ub->build($ratingID, $person->getName());
		
		// get the next and previous ratings and initialize the
		// previous and next URLs
		$prevRating = $rating->getPreviousRating();
		$nextRating = $rating->getNextRating();
		$prevRatingURL = null;
		$nextRatingURL = null;
		
		// construct the URL for the previous rating if the previous
		// rating is not empty
		if (!empty($prevRating))
		{
			$ub = new RatingURL($prevRating->getID());
			$prevRatingURL = $ub->build($prevRating->getID(), $person->getID());
		}
		
		// construct the URL for the next rating if the next rating
		// is not empty
		if (!empty($nextRating))
		{
			$ub = new RatingURL($nextRating->getID());
			$nextRatingURL = $ub->build($nextRating->getID(), $person->getID());
		}

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

		// get the rebuttals for the rating and initialize the rebuttals
		// list for the view
		$rebuttals = $rating->getRebuttals();
		$data["rating_rebuttals"] = array();
		
		// loop over the rebuttals
		foreach ($rebuttals as $rebuttal)
		{
			// build the flag rebuttal URL
			$ub = new FlagRebuttalURL($rebuttal->getID());
			$flagRebuttalURL = $ub->build($rebuttal->getID(), $person->getName());
			
			// add each rebuttal to the view
			$data["rating_rebuttals"][] = array(
					"rebuttal_flag_url" => $flagRebuttalURL,
					"rebuttal_date" => $rebuttal->getDate(),
					"rebuttal_comment" => $rebuttal->getComment());
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
		$data["person_review_url"] = $reviewURL;
		$data["rating_id"] = $ratingID;
		$data["rating_url"] = $ratingURL;
		$data["rating_flag_url"] = $flagURL;
		$data["rating_next_url"] = $nextRatingURL;
		$data["rating_prev_url"] = $prevRatingURL;
		$data["rating_date"] = $rating->getDate();
		$data["rating_comment"] = $rating->getComment();
		$data["rating_scores"] = $rating->getScores();
		$data["rebuttal_max_comment_chars"] = $coreConstants["max_rebuttal_comment_chars"];
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
		
		// load the show ratings view
		$this->load->view("show_rating", $data);
	}
}

?>