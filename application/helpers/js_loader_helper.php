<?php

/**
 * @ingroup core
 *
 * @brief
 * Helper used to load JavaScript files common to every view.
 *
 * This helper is used to load JavaScript files that are common to every
 * single view that is rendered. This helper saves the developer time by
 * ensuring that certain JavaScript files are always available.
 *
 * @author Adrian Rosebrock
 */

class JSLoader
{
	/**
	 * Loads the common JavaScript files needed for every view that is
	 * rendered.
	 *
	 * @return array()
	 *  A list of JavaScript file paths.
	 */
	public static function load()
	{
		// define the array of common JavaScript libraries
		$libs = array(
			"/js/core/mootools-1.2.1-core.js",
			"/js/core/mootools-1.2-more.js",
			"/js/core/notifier.js");
		
		// return the array of JavaScript libraries
		return $libs;
	}
}

?>