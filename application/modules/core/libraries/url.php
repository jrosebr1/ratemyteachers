<?php

/**
 * @ingroup core
 *
 * @brief
 * Abstract class to be implemented when a URL needs to be
 * constructed.
 *
 * This abstract class is to be implemented when a rateable
 * URL needs to be constructed. This class also provides
 * helper methods to aid in the construction of the URL.
 *
 * @author Adrian Rosebrock
 */

abstract class URL
{
	/**
	 * Variable used to interface with CodeIgniter.
	 *
	 * @var $ci
	 */
	var $ci = null;
	/**
	 * Core configuration of tables.
	 *
	 * @var $coreTables
	 * @see site.php
	 */
	var $coreTables = array();
	/**
	 * Core configuration of table schemas.
	 *
	 * @var $coreSchemas
	 * @see site.php
	 */
	var $coreSchemas = array();
	/**
	 * Core configuration of constants.
	 *
	 * @var $coreConstants
	 * @see core.php
	 */
	var $coreConstants = array();
	/**
	 * Core configuration of URL character trailers.
	 *
	 * @var $coreTrailers
	 * @see core.php
	 */
	var $coreTrailers = array();
	/**
	 * Boolean varaible used to determine if the URL is valid
	 * or not.
	 *
	 * @var $valid
	 */
	var $valid = false;
	
	/**
	 * Construct the URL.
	 */
	public function __construct()
	{
		// connect the class with CodeIgniter and grab the core tables,
		// schemas, constants, and URL trailers
		$this->ci = &get_instance();
		$this->coreTables = $this->ci->config->item("core.tables");
		$this->coreSchemas = $this->ci->config->item("core.tables.schemas");
		$this->coreConstants = $this->ci->config->item("core.constants");
		$this->coreTrailers = $this->ci->config->item("core.url.trailers");
	}
	
	/**
	 * Determine if the URL is valid or not.
	 *
	 * @return
	 *  TRUE if the URL is valid, FALSE if not.
	 */
	public function isValid()
	{
		// return if the URL (based on the organization and whether it
		// exists in the Organizations table) is valid or not
		return $this->valid;
	}
	
	/**
	 * Set whether or not the URL is valid.
	 *
	 * @param $valid
	 *  TRUE if the URL is valid, FALSE if not.
	 */
	public function setValid($valid)
	{
		// update the valid variable
		$this->valid = $valid;
	}
	
	/**
	 * Cleanup the URL by removing multiple occurrences of
	 * dashes.
	 *
	 * @param $url
	 *  The URL to be cleaned.
	 *
	 * @return
	 *  The cleaned URL.
	 */
	public function clean($url)
	{
		// cleanup the URL by replacing multiple occurrences of dashes
		// with a single dash
		return preg_replace("/-{2,}/", "-", $url);
	}
	
	/**
	 * Abstract method that should be implemented to create the
	 * actual URL based on the ID
	 *
	 * @param $id
	 *  ID of a rateable object that the URL should be built
	 *  around.
	 */
	abstract public function build($id);
	
	/**
	 * Abstract method that should be implemented to return
	 * the URL of the next tier that can be redirected to if
	 * the URL is not valid.
	 */
	abstract public function nextTier();
	
	/**
	 * Abstract method that should be implemented to determine
	 * if the URL is correct or not.
	 *
	 * @param $uri
	 *  The URI the user is currently viewing.
	 * @param $id
	 *  The ID of the rateable object.
	 */
	abstract public function isCorrect($uri, $id);
}

?>