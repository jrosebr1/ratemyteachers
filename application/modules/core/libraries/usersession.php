<?php

/**
 * @ingroup core
 *
 * @brief
 * Class used to represent a user as they navigate the site.
 *
 * This class is used to represent a user, such as logging in
 * and logging out, as well as their activity across the site.
 *
 * @see session.php
 * @author Adrian Rosebrock
 */

class UserSession extends Session
{
	/**
	 * Construct the UserSession.
	 */
	public function __construct()
	{
		// call the parent constructor
		parent::__construct();
	}
	
	/**
	 * Gets the ID of the user.
	 *
	 * @return
	 *  The ID of the user.
	 */
	public function getID()
	{
		// if the user is not logged in, the default the ID to
		// the anonymous user constant
		if (!$this->isLoggedIn())
		{
			return $this->coreConstants["anon_user_id"];
		}
		
		// otherwise, grab the ID of the user
		return (int)$this->getUserInfo($this->coreSchemas["Users"]["ID"]);
	}
	
	/**
	 * Gets the name of the user.
	 *
	 * @return
	 *  The name of the user.
	 */
	public function getName()
	{
		// get the first and last name of the user, then put them
		// together
		$firstName = $this->getUserInfo($this->coreSchemas["Users"]["FirstName"]);
		$lastName = $this->getUserInfo($this->coreSchemas["Users"]["LastName"]);
		$name = trim($firstName . " " . $lastName);
		
		// return the name of the user
		return $name;
	}
	
	/**
	 * Determines if the user is an administator or not.
	 *
	 * @return
	 *  TRUE if the user is an administrator, FALSE if not.
	 */
	public function isAdmin()
	{
		// by default, no user is an admin
		return false;
	}
}

?>