<?php

$config["drilldown_generator.countries"] = array(
	// drilldown configurations for the country with an ID of 0
	"0" => array(
			"country_code" => "us",
			"drillbit_path" => APPPATH . "modules/drilldown_generator/resources/drillbits",
			"drilldown_dump_path" => APPPATH . "modules/drilldown_generator/resources/drilldown_dumps",
			"drilldown_output_path" => APPPATH . "modules/drilldown_generator/resources/drilldown_output",
			"dump_states" => true
		)
);

?>