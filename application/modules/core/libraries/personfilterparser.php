<?php

/**
 * @ingroup core
 *
 * @brief
 * Class used to parse options when filtering a list of persons.
 *
 * This class is used to parse options when filtering a list
 * of persons.
 *
 * @see filterparse.php
 * @author Adrian Rosebrock
 */

class PersonFilterParser extends FilterParser
{
	/**
	 * Dictionary of filter options.
	 *
	 * @var $options
	 */
	var $options = array();
	/**
	 * Organization that the filter options belong to.
	 *
	 * @var Organization $org
	 */
	var $org = null;

	/**
	 * Construct the PersonFilterParser.
	 *
	 * @param $options
	 *  Dictionary of filter options.
	 */
	public function __construct($options)
	{
		// call the parent constructor and store the parseable filter
		// options
		parent::__construct();
		$this->options = $options;
		
		// build an organization from the ID supplied in the options
		$orgID = empty($options["id"]) ? -1 : $options["id"];
		$ob = new OrganizationBuilder($orgID);
		$this->org = $ob->build();
	}
	
	/**
	 * Parse and sanitize the filter options.
	 *
	 * @return array()
	 *  A dictionary of parsed and santized filter options.
	 */
	public function parse()
	{
		// if the organization is empty, then the organization does
		// not exist in the organizations table, so return false
		if (empty($this->org))
		{
			return false;
		}
		
		// sanitize all of the options
		$this->sanitizeURL();
		$this->sanitizeLetter();
		$this->sanitizeDepartment();
		$this->sanitizeOrderBy();
		$this->sanitizeOrderDir();
		
		// return the formatted options
		return $this->options;
	}
	
	/**
	 * Sanitize the URL by ensuring the organization URL is correct.
	 *
	 * @see organizationurl.php
	 */
	private function sanitizeURL()
	{
		// build the URL for the organization to ensure it is correct
		$ub = new OrganizationURL();
		$this->options["url"] = $ub->build($this->org->getID());
	}
	
	/**
	 * Sanitize the letter by ensuring it is part of the alphabet
	 * filters list.
	 *
	 * @see filter.php
	 */
	private function sanitizeLetter()
	{
		// get the alphabet filter and ensure the letter option is
		// uppercase
		$filter = new OrganizationFilter();
		$alpha = Filter::getAlphabetFilters();
		$this->options["letter"] = strtoupper($this->options["letter"]);
		
		// loop over the alphabet filters
		for ($enumAlpha = 0; $enumAlpha < count($alpha); $enumAlpha++)
		{
			// if the filters match, then the letter filter is valid
			if ($alpha[$enumAlpha] == $this->options["letter"])
			{
				return;
			}
		}
		
		// otherwise, a match was not found, so make the letter be
		// blank, indicating that no filtering on the letter should
		// be done
		$this->options["letter"] = "";
	}
	
	/**
	 * Sanitize the department by ensuring it belongs to the set
	 * of departments that exist in the particular organization.
	 */
	private function sanitizeDepartment()
	{
		// get the set of departments from the organization and make
		// the department in the options dictionary uppercase
		$depts = $this->org->getDepartments();
		$this->options["dept"] = strtoupper($this->options["dept"]);
		
		// loop over the departments
		for ($enumDepts = 0; $enumDepts < count($depts); $enumDepts++)
		{
			// if the current departments match, then the department
			// filter is valid
			if (strtoupper($depts[$enumDepts]) == $this->options["dept"])
			{
				return;
			}
		}
		
		// otherwise, the filter is invalid so set the department
		// to be blank, indicating that no filter on the department
		// should be done
		$this->options["dept"] = "";
	}
	
	/**
	 * Sanitize the order by column by ensuring it exists in the
	 * table schemas.
	 *
	 * @see site.php
	 */
	private function sanitizeOrderBy()
	{
		// grab the core table schemas, get the columns for the people
		// schema, and make the order by option uppercase
		$coreSchemas = $this->ci->config->item("core.tables.schemas");
		$columns = array_keys($coreSchemas["People"]);
		$this->options["order_by"] = strtoupper($this->options["order_by"]);
		
		// loop over the columns
		for ($enumCols = 0; $enumCols < count($columns); $enumCols++)
		{
			// if the current column matches, then the order by
			// filter is valid
			if (strtoupper($columns[$enumCols]) == $this->options["order_by"])
			{
				$this->options["order_by"] = $columns[$enumCols];
				return;
			}
		}
		
		// otherwise, the order by filter is invalid and should
		// default to the name column
		$this->options["order_by"] = "LastName";
	}
	
	/**
	 * Sanitize the order direction by ensuring it is either 'ASC"
	 * for ascending or 'DESC' for descending.
	 */
	private function sanitizeOrderDir()
	{	
		// make the order direction uppercase
		$this->options["order_dir"] = strtoupper($this->options["order_dir"]);
		
		// if the order direction is 'ASC' or 'DESC', then the
		// direction is valid
		if ($this->options["order_dir"] == "ASC" || $this->options["order_dir"] == "DESC")
		{
			return;
		}
		
		// otherwise, the order direction is invalid and should
		// default to 'ASC'
		$this->options["order_dir"] = "ASC";
	}

	/**
	 * Generates a clean slate of filter options.
	 *
	 * @param $orgID
	 *  ID of the organization the filter options belong to.
	 *
	 * @return array()
	 *  A dictionary of the filter options.
	 */
	public static function cleanSlate($orgID)
	{
		// build the URL for the organization
		$ub = new OrganizationURL();
		$orgURL = $ub->build($orgID);
	
		// define the dictionary of options to be their default values
		$options = array(
					"id" => $orgID,
					"url" => $orgURL,
					"letter" => "",
					"dept" => "",
					"order_by" => "LastName",
					"order_dir" => "ASC");

		// return the default options
		return $options;
	}
}

?>