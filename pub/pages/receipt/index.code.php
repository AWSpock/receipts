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