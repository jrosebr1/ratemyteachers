<?php

/**
 * @ingroup core
 *
 * @brief
 * Extends CI_Form_validation CodeIgniter library.
 *
 * This class extends the CI_Form_validation library to include a
 * method used to return a dictionary of errors.
 *
 * @author Adrian Rosebrock
 */

class CIEXT_Form_validation extends CI_Form_validation
{
	/**
	 * Construct the parent class.
	 */
	public function __construct($config = array())
	{
		// call the parent constructor
		parent::__construct($config);
	}
	
	/**
	 * Returns a dictionary of error messages after a form has been
	 * submitted for validation. Normally, when using the form validation
	 * library, you would have to know the key to the error dictionary to
	 * fetch the error, but when building a generic system, this is simply
	 * not possible. This way, a dictionary can be returned an errors can
	 * be fetched based on the field name in the "form_validation.php"
	 * configuration file.
	 *
	 * @returns array()
	 *  A dictionary of errors after a form has been submitted, where the
	 *  key is the field name and the value is the error message.
	 *
	 * @see form_validation.php
	 * @author Adrian Rosebrock
	 */
	public function error_array()
	{
		// if there are no errors, then return false
		if (count($this->_error_array) == 0)
		{
			return FALSE;
		}
		
		// otherwise, return the error array
		return $this->_error_array;
	}
}

?>