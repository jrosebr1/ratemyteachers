<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
	<head>
 		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Create Your Account | <?php echo $this->config->item("core.sitename"); ?></title>
		<?php $this->load->view("common/head_libs"); ?>
		<link rel="stylesheet" type="text/css" href="/css/core/register.css">
		<link rel="stylesheet" type="text/css" href="/css/core/contact_importer.css">
		<script type="text/javascript" src="/js/core/ajax_form.js"></script>
		<script type="text/javascript" src="/js/core/contact_importer_form.js"></script>
	</head>
	<body>
		<?php $this->load->view("common/header"); ?>
		
		<h1>Join <?php echo $this->config->item("core.sitename"); ?></h1>
		
		<p>
			<?php if ($step == 0): ?>
			<form id="register_form" name="register_form" method="post">
				The last organization you viewed as...
				
				<p>
					<b><?php echo $org_name; ?>?</b>
				</p>
				
				Do you want to register using this organization?<br>
				<input type="hidden" name="step" value="<?php echo $step; ?>">
				<input type="hidden" name="process" value="1">
				<input type="hidden" name="org_id" value="<?php echo $org_id; ?>">
				<input type="hidden" id="use_org" name="use_org" value="1">
				<input type="submit" value="Yes">
				<input type="button" id="no_btn" value="No">						
			</form>
			
			<script type="text/javascript">
				$("no_btn").addEvent("click", function(event){
					// update the form to stop it from using the
					// current organization
					$("use_org").value = -1;
					
					// submit the registration form
					$("register_form").submit();
				});
			</script>
			<?php elseif ($step == 1): ?>
			<p>
				<?php echo validation_errors("<div class=\"error\">", "</div>"); ?>	
			</p>

			<p>
			<?php if (isset($num_results) && $num_results == 0 && !empty($query)): ?>
				Your query returned 0 results. Perhaps try something different?
			<?php elseif (!empty($query)): ?>
				<div>
					<?php if ($num_results == 1): ?>
						1 result found.
					<?php else: echo $num_results; ?> results found.
					<?php endif; ?>
				</div>
			
				<table id="search_results">
					<?php foreach ($search_results as $search_result): ?>
					<tr id="<?php echo "result_" . $search_result["org_id"]; ?>">
						<td>
							<?php echo $search_result["org_name"]; ?><br>
							<font size="2"><?php echo $search_result["org_city"] . ", " . $search_result["org_state"]; ?></font>
						</td>
					</tr>
					<?php endforeach; ?>
				</table>
			<?php endif; ?>
			</p>

			<form id="register_form" name="register_form" method="post">
				Organization Name: <input type="text" name="search_org" value="<?php echo set_value("search_org"); ?>"><br>
				<?php echo $location_type; ?>:
				<select name="org_location">
					<option value="" <?php echo set_select("org_location", "", TRUE); ?>>Select a <?php echo $location_type; ?></option>
					<?php foreach ($locations as $location): ?>
					<option value="<?php echo $location; ?>" <?php echo set_select("org_location", $location); ?>><?php echo $location; ?></option>
					<?php endforeach; ?>
				</select>
				<br>
				<input type="hidden" id="org_id" name="org_id" value="">
				<input type="hidden" name="step" value="<?php echo $step; ?>">
				<input type="hidden" name="process" value="1">
				<input type="submit" value="Continue">
			</form>
			
			<script type="text/javascript">
				// grab the row elements
				rowElements = $("search_results").getElements("tr");

				// loop over each of the row elements
				for (enumRows = 0; enumRows < rowElements.length; enumRows++)
				{
					// add the click event to each row
					rowElements[enumRows].addEvent("click", function(event){
						// extract the row ID, update the form, and
						// submit it
						rowID = this.getAttribute("id").replace("result_", "");
						$("org_id").value = rowID;
						$("register_form").submit();
					});
				}
			</script>
			<?php elseif ($step == 2): ?>
			<p>
				<?php echo validation_errors("<div class=\"error\">", "</div>"); ?>
			</p>

			<form id="register_form" name="register_form" method="post">
				First Name: <input type="text" name="first_name" value="<?php echo set_value("first_name"); ?>"><br>
				E-Mail: <input type="text" name="email" value="<?php echo set_value("email"); ?>"><br>
				Re-type E-mail: <input type="text" name="conf_email" value="<?php echo set_value("conf_email"); ?>"><br>
				Password: <input type="password" name="password" value="<?php echo set_value("password"); ?>"><br>
				<input type="hidden" name="org_id" value="<?php echo $org_id; ?>">
				<input type="hidden" name="step" value="<?php echo $step; ?>">
				<input type="hidden" name="process" value="1">
				<input type="submit" value="Finish Registering">
			</form>
			<?php elseif ($step == 3): ?>
			<form id="import_contacts" method="post" action="/import_contacts">
				My email: <input type="text" id="email" name="email" value="<?php echo $email; ?>"><br>
				My email password: <input type="password" id="password" name="password"><br>
				<input type="hidden" id="service" name="service" value="">
				<input type="submit" value="Tell Your Friends">
				<a href="/dashboard">Not Now (tell your friends/coworkers later).</a>
			</form>
			
			<form id="contact_list" method="post" action="/send_contact_emails">
				Your contacts have been successfully imported! Which friends/co-workers do you want to tell about <?php echo $this->config->item("core.sitename"); ?>?
				
				<ul>
				</ul>
				
				<input type="submit" value="Send Emails">
			</form>
			
			<div id="contact_conf">
				Thank you!<br>
				Your friends/co-workers have been invited to <?php echo $this->config->item("core.sitename"); ?>!<br>
				Click <a href="/dashboard">here</a> to continue to your dashboard.
			</div>

			<script type="text/javascript">
				window.addEvent("domready", function(event){
					// show the notifier displaying which email services are
					// supported
					Notifier.create({"persistent": true, "closeable": true});
					Notifier.open("<?php echo $first_name; ?>, enter your GMail, Yahoo, Hotmail, or AOL email & password to tell your contacts about <?php echo $this->config->item("core.sitename"); ?>!");

					// create the contact importer form
					importForm = new Contact_Importer_Form({
						"inputs": {
							"input": ["text", "password", "hidden"]},
						"contact_form_id": "import_contacts",
						"contact_list_id": "contact_list"
					});
				
					// add the submit event to contact importer
					$("import_contacts").addEvent("submit", function(event){
						// stop the event from propagating any further
						event.stop();
						event.stopPropagation();
					
						// check to see if the email address or password
						// is empty
						if ($("email").value == "" || $("password").value == "")
						{
							// display the notifier if an email address or
							// password was not provided
							Notifier.create({"persistent": true, "closeable": true});
							Notifier.open("Please enter your email address and password.");
						
							return;
						}
					
						// grab the service from the email address
						service = importForm.getService($("email").value);
						$("service").value = service;
					
						// submit the form
						importForm.submit(this);
					});
				
					// add the submit event to the email contacts form
					$("contact_list").addEvent("submit", function(event){
						// stop the event from propagating any further
						event.stop();
						event.stopPropagation();
					
						// show the contact mailing confirmation
						$("contact_list").setStyle("display", "none");
						$("contact_conf").setStyle("display", "block");
					});
				});
			</script>
			<?php endif; ?>
		</p>
		
		<?php $this->load->view("common/footer"); ?>
	</body>
</html>
 