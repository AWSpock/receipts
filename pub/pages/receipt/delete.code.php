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

// $recReceipt->store_bills($data->bills($recAccount->id(), $recReceipt->id())->getRecords());

//

if (!empty($_POST)) {
    $res = $receiptData->deleteRecord($recReceipt);
    $_SESSION['last_message_text'] = $receiptData->actionDataMessage;
    if ($res == 1) {
        $_SESSION['last_message_type'] = "success";
        // header('Location: /account/' . $recAccount->id() . '/receipt');
        header('Location: /account/' . $recAccount->id() . '/summary');
        die();
    } else {
        $_SESSION['last_message_type'] = "danger";
    }
}
?>