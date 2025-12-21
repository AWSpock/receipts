<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<div class="header">
    <h1>Account Summary</h1>
</div>

<nav class="breadcrumbs">
    <ul>
        <li><a href="/">Accounts</a></li>
        <li><?php echo htmlentities($recAccount->name()); ?></li>
    </ul>
</nav>

<div class="content">
    <div class="row">
        <div class="options">
            <form method="post" action="" id="frm" class="inline">
                <?php
                if ($recAccount->favorite() == "No") {
                ?>
                    <button type="submit" name="warranty.favorite" value="Yes" class="link secondary" title="Add Favorite"><i class="fa-regular fa-star"></i></button>
                <?php
                } else {
                ?>
                    <button type="submit" name="warranty.favorite" value="No" class="link secondary" title="Remove Favorite"><i class="fa-solid fa-star"></i></button>
                <?php
                }
                ?>
            </form>
            <?php
            if ($recAccount->isOwner()) {
            ?>
                <a href="/account/<?php echo htmlentities($account_id); ?>/edit" class="button secondary"><i class="fa-solid fa-pencil"></i>Edit Account</a>
            <?php
            }
            ?>
        </div>
    </div>

    <div class="row">
        <h2>Receipts</h2>
        <div class="options">
            <?php
            if ($recAccount->isOwner() || $recAccount->isManager()) {
            ?>
                <a href="/account/<?php echo htmlentities($account_id); ?>/receipt/create" class="button secondary"><i class="fa-solid fa-plus"></i>Create Receipt</a>
            <?php
            }
            ?>
        </div>
        <p>Record Count: <span id="data-table-count">?</span></p>
        <div class="receipts" id="data-table"></div>
    </div>
</div>

<template id="template">
    <div class="receipt">
        <div data-id="edit"><a href="/account/ACCOUNT_ID/receipt/RECEIPT_ID/edit"><i class="fa-solid fa-pencil"></i></a></div>
        <h3 data-id="store"></h3>
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