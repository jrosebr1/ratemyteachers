<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @ingroup core
 * @file routes.php
 *
 * @brief
 * CodeIgniter configuration file used to create URL routes.
 *
 * @author Adrian Rosebrock
 */


 
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

/**
 * Define the default controller.
 */
$route['default_controller'] = "site_controller";
/**
 * Define the 404 override page.
 */
$route['404_override'] = '';



/**
 * Define the route for the Frequently Asked Questions page.
 */
$route['faq'] = "site_controller/faq";
/**
 * Define the route for the Contact page.
 */
$route['contact'] = "site_controller/contact";
/**
 * Define the route for the Legal page.
 */
$route['legal'] = "site_controller/legal";



/**
 * Define the route for users to login.
 */
$route['login'] = "session_controller/login";
/**
 * Define the route for users to logout.
 */
$route['logout'] = "session_controller/logout";



/**
 * Define the route for users to register.
 */
$route['register'] = "register_controller";



/**
 * Define the route for users to view their dashboard.
 */
$route['dashboard'] = "dashboard_controller";



/**
 * Define the route used for searching the rating site for
 * organizations.
 */
$route['search'] = "search_organizations_controller";
/**
 * Define the route for loading a specific organization by
 * looking at the organization ID.
 */
$route['([a-zA-Z0-9\-]+)/([0-9]+)-o'] = "organization_controller/index/$2";
/**
 * Define the route for loading a specific organization by
 * looking at the organization ID and then at the current
 * page number so the list of persons can be adjusted
 * accordingly.
 */
$route['([a-zA-Z0-9\-]+)/([0-9]+)-o/([0-9]+)'] = "organization_controller/page/$2/$3";



// define the routes used for persons
/**
 * Define the route used for loading a specific person by
 * looking at the person ID.
 */
$route['([a-zA-Z0-9\-]+)/([0-9]+)-p'] = "person_review_controller/index/$2";
/**
 * Define the route used for loading a specific person by
 * looking at the person ID and then at the current page
 * number so the list of ratings can be adjusted accordingly.
 */
$route['([a-zA-Z0-9\-]+)/([0-9]+)-p/([0-9]+)'] = "person_review_controller/page/$2/$3";
/**
 * Define the route used for asking a user whether or not
 * they would like to 'rate' or 'review' a person by looking
 * at the person ID.
 */
$route['([a-zA-Z0-9\-]+)/([a-zA-Z0-9\-]+)/([0-9]+)-req'] = "person_request_controller/index/$3";
/**
 * Define the route used for collecting ratings by looking
 * at the person ID.
 */
$route['([a-zA-Z0-9\-]+)/([0-9]+)-rate'] = "person_rating_controller/index/$2";
/**
 * Define the route used for adjusting how persons are filtered
 * at the organization tier.
 */
$route['person_filter'] = "person_filter_controller";
/**
 * Define the route used for adding a person to an organization.
 */
$route['add_person'] = "add_person_controller";



/**
 * Define the route used for loading a specific rating by
 * looking at the rating ID.
 */
$route['([a-zA-Z0-9\-]+)/([0-9]+)-r'] = "rating_review_controller/index/$2";
/**
 * Define the route used for flagging a rating by looking
 * at the rating ID.
 */
$route['([a-zA-Z0-9\-]+)/([0-9]+)-fra'] = "flag_rating_controller/index/$2";
/**
 * define the route used for flagging a rebuttal by looking
 * at the rebuttal ID.
 */
$route['([a-zA-Z0-9\-]+)/([0-9]+)-fre'] = "flag_rebuttal_controller/index/$2";
/**
 * Define the route used for handling the AJAX request to
 * add a rating.
 */
$route['add_rating'] = "add_rating_controller";
/**
 * Define the route used for handling the AJAX request to
 * add a rebuttal.
 */
$route['add_rebuttal'] = "add_rebuttal_controller";
/**
 * Define the route used for handling the AJAX request to
 * flag a rating.
 */
$route['flag_rating'] = "flag_rating_controller/flag_rating";
/**
 * Define the route used for handling the AJAX request to
 * flag a rebuttal.
 */
$route['flag_rebuttal'] = "flag_rebuttal_controller/flag_rebuttal";



/**
 * Drilldown module: Define the route used for handling the
 * organization with no parameters.
 */
$route['organization'] = "drilldown_controller/organization";
/**
 * Drilldown module: Define the route used for handling the
 * organization with a letter parameter.
 */
$route['organization/([A-Za-z]|ALL|all)'] = "drilldown_controller/organization/$1";
/**
 * Drilldown module: Define the route used for handling the
 * organization with a letter, start, and end parameter.
 */
$route['organization/([A-Za-z]|ALL|all)/([0-9]+)/([0-9]+)'] = "drilldown_controller/organization/$1/$2/$3";
/**
 * Drilldown module: Define the route used for handling the
 * organization with no parameters.
 */
$route['person'] = "drilldown_controller/person";
/**
 * Drilldown module: Define the route used for handling the
 * organization with a letter parameter.
 */
$route['person/([A-Za-z]|ALL|all)'] = "drilldown_controller/person/$1";
/**
 * Drilldown module: Define the route used for handling the
 * organization with a letter, start, and end parameter.
 */
$route['person/([A-Za-z]|ALL|all)/([0-9]+)/([0-9]+)'] = "drilldown_controller/person/$1/$2/$3";



/**
 * RSS feed module: Define the route used for handling the
 * homepage feed.
 */
$route['organizations.rss'] = "rss_feed_controller/homepage_feed";
/**
 * RSS feed module: Define the route used for handling the
 * organization feed.
 */
$route['([a-zA-Z0-9\-]+)/([0-9]+)-o.rss'] = "rss_feed_controller/organization_feed/$2";
/**
 * RSS feed module: Define the route used for handling the
 * person feed.
 */
$route['([a-zA-Z0-9\-]+)/([0-9]+)-p.rss'] = "rss_feed_controller/person_feed/$2";



/**
 * Blog module: Define the route used for handling the latest
 * blog entry.
 */
$route['blog'] = "blog_controller";
/**
 * Blog module: Define the route used for handling a specific
 * blog entry.
 */
$route['([a-zA-Z0-9\-]+)/([0-9]+)-b'] = "blog_controller/entry/$2";



/**
 * Contact importer module: Define the route used for showing
 * the contact importer form.
 */
$route['refer_a_friend'] = "contact_importer_controller";
/**
 * Contact importer module: Define the route used for handling
 * the actual importation of contacts.
 */
$route['import_contacts'] = "contact_importer_controller/pull_contacts";
/**
 * Contact importer module: Define the route used for handling
 * the sending of emails after contacts have been imported.
 */
$route['send_contact_emails'] = "contact_importer_controller/send_contact_emails";



/**
 * Sitemap module: Define the route used for handling the page
 * used to display the root list of states or cities.
 */
$route['location'] = "sitemap_controller";



/* End of file routes.php */
/* Location: ./application/config/routes.php */