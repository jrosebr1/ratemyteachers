<?php

class Contact_Importer_Controller extends CIEXT_CoreController
{
	public function __construct()
	{
		// construct the controller and load the contact importer
		// library
		parent::__construct();
		PackageLoader::load("contact_importer");
	}
	
	public function index()
	{
		// load the contact importer view
		$this->load->view("contact_importer");
	}
	
	public function pull_contacts()
	{
		// initialize the JSON dictionary
		$json = array(
			"success" => 0,
			"error" => null,
			"contacts" => array());
		
		// get the email address, password, and service from the
		// post data
		$email = $this->input->post("email");
		$password = $this->input->post("password");
		$service = $this->input->post("service");
	
		// attempt to grab the contacts
		$cg = new ContactGrabber($email, $password, $service);
		$success = $cg->grabContacts();
		
		// check to see if pulling the contacts was a success
		if ($success)
		{
			// grab the list of contacts and store them
			$json["success"] = 1;
			$json["contacts"] = $cg->getContacts();
			$cg->saveContacts();
		}
		
		// otherwise, an error has occurred
		else
		{
			$json["error"] = $cg->getError();
		}
		
		// endode the JSON dictionary, set the output type to JSON and
		// show the data
		$json = json_encode($json);
		$this->output->set_content_type("application/json")->set_output($json);
	}
	
	public function send_contact_emails()
	{
		// define the JSON dictionary
		$json = array();

		// endode the JSON dictionary, set the output type to JSON and
		// show the data
		$json = json_encode($json);
		$this->output->set_content_type("application/json")->set_output($json);
	}
}

?>