		<div>
			<?php if (isset($homepage_footer_drilldown)): ?>
			<a href="<?php echo $homepage_footer_drilldown["org_drilldown_url"]; ?>">Organizations</a>
			<a href="<?php echo $homepage_footer_drilldown["person_drilldown_url"]; ?>">Persons</a>
			<?php else: ?>
			<a href="/organization">Organizations</a>
			<a href="/person">Persons</a>
			<?php endif; ?>
			<a href="/contact">Contact</a>
			<a href="/faq">FAQ</a>
			<a href="/legal">Legal</a>
			<a href="/refer_a_friend">Refer a Friend</a>
		</div>