<?php

$config["recently_searched.type_mappings"]["org"] = array(
	"search" => array(
		"text" => "searched for",
		"num" => 0),
	"rated" => array(
		"text" => "rated",
		"num" => 1)
	);
$config["recently_searched.type_mappings"]["person"] = array(
	"search" => array(
		"text" => "searched for",
		"num" => 0),
	"rated" => array(
		"text" => "rated",
		"num" => 1)
	);

$config["recently_searched.constants"]["num_org_grabs"] = 2000;
$config["recently_searched.constants"]["num_person_grabs"] = 2000;

$config["recently_searched.constants"]["search_num_org_ratings"] = 40;
$config["recently_searched.constants"]["rated_num_org_ratings"] = 10;

//$config["recently_searched.constants"]["search_num_person_ratings"] = 20;
$config["recently_searched.constants"]["search_num_person_ratings"] = 5;
$config["recently_searched.constants"]["rated_num_person_ratings"] = 10;

$config["recently_searched.constants"]["last_rated_interval"] = "1 WEEK";

$config["recently_searched.tables"]["RecentlySearchedOrgs"] = "RecentlySearchedSchools";
$config["recently_searched.tables"]["RecentlySearchedPersons"] = "RecentlySearchedTeachers";

$config["recently_searched.tables.schemas"]["RecentlySearchedOrgs"] = array(
	"ID" => "SID",
	"CountryID" => "SCountry",
	"Type" => "SType",
	"Name" => "SName");
$config["recently_searched.tables.schemas"]["RecentlySearchedPersons"] = array(
	"ID" => "TID",
	"CountryID" => "SCountry",
	"Type" => "SType",
	"Name" => "TName");

?>