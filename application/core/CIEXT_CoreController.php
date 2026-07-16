<?php

/**
 * @ingroup core
 *
 * @brief
 * Extends the CI_Controller so sitewide packages can be loaded and sidewide
 * classes can be initialized.
 *
 * This controller extends the core CodeIgniter Controller (CI_Controller)
 * so that sitewide packages can be loaded, such as the core, and so that
 * sitewide classes can be initialized, such as the "user" class.
 *
 * @see package_loader_helper.php
 * @see usersession.php
 * @author Adrian Rosebrock
 */

class CIEXT_CoreController extends CI_Controller
{
	/**
	 * UserSession variable used to track a user while they are browsing
	 * the site.
	 *
	 * @var UserSession $user
	 */
	var $user = null;
	
	/**
	 * Construct the controller, load the "core" package, and create the
	 * user session.
	 */
	public function __construct()
	{
		// construct the controller and load the core package
		parent::__construct();
		PackageLoader::load("core");
		
		// initialize the user and away we go
		$this->user = new UserSession();
	}
}

?>