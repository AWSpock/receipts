<?php

$accountData = $data->accounts($userAuth->user()->id());

$recAccount = $accountData->getRecordById($account_id);
if ($recAccount->id() < 0) {
    echo "Account Not Found";
    http_response_code(404);
    die();
}

switch ($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        if (isset($receipt_id)) {
            echo $data->receipts($recAccount->id())->getRecordById($receipt_id)->toString();
        } else {
            $recs = [];
            foreach ($data->receipts($recAccount->id())->getRecords() as $rec) {
                array_push($recs, json_decode($rec->toString()));
            }
            echo json_encode($recs);
        }
        break;
    case "POST":
        break;
    default:
        echo "Unknown Method";
        http_response_code(405);
        break;
}
