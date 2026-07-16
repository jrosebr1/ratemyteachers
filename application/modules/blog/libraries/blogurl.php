<?php

class BlogURL extends URL
{
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
	 * Module configuration of URL trailers.
	 */
	var $modTrailers = array();
	/**
	 * URL of the blog entry.
	 *
	 * @var $url
	 */
	var $url = null;
	
	public function __construct()
	{
		// call the parent constructor and grab the module tables, schemas,
		// and URL trailers
		parent::__construct();
		$this->modTables = $this->ci->config->item("blog.tables");
		$this->modSchemas = $this->ci->config->item("blog.tables.schemas");
		$this->modTrailers = $this->ci->config->item("blog.url.trailers");
	}
	
	public function build($entryID, $entryTitle = null)
	{
		// if the URL has already been constructed, return it
		if (!empty($this->url))
		{
			return $this->url;
		}
		
		// if the blog entry title was already supplied, then we can skip
		// the database step
		if (empty($orgName))
		{
			// construct the query to get the blog entry title
			$sql = "SELECT " . $this->modSchemas["Blog"]["Title"] . " ";
			$sql .= "FROM " . $this->modTables["Blog"] . " ";
			$sql .= "WHERE " . $this->modSchemas["Blog"]["ID"] . "=?;";
			$bindings = array($entryID);
		
			// execute the query
			$query = $this->ci->db->query($sql, $bindings);
		
			// if the number of rows is zero, then the blog does entry
			// does not exist in the Blog table
			if ($query->num_rows() == 0)
			{
				// return false for the URL since it can't be created
				return false;
			}

			// grab the row the blog entry title
			$row = $query->row_array();
			$entryTitle = $row[$this->modSchemas["Blog"]["Title"]];
		}
		
		// the URL is valid
		$this->setValid(true);

		// sanitize the blog entry title		
		$entryTitle = preg_replace("/[^a-z0-9]/", "-", strtolower($entryTitle));
		$trailer = $this->modTrailers["BlogEntry"];
		$url = "/" . $entryTitle . "/" . $entryID . "-" . $trailer;
		$this->url = $this->clean($url);
		
		// return the fully constructed URL
		return $this->url;
	}
	
	public function nextTier()
	{
		// the next tier we can redirect to from a blog entry is
		// the main blog page
		return "/blog";
	}

	public function isCorrect($uri, $entryID)
	{
		// build the correct URL, then split the correct URL and the
		// request URI
		$correctURL = $this->build($entryID);
		$correctSplit = explode("/", $correctURL);
		$uriSplit = explode("/", $uri);

		// return true, indicatating that the URL is correct if the
		// URLs match
		return ($correctSplit[1] == $uriSplit[1]);
	}
}

?>