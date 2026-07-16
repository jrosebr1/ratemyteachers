<?php

$config["rss_feeds.constants"]["homepage_person_limit"] = 100;
$config["rss_feeds.constants"]["org_regen_seconds"] = 300;
$config["rss_feeds.constants"]["person_regen_seconds"] = 300;

$config["rss_feeds.tables"]["HomepageRSS"] = "HomepageRSS";
$config["rss_feeds.tables"]["OrganizationRSS"] = "SchoolRSS";
$config["rss_feeds.tables"]["PersonRSS"] = "TeacherRSS";

$config["rss_feeds.tables.schemas"]["HomepageRSS"] = array(
	"ID" => "HID",
	"CountryID" => "HCountry",
	"CreationDate" => "HCreationDate",
	"GenerateDate" => "HGenDate",
	"FeedData" => "HData");

$config["rss_feeds.tables.schemas"]["OrganizationRSS"] = array(
	"ID" => "SrID",
	"OrgID" => "SrSID",
	"CreationDate" => "SrCreationDate",
	"GenerateDate" => "SrGenDate",
	"FeedData" => "SrData");

$config["rss_feeds.tables.schemas"]["PersonRSS"] = array(
	"ID" => "TrID",
	"PersonID" => "TrTID",
	"CreationDate" => "TrCreationDate",
	"GenerateDate" => "TrGenDate",
	"FeedData" => "TrData");

?>