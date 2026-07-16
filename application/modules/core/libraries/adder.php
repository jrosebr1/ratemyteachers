<?php

/**
 * @ingroup core
 *
 * @brief
 * Abstract class to be extended whenever an object should be added
 * to the database.
 *
 * This abstract class is to be extended whenever an object needs
 * to be added to the database, such as when a person, rating, or
 * rebuttal is added.
 *
 * @author Adrian Rosebrock
 */

abstract class Adder
{
	/**
	 * Variable used to interface with CodeIgniter.
	 *
	 * @var $ci
	 */
	var $ci = null;
	
	/**
	 * Construct the Adder.
	 */
	public function __construct()
	{
		// connect the class with CodeIgniter
		$this->ci = &get_instance();
	}

	/**
	 * Abstract method that should be implemented when data should
	 * be added to the database.
	 */
	abstract public function add();
}

?>