<?php

/**
 * @ingroup core
 *
 * @brief
 * Controller used to load the "login" and "logout" pages.
 *
 * This controller is used to load the "login" and "logout" pages. The
 * login method should display a page along with error messages if
 * necessary. The login form is based on the CodeIgniter form validation
 * libary/helper so further configuration of the login form can be done
 * by editing "form_validation.php". The logout method should log the
 * user out and then redirect back to the homepage.
 *
 * @see form_validation.php
 * @author Adrian Rosebrock
 */

class Session_Controller extends CIEXT_CoreController
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
	 * Load the login page and display errors if necessary. If the login
	 * was a success, then redirect to the user's dashboard.
	 */
	public function login()
	{
		// initialize the data dictionary for the view with the
		// login error being set to false
		$data = array("login_error" => false);
	
		// if the login form did not validate correctly, then load
		// the login view again
		if ($this->form_validation->run("login") == FALSE)
		{
			$this->load->view("login", $data);
		}
		
		// otherwise, the form validated correctly, we just need to
		// see if the login was a success
		else
		{
			// grab the email address and password
			$email = $this->input->post("email");
			$password = $this->input->post("password");
			
			// try to login the user
			$success = $this->user->login($email, $password);
			
			// if the login failed, then set an error indicator in
			// the data dictionary and then load the login view
			if (!$success)
			{
				$data["login_error"] = true;
				$this->load->view("login", $data);
			}

			// otherwise, the login was a success and we should
			// redirect to the user's dashboard
			else
			{
				redirect("/dashboard", "location");
			}
		}
	}

	/**
	 * Log the user out and redirect back to the homepage.
	 */
	public function logout()
	{
		// log the user out and revert back to the homepage
		$this->user->logout();
		redirect("/", "location");
	}	
}

?>