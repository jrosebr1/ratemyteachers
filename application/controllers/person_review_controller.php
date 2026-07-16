<?php

/**
 * @ingroup core
 *
 * @brief
 * Controller used to load the ratings that belong to a person.
 *
 * This controller handles the request to load the ratings that belong
 * to a person. Just like the list of people that belong to an organization,
 * the list of ratings that belong to a person is paginated. Aggregate
 * scores for the person will be calculated based on the configured numerical
 * fields. The list of ratings will normally consist of at least a text
 * comment, as well as the numerical scores for the particular rating. The
 * numerical fields as well as comment fields can be configured by editing
 * the "site.php" configuration file.
 *
 * @see site.php
 * @author Adrian Rosebrock
 */

class Person_Review_Controller extends CIEXT_CoreController
{
	/**
	 * Construct the controller.
	 */
	public function __construct()
	{
		// create the controller and load the activity feeds module
		parent::__construct();
		PackageLoader::load("activity_feeds");
	}
	
	/**
	 * Loads the person review page with the supplied personID ID. Since
	 * there is no page number specified, pass it off the "page" method
	 * with a default page number of 1.
	 *
	 * @param $personID
	 *  The person ID of the person's ratings to be displayed.

	 */
	public function index($personID)
	{
		// call the page method using the person ID and the first page
		// as the arguments
		$this->page($personID, 1);
	}
	
	/**
	 * Loads the person's review page with the supplied person ID and
	 * page number.
	 *
	 * @param $personID
	 *  The personID of the person's ratings to be displayed.
	 * @param $pageNum
	 *  The page number used to offset which ratings are displayed based
	 *  on the total number of ratings the person has and the total
	 *  number of ratings to display per page.
	 *
	 * @see core.php
	 */
	public function page($personID, $pageNum)
	{
		// grab the core constants and the activity feed constants, then
		// initialize the data dictionary for the view
		$coreConstants = $this->config->item("core.constants");
		$afModConstants = $this->config->item("activity_feed.constants");
		$data = array();
		
		// build the person
		$pb = new PersonBuilder($personID);
		$person = $pb->build();

		// initialize the person URL builder
		$ub = new PersonReviewURL($personID);
		
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
		$reviewURL = $ub->build($personID, $person->getName());
		
		// if the URL is valid but not correct, then redirect to the
		// correct URL
		if (!$ub->isCorrect($_SERVER["REQUEST_URI"], $personID))
		{
			redirect($reviewURL, "location", "301");
		}
		
		// build the URL for the organization
		$ub = new OrganizationURL();
		$orgURL = $ub->build($org->getID());

		// build the URL for the request page
		$ub = new PersonRequestURL($personID);
		$reqURL = $ub->build($personID, $person->getName());
		
		// build the URL for the rate page
		$ub = new PersonRateURL($personID);
		$rateURL = $ub->build($personID, $person->getName());

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
		
		// get the list of ratings info for the person and then initialize
		// the list of ratings for the view
		$personRatings = $person->getRatings($pageNum);
		$data["person_ratings"] = array();

		// create the paginator
		$pg = new Paginator($reviewURL, $pageNum, $personRatings["total_matched"], $coreConstants["ratings_per_page"]);
		
		// if the page is invalid, then redirect to a valid page number
		if (($redirectURL = $pg->isRedirectable()))
		{
			redirect($redirectURL, "location", "301");
		}
		
		// loop over the ratings
		foreach ($personRatings["ratings_list"] as $rating)
		{
			// construct the rating URL
			$ub = new RatingURL($rating->getID());
			$ratingURL = $ub->build($rating->getID(), $person->getName());
			
			// construct the flag rating URL
			$ub = new FlagRatingURL($rating->getID());
			$flagURL = $ub->build($rating->getID(), $person->getName());
			
			$data["person_ratings"][] = array(
					"rating_url" => $ratingURL,
					"rating_flag_url" => $flagURL,
					"rating_date" => $rating->getDate(),
					"rating_comment" => $rating->getComment(),
					"rating_scores" => $rating->getScores()
				);
		}
		
		// grab the aggregate rating scores for the person (based
		// on the numerical rating fields) and initialize the
		// aggregate list for the view
		$aggrScores = $person->getAggregates();
		$data["person_aggr_scores"] = array();
		
		// loop over the aggregate scores and add them to the view
		foreach ($aggrScores as $aggr)
		{
			$data["person_aggr_scores"][] = array(
				"aggr_name" => $aggr["aggr_name"],
				"aggr_score" => $aggr["aggr_score"]);
		}

		// get the activity feed for the person and initialize the
		// list of feed items in the view
		$af = new PersonFeed($personID, $person->getName());
		$feedItems = $af->getFeed($afModConstants["num_person_feed_items"]);
		$data["feed_items"] = array();

		// loop over the feed items and add them to the page
		foreach ($feedItems as $feedItem)
		{
			$data["feed_items"][] = array(
				"feed_type" => $feedItem["type"],
				"feed_url" => $feedItem["url"],
				"feed_date" => TimeDifference::ago($feedItem["date"]));
		}
		
		// update the user session to keep track of the last organization
		// that was visited
		$this->session->set_userdata("last_org_visited", $org->getID());

		// add content to the page
		$orgName = ucwords(strtolower($org->getName()));
		$data["org_id"] = $org->getID();
		$data["org_name"] = $orgName;
		$data["org_city"] = ucwords(strtolower($org->getCity()));
		$data["person_id"] = $person->getID();
		$data["person_name"] = $person->getName();
		$data["person_total_ratings"] = $person->getTotalRatings();
		$data["person_rate_url"] = $rateURL;
		$data["person_cur_page"] = $pageNum;
		$data["person_rating_pagination"] = $pg->paginate();
		$data["org_filter_letter"] = $filterOptions["letter"];
		$data["org_filter_orderby"] = $filterOptions["order_by"];
		$data["org_filter_orderdir"] = $filterOptions["order_dir"];
		$data["org_filter_mappings"] = json_encode($coreConstants["filter_person_mappings"]);

		// define the breadcrumb dictionary
		$breadcrumb = array();
		$breadcrumb["type"] = "review";
		$breadcrumb["org_name"] = $orgName;
		$breadcrumb["org_url"] = $orgURL;
		$breadcrumb["per_dept"] = $person->getDepartment();
		$breadcrumb["per_name"] = $person->getName();
		$breadcrumb["per_req_url"] = $reqURL;
		$data["breadcrumb"] = $breadcrumb;
		
		// load the show person ratings view
		$this->load->view("show_person_ratings", $data);
	}
}

?>