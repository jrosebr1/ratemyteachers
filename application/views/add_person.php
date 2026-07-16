<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
	<head>
 		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Add Person to <?php echo $org_name; ?> | <?php echo $this->config->item("core.sitename"); ?></title>
		<?php $this->load->view("common/head_libs"); ?>
	</head>
	<body>
		<?php $this->load->view("common/header"); ?>
		
		<h1>Add Person to <?php echo $org_name; ?></h1>
		
		<p>
			Back to <a href="<?php echo $org_url; ?>"><?php echo $org_name; ?></a>
		</p>
		
		<?php echo validation_errors("<div class=\"error\">", "</div>"); ?>
		
		<p>
			<form action="/add_person" method="post">
				First Name: <input type="text" name="firstname" value="<?php echo set_value("firstname"); ?>"><br>
				Last Name: <input type="text" name="lastname" value="<?php echo set_value("lastname"); ?>"><br>
				Department: <?php $this->load->view("department_list.php"); ?><br>
				Gender:
				<select name="gender">
					<option value="M">Male</option>
					<option value="F">Female</option>
				</select>
				<br>
				<input type="submit" value="Add Person">
			</form>
		</p>
		
		<?php $this->load->view("common/footer"); ?>
	</body>
</html>
 