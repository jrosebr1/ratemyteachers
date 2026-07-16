<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
	<head>
 		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Organization Search Results | <?php echo $this->config->item("core.sitename"); ?></title>
		<?php $this->load->view("common/head_libs"); ?>
	</head>
	<body>
		<?php $this->load->view("common/header"); ?>
		
		<h1>Search Results</h1>
		
		<p>
		<?php if ($num_results == 0 && !empty($query)): ?>
			Your query returned 0 results. Perhaps try something different?
		<?php elseif (!empty($query)): ?>
			<div>
				<?php if ($num_results == 1): ?>
					1 result found.
				<?php else: echo $num_results; ?> results found.
				<?php endif; ?>
			</div>
			
			<table>
				<?php foreach ($search_results as $search_result): ?>
				<tr>
					<td>
						<a href="<?php echo $search_result["org_url"]; ?>"><?php echo $search_result["org_name"]; ?></a><br>
						<font size="2"><?php echo $search_result["org_city"] . ", " . $search_result["org_state"]; ?></font>
					</td>
				</tr>
				<?php endforeach; ?>
			</table>
		<?php endif; ?>
		</p>

		<?php echo validation_errors("<div class=\"error\">", "</div>"); ?>	
	
		<p>
			<form action="/search" method="post">
				Search again:
				<input type="text" name="search_org" value="<?php echo set_value("search_org"); ?>">
				<input type="submit" value="Search">
			</form>
		</p>
		
		<?php $this->load->view("common/footer"); ?>
	</body>
</html>
 