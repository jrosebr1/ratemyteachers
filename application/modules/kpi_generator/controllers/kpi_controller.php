<?php

class KPI_Controller extends CIEXT_CoreController
{
	public function __construct()
	{
		// construct the controller and load the KPI generator module
		parent::__construct();
		PackageLoader::load("kpi_generator");
	}
	
	public function generate_kpis()
	{
		// generate the KPIs
		$kpi = new KPIGenerator();
		$kpi->generate();
	}
}

?>