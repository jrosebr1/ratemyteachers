<?php

/**
 * @ingroup core
 *
 * @brief
 * Abstract class to be extended when filter options need to be
 * parsed.
 *
 * This abstract class is to be extended whenever a set of filter
 * options needs to be parsed.
 *
 * @author Adrian Rosebrock
 */

abstract class FilterParser
{
	/**
	 * Variable used to interface with CodeIgniter.
	 *
	 * @var $ci
	 */
	var $ci = null;
	
	/**
	 * Construct the FilterParser.
	 */
	public function __construct()
	{
		// connect the class with CodeIgniter
		$this->ci = &get_instance();
	}
	
	/**
	 * Abstract method that should be implemented when a set of
	 * filter options needs to be parsed.
	 */
	abstract public function parse();
}

?>