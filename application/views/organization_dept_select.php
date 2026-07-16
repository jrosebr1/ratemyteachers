<select id="dept_filter">
	<?php foreach ($org_depts as $dept): ?>
		<?php if (strtoupper($dept) == strtoupper($org_filter_dept)): ?>
		<option value="<?php echo $dept; ?>" selected><?php echo $dept; ?></option>	
		<?php else: ?>
		<option value="<?php echo $dept; ?>"><?php echo $dept; ?></option>
		<?php endif; ?>
	<?php endforeach; ?>
</select>