<?php

switch ($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        if (isset($store_id)) {
            $recs = [];
            foreach ($data->stores($userAuth->user()->id())->getReceiptsByName($account_id) as $rec) {
                array_push($recs, json_decode($rec->toString()));
            }
            echo json_encode($recs);
        } else {
            // $recs = [];
            // foreach ($data->stores($userAuth->user()->id())->getRecords() as $rec) {
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
