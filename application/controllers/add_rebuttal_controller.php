<?php

/**
 * @ingroup core
 *
 * @brief
 * Controller used to add a rebuttal to the database.
 *
 * This controller provides methods to handle the incoming request
 * to add a rebuttal, as well as a method to validate that the
 * rating ID the rebuttal belongs to is a valid one. All other form
 * field validations are done by using CodeIgniters form validation
 * libaray/helper.
 *
 * @see form_validation.php
 * @author Adrian Rosebrock
 */

class Add_Rebuttal_Controller extends CIEXT_CoreController
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
	 * Handles the request for a rebuttal to be added to the database.
	 * No other methods in this class should be called from the browser.
	 */
	public function index()
	{
		// grab the core constants initialize the JSON dictionary
		$coreConstants = $this->config->item("core.constants");
		$json = array("success" => 0, "errors" => array());
		
		// check to see if the form validation failed
		if ($this->form_validation->run("add_rebuttal") == FALSE)
		{
			// grab the errors from the form and put them in the JSON
			// dictionary
			$errors = $this->form_validation->error_array();
			$json["errors"] = $errors;
		}
		
		else
		{
			// grab the rating ID the and the rebuttal comment by using
			// the form helper function
			$ratingID = set_value("rating_id");
			$comment = set_value("rebuttal_comment");
			
			// grab the user's IP address and session ID
			$userIP = $this->input->ip_address();
			$sessionID = $this->user->getSessionID();
			
			// create the rebuttal adder
			$ra = new RebuttalAdder($ratingID, $this->user->getID(), $userIP, $sessionID, $comment);
			
			// if the rebuttal is not addable due to time constraints on
			// the last rebuttal added to to this rating, update the error
			// list
			if (!$ra->isAddable())
			{
				$json["errors"]["time_constraint"] = "You have already left a rebuttal for this rating. Please wait another " . strtolower($coreConstants["min_rebuttal_time_interval"]) . "s to leave another one.";
			}
			
			// otherwise, add the rating
			else
			{
				$ra->add();

				// set the flash data that the rebuttal was successfully
				// added
				$this->session->set_flashdata("add_rebuttal_successful", true);

				// update the JSON dictionary to indicate that adding
				// the rating was a success
				$json["success"] = 1;
			}
		}

		// endode the JSON dictionary, set the output type to JSON and
		// show the data
		$json = json_encode($json);
		$this->output->set_content_type("application/json")->set_output($json);
	}
	
	/**
	 * Validates that the rating ID supplied is indeed a valid one.
	 *
	 * @param $ratingID
	 *  The rating ID of the rating to be checked for existence and a
	 *  valid status in our database.
	 *
	 * @return
	 *  TRUE if the rating is valid, FALSE if not.
	 */
	public function rating_id_check($ratingID)
	{
		// construct a rating with the supplied ID
		$rb = new RatingBuilder($ratingID);
		$rating = $rb->build();
		
		// if the rating is empty, then the check has failed
		if (empty($rating))
		{
			$this->form_validation->set_message("rating_id_check", "The Rating ID field contains an invalid ID.");
			return FALSE;
		}
		
		// otherwise, the check was a success
		return TRUE;
	}
}

?>