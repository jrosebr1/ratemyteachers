<?php

class RSS_Feed_Controller extends CIEXT_CoreController
{
	public function __construct()
	{
		// construct the controller and load the RSS feed module
		parent::__construct();
		PackageLoader::load("rss_feeds");
	}
	
	public function generate_homepage_feed()
	{
		// generate and the homepage feed
		$rss = new HomepageFeed();
		$feed = $rss->generateFeed();
		$rss->saveFeed($feed);
		
		// print status update to the user
		echo "...homepage feed generation complete\n";
	}
	
	public function homepage_feed()
	{
		// build the homepage feed and show the feed
		$rss = new HomepageFeed();
		$this->show_feed($rss);
	}
	
	public function organization_feed($orgID)
	{
		// build the organization feed and show the feed
		$rss = new OrganizationFeed($orgID);
		$this->show_feed($rss);
	}
	
	public function person_feed($personID)
	{
		// build the person feed and show the feed
		$rss = new PersonFeed($personID);
		$this->show_feed($rss);
	}
	
	private function show_feed($rss)
	{
		// if the feed is invalid, then redirect back to the homepage
		if (!$rss->isValid())
		{
			redirect("/", "location", "301");
		}
		
		// otherwise, the organization is vavalid, so grab the feed
		$feed = (array)$rss->getFeed();
		
		// assign the header data to the view
		$data["generator"] = $feed["generator"];
		$data["title"] = $feed["title"];
		$data["link"] = $feed["link"];
		$data["description"] = $feed["description"];
		$data["language"] = $feed["language"];
		$data["publish_date"] = $feed["publish_date"];
		$data["last_build_date"] = $feed["last_build_date"];
		$data["items"] = array();
		
		// loop over each of the items in the feed
		foreach ($feed["items"] as $item)
		{
			// cast the item from an object to an array
			$item = (array)$item;
			
			// add each item to the view
			$data["items"][] = array(
				"title" => $item["title"],
				"link" => $item["link"],
				"desc" => $item["desc"],
				"guid" => $item["guid"]);
		}
		
		// set the output type to be RSS and XML and load the view
		$this->output->set_content_type("application/rss+xml");
		$this->load->view("rss", $data);
	}
}

?>