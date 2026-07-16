<select name="dept_list">
	<?php foreach ($departments as $dept): ?>
	<option value="<?php echo $dept; ?>"<?php echo set_select("dept_list", $dept); ?>><?php echo $dept; ?></option>
	<?php endforeach; ?>
</select>