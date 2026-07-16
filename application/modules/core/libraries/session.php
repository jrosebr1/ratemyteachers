<?php

/**
 * @ingroup core
 *
 * @brief
 * Abstract class to be extended when a session is needed.
 *
 * This abstract class should be extended when a session is needed
 * to login/logout users and track their activity across the site.
 *
 * @author Adrian Rosebrock
 */

abstract class Session
{
	/**
	 * Variable used to interface with CodeIgniter.
	 *
	 * @var $ci
	 */
	var $ci = null;
	/**
	 * Core configuration of tables.
	 *
	 * @var $coreTables
	 * @see site.php
	 */
	var $coreTables = array();
	/**
	 * Core configuration of table schemas.
	 *
	 * @var $coreSchemas
	 * @see site.php
	 */
	var $coreSchemas = array();
	/**
	 * Core configuration of constants.
	 *
	 * @var $coreConstants
	 * @see core.php
	 */
	var $coreConstants = array();
	
	/**
	 * Construct the Session.
	 */
	public function __construct()
	{
		// connect the class with CodeIgniter
		$this->ci = &get_instance();
		
		// grab the core table, schemas, and constants
		$this->coreTables = $this->ci->config->item("core.tables");
		$this->coreSchemas = $this->ci->config->item("core.tables.schemas");
		$this->coreConstants = $this->ci->config->item("core.constants");
		
		// get the http referrer and store it in the session
		$referrer = $this->ci->agent->referrer();
		$this->ci->session->set_userdata("referrer", $referrer);

		// grab the current sesion ID
		$sessionID = $this->getSessionID();

		// if the session ID is empty, then get a meaningful session ID that
		// can be used for tracking a user's activity on the site (unlike the
		// session ID from CodeIgniter that is just random) and store it in
		// the session
		if (empty($sessionID))
		{
			$sessionID = md5(time() . $this->ci->session->userdata("ip_address"));
			$this->ci->session->set_userdata("user_session_id", $sessionID);
		}
	}
	
	/**
	 * Get the ID of the session.
	 *
	 * @return
	 *  The ID of the session.
	 */
	public function getSessionID()
	{
		// return the trackable session ID of the user
		return $this->ci->session->userdata("user_session_id");
	}
	
	/**
	 * Try to login the user with the supplied email and password.
	 *
	 * @param $email
	 *  Email address of the user trying to login.
	 * @param $password
	 *  Password of the user trying to login.
	 *
	 * @return
	 *  TRUE if the login was successful, FALSE if not.
	 */
	public function login($email, $password)
	{
		// try to verify the email and password
		$userInfo = $this->verify($email, $password);
		
		// if the supplied credentials were invalid, then the
		// login failed
		if (!$userInfo)
		{
			return false;
		}
		
		// update the user information to include a simple variable
		// used to tell whether the user is still logged in or not
		// and store the user information in the session
		$userInfo["logged_in"] = true;
		$this->ci->session->set_userdata("user_info", $userInfo);
		
		// the login was a success
		return true;
	}
	
	/**
	 * Logs out the user.
	 *
	 * @return
	 *  TRUE if the logout was successful, FALSE if not.
	 */
	public function logout()
	{
		// destroy the user info session
		$this->ci->session->unset_userdata("user_info");
		
		// the logout was a success
		return true;
	}
	
	/**
	 * Verifies the credentials of the user trying to login.
	 *
	 * @param $email
	 *  Email address of the user trying to login.
	 * @param $password
	 *  Password of the user trying to login.
	 *
	 * @return
	 *  TRUE of the verification was a success, FALSE if not.
	 */
	private function verify($email, $password)
	{
		// if the provided email address or password is empty,
		// then the verification should automatically fail
		if (empty($email) || empty($password))
		{
			return false;
		}
		
		// construct the query to determine if the email address
		// and password combination exists in our database
		$sql = "SELECT * ";
		$sql .= "FROM " . $this->coreTables["Users"] . " ";
		$sql .= "WHERE " . $this->coreSchemas["Users"]["Email"] . "=? AND ";
		$sql .= $this->coreSchemas["Users"]["Password"] . "=? AND ";
		$sql .= $this->coreSchemas["Users"]["Status"] . " >= ?;";
		
		// define the bindings array
		$bindings = array(
			$email,
			$password,
			$this->coreConstants["min_user_status"]);
		
		// execute the query
		$query = $this->ci->db->query($sql, $bindings);
		
		// if the number of returned rows is greater than zero,
		// then there is a user in the table with the credentials
		// matching the ones supplied
		if ($query->num_rows() > 0)
		{
			// get the user information and free the query result
			$row = $query->row_array();
			$query->free_result();
			
			// return the user information
			return $row;
		}
		
		// otherwise, the credentials are invalid
		return false;
	}
	
	/**
	 * Get either the entire user information dictionary, or a
	 * single value based on the supplied $key.
	 *
	 * @param $key
	 *  Key into the user information dictionary, which could be
	 *  null or an empty string.
	 *
	 * @return
	 *  Either the entire user information dictionary or a single
	 *  value based on the supplied $key.
	 */
	public function getUserInfo($key = null)
	{
		// get the user information for the session
		$userInfo = $this->ci->session->userdata("user_info");
		
		// if the key is empty, then return the entire dictionary
		if (empty($key))
		{
			return $userInfo;
		}
		
		// otherwise, try to return the value with the supplied
		// key
		return $userInfo[$key];
	}
	
	/**
	 * Get the referring page of the user.
	 *
	 * @return
	 *  The referring page.
	 */
	public function getReferrer()
	{
		// return the http referrer
		return $this->ci->session->userdata("referrer");
	}
	
	/**
	 * Determines if the user is logged in or not.
	 *
	 * @return
	 *  TRUE if the user is logged in, FALSE if not.
	 */
	public function isLoggedIn()
	{
		// return true if the user is logged in
		return $this->getUserInfo("logged_in");
	}
	
	/**
	 * Abstract method that should be implemented to return the
	 * name of the user.
	 */
	abstract public function getName();

	/**
	 * Abstract method that should be implemented to determine if
	 * the user is an administrator or not.
	 */
	abstract public function isAdmin();
}

?>