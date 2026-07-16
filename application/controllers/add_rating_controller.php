<?php

/**
 * @ingroup core
 *
 * @brief
 * Controller used to add a rating to the database.
 *
 * This controller provides methods to handle the incoming request
 * to add a rating, as well as a method used to validate the person
 * ID associated with the rating (i.e. the ID of the person who is
 * adding the rating; this ID could be the 'anonymous' user ID).
 *
 * Instead of replying on CodeIgniter's form validation library/helper,
 * a custom set of rules found in the site configuration file are applied
 * to ensure valid data was submitted.
 *
 * @see core.php
 * @see site.php
 * @author Adrian Rosebrock
 */

class Add_Rating_Controller extends CIEXT_CoreController
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
	 * Handles the incoming request for a rating to be added to the
	 * database. No other methods from this class should be called
	 * from the browser.
	 */
	public function index()
	{
		// initialize the JSON dictionary
		$json = array("success" => 0, "errors" => array());

		// grab the numerical rating fields and attribute rating fields
		$numerFields = $this->config->item("site.ratings.numerical_fields");
		$attrFields = $this->config->item("site.ratings.attr_fields");
		
		// loop over the numerical fields and set the form validation
		// rules
		foreach ($numerFields as $field => $fieldInfo)
		{
			$this->form_validation->set_rules($field, $fieldInfo["field_label"], $fieldInfo["field_rules"]);
		}

		// loop over the attribute fields and set the form validation
		// rules
		foreach ($attrFields as $field => $fieldInfo)
		{
			$this->form_validation->set_rules($field, $fieldInfo["field_label"], $fieldInfo["field_rules"]);
		}
		
		// check to see if the form validation failed
		if ($this->form_validation->run() == FALSE)
		{
			// grab the errors from the form and put them in the JSON
			// dictionary
			$errors = $this->form_validation->error_array();
			$json["errors"] = $errors;
		}
		
		// otherwise, the form validation succeeded, but there are fields
		// that still need to be manually checked
		else
		{
			// initialize the dictionary of numerical and attribute values
			$numerValues = array();
			$attrValues = array();
			
			// loop over the numerical field items
			foreach ($numerFields as $field => $fieldInfo)
			{
				// get the form value for the field as well as the start and
				// stop range of of the field
				$value = set_value($field);
				$start = $fieldInfo["range"]["start"];
				$end = $fieldInfo["range"]["end"];
				
				// if the value is not within the range, then the supplied
				// value is invalid
				if ($value < $start || $value > $end)
				{
					// manually add the error to the errors array since we
					// no longer have control over the form validation
					// class provided by CodeIgniter
					$json["errors"][$field] = $fieldInfo["field_label"] . " contains a value not within the acceptable range.";
				}
				
				// otherwise, the value is within the range and can be
				// stored in the numerical value dictionary
				else
				{
					$numerValues[$field] = $value;
				}
			}

			// loop over the attribute field items
			foreach ($attrFields as $field => $fieldInfo)
			{
				// get the form value for the field and update the
				// attribute value dictionary; no validation is needed
				// here since these values are validated using the form
				// rules
				$value = set_value($field);
				$attrValues[$field] = $value;
			}

			// if the number of entries in the errors array is zero,
			// then it is safe to add the rating			
			if (count($json["errors"]) == 0)
			{
				// grab the person ID and build a person from it
				$personID = set_value("person_id");
				$pb = new PersonBuilder($personID);
				$person = $pb->build();
				
				// grab the user's IP address, user agent, referring page
				// and session ID
				$userIP = $this->input->ip_address();
				$userAgent = $this->agent->agent_string();
				$userReferer = $this->agent->referrer();
				$sessionID = $this->user->getSessionID();
			
				// create the rating adder
				$ra = new RatingAdder($numerValues, $attrValues, $this->user->getID(), $userIP, $userAgent, $userReferer, $sessionID);
				
				// if the rating is not addable due to time constraints on
				// the last rating added to to this person, update the error
				// list
				if (!$ra->isAddable($personID))
				{
					$json["errors"]["time_constraint"] = "You have already rated this person!";
				}
				
				// otherwise, add the rating
				else
				{
					$sql = $ra->add();
				
					// sync the person
					$sp = new SyncPerson($personID);
					$sp->sync();
				
					// sync the organization the person belongs to
					$so = new SyncOrganization($person->getOrganization()->getID());
					$so->sync();

					// set the flash data that the rating was successfully
					// added
					$this->session->set_flashdata("add_rating_successful", true);			
			
					// update the JSON dictionary to indicate that adding
					// the rating was a success
					$json["success"] = 1;
				}
			}
		}
		
		// endode the JSON dictionary, set the output type to JSON and
		// show the data
		$json = json_encode($json);
		$this->output->set_content_type("application/json")->set_output($json);
	}
	
	/**
	 * Validates that the person ID supplied is indeed a valid one.
	 *
	 * @param $personID
	 *  ID of the person to check for existance.
	 *
	 * @return
	 *  TRUE if the person exists in our tables, FALSE if they do not.
	 */
	public function person_id_check($personID)
	{
		// construct a person with the supplied ID
		$pb = new PersonBuilder($personID);
		$person = $pb->build();
		
		// if the person is empty, then the check has failed
		if (empty($person))
		{
			$this->form_validation->set_message("person_id_check", "The Person ID field contains an invalid ID.");
			return FALSE;
		}
		
		// otherwise, the check was a success
		return TRUE;
	}
}

?>