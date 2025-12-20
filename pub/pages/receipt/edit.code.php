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

$receiptData = $data->receipts($recAccount->id());
$recReceipt = $receiptData->getRecordById($receipt_id);
if ($recReceipt->id() < 0) {
    header('Location: /account/' . $recAccount->id() . '/receipt');
    die();
}

$stores = [];

//

if (!empty($_POST)) {
    $recReceipt = Receipt::fromPost($_POST, $_FILES);
    $res = $receiptData->updateRecord($recReceipt);
    $_SESSION['last_message_text'] = $receiptData->actionDataMessage;
    if ($res == 1 || $res == 2) {
        $_SESSION['last_message_type'] = "success";
        // header('Location: /account/' . $recAccount->id() . '/receipt');
        header('Location: /account/' . $recAccount->id() . '/summary');
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
