<?php

/**
 * @ingroup core
 *
 * @brief
 * Helper used to load modules.
 *
 * This helper is used to load modules into the system. A module is
 * a collection of classes that are used together to create a feature
 * on the rating website. The "core" of the rating system is considered
 * a module, similar to a kernel in an operating system. It is the first
 * module loaded, and initially, it is the only module loaded unless a
 * developer defines more modules.
 *
 * To define a new module, edit this file and create a method with the
 * following signature:
 * @par public static function load_&lt;module_name&gt;().
 *
 * Inside of that method place all the "require_once" statements needed
 * to load the classes for your new module. From there, you can load your
 * module with the following code:
 * @par PackageLoader::load("<module_name>");
 *
 * @author Adrian Rosebrock
 */

class PackageLoader
{
	/**
	 * Loads a module with the name $package.
	 *
	 * @param $package
	 *  The name of the package to be loaded.
	 */
	public static function load($package)
	{
		// if the loader method exists for the package, then load
		// the supplied package
		if (method_exists("PackageLoader", "load_" . $package))
		{
			call_user_func("PackageLoader::load_" . $package);
		}
	}
	
	/**
	 * Loads the core of the rating system. Think of this module as the
	 * kernel of an operating system.
	 */
	public static function load_core()
	{
		// define the base path for the libraries
		$base = APPPATH . "/modules/core/libraries";
		
		// load the classes used for user sessions
		require_once($base . "/session" . EXT);
		require_once($base . "/usersession" . EXT);
		
		// load the classes used to build URLs for organizations,
		// persons, and ratings
		require_once($base . "/url" . EXT);
		require_once($base . "/organizationurl" . EXT);
		require_once($base . "/personrequesturl" . EXT);
		require_once($base . "/personrateurl" . EXT);
		require_once($base . "/personreviewurl" . EXT);
		require_once($base . "/ratingurl" . EXT);
		require_once($base . "/flagratingurl" . EXT);
		require_once($base . "/flagrebuttalurl" . EXT);
		
		// load the organization, person, and rating classes that
		// are used to generically represent different tiers within
		// the core system
		require_once($base . "/rateable" . EXT);
		require_once($base . "/rateablebuilder" . EXT);
		require_once($base . "/organization" . EXT);
		require_once($base . "/person" . EXT);
		require_once($base . "/rating" . EXT);
		require_once($base . "/rebuttal" . EXT);
		require_once($base . "/organizationbuilder" . EXT);
		require_once($base . "/personbuilder" . EXT);
		require_once($base . "/ratingbuilder" . EXT);
		require_once($base . "/rebuttalbuilder" . EXT);
		
		// load the person and rating classes that are used to add
		// users, people, and ratings into the tables
		require_once($base . "/adder" . EXT);
		require_once($base . "/useradder" . EXT);
		require_once($base . "/personadder" . EXT);
		require_once($base . "/ratingadder" . EXT);
		require_once($base . "/rebuttaladder". EXT);
		
		// load the organization and person filters that are used
		// to filter out certain organizations/persons based on a
		// set of rules
		require_once($base . "/filter" . EXT);
		require_once($base . "/filterparser" . EXT);
		require_once($base . "/organizationfilter" . EXT);
		require_once($base . "/personfilter" . EXT);
		require_once($base . "/personfilterparser" . EXT);
		require_once($base . "/ratingfilter". EXT);
		require_once($base . "/rebuttalfilter". EXT);
		require_once($base . "/paginator" . EXT);
		
		// load the classes used to flag ratings and rebuttals
		require_once($base . "/flaggedratingadder". EXT);
		require_once($base . "/flaggedrebuttaladder". EXT);
		
		// load the classes used to calculate statistics throughout
		// the site
		require_once($base . "/stats" . EXT);
		require_once($base . "/organizationstats" . EXT);
		require_once($base . "/sync" . EXT);
		require_once($base . "/syncorganization" . EXT);
		require_once($base . "/syncperson" . EXT);
		
		// load miscellaneous classes that are used by the core
		require_once($base . "/country" . EXT);
		require_once($base . "/departmentfetcher" . EXT);
	}
	
	/**
	 * Loads the nearby organizations module.
	 */
	public static function load_nearby_organizations()
	{
		// define the base path for the nearby organizations module
		$base = APPPATH . "/modules//nearby_organizations/libraries";
		
		// load the files needed for the module
		require_once($base . "/nearbyorganizations" . EXT);

		// load the module configuration
		$ci = &get_instance();
		$ci->load->config("../modules/nearby_organizations/config/config");
	}
	
	/**
	 * Loads the activity feeds module.
	 */
	public static function load_activity_feeds()
	{
		// define the base path for the activity feeds module
		$base = APPPATH . "/modules/activity_feeds/libraries";
		
		// load the files needed for the module
		require_once($base . "/activityfeed" . EXT);
		require_once($base . "/organizationfeed" . EXT);
		require_once($base . "/personfeed" . EXT);

		// load the module configuration
		$ci = &get_instance();
		$ci->load->config("../modules/activity_feeds/config/config");
	}
	
	/**
	 * Loads the drilldown module.
	 */
	public static function load_drilldown()
	{
		// define the base path for the RSS feeds module
		$base = APPPATH . "/modules/drilldown/libraries";
		
		// load the files needed for the module
		require_once($base . "/drilldownbuilder" . EXT);
		require_once($base . "/randomdrilldownbuilder" . EXT);

		// load the module configuration
		$ci = &get_instance();
		$ci->load->config("../modules/drilldown/config/config");
	}
	
