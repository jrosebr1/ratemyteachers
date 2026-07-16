<?php

class Numerical_Only_Controller extends CIEXT_CoreController
{
	public function __construct()
	{
		// construct the controller and load the numerical only
		// rating remover module
		parent::__construct();
		PackageLoader::load("numerical_only_remover");
	}
	
	public function remove()
	{
		// get the status that removed ratings will be set to
		$coreConstants = $this->config->item("core.constants");
		$status = $coreConstants["min_rating_status"] - 2;
		
		// grab the number of ratings to remove
		$modConstants = $this->config->item("numerical_only_remover.constants");
		$total = $modConstants["num_ratings"];

		// grab the queue of ratings to be removed
		$pq = new NumericalOnlyQueue();
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