			<form id="import_contacts" method="post" action="/import_contacts">
				My email: <input type="text" id="email" name="email"><br>
				My email password: <input type="password" id="password" name="password"><br>
				<input type="hidden" id="service" name="service" value="">
				<input type="submit" value="Tell Your Friends">
			</form>
			
			<form id="contact_list" method="post" action="/send_contact_emails">
				Your contacts have been successfully imported! Which friends/co-workers do you want to tell about <?php echo $this->config->item("core.sitename"); ?>?
				
				<ul>
				</ul>
				
				<input type="submit" value="Send Emails">
			</form>
			
			<div id="contact_conf">
				Thank you!<br>
				Your friends/co-workers have been invited to <?php echo $this->config->item("core.sitename"); ?>!
			</div>