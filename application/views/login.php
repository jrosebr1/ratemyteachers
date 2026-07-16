<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
	<head>
 		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Login | <?php echo $this->config->item("core.sitename"); ?></title>
		<?php $this->load->view("common/head_libs"); ?>
	</head>
	<body>
		<?php $this->load->view("common/header"); ?>
		
		<h1>Login</h1>

		<?php echo validation_errors("<div class=\"error\">", "</div>"); ?>

		<?php if ($login_error): ?>
		<div class="error">
			Invalid email/password combination.
		</div>
		<?php endif; ?>
		
		<form action="/login" method="post">
			<input type="hidden" name="process" value="1">
			Email: <input type="text" name="email" value="<?php echo set_value("email"); ?>"><br>
			Password: <input type="password" name="password"><br>
			<input type="submit" value="Login">
		</form>
		<br>
		
		<?php $this->load->view("common/footer"); ?>
	</body>
</html>
 