	/**
	 * Loads the drilldown generator module.
	 */
	public static function load_drilldown_generator()
	{
		// define the base path for the RSS feeds module
		$base = APPPATH . "/modules/drilldown_generator/libraries";
		
		// load the files needed for the module
		require_once($base . "/drillbitcreator" . EXT);
		require_once($base . "/statedrillbitcreator" . EXT);
		require_once($base . "/drilldowndumper" . EXT);
		require_once($base . "/organizationdrilldown" . EXT);
		require_once($base . "/persondrilldown" . EXT);
		require_once($base . "/statedrilldown" . EXT);
		require_once($base . "/citydrilldown" . EXT);

		// load the module configuration
		$ci = &get_instance();
		$ci->load->config("../modules/drilldown_generator/config/config");
	}
	
	/**
	 * Loads the RSS feeds module.
	 */
	public static function load_rss_feeds()
	{
		// define the base path for the RSS feeds module
		$base = APPPATH . "/modules/rss_feeds/libraries";
		
		// load the files needed for the module
		require_once($base . "/rssfeed" . EXT);
		require_once($base . "/homepagefeed" . EXT);
		require_once($base . "/organizationfeed" . EXT);
		require_once($base . "/personfeed" . EXT);

		// load the module configuration
		$ci = &get_instance();
		$ci->load->config("../modules/rss_feeds/config/config");
	}
	
	/**
	 * Loads the blog module.
	 */
	public static function load_blog()
	{
		// define the base path for the blog module
		$base = APPPATH . "/modules/blog/libraries";
		
		// load the files needed for the module
		require_once($base . "/blog" . EXT);
		require_once($base . "/blogurl" . EXT);
		
		// load the module configuration
		$ci = &get_instance();
		$ci->load->config("../modules/blog/config/config");
	}
	
	/**
	 * Loads the contact importer module.
	 */
	public static function load_contact_importer()
	{
		// define the base path for the contact importer module
		$base = APPPATH . "/modules/contact_importer/libraries";
		
		// load the files needed for the module
		require_once($base . "/contactgrabber" . EXT);
		require_once($base . "/openinviter/openinviter" . EXT);

		// load the module configuration
		$ci = &get_instance();
		$ci->load->config("../modules/contact_importer/config/config");
	}
	
	/**
	 * Loads the sitemap module.
	 */
	public static function load_sitemap()
	{
		// define the base path for the sitemap module
		$base = APPPATH . "/modules/sitemap/libraries";
		
		// load the files needed for the module
		require_once($base . "/sitemaploader" . EXT);
		require_once($base . "/stateloader" . EXT);

		// load the module configuration
		$ci = &get_instance();
		$ci->load->config("../modules/sitemap/config/config");
	}
	
	/**
	 * Loads the sitemap generator module.
	 */
	public static function load_sitemap_generator()
	{
		// define the base path for the sitemap module
		$base = APPPATH . "/modules/sitemap_generator/libraries";
		
		// load the files needed for the module
		require_once($base . "/sitemapdump" . EXT);
		require_once($base . "/organizationpersondump" . EXT);
		require_once($base . "/ratingdump" . EXT);

		// load the module configuration
		$ci = &get_instance();
		$ci->load->config("../modules/sitemap_generator/config/config");
	}
	
	/**
	 * Loads the recently searched module.
	 */
	public static function load_recently_searched()
	{
		// define the base path for the recently searched module
		$base = APPPATH . "/modules/recently_searched/libraries";
		
		// load the files needed for the module
		require_once($base . "/recentlysearched" . EXT);
		require_once($base . "/recentlysearchedorg" . EXT);
		require_once($base . "/recentlysearchedperson" . EXT);

		// load the module configuration
		$ci = &get_instance();
		$ci->load->config("../modules/recently_searched/config/config");
	}
	
	/**
	 * Loads the KPI generator module.
	 */
	public static function load_kpi_generator()
	{
		// define the base path for the KPI module
		$base = APPPATH . "/modules/kpi_generator/libraries";
		
		// load the files needed for the module
		require_once($base . "/kpigenerator" . EXT);

		// load the module configuration
		$ci = &get_instance();
		$ci->load->config("../modules/kpi_generator/config/config");
	}
	
	/**
	 * Loads the pending review remover module.
	 */
	public static function load_pending_review_remover()
	{
		// define the base path for the pending review remover module
		$base = APPPATH . "/modules/pending_review_remover/libraries";
		
		// load the files needed for the module
		require_once($base . "/queue" . EXT);
		require_once($base . "/pendingreviewqueue" . EXT);
		require_once($base . "/ratingremover" . EXT);

		// load the module configuration
		$ci = &get_instance();
		$ci->load->config("../modules/pending_review_remover/config/config");
	}
	
	/**
	 * Loads the numerical rating only rating remover module.
	 */
	public static function load_numerical_only_remover()
	{
		// define the base path for the pending numerical only rating
		// remover module
		$base = APPPATH . "/modules/numerical_only_remover/libraries";

		// the numerical only remover module requires code from the
		// pending review module, so pre-load it
		PackageLoader::load("pending_review_remover");
		
		// load the files needed for the module
		require_once($base . "/numericalonlyqueue" . EXT);

		// load the module configuration
		$ci = &get_instance();
		$ci->load->config("../modules/numerical_only_remover/config/config");
	}
}

?>