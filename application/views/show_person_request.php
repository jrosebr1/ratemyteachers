<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
	<head>
 		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Review or Rate <?php echo $person_name; ?> from <?php echo $org_name; ?> in <?php echo $org_city; ?>, <?php echo $org_state; ?> | <?php echo $this->config->item("core.sitename"); ?></title>
		<?php $this->load->view("common/head_libs"); ?>
		<script type="text/javascript" src="/js/core/person_filter.js"></script>
	</head>
	<body>
		<?php $this->load->view("common/header"); ?>
		
		<p>
			<?php $this->load->view("common/breadcrumb"); ?>
		</p>
		
		<h1><?php echo $person_name; ?></h1>
		
		<p>
			What would you like to do?
		</p>
		
		<p>
			<table>
				<tr>
					<td width="200">
						<a href="<?php echo $person_rate_url; ?>">Rate <?php echo $person_name; ?></a>
					</td>
					<td width="200">
						<a href="<?php echo $person_review_url; ?>">Review <?php echo $person_name; ?></a>
					</td>
				</tr>
			</table>
		</p>
				
		<?php $this->load->view("common/footer"); ?>
	</body>
</html>
 