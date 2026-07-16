<?php

class ContactGrabber
{
	/**
	 * Variable used to interface with CodeIgniter.
	 *
	 * @var $ci
	 */
	var $ci = null;
	/**
	 * Module configuration of tables.
	 *
	 * @var $modTables
	 */
	var $modTables = array();
	/**
	 * Module configuration of table schemas.
	 *
	 * @var $modSchemas
	 */
	var $modSchemas = array();
	/**
	 * OpenInviter object used to fetch contacts from an email
	 * address.
	 *
	 * @var $inviter
	 */
	var $inviter = null;
	/**
	 * Email address that contacts will be pulled from.
	 *
	 * @var $email
	 */
	var $email = null;
	/**
	 * Password for the email address contacts will be pulled from.
	 *
	 * @var $password
	 */
	var $password = null;
	/**
	 * Email service (GMail, Hotmail, Yahoo, or etc.) that of the
	 * email address.
	 *
	 * @var $service
	 */
	var $service = null;
	/**
	 * List of contacts pulled from the email address.
	 *
	 * @var $contacts
	 */
	var $contacts = null;
	/**
	 * Boolean variable used to determine if the email service is
	 * usable or not.
	 *
	 * @var $usable
	 */
	var $usable = false;
	/**
	 * Holds error message if an error were to occur when importing
	 * contacts.
	 *
	 * @var $error
	 */
	var $error = null;
	
	public function __construct($email, $password, $service)
	{
		// connect the class with CodeIgniter and then grab the module
		// tables and schemas
		$this->ci = &get_instance();
		$this->modTables = $this->ci->config->item("contact_importer.tables");
		$this->modSchemas = $this->ci->config->item("contact_importer.tables.schemas");
		
		// store the email address, password, and service
		$this->email = $email;
		$this->password = $password;
		$this->service = $service;
		
		// determine if the email service is usable
		$this->usable = $this->isValidService($this->service);
		
		// check to see if the email service is usable
		if ($this->isUsable())
		{
			// construct the OpenInviter
			$this->inviter = new OpenInviter();
			$this->inviter->getPlugins();			
		}
	}

	public function grabContacts()
	{		
		// if service was not usable, return false
		if (!$this->isUsable())
		{
			$this->error = "Your contacts could not be imported.";
			return false;
		}
		
		// start the plugin for the email service and check to see if
		// any errors occurred
		$this->inviter->startPlugin($this->service);
		$error = $this->inviter->getInternalError();
		
		// if an error occurred, false
		if ($error)
		{
			$this->error = "Your contacts could not be imported.";
			return false;
		}
		
		// try to login to the service with the supplied email address
		// and password
		else if (!$this->inviter->login($this->email, $this->password))
		{
			$this->error = "Invalid email address and password.";
			return false;
		}
		
		// so far so good, so grab the contacts
		$this->contacts = $this->inviter->getMyContacts();
		
		// if the contacts array is empty, then an error has occurred
		if ($this->contacts === false)
		{
			$this->error = "Your contacts could not be imported.";
			return false;
		}
		
		// otherwise, pulling the contacts was a success
		return true;
	}
	
	public function getContacts()
	{
		// return the list of grabbed contacts
		return array_keys($this->contacts);
	}
	
	public function getError()
	{
		// return the error message
		return $this->error;
	}
	
	public function isUsable()
	{
		// return if the email service is valid or not
		return $this->usable;
	}
	
	public function saveContacts()
	{
		// start constructing the query to store the imported contacts
		// in the database
		$sql = "INSERT INTO " . $this->modTables["InviteEmails"] . "(";
		$sql .= $this->modSchemas["InviteEmails"]["ImportingEmail"] . ", ";
		$sql .= $this->modSchemas["InviteEmails"]["ImportedEmail"] . ") VALUES";
		$bindings = array();
		
		// loop over the contacts
		foreach (array_keys($this->contacts) as $contact)
		{
			// add each contact to the query and bindings list
			$sql .= "(?, ?), ";
			$bindings = array_merge($bindings, array($this->email, $contact));
		}
		
		// remove the trailing space and comma and finish off the
		// query
		$sql = substr($sql, 0, -2);
		$sql .= ";";

		// execute the query
		$this->ci->db->query($sql, $bindings);		
	}
	
	private function isValidService($service)
	{
		// convert the service to lowercase
		$service = strtolower($service);
		
		// determine if the service is valid or not
		switch ($service)
		{
			// handle the special case of changing Windows Live to
			// hotmail
			case "live":
				$this->service = "hotmail";
				return true;
				
			// check to see if the service is one we are interested in
			case "gmail":
			case "yahoo":
			case "hotmail":
			case "aol":
				return true;
		}
		
		// return false, the service is not valid
		return false;
	}
}

?>