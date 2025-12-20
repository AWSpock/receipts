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
            $receipt = $data->receipts($recAccount->id())->getRecordById($receipt_id);

            header('Content-Type: ' . $receipt->file_type());
            header('Last-Modified: ' . $receipt->file_modified());
            header('Content-Length: ' . $receipt->file_size());
            header('Content-Disposition: inline; filename="' . $receipt->file_name() . '"');
            header('Content-Transfer-Encoding: binary');
            echo file_get_contents($receipt->file_path());
        } else {
            // $recs = [];
            // foreach ($data->receipts($recAccount->id())->getRecords() as $rec) {
            //     array_push($recs, json_decode($rec->toString()));
            // }
            // echo json_encode($recs);
        }
        break;
    case "POST":
        break;
    default:
        echo "Unknown Method";
        http_response_code(405);
        break;
}
