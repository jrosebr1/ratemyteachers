<?php

/**
 * @ingroup core
 * @file site.php
 *
 * @brief
 * Rating platform configuration file specific to the site being
 * developed.
 *
 * This file extends the core.php configuration file override
 * previous values or introduce new configuration values. Furthermore,
 * this file also includes the core table name, core table schemas,
 * and the rating form numerical and attribute fields.
 *
 * @see core.php
 * @author Adrian Rosebrock
 */

/**
 * Initialize the core tables dictionary.
 */
$config["core.tables"] = array();
/**
 * Initialize the core table schemas.
 */
$config["core.tables.schemas"] = array();



/**
 * Define the name of the table used for organizations.
 */
$config["core.tables"]["Organizations"] = "Schools";
/**
 * Define the name of the table used for persons.
 */
$config["core.tables"]["People"] = "Teachers";
/**
 * Define the name of the table used for ratings.
 */
$config["core.tables"]["Ratings"] = "Ratings";
/**
 * Define the name of the table used for ratings.
 */
$config["core.tables"]["Rebuttals"] = "Rebuttals";
/**
 * Define the name of the table used for users.
 */
$config["core.tables"]["Users"] = "People";
/**
 * Define the name of the table used for flagged ratings.
 */
$config["core.tables"]["FlaggedRatings"] = "FlaggedRatings";
/**
 * Define the name of the table used for flagged rebuttals.
 */
$config["core.tables"]["FlaggedRebuttals"] = "FlaggedRebuttals";
/**
 * Define the name of the table used for storing valid
 * department values.
 */
$config["core.tables"]["Departments"] = "ValidDepartments";
/**
 * Define the name of the table used to store state abbreviation
 * to full name mappings.
 */
$config["core.tables"]["StateMappings"] = "States";



/**
 * Define the schema for the 'Organizations' table.
 */
$config["core.tables.schemas"]["Organizations"] = array(
	"ID" => "SID",
	"Name" => "SName",
	"State" => "SState",
	"City" => "SCity",
	"CountryID" => "SCountry",
	"NumRatings" => "SRatings");
/**
 * Define the schema for the 'People table.
 */
$config["core.tables.schemas"]["People"] = array(
	"ID" => "TID",
	"OrgID" => "SID",
	"UserID" => "PID",
	"UserIP" => "TIP",
	"FirstName" => "TFName",
	"LastName" => "TLName",
	"Department" => "TDept",
	"Gender" => "TGender",
	"Status" => "TStatus",
	"NumRatings" => "TNumRatings",
	"AvgScore" => "TAvgRating",
	"DateAdded" => "TDate",
	"LastRatedDate" => "TRatedDate");
/**
 * Define the schema for the 'Ratings' table.
 */
$config["core.tables.schemas"]["Ratings"] = array(
	"ID" => "RID",
	"PersonID" => "TID",
	"UserID" => "PID",
	"UserIP" => "RIP",
	"Comment" => "RComments",
	"Date" => "RDate",
	"UserAgent" => "RUserAgent",
	"UserReferer" => "RReferer",
	"UserSessionID" => "RSession",
	"Status" => "RStatus");
/**
 * Define the schema for the 'Rebuttals' table.
 */
$config["core.tables.schemas"]["Rebuttals"] = array(
	"ID" => "RebuttalID",
	"RatingID" => "RID",
	"UserID" => "PID",
	"UserIP" => "IPAddr",
	"UserSessionID" => "SessionID",
	"Comment" => "RebuttalText",
	"Date" => "RebuttalTime",
	"Status" => "RebuttalStatus");
/**
 * Define the schema for the 'Users' table.
 */
$config["core.tables.schemas"]["Users"] = array(
	"ID" => "PID",
	"OrgID" => "SID",
	"FirstName" => "PFName",
	"LastName" => "PLName",
	"Email" => "PEmail",
	"Password" => "PPassword",
	"Type" => "PType",
	"Status" => "PStatus",
	"DateAdded" => "PDate");
/**
 * Define the schema for the 'FlaggedRatings' table.
 */
$config["core.tables.schemas"]["FlaggedRatings"] = array(
	"ID" => "FID",
	"RatingID" => "RID",
	"UserID" => "PID",
	"UserIP" => "FIP",
	"Reason" => "FReason");
/**
 * Define the schema for the 'FlaggedRebuttals' table.
 */
$config["core.tables.schemas"]["FlaggedRebuttals"] = array(
	"ID" => "FID",
	"RebuttalID" => "RebuttalID",
	"UserID" => "PID",
	"UserIP" => "FIP",
	"Reason" => "FReason");
/**
 * Define the schema for the 'Departments' table.
 */
$config["core.tables.schemas"]["Departments"] = array(
	"ID" => "VID",
	"Department" => "VDept");
/**
 * Define the schema for the 'StateMappings' table.
 */
$config["core.tables.schemas"]["StateMappings"] = array(
	"ID" => "SID",
	"CountryID" => "CID",
	"FullName" => "name",
	"Abbrev" => "code");



