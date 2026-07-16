<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
	<head>
 		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Rate <?php echo $person_name; ?> from <?php echo $org_name; ?> in <?php echo $org_city; ?>, <?php echo $org_state; ?> | <?php echo $this->config->item("core.sitename"); ?></title>
		<?php $this->load->view("common/head_libs"); ?>
		<?php $this->load->view("common/squeezebox"); ?>
		<script type="text/javascript" src="/js/core/person_filter.js"></script>
		<script type="text/javascript" src="/js/core/text_limiter.js"></script>
		<script type="text/javascript" src="/js/core/ajax_form.js"></script>
		<script type="text/javascript" src="/js/core/rating_form.js"></script>
	</head>
	<body>
		<?php $this->load->view("common/header"); ?>
		
		<p>
			<?php $this->load->view("common/breadcrumb"); ?>
		</p>

		<h1>Rate <?php echo $person_name; ?></h1>
		
		<p>
			<form id="add_rating" action="/add_rating" method="post">
				<p>
					<table cellpadding="10">
						<?php foreach ($rating_numerical_fields as $field): ?>
						<tr>
							<td width="150"><?php echo $field["field_label"]; ?></td>
							<?php foreach ($field["field_range"] as $value): ?>
							<td width="30">
								<input type="radio" name="<?php echo $field["field_name"]; ?>" value="<?php echo $value; ?>">
							</td>
							<?php endforeach; ?>
						</tr>
						<?php endforeach; ?>
					</table>
				</p>
				
				Please provide a detailed description of your experience with <?php echo $person_name; ?>.
				<div>
					[<span id="char_counter"><?php echo $rating_max_comment_chars; ?></span>/<?php echo $rating_max_comment_chars; ?> characters remaining]
				</div>
				<input id="rating_comment" name="rating_comment" type="text" size="75"><br>
				<input name="person_id" type="hidden" value="<?php echo $person_id; ?>">
				<input type="submit" value="Add Rating">
			</form>
		</p>
		
		<script type="text/javascript">
			window.addEvent("domready", function(event){			
				// create the rating form and add events to it					
				ratingForm = new Rating_Form({
					"redirect_url": "<?php echo $person_review_url; ?>",
					"numerical_field_names": "<?php echo $rating_numerical_field_names; ?>",
					"error_msg": "It looks like an error has occurred. Please try submitting your rating in a few minutes."
				});
				ratingForm.addEvents($("rating_comment"), $("char_counter"), <?php echo $rating_max_comment_chars; ?>);

				// add an event to the rating form when it is submitted
				$("add_rating").addEvent("submit", function(event){
					// stop the event from propagating any further
					event.stop();
					event.stopPropagation();
					
					// submit the form
					ratingForm.submit(this);
				});
			});
		</script>

		<?php if (isset($person_just_added)): ?>
		<script type="text/javascript">
			Notifier.create({"persistent": true, "closeable": true});
			Notifier.open("You have successfully added <?php echo $person_name; ?>, please leave a rating for them.");
		</script>
		<?php endif; ?>
				
		<?php $this->load->view("common/footer"); ?>
	</body>
</html>
 