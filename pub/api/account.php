<?php

switch ($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        if (isset($account_id)) {
            echo $data->accounts($userAuth->user()->id())->getRecordById($account_id)->toString();
        } else {
            $recs = [];
            foreach ($data->accounts($userAuth->user()->id())->getRecords() as $rec) {
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
