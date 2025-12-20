<?php

$accountData = $data->accounts($userAuth->user()->id());
$categories = $accountData->getRecords();

$recAccount = $accountData->getRecordById($account_id);
if ($recAccount->id() < 0) {
    header('Location: /');
    die();
}

$recAccount->store_receipts($data->receipts($recAccount->id())->getRecords());

//

if (!empty($_POST['warranty_favorite'])) {
    $res = 0;
    switch ($_POST['warranty_favorite']) {
        case "Yes":
            $res = $accountData->setFavorite($recAccount->id());
            break;
        case "No":
            $res = $accountData->removeFavorite($recAccount->id());
            break;
    }
    $_SESSION['last_message_text'] = $accountData->actionDataMessage;
    if ($res === true) {
        $_SESSION['last_message_type'] = "success";
        header('Location: /account/' . $recAccount->id() . '/summary');
        die();
    } else {
        $_SESSION['last_message_type'] = "danger";
    }
}