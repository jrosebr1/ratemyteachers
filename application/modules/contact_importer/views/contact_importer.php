<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
	<head>
 		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Tell Your Friends and Co-workers about <?php echo $this->config->item("core.sitename"); ?>! | <?php echo $this->config->item("core.sitename"); ?></title>
		<?php $this->load->view("common/head_libs"); ?>
		<link rel="stylesheet" type="text/css" href="/css/core/contact_importer.css">
		<script type="text/javascript" src="/js/core/ajax_form.js"></script>
		<script type="text/javascript" src="/js/core/contact_importer_form.js"></script>
	</head>
	<body>
		<?php $this->load->view("common/header"); ?>

		<h1>Tell your friends/coworkers about <?php echo $this->config->item("core.sitename"); ?>!</h1>
		
		<p>
			<?php $this->load->view("contact_importer_forms"); ?>
		</p>
		
		<script type="text/javascript">
			window.addEvent("domready", function(event){
				// show the notifier displaying which email services are
				// supported
				Notifier.create({"persistent": true, "closeable": true});
				Notifier.open("Enter your GMail, Yahoo, Hotmail, or AOL email and password to tell your contacts about <?php echo $this->config->item("core.sitename"); ?>!");

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
		
		<?php $this->load->view("common/footer"); ?>
	</body>
</html>
 