/**
 * Define the numerical rating fields for the rating form. The
 * key is the name of the field. The values are detailed below:
 * - field_label: The label that is displayed when rendered for
 *   the browser.
 * - field_rules: The list of rules that CodeIgniter will apply
 *   when processing the field.
 * - column: The column in the 'Ratings' table that is used when
 *   storing the numerical value.
 * - displayble: Boolean value used for determining if the value
 *   is displayable on the rating page.
 * - aggregate: The column in the 'Persons' table that is used
 *   when calculating aggregate values across all ratings for
 *   a particular person.
 * - start: The starting value for the possible range of values
 *   in the rating form.
 * - end: The ending vlaue for the possible range of values in
 *   the rating form.
 * - incr: The increment value for the possible range of values
 *   in the rating form.
 */
$config["site.ratings.numerical_fields"] = array(
	"easiness" => array(
		"field_label" => "Easiness",
		"field_rules" => "trim|required|integer|xss_clean",
		"column" => "REasy",
		"displayable" => true,
		"aggregate" => "TAvgEasy",
		"range" => array(
					"start" => 1,
					"end" => 5,
					"incr" => 1)
		),
	"helpfulness" => array(
		"field_label" => "Helpfulness",
		"field_rules" => "trim|required|integer|xss_clean",
		"column" => "RHelpful",
		"displayable" => true,
		"aggregate" => "TAvgHelpful",
		"range" => array(
			"start" => 1,
			"end" => 5,
			"incr" => 1)
		),
	"clarity" => array(
		"field_label" => "Clarity",
		"field_rules" => "trim|required|integer|xss_clean",
		"column" => "RClarity",
		"displayable" => true,
		"aggregate" => "TAvgClarity",
		"range" => array(
			"start" => 1,
			"end" => 5,
			"incr" => 1)
		),
	"knowledgeable" => array(
		"field_label" => "Knowledgeable",
		"field_rules" => "trim|required|integer|xss_clean",
		"column" => "RKnowledgeable",
		"displayable" => false,
		"aggregate" => false,
		"range" => array(
			"start" => 1,
			"end" => 5,
			"incr" => 1)
		),
	"strict" => array(
		"field_label" => "Strict",
		"field_rules" => "trim|required|integer|xss_clean",
		"column" => "RStrict",
		"displayable" => false,
		"aggregate" => false,
		"range" => array(
			"start" => 1,
			"end" => 5,
			"incr" => 1)
		)
	/*"popularity" => array(
		"field_label" => "Popularity",
		"field_rules" => "trim|required|integer|xss_clean",
		"column" => "",
		"displayable" => false,
		"aggregate" => false,
		"range" => array(
			"start" => 1,
			"end" => 5,
			"incr" => 1)
		)*/
);



/**
 * Define the attribute (qualitative) rating fields. The key is
 * the name of the field. The values are detailed below:
 * - field_label: The label that is displayed when rendered for
 *   the browser.
 * - field_rules: The list of rules that CodeIgniter will apply
 *   when processing the field.
 * - column: The column in the 'Ratings' table that is used when
 *   storing the attribute value.
 */
$config["site.ratings.attr_fields"] = array(
	"person_id" => array(
		"field_label" => "Person ID",
		"field_rules" => "trim|required|integer|xss_clean|callback_person_id_check",
		"column" => $config["core.tables.schemas"]["Ratings"]["PersonID"]
		),
	"rating_comment" => array(
		"field_label" => "Rating Comment",
		"field_rules" => "trim|required|min_length[5]|max_length[200]|xss_clean",
		"column" => $config["core.tables.schemas"]["Ratings"]["Comment"]
		)
);



$config["site.registration_form"] = array(
	"visited_step" => array(
		"org_id" => array(
			"field_label" => "Organization ID",
			"field_rules" => "trim|required|integer|xss_clean|callback_org_id_check"
			),
		"use_org" => array(
			"field_label" => "Use Organization",
			"field_rules" => "trim|required|integer|xss_clean"
			),
		),
	"select_org_step" => array(
		"org_id" => array(
			"field_label" => "Organization ID",
			"field_rules" => "trim|integer|xss_clean"
			),
		"search_org" => array(
			"field_label" => "Organization Name",
			"field_rules" => "trim|required|xss_clean",
			),
		"org_location" => array(
			"field_label" => "Location",
			"field_rules" => "trim|required|xss_clean"
			)
		),
	"user_info_step" => array(
		"first_name" => array(
			"field_label" => "First Name",
			"field_rules" => "trim|required|xss_clean|min_length[3]|max_length[30]|callback_name_check"
			),
		"email" => array(
			"field_label" => "E-Mail",
			"field_rules" => "trim|required|xss_clean|valid_email"
			),
		"conf_email" => array(
			"field_label" => "Re-type E-mail",
			"field_rules" => "trim|required|xss_clean|valid_email|callback_conf_email_check"
			),
		"password" => array(
			"field_label" => "Password",
			"field_rules" => "trim|required|xss_clean|min_length[4]|max_length[30]"
			),
		"org_id" => array(
			"field_label" => "Organization ID",
			"field_rules" => "trim|required|integer|xss_clean|callback_org_id_check"
			)
		)
);



$config["site.registration_form.country_mappings"] = array(
	"us" => array(
		"name" => "State",
		"method" => "Country::getStates"
		)
);

?>