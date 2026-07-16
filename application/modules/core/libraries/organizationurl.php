<?php

/**
 * @ingroup core
 *
 * @brief
 * Class used to build organization URLs.
 *
 * This class is used to build the URLs for organizations.
 *
 * @see url.php
 * @see organization.php
 * @author Adrian Rosebrock
 */

class OrganizationURL extends URL
{
	/**
	 * URL of the organization.
	 *
	 * @var $url
	 */
	var $url = null;
	
	/**
	 * Construct the Organization URL.
	 */
	public function __construct()
	{
		// call the parrent constructor
		parent::__construct();
	}
	
	/**
	 * Build the organization URL.
	 *
	 * @param $orgID
	 *  The ID of the organization.
	 * @param $orgName
	 *  The name of the organization.
	 *
	 * @return
	 *  The fully constructed organization URL.
	 */
	public function build($orgID, $orgName = null)
	{
		// if the URL has already been constructed, return it
		if (!empty($this->url))
		{
			return $this->url;
		}
		
		// if the organization name was already supplied, then we can skip
		// the database step
		if (empty($orgName))
		{
			// construct the query to get the organization name
			$sql = "SELECT " . $this->coreSchemas["Organizations"]["Name"] . " ";
			$sql .= "FROM " . $this->coreTables["Organizations"] . " ";
			$sql .= "WHERE " . $this->coreSchemas["Organizations"]["ID"] . "=?;";
			$bindings = array($orgID);
		
			// execute the query
			$query = $this->ci->db->query($sql, $bindings);
		
			// if the number of rows is zero, then the organization does
			// not exist in the Organizations table
			if ($query->num_rows() == 0)
			{
				// return false for the URL since it can't be created
				return false;
			}

			// grab the row the organization name
			$row = $query->row_array();
			$orgName = $row[$this->coreSchemas["Organizations"]["Name"]];
		}
		
		// the URL is valid
		$this->setValid(true);

		// sanitize the name		
		$orgName = preg_replace("/[^a-z0-9]/", "-", strtolower($orgName));
		$trailer = $this->coreTrailers["Organizations"];
		$url = "/" . $orgName . "/" . $orgID . "-" . $trailer;
		$this->url = $this->clean($url);
		
		// return the fully constructed URL
		return $this->url;
	}
	
	/**
	 * Get the next tier that can be redirected to if the
	 * organization is invalid.
	 *
	 * @return
	 *  The redirect URL.
	 */
	public function nextTier()
	{
		// the next tier we can redirect to from the organization page
		// is the homepage
		return "/";
	}
	
	/**
	 * Determine if the organization URL is correct or not.
	 *
	 * @param $uri
	 *  The URI the user is currently viewing.
	 * @param $orgID
	 *  The ID of the organization.
	 *
	 * @return
	 *  TRUE if the URL is correct, FALSE if not.
	 */
	public function isCorrect($uri, $orgID)
	{
		// build the correct URL, then split the correct URL and the
		// request URI
		$correctURL = $this->build($orgID);
		$correctSplit = explode("/", $correctURL);
		$uriSplit = explode("/", $uri);

		// return true, indicatating that the URL is correct if the
		// URLs match
		return ($correctSplit[1] == $uriSplit[1]);
	}
}

?>