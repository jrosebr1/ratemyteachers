		<?php foreach (CSSLoader::load() as $css): ?>
		<link rel="stylesheet" type="text/css" href="<?php echo $css; ?>">
		<?php endforeach; ?>
		<?php foreach (JSLoader::load() as $js): ?>
		<script type="text/javascript" src="<?php echo $js; ?>"></script>
		<?php endforeach; ?>