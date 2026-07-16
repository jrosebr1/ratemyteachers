<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
	<head>
 		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $org_name; ?> - <?php echo $org_city; ?>, <?php echo $org_state; ?> | <?php echo $this->config->item("core.sitename"); ?></title>
		<?php $this->load->view("common/head_libs"); ?>
		<script type="text/javascript" src="/js/core/person_filter.js"></script>
		<link rel="alternate" type="application/rss+xml" href="<?php echo $org_rss_url; ?>">
	</head>
	<body>
		<?php $this->load->view("common/header"); ?>
		
		<p>
			<?php $this->load->view("common/breadcrumb"); ?>
		</p>
		
		<h1><?php echo $org_name; ?></h1>
		
		<p>		
			<div>
				<?php echo $org_city; ?>, <?php echo $org_state; ?><br>
				Total Persons: <?php echo $org_total_persons; ?><br>
				Avg. Person Rating: <?php echo $org_avg_person_score; ?><br>
				Organization Ratings: <?php echo $org_total_ratings; ?><br>
			</div>
			<a href="/add_person">Add Person</A>
		</p>
		
		<p>
			<?php $this->load->view("organization_filter_alpha"); ?>
		</p>
		
		<p>
			Filter by department: <?php $this->load->view("organization_dept_select"); ?>
			<br>
			
			<table>
				<tr>
					<td width="50"><b>Rate</b></td>
					<td width="200">
						<b><a class="sort_filter" href="#">Name</a></b>
						<?php if ($org_filter_mappings_revr[$org_filter_orderby] == "Name"): ?>
						<img src="/images/sort_<?php echo strtolower($org_filter_orderdir); ?>.png" alt="">
						<?php endif; ?>
					</td>
					<td width="200">
						<b><a class="sort_filter" href="#">Department</a><b>
						<?php if ($org_filter_mappings_revr[$org_filter_orderby] == "Department"): ?>
						<img src="/images/sort_<?php echo strtolower($org_filter_orderdir); ?>.png" alt="">
						<?php endif; ?>
					</td>
				</tr>
				<?php foreach ($org_persons as $person): ?>
				<tr>
					<td><a href="<?php echo $person["person_url"]; ?>">Rate</a></td>
					<td><a href="<?php echo $person["person_url"]; ?>"><?php echo $person["person_name"]; ?></a></td>
					<td><?php echo $person["person_dept"]; ?></td>
				</tr>
				<?php endforeach; ?>
			</table>
		</p>

		<p>
			Page:
			<?php foreach ($org_pagination as $page): ?>
			<span>
				<?php if ($page["pagination_num"] == $org_cur_page): ?>
				<?php echo $page["pagination_num"]; ?>
				<?php else: ?>
				<a href="<?php echo $page["pagination_url"]; ?>"><?php echo $page["pagination_num"]; ?></a>
				<?php endif; ?>
			</span>
			<?php endforeach; ?>
		</p>
		
		<p>
			<?php $this->load->view("organization_filter_alpha"); ?>
		</p>
		
		<p>
			Schools Near <?php echo $org_name; ?>
			
			<table>
				<?php foreach ($org_nearby as $nearby): ?>
				<tr>
					<td>
						<a href="<?php echo $nearby["org_url"]; ?>"><?php echo $nearby["org_name"]; ?></a>
					</td>
				</tr>
				<?php endforeach; ?>
			</table>
		</p>
		
		<p>
			<?php echo $org_name; ?>'s Feed
			
			<table>
				<?php foreach ($feed_items as $feed_item): ?>
				<tr>
					<td>
						<a href="<?php echo $feed_item["feed_url"]; ?>"><?php echo $feed_item["feed_name"]; ?></a> from <?php echo $org_name; ?> was <?php echo $feed_item["feed_type"]; ?> <?php echo $feed_item["feed_date"]; ?>.
					</td>
				</td>
				<?php endforeach; ?>
			</table>
		</p>
		
		<script type="text/javascript">
			window.addEvent("domready", function(event){
				// initialize the person filter
				PersonFilter.init({
					"id": "<?php echo $org_id; ?>",
					"url": "<?php echo $org_url; ?>",
					"letter": "<?php echo $org_filter_letter; ?>",
					"dept": "<?php echo $org_filter_dept; ?>",
					"order_by": "<?php echo $org_filter_orderby; ?>",
					"order_dir": "<?php echo $org_filter_orderdir; ?>",
					"order_mappings": <?php echo $org_filter_mappings; ?>});
												
					// add events to the filter
					PersonFilter.addEvents();});
		</script>
		
		<?php $this->load->view("common/footer"); ?>
	</body>
</html>
 