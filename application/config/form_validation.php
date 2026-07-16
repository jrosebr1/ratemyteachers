<?php

/**
 * @ingroup core
 * @file form_validation.php
 *
 * @brief
 * CodeIgniter form validation configuration file used to configure various
 * forms on the rating platform.
 *
 * @author Adrian Rosebrock
 */

/*
 * Looking for the form to add ratings? It's actually located in the site
 * configuration file so we can easily add numerical fields to the platform
 * as well as manage the non-numerical fields.
 */

/**
 * Dictionary of form names as keys and form configurations as values.
 */
$config = array(
		// define the rules for the form to used for logging users into their
		// acounts
		"login" => array(
				array(
					"field" => "email",
					"label" => "Email",
					"rules" => "trim|required|valid_email|xss_clean"
					),
				array(
					"field" => "password",
					"label" => "Password",
					"rules" => "trim|required|xss_clean"
					)
			),
		// define the rules for the form used for searching for organizations
		"search" => array(
				array(
					"field" => "search_org",
					"label" => "Search",
					"rules" => "trim|required|xss_clean",
					),
			),
		// define the rules for the form used to add people to organizations
		"add_person" => array(
				array(
					"field" => "firstname",
					"label" => "First Name",
					"rules" => "trim|required|min_length[3]|max_length[20]|xss_clean"
					),
				array(
					"field" => "lastname",
					"label" => "Last Name",
					"rules" => "trim|required|min_length[3]|max_length[30]|xss_clean"
					),
				array(
					"field" => "dept_list",
					"label" => "Department",
					"rules" => "trim|required|xss_clean|callback_dept_check"
					),
				array(
					"field" => "gender",
					"label" => "Gender",
					"rules" => "trim|required|xss_clean|callback_gender_check"
					)
			),
		// define the rules for the form used to add rebuttals
		"add_rebuttal" => array(
				array(
					"field" => "rating_id",
					"label" => "Rating ID",
					"rules" => "trim|required|integer|xss_clean|callback_rating_id_check"
					),
				array(
					"field" => "rebuttal_comment",
					"label" => "Rebuttal Comment",
					"rules" => "trim|required|min_length[5]|max_length[200]|xss_clean"
					),
			),
		// define the rules for the form used to flag ratings
		"flag_rating" => array(
				array(
					"field" => "rating_id",
					"label" => "Rating ID",
					"rules" => "trim|required|integer|xss_clean|callback_rating_id_check"
					),
				array(
					"field" => "flag_reason",
					"label" => "Flag Reason",
					"rules" => "trim|required|min_length[5]|max_length[200]|xss_clean"
					)
			),
		// define the rules for the form used to flag rebuttals
		"flag_rebuttal" => array(
				array(
					"field" => "rebuttal_id",
					"label" => "Rebuttal ID",
					"rules" => "trim|required|integer|xss_clean|callback_rebuttal_id_check"
					),
				array(
					"field" => "flag_reason",
					"label" => "Flag Reason",
					"rules" => "trim|required|min_length[5]|max_length[200]|xss_clean"
					)
			)
	);

?>