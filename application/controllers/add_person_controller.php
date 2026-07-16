<?php

/**
 * @ingroup core
 *
 * @brief
 * Controller used to add a person to the database.
 *
 * This controller provides methods used to handle the incoming request for
 * the page, as well as two methods for validation: one to validate the
 * department the person belongs to and another method to validate their
 * gender. These validation methods are used by the form validation library/helper
 * in CodeIgniter.
 *
 * @see form_validation.php
 * @author Adrian Rosebrock
 */

class Add_Person_Controller extends CIEXT_CoreController
{
	/**
	 * Constructs the controller.
	 */
	public function __construct()
	{
		// create the controller
		parent::__construct();
	}
	
	/**
	 * Handles the incoming request for a person to added to the database.
	 * No other method in this class should be called from the browser.
	 */
	public function index()
	{
		// grab the last visited organization ID and initialize the
		// data dictionary for the view
		$orgID = $this->session->userdata("last_org_visited");
		$data = array();
		
		// if the organization ID is empty, a user has landed on this
		// page in the wrong manner, so just redirect back to the
		// homepage
		if (empty($orgID))
		{
			redirect("/", "location", "301");
		}

		// build the organization and initialize the organization URL
		// builder
		$ob = new OrganizationBuilder($orgID);
		$org = $ob->build();
		$ub = new OrganizationURL();
		
		// if the organization is null, indicating that the organization
		// does not exist in the database, redirect to the next highest
		// tier
		if (empty($org))
		{
			redirect($ub->nextTier(), "location", "301");
		}

		// build the organization URL
		$orgURL = $ub->build($org->getID());

		// add content to the page
		$data["org_url"] = $orgURL;
		$data["org_name"] = ucwords(strtolower($org->getName()));
		$data["departments"] = DepartmentFetcher::fetch();

		// if the form validation failed, then load the form again
		if ($this->form_validation->run("add_person") == FALSE)
		{
			$this->load->view("add_person", $data);
		}
		
		// otherwise, the form validation succeeded
		else
		{
			// get the IP address, first name, last name, department,
			// and gender from POST data
			$ipAddr = $this->input->ip_address();
			$firstName = $this->input->post("firstname");
			$lastName = $this->input->post("lastname");
			$dept = $this->input->post("dept_list");
			$gender = $this->input->post("gender");
			
			// add the person to the database
			$pa = new PersonAdder($org->getID(), $this->user->getID(), $ipAddr, $firstName, $lastName, $dept, $gender);
			$personID = $pa->add();

			// set the session data to indicate that the person was
			// successfully added
			$this->session->set_userdata("add_person_successful", true);

			// construct the URL to the person rating page
			$ub = new PersonRateURL($personID);
			$rateURL = $ub->build($personID, $firstName, $lastName);

			// redirect to the person rating page
			redirect($rateURL, "refresh");
		}
	}
	
	/**
	 * Validates that the supplied department is indeed a valid one by
	 * checking the database of valid department values.
	 *
	 * @see PersonAdder::isDeptValid()
	 * @param $dept
	 *  The department to be checked.
	 *
	 * @return
	 *  TRUE if the department supplied is valid; FALSE if it is
	 *  invalid.
	 */
	public function dept_check($dept)
	{
		// check to see if the department is valid or not
		if (!PersonAdder::isDeptValid($dept))
		{
			// the department is invalid, so set the error message
			$this->form_validation->set_message("dept_check", "The Department field is invalid.");
			return FALSE;
		}

		// otherwise, the department is valid		
		return TRUE;
	}
	
	/**
	 * Validates that the supplied gender is indeed a valid one.
	 *
	 * @param $gender
	 *  The gender of the person that will be checked for validity.
	 *  Obviously, this value should either be 'M' for male, or 'F'
	 *  for female.
	 *
	 * @return
	 *  TRUE if the gender is valid, FALSE if it is invalid.
	 */
	public function gender_check($gender)
	{
		// make the gender text uppercase
		$gender = strtoupper($gender);
	
		// if the gender is either 'M' or 'F' then it is valid
		if ($gender == "M" || $gender == "F")
		{
			return TRUE;
		}
		
		// otherwise, the gender is invalid
		$this->form_validation->set_message("gender_check", "The Gender field can only be 'Male' or 'Female'.");
		
		return FALSE;
	}
}

?>