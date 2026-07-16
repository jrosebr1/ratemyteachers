<?php

/**
 * @ingroup core
 *
 * @brief
 * Controller used to handle the request for the user's dashboard.
 *
 * This controller loads the view for the user's dashboard.
 *
 * @author Adrian Rosebrock
 */

class Dashboard_Controller extends CIEXT_CoreController
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
	 * Handle the request for the user's dashboard.
	 */
	public function index()
	{
		// if the user is not logged in, rediret to the login page
		if (!$this->user->isLoggedIn())
		{
			redirect("/login", "refresh");
		}
	
		// load the dashboard view
		$this->load->view("dashboard");
	}
}

?>