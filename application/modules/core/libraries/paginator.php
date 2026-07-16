<?php

/**
 * @ingroup core
 *
 * @brief
 * Class used to paginate a list of objects.
 *
 * This class is used to generate a list of pages, including the
 * page number and the page URL for a list of objects.
 *
 * @author Adrian Rosebrock
 */

class Paginator
{
	/**
	 * Base URL that the page numbers will be appended to.
	 *
	 * @var $baseURL
	 */
	var $baseURL = null;
	/**
	 * The current page number the user is viewing.
	 *
	 * @var $curPage
	 */
	var $curPage = 0;
	/**
	 * The total number of entries for the particular set of
	 * objects to be paginated.
	 *
	 * @var $totalRows
	 */
	var $totalRows = 0;
	/**
	 * Total number of entries per page.
	 *
	 * @var $rowsPerPage
	 */
	var $rowsPerPage = 0;
	/**
	 * Largest possible page number based on the total number of
	 * entries and the number of entries per page.
	 *
	 * @var $maxPage
	 */
	var $maxPage = 0;
	
	/**
	 * Construct the Paginator.
	 *
	 * @param $baseURL
	 *  Base URL that the page numbers will be appended to.
	 * @param $curPage
	 *  The current page number the user is viewing.
	 * @param $totalRows
	 *  The total number of entries for the particular set of
	 *  objects to be paginated.
	 * @param $rowsPerPage
	 *  Total number of entries per page.
	 */
	public function __construct($baseURL, $curPage, $totalRows, $rowsPerPage)
	{
		// store the base URL, current page, total number of rows, and
		// the number of rows per page
		$this->baseURL = $baseURL;
		$this->curPage = $curPage;
		$this->totalRows = $totalRows;
		$this->rowsPerPage = $rowsPerPage;

		// calculate the largest possible page number
		$this->maxPage = ceil($this->totalRows / $this->rowsPerPage);
	}
	
	/**
	 * Paginates a set of objects and returns the list of page
	 * numbers and page URLs.
	 *
	 * @return array()
	 *  An array of page numbers and coressponding page URLs.
	 */
	public function paginate()
	{
		// initialize the list of pages
		$pages = array();
		
		// loop over the valid pages
		for ($enumPages = 1; $enumPages <= $this->maxPage; $enumPages++)
		{
			// construct the URL normally
			$url = $this->baseURL . "/" . $enumPages;
			
			// if the current page number is '1', then do not include the
			// page number in the URL
			if ($enumPages == 1)
			{
				$url = $this->baseURL;
			}
			
			// add each page to the pages list
			$pages[] = array(
						"pagination_url" => $url,
						"pagination_num" => $enumPages);
		}
		
		// return the list of pages
		return $pages;
	}

	/**
	 * Determines if the paginator is redirectable, such as if
	 * the current page is zero or exceeds $maxPage.
	 *
	 * @return
	 *  TRUE if the paginator is redirectable, FALSE if not.
	 */
	public function isRedirectable()
	{
		// if the page number is less than or equal to zero, redirect
		// to original organization URL
		if ($this->curPage <= 0)
		{
			return $this->baseURL;
		}
		
		// if the page number is greater than the largest possible page,
		// redirect to the URL with the largest possible page
		else if ($this->curPage > $this->maxPage && $this->maxPage != 0)
		{
			return ($this->baseURL . "/" . $this->maxPage);
		}
		
		// return false, the page is not redirectable
		return false;
	}
}

?>