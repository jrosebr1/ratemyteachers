<?php

$config["drilldown.tables"]["RandomDrilldown"] = "RandomDirectoryURL";
$config["drilldown.tables.schemas"]["RandomDrilldown"] = array(
	"ID" => "RDID",
	"CountryID" => "RCountry",
	"Type" => "RDType",
	"URL" => "RDURL");

$config["drilldown.constants"]["drilldown_output_path"] = APPPATH . "modules/drilldown/resources/drilldownoutput";
$config["drilldown.constants"]["valid_drilldown_types"] = array("organization", "person");

$config["drilldown.constants"]["num_random_org_selects"] = 500;
$config["drilldown.constants"]["num_random_person_selects"] = 500;

$config["drilldown.constants"]["num_org_homepage_grabs"] = 2;
$config["drilldown.constants"]["num_person_homepage_grabs"] = 2;

$config["drilldown.type_mappings"]["organization"] = array(
	"path_name" => "school",
	"display_name" => "organization",
	"type" => 0);
$config["drilldown.type_mappings"]["person"] = array(
	"path_name" => "teacher",
	"display_name" => "person",
	"type" => 1);

$config["drilldown.url_mappings"]["organization"] = "OrganizationURL";
$config["drilldown.url_mappings"]["person"] = "PersonReviewURL";

?>