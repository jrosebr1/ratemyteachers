<?php

/**
 * @ingroup core
 *
 * @brief
 * Abstract class to be extended whenter a set of objects needs
 * to be filtered.
 *
 * This abstract class is to be extended whenever a set of objects,
 * such as a set of organizations or people, need to be filterted.
 * For example, a list of people belonging to an organization might
 * need to be filtered by name.
 *
 * @author Adrian Rosebrock
 */

abstract class Filter
{
	/**
	 * Variable used to interface with CodeIgniter.
	 *
	 * @var $ci
	 */
	var $ci = null;
	
	/**
	 * Construct the Filter.
	 */
	public function __construct()
	{
		// connect the class with CodeIgniter
		$this->ci = &get_instance();
	}
	
	/**
	 * Returns the set of alhphabetical filters that could be used
	 * filter objects by name. The 'ALL' keyword is included in the
	 * returned set since it might be necessary to filter by 'ALL'
	 * objects, implying that no filter is actually used.
	 *
	 * @return array()
	 *  The set of alphabetical filters, including 'ALL'.
	 */
	public static function getAlphabetFilters()
	{
		// initialize the alphabet and array of filters
		$alpha = "abcdefghijklmnopqrstuvwxyz";
		$filters = array("ALL");
		
		// loop over the alphabet
		for ($enumAlpha = 0; $enumAlpha < strlen($alpha); $enumAlpha++)
		{
			// add the current letter to the filter
			$filters[] = strtoupper($alpha[$enumAlpha]);
		}
		
		// return the array of filters
		return $filters;
	}

	/**
	 * Abstract method that should be implemented when a set of
	 * objects needs to be filtered according to a set of rules.
	 */
	abstract public function apply();
}

?>