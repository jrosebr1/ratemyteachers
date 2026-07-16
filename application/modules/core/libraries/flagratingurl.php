<?php

/**
 * @ingroup core
 *
 * @brief
 * Class used to construct the URL to flag ratings.
 *
 * This class is used to construct the URL to flag ratings.
 *
 * @see url.php
 * @see ratingurl.php
 * @author Adrian Rosebrock
 */

class FlagRatingURL extends RatingURL
{
	/**
	 * Construct the FlagRatingURL.
	 *
	 * @param $ratingID
	 *  The ID of the rating to be flagged.
	 */
	public function __construct($ratingID)
	{
		// call the parent constructor and update the trailer URL
		parent::__construct($ratingID);
		$this->trailer = $this->coreTrailers["FlagRatings"];
	}
}

?>