<?php if ($breadcrumb["type"] == "org"): ?>
<?php echo $breadcrumb["org_name"]; ?>
<?php elseif ($breadcrumb["type"] == "request"): ?>
<a href="<?php echo $breadcrumb["org_url"]; ?>"><?php echo $breadcrumb["org_name"]; ?></a>
&raquo; <a id="breadcrumb_dept" href="#"><?php echo $breadcrumb["per_dept"]; ?></a>
&raquo; <?php echo $breadcrumb["per_name"]; ?>
<?php else: ?>
<a href="<?php echo $breadcrumb["org_url"]; ?>"><?php echo $breadcrumb["org_name"]; ?></a>
&raquo; <a id="breadcrumb_dept" href="#"><?php echo $breadcrumb["per_dept"]; ?></a>
&raquo; <a href="<?php echo $breadcrumb["per_req_url"]; ?>"><?php echo $breadcrumb["per_name"]; ?></a>
<?php endif; ?>

<?php if ($breadcrumb["type"] != "org"): ?>
<script type="text/javascript">
$("breadcrumb_dept").addEvent("click", function(event){
	// stop the event from propagating any further
	event.stop();
	event.stopPropagation();
											
	// initialize the person filter
	PersonFilter.init({
		"id": "<?php echo $org_id; ?>",
		"url": "<?php echo $breadcrumb["org_url"]; ?>",
		"letter": "<?php echo $org_filter_letter; ?>",
		"dept": "<?php echo $breadcrumb["per_dept"]; ?>",
		"order_by": "<?php echo $org_filter_orderby; ?>",
		"order_dir": "<?php echo $org_filter_orderdir; ?>",
		"order_mappings": <?php echo $org_filter_mappings; ?>});
											
	// apply the filter
	PersonFilter.filter();});
</script>
<?php endif; ?>