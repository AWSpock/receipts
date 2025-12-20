<?php

$recAccount = new Account();

// 

if (!empty($_POST)) {
    $data = new DataAccess();
    $accountData = $data->accounts($userAuth->user()->id());

    $recAccount = Account::fromPost($_POST);
    $account_id = $accountData->insertRecord($recAccount);
    $_SESSION['last_message_text'] = $accountData->actionDataMessage;
    if ($account_id > 0) {
        $_SESSION['last_message_type'] = "success";
        header('Location: /account/' . $account_id . '/summary');
        die();
    } else {
        $_SESSION['last_message_type'] = "danger";
    }
}
