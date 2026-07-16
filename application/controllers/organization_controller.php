<?php

/**
 * @ingroup core
 *
 * @brief
 * Controller used to load the organization page.
 *
 * This controller is used to load the organization page. An organization
 * page will typically consist the organization information along with a
 * list of people that belong to the organization. The list of people is
 * also normally paginated, hence the "page" method.
 *
 * @author Adrian Rosebrock
 */

class Organization_Controller extends CIEXT_CoreController
{
	/**
	 * Construct the controller.
	 */
	public function __construct()
	{
		// create the controller then load the nearby organizations module
		// and the activity feeds module
		parent::__construct();
		PackageLoader::load("nearby_organizations");
		PackageLoader::load("activity_feeds");
	}

	/**
	 * Loads the organization page with the supplied organization ID. Since
	 * there is no page number specified, pass it off the "page" method with
	 * a default page number of 1.
	 *
	 * @param $orgID
	 *  The organization ID of the organization to be displayed.
	 */
	public function index($orgID)
	{
		// call the page method using the organization ID and the
		// first page as the arguments
		$this->page($orgID, 1);
	}
	
	/**
	 * Loads the organization with the supplied organization ID and page
	 * number.
	 *
	 * @param $orgID
	 *  The organization ID of the organization to be displayed.
	 * @param $pageNum
	 *  The page number used to offset which people are displayed based
	 *  on the total number of people in the organization and the total
	 *  number of people to display per page.
	 *
	 * @see core.php
	 */
	public function page($orgID, $pageNum)
	{
		// grab the core constants, activity feed constants, and nearby
		// organization constants, then initialize the data dictionary
		// for the view
		$coreConstants = $this->config->item("core.constants");
		$afModConstants = $this->config->item("activity_feed.constants");
		$nbModConstants = $this->config->item("nearby_organizations.constants");
		$data = array();
	
		// build the organization
		$ob = new OrganizationBuilder($orgID);
		$org = $ob->build();

		// initialize the URL builder and the organization filter
		$ub = new OrganizationURL();
		$filter = new OrganizationFilter();

		// if the organization is null, indicating that the organization
		// does not exist in the database, redirect to the next highest
		// tier
		if (empty($org))
		{
			redirect($ub->nextTier(), "location", "301");
		}
		
		// the URL is valid, so it is safe to build the URL for the
		// organization and grab the organization statistics
		$orgURL = $ub->build($org->getID());
		$orgStats = $org->getStats();
		
		// if the URL is valid but not correct, then redirect to the
		// correct URL
		if (!$ub->isCorrect($_SERVER["REQUEST_URI"], $org->getID()))
		{
			redirect($orgURL, "location", "301");
		}
		
		// get the referrer URL and get the list of persons info in
		// the organizations, then initialize the list of organization
		// persons for the view
		$refURL = $this->user->getReferrer();
		$useCleanSlate = PersonFilter::useCleanSlate($this->user->getReferrer(), $orgURL);
		$orgPersons = $org->getPersons($pageNum, $useCleanSlate);
		$data["org_persons"] = array();
		
		// create the paginator
		$pg = new Paginator($orgURL, $pageNum, $orgPersons["total_matched"], $coreConstants["persons_per_page"]);
		
		// if the page is invalid, then redirect to a valid page number
		if (($redirectURL = $pg->isRedirectable()))
		{
			redirect($redirectURL, "location", "301");
		}
		
		// loop over the persons
		foreach ($orgPersons["persons_list"] as $person)
		{
			// construct the request URL for the person
			$ub = new PersonRequestURL($person->getID());
			$reqURL = $ub->build($person->getID(), $org->getName(), $person->getName());
			
			// add the person to the view
			$data["org_persons"][] = array(
					"person_url" => $reqURL,
					"person_name" => $person->getName(),
					"person_dept" => $person->getDepartment());
		}
		
		// get the list of nearby organizations and initialize
		// the list of nearby organizations in the view
		$nb = new NearbyOrganizations($orgID);
		$nearbyOrgs = $nb->getNearbyOrgs($org->getState(), $org->getCity(), $nbModConstants["num_nearby_orgs"]);
		$data["org_nearby"] = array();
		
		// loop over the nearby organizations
		foreach ($nearbyOrgs as $nearby)
		{
			// add the nearby organization to the view
			$data["org_nearby"][] = array(
				"org_name" => ucwords(strtolower($nearby["name"])),
				"org_url" => $nearby["url"]);
		}

		// get the activity feed for the organization and initialize the
		// list of feed items in the view
		$of = new OrganizationFeed($orgID);
		$feedItems = $of->getFeed($afModConstants["num_org_feed_items"]);
		$data["feed_items"] = array();
		
		// loop over the feed items and add them to the page
		foreach ($feedItems as $feedItem)
		{
			$data["feed_items"][] = array(
				"feed_type" => $feedItem["type"],
				"feed_url" => $feedItem["url"],
				"feed_name" => $feedItem["name"],
				"feed_date" => TimeDifference::ago($feedItem["date"]));
		}
		
		// update the user session to keep track of the last organization
		// that was visited
		$this->session->set_userdata("last_org_visited", $org->getID());
		
		// add content to the page
		$name = ucwords(strtolower($org->getName()));
		$data["org_id"] = $org->getID();
		$data["org_url"] = $orgURL;
		$data["org_rss_url"] = $orgURL . ".rss";
		$data["org_name"] = $name;
		$data["org_city"] = ucwords(strtolower($org->getCity()));
		$data["org_state"] = $org->getState();
		$data["org_total_ratings"] = $org->getTotalRatings();
		$data["org_total_persons"] = $orgStats["total_persons"];
		$data["org_avg_person_score"] = $orgStats["avg_person_score"];
		$data["org_depts"] = $org->getDepartments();
		$data["org_alpha_filters"] = Filter::getAlphabetFilters();
		$data["org_cur_page"] = $pageNum;
		$data["org_pagination"] = $pg->paginate();
		$data["org_filter_letter"] = $orgPersons["filter_options"]["letter"];
		$data["org_filter_dept"] = $orgPersons["filter_options"]["dept"];
		$data["org_filter_orderby"] = $orgPersons["filter_options"]["order_by"];
		$data["org_filter_orderdir"] = $orgPersons["filter_options"]["order_dir"];
		$data["org_filter_mappings"] = json_encode($coreConstants["filter_person_mappings"]);
		$data["org_filter_mappings_revr"] = array_flip($coreConstants["filter_person_mappings"]);

		// define the breadcrumb dictionary
		$breadcrumb = array();
		$breadcrumb["type"] = "org";
		$breadcrumb["org_name"] = $name;
		$data["breadcrumb"] = $breadcrumb;
		
		// load the organiztion view
		$this->load->view("show_organization", $data);
	}
}

?>