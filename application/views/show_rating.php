<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
	<head>
 		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $person_name; ?>'s from <?php echo $org_city; ?>, <?php echo $org_name; ?> was rated on <?php echo $rating_date; ?> | <?php echo $this->config->item("core.sitename"); ?></title>
		<?php $this->load->view("common/head_libs"); ?>
		<?php $this->load->view("common/squeezebox"); ?>
		<script type="text/javascript" src="/js/core/person_filter.js"></script>
		<script type="text/javascript" src="/js/core/text_limiter.js"></script>
		<script type="text/javascript" src="/js/core/ajax_form.js"></script>
	</head>
	<body>
		<?php $this->load->view("common/header"); ?>
		
		<p>
			<?php $this->load->view("common/breadcrumb"); ?>
		</p>
		
		<h1><?php echo $person_name; ?> was rated on <?php echo $rating_date; ?></h1>
				
		<p>
			<table cellpadding="10" border="1">
				<tr>
					<td width="80">Date</td>
					<td width="125">Scores</td>
					<td width="400">Comment</td>
					<td width="125"></td>
				</tr>
				<tr>
					<td cellpadding="2"><?php echo $rating_date; ?></td>
					<td>
						<font size="2">
							<?php foreach ($rating_scores as $score): ?>
							<?php echo $score["field_label"]; ?>: <?php echo $score["field_score"]; ?><br>
							<?php endforeach; ?>
						</font>
					</td>
					<td><?php echo $rating_comment; ?></td>
					<td>
						<font size="2">
							<a href="<?php echo $rating_flag_url; ?>">Flag Rating</a>
						</font>
					</td>
				</tr>
			</table>
		</p>
		
		<?php if (count($rating_rebuttals) > 0): ?>
		<p>
			<b>Rebuttals:</b>
			
			<table cellpadding="10" border="1">
				<tr>
					<td width="125">Date</td>
					<td width="400">Comment</td>
					<td width="125"></td>
				</tr>
				<?php foreach ($rating_rebuttals as $rebuttal): ?>
				<tr>
					<td width="125"><?php echo $rebuttal["rebuttal_date"]; ?></td>
					<td width="400"><?php echo $rebuttal["rebuttal_comment"]; ?></td>
					<td width="125">
						<font size="2">
							<a href="<?php echo $rebuttal["rebuttal_flag_url"]; ?>">Flag Rebuttal</a>
						</font>
					</td>
				</tr>
				<?php endforeach; ?>
			</table>
		</p>
		<?php endif; ?>
		
		<p>
			Comment on <?php echo $person_name; ?>'s rating.
			<form id="add_rebuttal" action="/add_rebuttal" method="post">
				<div>
					[<span id="char_counter"><?php echo $rebuttal_max_comment_chars; ?></span>/<?php echo $rebuttal_max_comment_chars; ?> characters remaining]
				</div>
				Comment: <input id="rebuttal_comment" name="rebuttal_comment" type="text" size="75"><br>
				<input name="rating_id" type="hidden" value="<?php echo $rating_id; ?>">
				<input type="submit" value="Add Rebuttal">
			</form>
		</p>
		
		<p>
			<table cellpadding="10">
				<tr>
					<td width="125">
						<?php if (!empty($rating_prev_url)): ?>
						<a href="<?php echo $rating_prev_url; ?>">&laquo; Previous Rating</a>
						<?php endif; ?>
					</td>
					<td width="400" align="center"><a href="<?php echo $person_review_url; ?>">All Ratings for <?php echo $person_name; ?></a></td>
					<td width="125">
						<?php if (!empty($rating_next_url)): ?>
						<a href="<?php echo $rating_next_url; ?>">Next Rating &raquo;</a>
						<? endif; ?>
					</td>
				</tr>
			</table>
		</p>

		<script type="text/javascript">
			window.addEvent("domready", function(event){
				// create the rebuttal form and add events to it					
				rebuttalForm = new AJAX_Form({
					"redirect_url": "<?php echo $rating_url; ?>",
					"error_msg": "It looks like an error has occurred. Please try submitting your rebuttal in a few minutes."
				});
				rebuttalForm.addEvents($("rebuttal_comment"), $("char_counter"), <?php echo $rebuttal_max_comment_chars; ?>);

				// add an event to the rebuttal form when it is submitted
				$("add_rebuttal").addEvent("submit", function(event){
					// stop the event from propagating any further
					event.stop();
					event.stopPropagation();
					
					// submit the form
					rebuttalForm.submit(this);
				});
			});
		</script>

		<?php if ($this->session->flashdata("add_rebuttal_successful")): ?>
		<script type="text/javascript">
			Notifier.create({"persistent": true, "closeable": true});
			Notifier.open("Your rebuttal has been successfully added.");
		</script>
		<?php endif; ?>

		<?php if ($this->session->flashdata("flag_rating_successful")): ?>
		<script type="text/javascript">
			Notifier.create({"persistent": true, "closeable": true});
			Notifier.open("You have successfully flagged <?php echo $person_name; ?>'s rating on <?php echo $rating_date; ?>.");
		</script>
		<?php endif; ?>

		<?php if ($this->session->flashdata("flag_rebuttal_successful")): ?>
		<script type="text/javascript">
			Notifier.create({"persistent": true, "closeable": true});
			Notifier.open("You have successfully flagged <?php echo $person_name; ?>'s rebuttal.");
		</script>
		<?php endif; ?>
						
		<?php $this->load->view("common/footer"); ?>
	</body>
</html>
 