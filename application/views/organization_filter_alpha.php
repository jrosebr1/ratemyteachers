<table>
	<tr>
		<?php foreach ($org_alpha_filters as $filter): ?>
			<td>
				<?php if ($filter == "ALL"): ?>
					<?php if ($org_filter_letter == ""): ?>
					<?php echo $filter; ?>
					<?php else: ?>
					<a class="alpha_filter" href="#"><?php echo $filter; ?></a>
					<?php endif; ?>
				<?php else: ?>
					<?php if ($org_filter_letter == $filter): ?>
					<?php echo $filter; ?>
					<?php else: ?>
					<a class="alpha_filter" href="#"><?php echo $filter; ?></a>
					<?php endif; ?>
				<?php endif; ?>
			</td>
		<?php endforeach; ?>
	</tr>
</table>