<?php

$accountData = $data->accounts($userAuth->user()->id());

$recAccount = $accountData->getRecordById($account_id);
if ($recAccount->id() < 0) {
    header('Location: /');
    die();
}
if (!($recAccount->isOwner() || $recAccount->isManager())) {
    header('Location: /account/' . $recAccount->id() . '/summary');
    die();
}

$recReceipt = new Receipt();

$stores = [];

// 

if (!empty($_POST)) {
    $receiptData = $data->receipts($recAccount->id());
    $recReceipt = Receipt::fromPost($_POST, $_FILES);
    $receipt_id = $receiptData->insertRecord($recReceipt);
    $_SESSION['last_message_text'] = $receiptData->actionDataMessage;
    if ($receipt_id > 0) {
        $_SESSION['last_message_type'] = "success";
        // header('Location: /account/' . $recAccount->id() . '/receipt');
        header('Location: /account/' . $recAccount->id() . '/receipt/' . $receipt_id . "/edit");
        die();
    } else {
        $_SESSION['last_message_type'] = "danger";
    }
} else {
    $storeData = $data->stores($userAuth->user()->id());
    foreach ($storeData->getRecords() as $store) {
        if (!in_array($store->store(), $stores))
            array_push($stores, $store->store());
    }
    sort($stores);
}
