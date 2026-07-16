<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
	<head>
 		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $blog_title; ?> | <?php echo $this->config->item("core.sitename"); ?></title>
		<?php $this->load->view("common/head_libs"); ?>
	</head>
	<body>
		<?php $this->load->view("common/header"); ?>
		
		<p>
			<?php echo $blog_title; ?>
			<br>
			<?php echo $blog_author; ?> &dash; <?php echo $blog_date; ?>
		</p>
		
		<p>
			<table width="750">
				<tr>
					<td>
						<?php echo $blog_entry; ?>
					</td>
				</tr>
			</table>
		</p>
		
		<p>
			<table width="750">
				<tr>
					<td width="375">
						<?php if (!empty($blog_prev_url)): ?>
							<a href="<?php echo $blog_prev_url; ?>">Previous Post</a>
						<?php endif; ?>
					</td>
					<td width="375" align="right">
						<?php if (!empty($blog_next_url)): ?>
							<a href="<?php echo $blog_next_url; ?>">Next Post</a>
						<?php endif; ?>
					</td>
				</tr>
			</table>
		</p>
		
		<?php $this->load->view("common/footer"); ?>
	</body>
</html>
 