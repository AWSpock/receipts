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

//

if (!empty($_POST)) {
    $recAccount = Account::fromPost($_POST);
    $res = $accountData->updateRecord($recAccount);
    $_SESSION['last_message_text'] = $accountData->actionDataMessage;
    if ($res == 1 || $res == 2) {
        $_SESSION['last_message_type'] = "success";
        header('Location: /account/' . $recAccount->id() . '/summary');
        die();
    } else {
        $_SESSION['last_message_type'] = "danger";
    }
}
