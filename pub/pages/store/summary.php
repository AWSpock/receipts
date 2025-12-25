<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<div class="header">
    <h1>Store Summary</h1>
</div>

<nav class="breadcrumbs">
    <ul>
        <li><a href="/">Accounts</a></li>
        <li><a href="/store">Stores</a></li>
        <li><?php echo htmlentities($recStore->store()); ?></li>
    </ul>
</nav>

<div class="content">
    <div class="row">
        <h2>Receipts</h2>
        <p>Record Count: <span id="data-table-count">?</span></p>
        <div class="receipts" id="data-table"></div>
    </div>
</div>

<template id="template">
    <div class="receipt">
        <div data-id="edit"><a href="/account/ACCOUNT_ID/receipt/RECEIPT_ID/edit" target="_blank"><i class="fa-solid fa-pencil"></i></a></div>
        <h3 data-id="account"></h3>
        <div class="receipt-info">
            <div data-id="date" data-dateonlyformatter></div>
            <div data-id="amount" data-moneyformatter></div>
            <div data-id="size" data-bitesformatter></div>
            <div data-id="type"></div>
        </div>
        <div data-id="show-preview">
            <p><a href="/api/account/ACCOUNT_ID/view-receipt/RECEIPT_ID" target="_blank">View</a></p>
        </div>
    </div>
</template>