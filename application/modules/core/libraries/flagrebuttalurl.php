<?php

/**
 * @ingroup core
 *
 * @brief
 * Class used to construct the URL to flag rebuttals.
 *
 * This class is used to construct the URL to flag rebuttals.
 *
 * @see url.php
 * @see ratingurl.php
 * @author Adrian Rosebrock
 */

class FlagRebuttalURL extends RatingURL
{
	/**
	 * Construct the FlagRebuttalURL.
	 *
	 * @param $rebuttalID
	 *  The ID of the rebuttal to be flagged.
	 */
	public function __construct($rebuttalID)
	{
		// call the parent constructor and update the trailer URL
		parent::__construct($rebuttalID);
		$this->trailer = $this->coreTrailers["FlagRebuttals"];
	}
}

?>