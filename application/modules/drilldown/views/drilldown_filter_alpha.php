<table>
	<tr>
		<?php foreach ($drilldown_alpha_filters as $filter): ?>
			<td>
				<?php if ($filter == "ALL"): ?>
					<?php if ($drilldown_filter_letter == "ALL" || $drilldown_filter_letter == ""): ?>
					<?php echo $filter; ?>
					<?php else: ?>
					<a href="<?php echo "/" . $drilldown_type . "/" . $filter; ?>"><?php echo $filter; ?></a>
					<?php endif; ?>
				<?php else: ?>
					<?php if ($drilldown_filter_letter == $filter): ?>
					<?php echo $filter; ?>
					<?php else: ?>
					<a href="<?php echo "/" . $drilldown_type . "/" . $filter; ?>"><?php echo $filter; ?></a>
					<?php endif; ?>
				<?php endif; ?>
			</td>
		<?php endforeach; ?>
	</tr>
</table>