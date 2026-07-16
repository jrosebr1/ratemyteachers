<?php

/**
 * @ingroup core
 *
 * @brief
 * Helper used to load CSS files common to every view.
 *
 * This helper is used to load CSS files that are common to every single
 * view that is rendered. This helper saves the developer time by ensuring
 * that certain CSS files are always available
 *
 * @author Adrian Rosebrock
 */

class CSSLoader
{
	/**
	 * Loads the common CSS files needed for every view that is rendered.
	 *
	 * @return array()
	 *  A list of CSS file paths.
	 */
	public static function load()
	{
		// define the array of common CSS files
		$files = array(
			"/css/core/common.css",
			"/css/core/notifier.css");
				
		// return the array of CSS files
		return $files;
	}
}

?>