<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
	<head>
 		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Find <?php echo ucwords($drilldown_type); ?>s | <?php echo $this->config->item("core.sitename"); ?></title>
		<?php $this->load->view("common/head_libs"); ?>
	</head>
	<body>
		<?php $this->load->view("common/header"); ?>

		<p>
			<?php $this->load->view("drilldown_filter_alpha"); ?>
		</p>

		<p>
			<table width="900">
				<tr>
					<td width="450">
						<?php echo $drilldown_range_start; ?>
					</td>
					<td align="right" width="450">
						<?php echo $drilldown_range_end; ?>
					</td>
				</tr>
			</table>
			
			<table width="900" border="1">
				<?php foreach ($drilldown_entries as $entry): ?>
				<tr>
					<td width="450">
						<a href="<?php echo $entry["left"]["url"]; ?>"><?php echo $entry["left"]["name"]; ?></a>
					</td>
					<?php if (!empty($entry["right"])): ?>
					<td width="450">
						<a href="<?php echo $entry["right"]["url"]; ?>"><?php echo $entry["right"]["name"]; ?></a>
					</td>
					<?php endif; ?>
				</tr>
				<?php endforeach; ?>
			</table>
		</p>
		
		<?php $this->load->view("common/footer"); ?>
	</body>
</html>
 