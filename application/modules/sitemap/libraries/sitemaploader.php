<?php

abstract class SitemapLoader
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
	 */
	var $coreConstants = array();
	
	/**
	 * Construct the SitemapLoader.
	 */
	public function __construct()
	{
		// connect the class with CodeIgniter and grab the core tables,
		// schemas, and constants
		$this->ci = &get_instance();
		$this->coreTables = $this->ci->config->item("core.tables");
		$this->coreSchemas = $this->ci->config->item("core.tables.schemas");
		$this->coreConstants = $this->ci->config->item("core.constants");
	}

	/**
	 * Abstract method to be implemented to determine if the country
	 * using the sitemap loader uses states or cities as their main
	 * form of geographic disambiguation.
	 */	
	abstract public function useStates();
	
	/**
	 * Abstract method to be implemented to return a list of states
	 * for the country.
	 */
	abstract public function loadStates();
	
	/**
	 * Abstract method to be implemented to return a list of organizations
	 * from the supplied city in the country.
	 *
	 * @param $city
	 *  The city that the returned organizations reside in.
	 */
	abstract public function loadOrgs($city = null);
	
	/**
	 * Abstract method to be implemented to return the full name and
	 * state abbreviation for a supplied state.
	 *
	 * @param $state
	 *  The state that will be checked for extra information.
	 */
	abstract public function getStateInfo($state);
	
	/**
	 * Abstract method to be implemented to return the name of a city
	 * since it may have been manipuldated in the URL by the user.
	 *
	 * @param $city
	 *  The city that will be checked for extra information.
	 */
	abstract public function getCityInfo($city);
	
	/**
	 * Abstract method to be implemented to return the name of the state
	 * that the supplied city resides in.
	 *
	 * @param $city
	 *  The city that will be used to fetch state information.
	 */
	abstract public function getStateInfoFromCity($city);

	/**
	 * Abstract method to be implemented to determine if the supplied
	 * state is valid or not.
	 *
	 * @param $state
	 *  The state that will we becked for validity.
	 */	
	abstract public function isStateValid($state);

	/**
	 * Abstract method to be implemented to determine if the supplied
	 * city is valid or not.
	 *
	 * @param $city
	 *  The city that will be checked for validity.
	 */	
	abstract public function isCityValid($city);
}

?>