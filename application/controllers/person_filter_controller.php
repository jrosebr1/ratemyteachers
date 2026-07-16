<?php

/**
 * @ingroup core
 *
 * @brief
 * Controller used to update the options used to filter people
 * within an organization.
 *
 * This controller is used to handle the request to filter the list of
 * people that belong to an organization. For instance, the list might
 * need to be ordered by "department" rather than "last name", and then
 * the list might have to be ordered in descending order rather than
 * ascending order. This controller handles updates to these filter rules
 * and ensures the user's session is set to reflect the filter changes.
 *
 * @see personfilterparser.php
 * @author Adrian Rosebrock
 */

class Person_Filter_Controller extends CIEXT_CoreController
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
	 * Handles the request to update the options used to filter the list
	 * of people that belong to an organization.
	 */
	public function index()
	{
		// get the filter options from POST data and decode the JSON data
		$options = $this->input->post("person_filter");
		$options = (array)json_decode($options);
		
		// get the filter options from the session and decode the JSON
		// data
		$sessOptions = $this->session->userdata("person_filter_options");
		$sessOptions = (array)json_decode($sessOptions);
		
		// parse the options and sanitize them
		$pf = new PersonFilterParser($options);
		$options = $pf->parse();
		
		// parse the session filter options and sanitize them
		$pf = new PersonFilterParser($sessOptions);
		$sessOptions = $pf->parse();

		// handle the special case when the order by options do not
		// match, then force the order direction to be ascending
		if ($options["order_by"] != $sessOptions["order_by"])
		{
			$options["order_dir"] = "ASC";
		}		
		
		// if the options returned is false, then the ID for the organization
		// does not exist in the organizations table, so redirect back to
		// the homepage
		if (!$options)
		{
			redirect("/", "location", "301");
		}

		// grab the redirect URL and then store the filter options in the
		// session
		$url = $options["url"];
		$options = json_encode($options);
		$this->session->set_userdata("person_filter_options", $options);
		
		// redirect back to the organization page so the options can be
		// used
		redirect($url, "refresh");
	}
}

?>