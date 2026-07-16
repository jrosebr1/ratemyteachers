<?php

/**
 * @ingroup core
 * @file score_format_helper.php
 *
 * @brief
 * Provides a helper method to format the score of a numerical rating
 * field.
 *
 * @author Adrian Rosebrock
 */

/**
 * Formats the score of a numerical rating field by ensuring that the
 * score is rounded to the first decimal place and then padding it with
 * a ".0" if need be.
 *
 * @param $score
 *  The numerical field score to be formatted.
 *
 * @return
 *  The formatted numerical score.
 */
function formatFieldScore($score)
{
	// if the score is empty, return 0.0
	if (empty($score))
	{
		return 0.0;
	}
		
	// otherwise, the round score to the first decimal place
	$score = round($score, 1);
		
	// if the score does not have a decimal spot, that means that
	// the score was rounded to an integer and a '.0' needs to be
	// appended to the score
	if (strpos($score, ".") === false)
	{
		$score .= ".0";
	}
		
	// return the formatted score
	return $score;
}

?>