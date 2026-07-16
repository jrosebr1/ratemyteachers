<?php

/**
 * @ingroup core
 *
 * @brief
 * Abstract class to be extended whenever statistical values
 * needs to be calculated.
 *
 * This abstract class is to be extended whenever aggregate,
 * statistical values need to be calculated.
 *
 * @author Adrian Rosebrock
 */

abstract class Stats
{
	/**
	 * Variable used to interface with CodeIgniter.
	 *
	 * @var $ci
	 */
	var $ci = null;
	/**
	 * Dictionary of calculated statistics.
	 *
	 * @var $stats
	 */
	var $stats = array();
	
	/**
	 * Construct the Stats.
	 */
	public function __construct()
	{
		// connect the class with CodeIgniter
		$this->ci = &get_instance();
	}
	
	/**
	 * Get the dictionary of calculated statistics.
	 *
	 * @return array()
	 *  The dictionary of calculated statistics.
	 */
	public function getStats()
	{
		// return the dictionary of statistics
		return $this->stats;
	}

	/**
	 * Abstract method that should be implemented when a set
	 * of statistical values needs to be calculated.
	 */
	abstract public function calculate();	
}

?>