<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
	<head>
 		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $person_name; ?>'s Ratings - <?php echo $org_city; ?>, <?php echo $org_state; ?>, <?php echo $org_name; ?> | <?php echo $this->config->item("core.sitename"); ?></title>
		<?php $this->load->view("common/head_libs"); ?>
		<script type="text/javascript" src="/js/core/person_filter.js"></script>
	</head>
	<body>
		<?php $this->load->view("common/header"); ?>
		
		<p>
			<?php $this->load->view("common/breadcrumb"); ?>
		</p>
		
		<h1><?php echo $person_name; ?> | <?php echo $org_name; ?></h1>
		
		<p>
			<?php foreach ($person_aggr_scores as $aggr): ?>
			Overall <?php echo $aggr["aggr_name"]; ?>: <?php echo $aggr["aggr_score"]; ?><br>
			<?php endforeach; ?>
			Total Ratings: <?php echo $person_total_ratings; ?><br>
			<a href="<?php echo $person_rate_url; ?>">Rate Now</a>
		</p>
		
		<p>
			<table cellpadding="10" border="1">
				<tr>
					<td width="80">Date</td>
					<td width="125">Scores</td>
					<td width="400">Comment</td>
					<td width="125"></td>
				</tr>
				<?php foreach ($person_ratings as $rating): ?>
				<tr>
					<td cellpadding="2"><a href="<?php echo $rating["rating_url"]; ?>"><?php echo $rating["rating_date"]; ?></a></td>
					<td>
						<font size="2">
							<?php foreach ($rating["rating_scores"] as $score): ?>
							<?php echo $score["field_label"]; ?>: <?php echo $score["field_score"]; ?><br>
							<?php endforeach; ?>
						</font>
					</td>
					<td><?php echo $rating["rating_comment"]; ?></td>
					<td>
						<font size="2">
							<a href="<?php echo $rating["rating_url"]; ?>">Review</a> or
							<a href="<?php echo $rating["rating_flag_url"]; ?>">Flag Rating</a>
						</font>
					</td>
				</tr>
			<?php endforeach; ?>
			</table>
		</p>

		<p>
			Page:
			<?php foreach ($person_rating_pagination as $page): ?>
			<span>
				<?php if ($page["pagination_num"] == $person_cur_page): ?>
				<?php echo $page["pagination_num"]; ?>
				<?php else: ?>
				<a href="<?php echo $page["pagination_url"]; ?>"><?php echo $page["pagination_num"]; ?></a>
				<?php endif; ?>
			</span>
			<?php endforeach; ?>
		</p>
		
		<p>
			<?php echo $person_name; ?>'s Feed
			
			<table>
				<?php foreach ($feed_items as $feed_item): ?>
				<tr>
					<td>
						<?php echo $person_name; ?> from <a href="<?php echo $breadcrumb["org_url"]; ?>"><?php echo $org_name; ?></a> was <a href="<?php echo $feed_item["feed_url"]; ?>">rated</a> <?php echo $feed_item["feed_date"]; ?>.
					</td>
				</td>
				<?php endforeach; ?>
			</table>
		</p>

		<?php if ($this->session->flashdata("add_rating_successful")): ?>
		<script type="text/javascript">
			Notifier.create({"persistent": true, "closeable": true});
			Notifier.open("Your rating was successfully added.");
		</script>
		<?php endif; ?>
						
		<?php $this->load->view("common/footer"); ?>
	</body>
</html>
 