<?php

$accountData = $data->accounts($userAuth->user()->id());

$recAccount = $accountData->getRecordById($account_id);
if ($recAccount->id() < 0) {
    header('Location: /');
    die();
}
if (!$recAccount->isOwner()) {
    header('Location: /account/' . $recAccount->id() . '/summary');
    die();
}

$recAccount->store_receipts($data->receipts($recAccount->id())->getRecords());

//

if (!empty($_POST)) {
    $res = $accountData->deleteRecord($recAccount);
    $_SESSION['last_message_text'] = $accountData->actionDataMessage;
    if ($res == 1) {
        $_SESSION['last_message_type'] = "success";
        header('Location: /');
        die();
    } else {
        $_SESSION['last_message_type'] = "danger";
    }
}
?>