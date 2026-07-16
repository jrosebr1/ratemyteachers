<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
	<head>
 		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Flag a Rebuttal | <?php echo $this->config->item("core.sitename"); ?></title>
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

		<h1>Flag <?php echo $person_name; ?>'s Rebuttal on <?php echo $rebuttal_date; ?></h1>
		
		<p>
			<a href="<?php echo $rating_review_url; ?>">Back to rating page.</a>
		</p>

		<p>
			Only flag a rebuttal if:
			
			<ul>
				<li>it contains <b>vulgar</b> or <b>profane</b> words.</li>
				<li>it is <b>sexual in nature</b> &dash; including "Sexy" or "Hot".</li>
				<li>it has to do with <b>personal appearance</b> (cute, short, fat, bad clothes, etc.).</li>
				<li>it has to do with <b>physical disabilities</b> (stutters, limps, wears a hearing aid, etc.).</li>
				<li>it involves <b>name-calling</b> (jerk, creep, etc.).</li>
				<li>it references <b>mental problems</b> or <b>alcohol/drug use.</b></li>
				<li>it references problems with the <b>law</b>.</li>
				<li>it references race, religion, ethnic background, sexual orientation, age.</li>
				<li>it includes <b>names</b> or initials of other students or any email addresses.</li>
				<li>it references the <b>person's personal life</b>, including family members (Just got married, Don't like her son, Wife is pretty, How did he afford that car? etc.).</li>
				<li>it contains <b>advertising</b>.</li>
			</ul>
		</p>

		<p>
			<table cellpadding="10" border="1">
				<tr>
					<td width="125">Date</td>
					<td width="400">Comment</td>
				</tr>
				<tr>
					<td cellpadding="2"><?php echo $rebuttal_date; ?></td>
					<td><?php echo $rebuttal_comment; ?></td>
				</tr>
			</table>
		</p>

		<?php echo validation_errors("<div class=\"error\">", "</div>"); ?>
		
		<p>
			<form id="flag_rebuttal" action="/flag_rebuttal" method="post">
				After reading the rules above, please tell us why you are flagging this rebuttal.<br>
				<div> 
					[<span id="char_counter"><?php echo $max_flag_reason_chars; ?></span>/<?php echo $max_flag_reason_chars; ?> characters remaining]
				</div> 
				Flag Reason: <input id="flag_reason" name="flag_reason" type="text" size="75"><br>
				<input name="rebuttal_id" type="hidden" value="<?php echo $rebuttal_id; ?>">
				<input type="submit" value="Flag This Rebuttal">
			</form>
		</p>
		
		<script type="text/javascript">
			window.addEvent("domready", function(event){
				// create the flag form and add events to it					
				flagForm = new AJAX_Form({
					"redirect_url": "<?php echo $rating_review_url; ?>",
					"error_msg": "It looks like an error has occurred. Please try flagging the rebuttal again in a few minutes."
				});
				flagForm.addEvents($("flag_reason"), $("char_counter"), <?php echo $max_flag_reason_chars; ?>);

				// add an event to the flag form when it is submitted
				$("flag_rebuttal").addEvent("submit", function(event){
					// stop the event from propagating any further
					event.stop();
					event.stopPropagation();
					
					// submit the form
					flagForm.submit(this);
				});
			});
		</script>
						
		<?php $this->load->view("common/footer"); ?>
	</body>
</html>
 