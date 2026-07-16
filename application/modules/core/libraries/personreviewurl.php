<?php

/**
 * @ingroup core
 *
 * @brief
 * Class used to construct the review URL.
 *
 * This class is used to construct the review URL, which shows
 * the list of ratings for a person.
 *
 * @see url.php
 * @see personrateurl.php
 * @author Adrian Rosebrock
 */

class PersonReviewURL extends PersonRateURL
{
	/**
	 * Construct the PersonReviewURL.
	 *
	 * @param $personID
	 *  The ID of the person the review URL is for.
	 */
	public function __construct($personID)
	{
		// call the parent constructor and then update the URL trailer
		// that will be used for the review URL
		parent::__construct($personID);
		$this->trailer = $this->coreTrailers["People"];
	}

	/**
	 * Determine if the review URL is correct or not.
	 *
	 * @param $uri
	 *  The URI the user is currently viewing.
	 * @param $personID
	 *  The ID of the person.
	 *
	 * @return
	 *  TRUE if the URL is correct, FALSE if not.
	 */
	public function isCorrect($uri, $personID)
	{
		// build the correct URL, then split the correct URL and the
		// request URI
		$correctURL = $this->build($personID);
		$correctSplit = explode("/", $correctURL);
		$uriSplit = explode("/", $uri);
		
		// return true, indicatating that the URL is correct if the
		// splits match
		return ($correctSplit[1] == $uriSplit[1]);
	}
}

?>