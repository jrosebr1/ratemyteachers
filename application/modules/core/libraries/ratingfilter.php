<?php

/**
 * @ingroup core
 *
 * @brief
 * Class used to filter ratings based on page number.
 *
 * This class extends the abstract Filter class and is used to
 * filter ratings based on the page number that the user is
 * currently viewing. The number of ratings per page can be tweaked
 * by editing core.php.
 *
 * @see filter.php
 * @see core.php
 * @author Adrian Rosebrock
 */

class RatingFilter extends Filter
{
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
	 * Core configuration of generic rateable objects.
	 *
	 * @var $coreTables
	 * @see site.php
	 */
	var $coreClasses = array();
	/**
	 * ID of the person the ratings belong to.
	 *
	 * @var $personID
	 */
	var $personID = null;
	/**
	 * The page number (of the person) that is currently being
	 * viewed.
	 *
	 * @var $pageNum
	 */
	var $pageNum = null;
	
	/**
	 * Construct the RatingFilter.
	 *
	 * @param $personID
	 *  ID of the person the ratings belong to.
	 *
	 * @param $pageNum
	 *  The page number (of the person) that is currently being
	 *  viewed.
	 */
	public function __construct($personID, $pageNum)
	{
		// call the parent constructor and store the person ID and
		// page number
		parent::__construct();
		$this->personID = $personID;
		$this->pageNum = $pageNum;
		
		// store the core tables, schemas, constants, and classes
		$this->coreTables = $this->ci->config->item("core.tables");
		$this->coreSchemas = $this->ci->config->item("core.tables.schemas");
		$this->coreConstants = $this->ci->config->item("core.constants");
		$this->coreClasses = $this->ci->config->item("core.classes");
	}
	
	/**
	 * Apply the filter based on page number and return an array
	 * containing the total number of ratings the filter matches
	 * along with a list of ratings.
	 *
	 * @return array()
	 *  An array containing the total number of matched ratings
	 *  and a list of ratings to be displayed.
	 */
	public function apply()
	{
		// build the queries based on the filters and execute the query
		// to get the total amount of matches
		$queries = $this->buildQueries();
		$query = $this->ci->db->query($queries["select_total"], $queries["bindings"]);
		$row = $query->row_array();
		$totalRatings = $row["total"];
		
		// execute the query to select the ratings based on the filters
		// and the pagination, then initialize the ratings list
		$query = $this->ci->db->query($queries["select_ratings"], $queries["bindings"]);
		$ratings = array();

		// build the person
		$pb = new PersonBuilder($this->personID);
		$person = $pb->build();
		
		// loop over the results
		foreach ($query->result_array() as $row)
		{
			// create the rating and add the rating to the list
			$Class = $this->coreClasses["Rating"];
			$ratings[] = new $Class($person, $row);
		}
		// create a dictionary to store the total amount of ratings
		// matching the filter as well as the ratings that matched
		// the filter and the pagination
		$ratingsInfo = array(
						"total_matched" => $totalRatings,
						"ratings_list" => $ratings);
		
		// return the ratings dictionary
		return $ratingsInfo;
	}

	/**
	 * Builds the query used to filter ratings.
	 *
	 * @return array()
	 *  The query used to filter the ratings, along with the
	 *  bindings needed to execute the query.
	 */
	private function buildQueries()
	{
		// starting building the query to select the ratings
		// construct the query to get the rating information
		$sql = "SELECT r.* ";
		$sql .= "FROM " . $this->coreTables["Ratings"] . " r ";
		$sql .= "INNER JOIN " . $this->coreTables["People"] . " p ON ";
		$sql .= "r." . $this->coreSchemas["Ratings"]["PersonID"] . "=p." . $this->coreSchemas["People"]["ID"] . " ";
		$sql .= "WHERE r." . $this->coreSchemas["Ratings"]["PersonID"] . "=? AND ";
		$sql .= "r." . $this->coreSchemas["Ratings"]["Status"] . " >= " . $this->coreConstants["min_rating_status"] . " AND ";
		$sql .= "p." . $this->coreSchemas["People"]["Status"] . " >= " . $this->coreConstants["min_person_status"] . " ";

		// at this point, create the query that will be used to
		// to the total amount of ratings matching the query so
		// that pagination can be done
		$sqlTotal = str_replace("SELECT r.*", "SELECT COUNT(*) AS total", trim($sql));
		$sqlTotal .= ";";
		
		// order the ratings by ID, provide the offset based on
		// the page number and limit the results
		$perPage = $this->coreConstants["ratings_per_page"];
		$sql .= "ORDER BY " . $this->coreSchemas["Ratings"]["ID"] . " DESC ";
		$sql .= "LIMIT " . (($this->pageNum - 1) * $perPage) . ", " . $perPage . ";";

		// define the bindings array
		$bindings = array($this->personID);

		// create a dictionary to hold the queries and bindings
		$queries = array(
					"select_ratings" => $sql,
					"select_total" => $sqlTotal,
					"bindings" => $bindings);
		
		// return the finished queries and bindings
		return $queries;
	}
}

?>