<div class="header">
	<h1>Account Documents</h1>
</div>

<nav class="breadcrumbs">
	<ul>
		<li><a href="/">Accounts</a></li>
		<li><a href="/account/<?php echo htmlentities($recAccount->id()); ?>/summary"><?php echo htmlentities($recAccount->name()); ?></a></li>
		<li>Documents</li>
	</ul>
</nav>

<div class="content">
	<div class="row">
		<a href="/account/<?php echo htmlentities($recAccount->id()); ?>/document/create" class="button primary"><i class="fa-solid fa-plus"></i>Add Document</a>
	</div>
	<div class="row">
		<p>Record Count: <span id="data-table-count">?</span></p>
		<div class="data-table" id="data-table">
			<div class="data-table-row header-row">
				<div class="data-table-cell header-cell" data-id="name">
					<div class="data-table-cell-label">Name</div>
				</div>
				<div class="data-table-cell header-cell" data-id="description">
					<div class="data-table-cell-label">Description</div>
				</div>
				<!-- <div class="data-table-cell header-cell" data-id="created">
					<div class="data-table-cell-label">Created</div>
				</div> -->
				<div class="data-table-cell header-cell" data-id="updated">
					<div class="data-table-cell-label">Updated</div>
				</div>
			</div>
		</div>
	</div>
</div>

<template id="template">
	<a href="/account/ACCOUNT_ID/document/RECEIPT_ID/edit" class="data-table-row">
		<div class="data-table-cell" data-id="name">
			<div class="data-table-cell-label">Name</div>
			<div class="data-table-cell-content"></div>
		</div>
		<div class="data-table-cell header-cell" data-id="description">
			<div class="data-table-cell-label">Description</div>
			<div class="data-table-cell-content"></div>
		</div>
		<!-- <div class="data-table-cell" data-id="created">
			<div class="data-table-cell-label">Create</div>
			<div class="data-table-cell-content" data-dateformatter></div>
		</div> -->
		<div class="data-table-cell" data-id="updated">
			<div class="data-table-cell-label">Updated</div>
			<div class="data-table-cell-content" data-dateformatter></div>
		</div>
	</a>
</template>