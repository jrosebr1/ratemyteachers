<a href="/"><?php echo $this->config->item("core.sitename"); ?></a>
<?php if ($this->user->isLoggedIn()): ?>
<a href="/dashboard">Dashboard</a>
<a href="/logout">Logout</a>
<?php else: ?>
<a href="/login">Login</a>
<a href="/register">Register</a>
<?php endif; ?>
<a href="/blog">Blog</a>
<a href="/organization">Organization Directory</a>
<a href="/person">Person Directory</a>