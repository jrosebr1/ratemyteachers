<?php

class RatingRemover
{
	public static function remove($ratingID, $personID, $orgID, $status = -4)
	{
		// grab the core tables and schemas
		$ci = &get_instance();
		$coreTables = $ci->config->item("core.tables");
		$coreSchemas = $ci->config->item("core.tables.schemas");
		
		// construct the query to hide the rating
		$sql = "UPDATE " . $coreTables["Ratings"] . " ";
		$sql .= "SET " . $coreSchemas["Ratings"]["Status"] . "=? ";
		$sql .= "WHERE " . $coreSchemas["Ratings"]["ID"] . "=?;";
		$bindings = array($status, $ratingID);
		
		// execute the query
		$ci->db->query($sql, $bindings);
		
		// sync the person
		$sp = new SyncPerson($personID);
		$sp->sync();
		
		// sync the organization
		$so = new SyncOrganization($orgID);
		$so->sync();
	}
}

?>