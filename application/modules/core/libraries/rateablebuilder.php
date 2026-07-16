<?php

/**
 * @ingroup core
 *
 * @brief
 * Abstract class to be implemented when a Rateable object needs
 * to be built.
 *
 * This abstract class is to be implemented when a Rateable object,
 * such as an Organization or Perosn, needs to constructed by accessing
 * data from the database.
 *
 * @author Adrian Rosebrock
 */

abstract class RateableBuilder
{
	/**
	 * Variable used to interface with CodeIgniter.
	 *
	 * @var $ci
	 */
	var $ci = null;
	/**
	 * Boolean variable used to determine if the rateable object is
	 * valid or not.
	 *
	 * @var $valid
	 */
	var $valid = false;
	
	/**
	 * Construct the RateableBuilder.
	 */
	public function __construct()
	{
		// connect the class with CodeIgniter
		$this->ci = &get_instance();
	}
	
	/**
	 * Determine whether or not the rateable object is valid or
	 * not.
	 *
	 * @return
	 *  TRUE if the rateable object is valid, FALSE if not.
	 */
	public function isValid()
	{
		// return whether or not the rateable object is valid or
		// not
		return $this->valid;
	}

	/**
	 * Set whether or not the rateable object is valid or not.
	 *
	 * @param $valid
	 *  Boolean variable, TRUE if the rateable object is valid
	 *  and FALSE if not.
	 */
	public function setValid($valid)
	{
		// update the valid variable
		$this->valid = $valid;
	}
	
	/**
	 * Abstract method to be implemented when the rateable object
	 * needs to be built. This method should also update the $valid
	 * variable if the rateable object is valid.
	 */
	abstract public function build();
}

?>