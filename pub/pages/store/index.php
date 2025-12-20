<div class="header">
	<h1>Stores</h1>
</div>

<nav class="breadcrumbs">
	<ul>
		<li><a href="/">Accounts</a></li>
		<li>Stores</li>
	</ul>
</nav>

<div class="content">
	<div class="row">
		<p>Record Count: <span id="data-table-count">?</span></p>
		<div class="data-table" id="data-table">
			<div class="data-table-row header-row">
				<div class="data-table-cell header-cell" data-id="name">
					<div class="data-table-cell-label">Name</div>
				</div>
			</div>
		</div>
	</div>
</div>

<template id="template">
	<a href="/store/STORE_ID/summary" class="data-table-row">
		<div class="data-table-cell" data-id="name">
			<div class="data-table-cell-label">Name</div>
			<div class="data-table-cell-content"></div>
		</div>
	</a>
</template>