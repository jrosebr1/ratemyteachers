<?php

/**
 * @ingroup core
 *
 * @brief
 * Controller used when a user registers on the site.
 *
 * This controller is used when a user enters in their personal
 * information to create an account.
 *
 * @author Adrian Rosebrock
 */

class Register_Controller extends CIEXT_CoreController
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
	 * Load the registration page.
	 */
	public function index()
	{
		// if the user is logged in, they should not be accessing the
		// registration page, so redirect back to the dashboard
		if ($this->user->isLoggedIn())
		{
			redirect("/dashboard", "refresh");
		}
		
		// get the last organization visisted, the current step for
		// the form, and whether or not the form is being processed
		$lastOrgID = $this->session->userdata("last_org_visited");
		$step = $this->input->post("step");
		$process = $this->input->post("process");
		
		// if the last organization visited is not empty, the step is
		// empty, and the process form indicator is empty, then hand
		// off the request to the visited step
		if (!empty($lastOrgID) && empty($step) && empty($process))
		{
			$this->visited_step($lastOrgID);
			return;
		}
		
		// grab the registration form fields for each step
		$regForm = $this->config->item("site.registration_form");

		// switch on the current step
		switch ($step)
		{
			// handle the visited step
			case 0:
				// set the form rules for the visited step
				$this->set_rules($regForm["visited_step"]);
								
				// check to see if the form validation succeeded
				if ($this->form_validation->run() == TRUE)
				{
					// grab the use organization indicator
					$useOrg = $this->input->post("use_org");
					
					// if the indicator is one, then pass the request
					// off to the user information step
					if ($useOrg == 1)
					{
						$this->user_info_step();
						return;
					}
				}

				// at this point, either the form validation failed or
				// the use organization indicator was not one, so just
				// load the select organization step				
				$this->select_org_step();
				break;
			
			// handle the select organization step
			case 1:
				// set the form rules for the select organization step
				$this->set_rules($regForm["select_org_step"]);
				
				// check to see if the form validation succeeded
				if ($this->form_validation->run() == TRUE)
				{
					// grab the organization ID (if one was supplied)
					// from the form
					$orgID = $this->input->post("org_id");
					
					// if the organization ID is valid, then pass
					// the requset off to the user information step
					if ($this->org_id_check($orgID))
					{
						$this->user_info_step();
						return;
					}
				}

				// at this point, either the form validation failed or
				// the search results need to be displayed				
				$this->select_org_step();
				break;
			
			// handle the user information step
			case 2:
				// set the form rules for the user information step
				$this->set_rules($regForm["user_info_step"]);
				
				// check to see if the form validation succeeded
				if ($this->form_validation->run() == TRUE)
				{
					// grab the first name, email address, password and
					// organization ID from the form
					$firstName = $this->input->post("first_name");
					$email = $this->input->post("email");
					$password = $this->input->post("password");
					$orgID = $this->input->post("org_id");
					
					// add the person to the database
					$ua = new UserAdder($firstName, $email, $password, $orgID);
					$ua->add();
					
					// log them in
					$this->user->login($email, $password);
					
					// pass the request to the contact importer step
					$this->import_contacts_step();
					return;
				}

				// there was an error submitting the form, so load the
				// user information step again
				$this->user_info_step();				
				break;
		}
	}
	
	private function visited_step($orgID)
	{
		// build the organization
		$ob = new OrganizationBuilder($orgID);
		$org = $ob->build();
		
		// add data to the view
		$data = array();
		$data["step"] = 0;
		$data["org_id"] = $org->getID();
		$data["org_name"] = ucwords(strtolower($org->getName()));
		
		// load the registration view
		$this->load->view("show_register", $data);
	}
	
	private function select_org_step()
	{
		// get the set of location that can be searched
		$coreConstants = $this->config->item("core.constants");
		$mappings = $this->config->item("site.registration_form.country_mappings");
		$mappings = $mappings[$coreConstants["country_code"]];
		$locations = call_user_func($mappings["method"]);
				
		// add data to the view
		$data = array();
		$data["step"] = 1;
		$data["location_type"] = $mappings["name"];
		$data["locations"] = $locations;

		// grab the organization query and location
		$query = $this->input->post("search_org");
		$location = $this->input->post("org_location");

		// check to see if both the query and location are not empty
		if (!empty($query) && !empty($location))
		{
			// filter the organizations
			$filter = new OrganizationFilter();
			$filter->addContains($query);
			$orgs = $filter->apply();
			
			// loop over the search results
			foreach ($orgs as $org)
			{
				// add each organization to the search results
				$data["search_results"][] = array(
						"org_id" => $org->getID(),
						"org_name" => ucwords(strtolower($org->getName())),
						"org_city" => ucwords(strtolower($org->getCity())),
						"org_state" => $org->getState());
			}
			
			// add search specific data to the view
			$data["query"] = $query;
			$data["num_results"] = count($orgs);
		}
				
		// load the registration view
		$this->load->view("show_register", $data);
	}
	
	private function user_info_step()
	{
		// add data to the view
		$data = array();
		$data["step"] = 2;
		$data["org_id"] = $this->input->post("org_id");
	
		// load the registration view
		$this->load->view("show_register", $data);
	}
	
	private function import_contacts_step()
	{
		// grab the email address and extract the domain
		$email = $this->input->post("email");
		$atPos = strpos($email, "@");
		$domain = substr($email, $atPos + 1);
		$dotPos = strpos($domain, ".");
		$domain = substr($domain, 0, $dotPos);
		
		// if the domain is not in the list, then reset the email
		// address
		if (!in_array($domain, array("gmail", "yahoo", "hotmail", "live", "aol")))
		{
			$email = "";
		}
		
		// add data to the view
		$data = array();
		$data["step"] = 3;
		$data["first_name"] = $this->input->post("first_name");
		$data["email"] = $email;
		
		// load the registration view
		$this->load->view("show_register", $data);
	}
	
	private function set_rules($fields)
	{
		// loop over the registration fields and set the form
		// validation rules
		foreach ($fields as $field => $fieldInfo)
		{
			$this->form_validation->set_rules($field, $fieldInfo["field_label"], $fieldInfo["field_rules"]);
		}
	}
	
	public function org_id_check($orgID)
	{
		// build the organization
		$ob = new OrganizationBuilder($orgID);
		$org = $ob->build();
		
		// the organization is valid if it is not null
		return !is_null($org);
	}
	
	public function name_check($name)
	{
		// check to see if the pattern matches the name
		$match = preg_match("/[0123456789_~!@#$%^&*\(\)]/", $name);
		
		// if there is a match, then the name is invalid
		if ($match)
		{
			$this->form_validation->set_message("name_check", "The First Name field can only contain alphabetical characters.");
			
			return FALSE;
		}
		
		// the name is valid
		return TRUE;
	}
	
	public function conf_email_check($confEmail)
	{
		// grab the email address
		$email = $this->input->post("email");
		
		// if both the email addresses are not empty and they do
		// not match, then the check has failed
		if (!empty($email) && !empty($confEmail) && $email != $confEmail)
		{
			$this->form_validation->set_message("conf_email_check", "The E-Mail and Re-Type E-Mail fields must match.");
			
			return FALSE;
		}
		
		return TRUE;
	}
}

?>