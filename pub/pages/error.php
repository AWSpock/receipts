<div class="header">
	<h1>Error</h1>
</div>

<nav class="breadcrumbs">
	<ul>
		<li><a href="/">Accounts</a></li>
		<li>Error</li>
	</ul>
</nav>

<div class="content">
	<div class="row">
		<?php
		if (isset($_GET['message'])) {
		?>
			<p>Message: <?php echo htmlspecialchars($_GET['message']); ?></p>
		<?php
		}
		if (isset($_GET['route'])) {
		?>
			<p>Route: <?php echo htmlspecialchars($_GET['route']); ?></p>
		<?php
		}
		?>
	</div>
</div>