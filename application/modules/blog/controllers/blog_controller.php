<?php

class Blog_Controller extends CIEXT_CoreController
{
	public function __construct()
	{
		// construct the controller and load the blog module
		parent::__construct();
		PackageLoader::load("blog");
	}
	
	public function index()
	{
		// construct the blog and get the latest entry
		$blog = new Blog();
		$entry = $blog->getLatestEntry();
		
		// show the entry
		$this->show_entry($blog, $entry);
	}
	
	public function entry($entryID)
	{
		// grab the module table schemas
		$modSchema = $this->config->item("blog.tables.schemas");
		
		// construct the blog and the blog URL builder
		$blog = new Blog();
		$ub = new BlogURL();
		
		// if the entry is invalid, redirect to the next tier
		if (!$blog->isValidEntry($entryID))
		{
			redirect($ub->nextTier(), "location", "301");
		}

		// the entry is valid so grab it and build the entry URL
		$entry = $blog->getEntry($entryID);
		$entryURL = $ub->build($entryID, $entry[$modSchema["Blog"]["Title"]]);
		
		// if the URL is valid but not correct, then redirect to the
		// correct URL
		if (!$ub->isCorrect($_SERVER["REQUEST_URI"], $entryID))
		{
			redirect($entryURL, "location", "301");
		}
		
		$this->show_entry($blog, $entry);
	}
	
	public function show_entry($blog, $entry)
	{
		// grab the module table schemas
		$modSchema = $this->config->item("blog.tables.schemas");

		// get the previous entry and the next entry and initialize
		// their URLs
		$prevEntry = $blog->getPreviousEntry($entry[$modSchema["Blog"]["ID"]]);
		$nextEntry = $blog->getNextEntry($entry[$modSchema["Blog"]["ID"]]);
		$prevURL = null;
		$nextURL = null;
		
		// if the previous entry is not empty, then build the URL for it
		if (!empty($prevEntry))
		{
			$ub = new BlogURL();
			$prevURL = $ub->build($prevEntry[$modSchema["Blog"]["ID"]], $prevEntry[$modSchema["Blog"]["Title"]]);
		}
		
		// if the next entry is not empty, then build the URL for it
		if (!empty($nextEntry))
		{
			$ub = new BlogURL();
			$nextURL = $ub->build($nextEntry[$modSchema["Blog"]["ID"]], $nextEntry[$modSchema["Blog"]["Title"]]);
		}
		
		// add content to the view
		$data = array();
		$data["blog_id"] = $entry[$modSchema["Blog"]["ID"]];
		$data["blog_title"] = $entry[$modSchema["Blog"]["Title"]];
		$data["blog_meta_desc"] = $entry[$modSchema["Blog"]["MetaDesc"]];
		$data["blog_meta_keywords"] = $entry[$modSchema["Blog"]["MetaKeywords"]];
		$data["blog_author"] = $entry[$modSchema["Blog"]["Author"]];
		$data["blog_date"] = $entry["live_format_date"];
		$data["blog_entry"] = $entry[$modSchema["Blog"]["Entry"]];
		$data["blog_prev_url"] = $prevURL;
		$data["blog_next_url"] = $nextURL;
		
		// load the blog view
		$this->load->view("blog", $data);
	}	
}

?>