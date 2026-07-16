<?php

class Pending_Review_Controller extends CIEXT_CoreController
{
	public function __construct()
	{
		// construct the controller and load the pending review
		// remover module
		parent::__construct();
		PackageLoader::load("pending_review_remover");
	}
	
	public function remove()
	{
		// get the status that removed ratings will be set to
		$coreConstants = $this->config->item("core.constants");
		$status = $coreConstants["min_rating_status"] - 2;
		
		// grab the number of ratings to remove
		$modConstants = $this->config->item("pending_review_remover.constants");
		$total = $modConstants["num_ratings"];
		
		// grab the queue of ratings to be removed
		$pq = new PendingReviewQueue();
		$queue = $pq->queue($total);
		
		// loop over the queue
		foreach ($queue as $rating)
		{
			// remove the rating and then sleep for 40 milliseconds
			// to prevent server load from getting too high
			RatingRemover::remove(
				$rating["rating_id"],
				$rating["person_id"],
				$rating["org_id"],
				$status);
			usleep(40000);
		}
	}
}

?>