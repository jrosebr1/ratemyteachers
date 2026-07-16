<?php

/**
 * @ingroup core
 *
 * @brief
 * Abstract class to be extended to form a tier of the rating 
 * platform.
 *
 * This abstract class is used to represent a tier of the rating
 * platform. All tiers (organizations, person, rating) should be
 * extended from this class.
 *
 * @author Adrian Rosebrock
 */

abstract class Rateable
{
	/**
	 * Variable used to interface with CodeIgniter.
	 *
	 * @var $ci
	 */
	var $ci = null;
	/**
	 * Dictionary of data used to represent the rateable object.
	 *
	 * @var $data
	 */
	var $data = array();

	/**
	 * Construct the Rateable.
	 */
	public function __construct()
	{
		// connect the class with CodeIgniter
		$this->ci = &get_instance();
	}

	/**
	 * Get either the entire rateable information dictionary, or a
	 * single value based on the supplied $key.
	 *
	 * @param $key
	 *  Key into the rateable information dictionary, which could be
	 *  null or an empty string.
	 *
	 * @return
	 *  Either the entire rateable information dictionary or a single
	 *  value based on the supplied $key.
	 */
	public function getInfo($key = null)
	{
		// if the key is empty, then return the entire data
		// dictionary
		if (empty($key))
		{
			return $this->data;
		}
		
		// otherwise, try to return the value with the supplied
		// key
		return $this->data[$key];
	}
	
	/**
	 * Abstract method that should be implemented to return the
	 * ID of the rateable object.
	 */
	abstract public function getID();
	
	/**
	 * Abstract method that should be implemented to return the
	 * formatted, displayable name of the rateable object.
	 */
	abstract public function getName();	
}

?>