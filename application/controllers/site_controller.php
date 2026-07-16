<?php

/**
 * @mainpage Reactor Core, v 0.1
 *
 * @section Introduction
 * This manual documents the Reactor Core rating platform. The platform
 * is built using CodeIgniter 2.0.2, with the assumption that it will
 * be installed in a Linux, mySQL, Apache, PHP environment.
 *
 * It is important to note that while this platform is functional and
 * useable out of the box, it is <b>NOT</b> a solution for every
 * single rating website that will be created. It is simply the barebones
 * "core" of such a website, providing features such as search, 
 * registration, user functions (logging in/logging out), adding new
 * fields to the form used to collect ratings, flagging ratings, etc.
 *
 * @section The Rating Platform
 * The platform itself is a three tiered platform, with an optional
 * fourth tier, built generically so that the entire site is configurable
 * by editing only a handful of well documented configuration files, thus
 * making it easy to rapidly introduce new rating websites.
 *
 * The code is organized into modules, making extending the core simple.
 * The main module of the rating platform is called the "core". It is
 * treated like a module, although without it, the rating platform
 * would be absolutely useless. Consider it to be like the kernel of
 * an operating system.
 *
 * The first of the four tiers is an Organization page, synonymous
 * with a school page. The Organization page consists of a list of
 * Persons (synonymous with teachers), mechanisms to filter the list
 * of Persons by name (alphabetical list, department, etc), and
 * aggregate scores for the Organization.
 *
 * The second tier is the Person page which consists of a list of
 * Ratings for the Person. Aggregate scores are also calculated for
 * the Person based on the values supplied in their Ratings.
 *
 * The third tier is the Rating. A Rating is simply the numerical
 * values, the attribute values, and the comment collected by the
 * platform. For example, a numerical value would be asking the
 * user to rate a Person on a scale of 1-5 on the "helpfulness" of
 * a Person. An attribute value is asking what grade the user
 * received from the Person while taking their class. And a comment
 * is simply a text field for the user to enter their thoughts and
 * opinions on the Person.
 *
 * The fourth, optional tier is the Rebuttal. A Rebuttal is nothing
 * more than a comment on a Rating. If a user posts a Rating and
 * another user disagrees with the Rating, they can post a Rebuttal
 * underneath the rating in question. This is not necessary for all
 * ratings websites, but it was important to include in the platform.
 */

/**
 * @defgroup core Core: main module of the rating platform.
 * @ingroup core
 *
 * @brief
 * Controller to handle all the basic "one-and-off" pages.
 *
 * This controller handles all the requests for the basic pages that
 * are needed for any rating website, such as a homepage, FAQ page,
 * contact page, and legal page.
 *
 * @author Adrian Rosebrock
 */

class Site_Controller extends CIEXT_CoreController
{
	/**
	 * Construct the controller.
	 */
	public function __construct()
	{
		// construct the controller
		parent::__construct();
	}

	/**
	 * Handle the request for the homepage.
	 */
	public function index()
	{
		// load the drilldown and the recently searched module
		PackageLoader::load("drilldown");
		PackageLoader::load("recently_searched");
		
		// grab the country ID, and the drilldown module constants and
		// type mappings
		$countryID = $this->config->item("core.country_id");
		$modConstants = $this->config->item("drilldown.constants");
		$modTypes = $this->config->item("drilldown.type_mappings");
		
		// grab random drilldown organization and person entries
		$orgEntries = RandomDrilldownBuilder::grabRandom($countryID, $modTypes["organization"]["type"], $modConstants["num_org_homepage_grabs"]);
		$personEntries = RandomDrilldownBuilder::grabRandom($countryID, $modTypes["person"]["type"], $modConstants["num_person_homepage_grabs"]);

		// initialize the list of recent activity
		$recentActivity = array();
		
		// grab the recently searched for and rated organizations
		// and store them in the recentl activity list
		$rs = new RecentlySearchedOrg();
		$recentActivity = array_merge($recentActivity, $rs->getRecentlySearched());
		$recentActivity = array_merge($recentActivity, $rs->getRecentlyRated());

		// grab the recently searched for and rated persons and
		// store them in the recent activity list
		$rs = new RecentlySearchedPerson();
		$recentActivity = array_merge($recentActivity, $rs->getRecentlySearched());
		$recentActivity = array_merge($recentActivity, $rs->getRecentlyRated());
		
		// randomize the recent activity
		shuffle($recentActivity);
		
		// add data to the view
		$data = array();
		$data["homepage_footer_drilldown"] = array(
			"org_drilldown_url" => $orgEntries[0],
			"person_drilldown_url" => $personEntries[0]);
		$data["homepage_search_drilldown"] = array(
			"org_drilldown_url" => $orgEntries[1],
			"person_drilldown_url" => $personEntries[1]);
		$data["recent_activity"] = $recentActivity;
		
		// load the homepage view
		$this->load->view("homepage", $data);
	}

	/**
	 * Handle the request for the FAQ page.
	 */
	public function faq()
	{
		// load the FAQ view
		$this->load->view("faq");
	}

	/**
	 * Handle the request for the contact page.
	 */
	public function contact()
	{
		// load the contact view
		$this->load->view("contact");
	}

	/**
	 * Handle the request for the legal page.
	 */
	public function legal()
	{
		// load the legal view
		$this->load->view("legal");
	}
}

?>